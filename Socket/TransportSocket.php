<?php
/**
 * Copyright (c) 2008-2009, Fabian "smf68" Hahn <smf68@smf68.ch>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace PhpFramework\Socket;

use \PhpFramework\PhpFramework as PF;
use \PhpFramework\Event\Event;
use \PhpFramework\LineStringBuffer\LineStringBuffer;

/**
 * Represents a socket that transports data (read and write)
 */
abstract class TransportSocket extends Socket
{
	/**
	 * Reading modes
	 */
	const READING_MODE_LINES = 0;
	const READING_MODE_RAW = 1;

	/**
	 * Event that gets triggered when the socket disconnects
	 * @var Event
	 */
	protected $disconnected_event;

	/**
	 * Event that gets triggered when something is read from the socket
	 * @var Event
	 */
	protected $read_event;

	/**
	 * Event that gets triggered when something is sent to the socket
	 * @var Event
	 */	
	protected $send_event;

	/**
	 * This socket's reading mode
	 * @var int
	 */
	protected $reading_mode;

	/**
	 * Stores read data from the socket if line reading mode is used
	 * @var LineStringBuffer
	 */
	protected $reading_buffer;

	/**
	 * Specifies whether this socket uses write throttling, if yes, $writing_throttle_bps contains the maximum bps, otherwise null
	 * @var int
	 */
	protected $writing_throttle_bps;

	/**
	 * Factor that specifies the maximum written bytes possible in one send attempt (maximum = $writing_throttle_bps * $writing_throttle_peak)
	 * @var double
	 */
	protected $writing_throttle_peak;

	/**
	 * Stores the last writing time
	 * @var integer
	 */
	protected $writing_last_time;

	/**
	 * Stores the writing data that will be sent out
	 * @var LineStringBuffer
	 */
	protected $writing_buffer;

	/**
	 * Stores the line ending used for writing lines
	 * @var string
	 */
	protected $writing_line_ending;

	/**
	 * Constructs a socket object
	 * @override
	 */
	public function __construct()
	{
		parent::__construct();

		$this->disconnected_event = new Event();
		$this->read_event = new Event();
		$this->send_event = new Event();
		$this->reading_mode = self::READING_MODE_LINES;
		$this->reading_buffer = new LineStringBuffer();
		$this->writing_throttle_bps = null;
		$this->writing_line_ending = "\n";
	}

	/**
	 * Writes data directly into the socket
	 * @param string $data
	 */
	public function write($data)
	{
		if(!$this->isWriteThrottlingEnabled())
		{
			if($this->isConnected())
			{
				if(socket_write($this->resource, $data) !== false)
				{
					$this->send_event->triggerEvent($this, $data);
				}
				else
				{
					PF::log(PF::LOG_WARNING, "Failed to write to socket " . $this->socket_id . ". " . $this->getLastSocketError());
				}
			}
			else
			{
				PF::log(PF::LOG_WARNING, "Cannot write to unconnected socket");
			}
		}
		else
		{
			throw new Exception("Direct socket writing can only be used if write throttling is disabled.");
		}
	}

	/**
	 * Writes a line to the socket
	 * @param string $line					(optional) the line to send
	 * @param boolean $high_priority		(optional) prepend the line to the buffer instead of extend?
	 * @param boolean $force_flush			(optional) skip throttling and write direchtly?
	 */
	public function writeLine($line = "", $high_priority = false, $force_flush = false)
	{
		if($this->isWriteThrottlingEnabled() && !$force_flush)
		{
			if($this->isConnected())
			{
				$linelen = strlen($line . $this->writing_line_ending);
					
				// Check if the line would even burst the peak
				if($linelen > ($this->writing_throttle_bps * $this->writing_throttle_peak))
				{
					$line = substr($line, 0, ($this->output_throttle_bps * $this->output_throttle_peak));
					PF::log(PF::LOG_WARNING, "Trimmed too long writeLine for socket " . $this->socket_id);
				}

				if(strspn($line, $this->writing_line_ending))
				{
					PF::log(PF::LOG_WARNING, "Warning: Line to be written contains newline characters!");
				}

				if($high_priority)
				{
					$this->writing_buffer->prepend($line . $this->writing_line_ending);
				}
				else
				{
					$this->writing_buffer->extend($line . $this->writing_line_ending);
				}
			}
			else
			{
				PF::log(PF::LOG_WARNING, "Cannot write to unconnected socket");
			}
		}
		else
		{
			$this->write($line . $this->writing_line_ending);
		}
	}

	/**
	 * Disconnects a socket
	 * @override
	 */
	public function disconnect()
	{
		if($this->resource)
		{
			socket_close($this->resource);
			$this->disconnected_event->triggerEvent($this);
			$this->reading_buffer->clear();
		}

		parent::disconnect();
	}

	/**
	 * Handles a read event from socket_select
	 */
	public function handleSelectEvent()
	{
		if(($read = socket_read($this->resource, 4096, PHP_BINARY_READ)) !== false && !empty($read)) // Check if the socket isn't closed yet
		{
			// Something readable in it
			switch($this->reading_mode)
			{
				case self::READING_MODE_LINES:
					$this->reading_buffer->extend($read);

					while($this->reading_buffer->hasLine() !== false)
					{
						$this->read_event->triggerEvent($this, $this->reading_buffer->popLine());
					}
				break;
				case self::READING_MODE_RAW:
					$this->read_event->triggerEvent($this, $read);
				break;
				default:
					throw new Exception("Invalid reading mode " . $this->reading_mode . " set!");
				break;
			}
		}
		else
		{
			// Retrieve the remaining buffer if line reading mode
			if($this->reading_mode == self::READING_MODE_LINES)
			{
				$buffer = $this->reading_buffer->getBuffer();
				
				if(strlen($buffer))
				{
					$this->read_event->triggerEvent($this, $buffer);
				}
			}
			
			PF::log(PF::LOG_INFO, "Socket " . $this->socket_id . " closed by peer");
			$this->destroy();
		}
	}

	/**
	 * Handles a pre poll event
	 */
	public function handlePrePollEvent()
	{

	}

	/**
	 * Handles a post poll event
	 */
	public function handlePostPollEvent()
	{
		if($this->isConnected() && $this->isWriteThrottlingEnabled())
		{
			$diff = microtime(true) - $this->writing_last_time;

			// Determine byte quote we're allowed to send at the moment
			$quota = min($diff * $this->writing_throttle_bps, $this->writing_throttle_bps * $this->writing_throttle_peak);
				
			while(($linelength = $this->writing_buffer->hasLine()) !== false) // While there still is a line
			{
				$linelength += strlen($this->writing_line_ending);

				if($quota >= $linelength)
				{
					$line = $this->writing_buffer->popLine();
					$line .= $this->writing_line_ending;
						
					if(socket_write($this->resource, $line) !== false)
					{
						$this->writing_last_time = microtime(true);
						$quota -= $linelength;
						$this->send_event->triggerEvent($this, $line);				
					}
					else
					{
						PF::log(PF::LOG_WARNING, "Failed to write to socket " . $this->socket_id . ". " . $this->getLastSocketError());
					}
				}
				else // Throttle
				{
					break;
				}
			}
		}
	}

	/**
	 * Enables write throttling
	 * @param int $bps			allowed bytes per second
	 * @param int $peak			maximum allowed writing peak factor
	 */
	public function enableWriteThrottling($bps, $peak)
	{
		$this->writing_throttle_bps = $bps;
		$this->writing_throttle_peak = $peak;
		$this->writing_last_time = 0;
		$this->writing_buffer = new LineStringBuffer($this->writing_line_ending);
	}

	/**
	 * Disables write throttling
	 */
	public function disableWriteThrottling()
	{
		$this->writing_throttle_bps = null;
		$this->writing_throttle_peak = null;
		$this->writing_last_time = 0;
		$this->writing_buffer = null;
	}
	
	/**
	 * Returns this socket's peer name
	 * @return string|boolean		this socket's peer name or false if lookup failed
	 */	
	public function getPeerName()
	{
		if($this->isConnected())
		{
			$address = null;
			$port = null;
				
			if(socket_getpeername($this->resource, $address, $port) !== false)
			{
				if($port == null)
				{
					return $address;
				}
				else
				{
					return $address . ":" . $port;
				}
			}
			else
			{
				PF::log(PF::LOG_WARNING, "Peer lookup for socket " . $this->socket_id . " failed. " . $this->getLastSocketError());
				return false;
			}
		}
		else
		{
			PF::log(PF::LOG_WARNING, "Trying to look up peer name of unconnected socket " . $this->socket_id);
			return false;
		}
	}

	/**
	 * Checks if write throttling is enabled for this socket
	 * @return boolean		true if enabled
	 */
	public function isWriteThrottlingEnabled()
	{
		return $writing_throttle_bps != null;
	}

	/**
	 * Sets this socket's reading mode
	 * @param int $reading_mode		the new reading mode
	 */
	public function setReadingMode($reading_mode)
	{
		$this->reading_mode = $reading_mode;
	}

	/**
	 * Sets the reading buffer's line ending when using line reading mode.
	 * Will clear the existing reading buffer.
	 * @param string $line_ending		the line ending to use
	 */
	public function setReadingLineEnding($line_ending)
	{
		$this->reading_buffer = new LineStringBuffer($line_ending);
	}

	/**
	 * Sets the writing line ending.
	 * If write throttling is enabled, this will clear the writing buffer
	 * @param string $line_ending		the line ending to use
	 */
	public function setWritingLineEnding($line_ending)
	{
		$this->writing_line_ending = $line_ending;

		if($this->isWriteThrottlingEnabled())
		{
			$this->writing_buffer = new LineStringBuffer($line_ending);
		}
	}

	/**
	 * Sets the line ending for reading and writing simultaneously
	 * This will clear the existing reading buffer.
	 * If write throttling is enabled, this will clear the writing buffer
	 * @param string $line_ending		the line ending to use
	 */
	public function setLineEnding($line_ending)
	{
		$this->setReadingLineEnding($line_ending);
		$this->setWritingLineEnding($line_ending);
	}

	/**
	 * Returns this socket's reading mode
	 * @return int		the reading mode
	 */
	public function getReadingMode()
	{
		return $this->reading_mode;
	}

	/**
	 * Returns this socket's disconnected event
	 * @return Event		the disconnected event
	 */
	public function getDisconnectedEvent()
	{
		return $this->disconnected_event;
	}

	/**
	 * Returns this socket's read event
	 * @return Event		the read event
	 */
	public function getReadEvent()
	{
		return $this->read_event;
	}
	
	/**
	 * Returns this socket's send event
	 * @return Event		the send event
	 */
	public function getSendEvent()
	{
		return $this->send_event;
	}	
}
?>
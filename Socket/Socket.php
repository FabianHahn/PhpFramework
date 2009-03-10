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

require "TransportSocket.php";
require "ServerSocket.php";
require "ChildSocket.php";

/**
 * Class that represents any kind of socket
 */
abstract class Socket
{
	/**
	 * Static array containing all sockets created so far
	 *
	 * @var array[Socket]
	 */
	private static $sockets = array();
	
	/**
	 * Next socket id that will be used
	 *
	 * @var integer
	 */
	private static $socket_next_id = 0;
	
	/**
	 * PHP socket resource of the current socket object
	 *
	 * @var resource
	 */
	protected $resource;
	
	/**
	 * This socket's socket id
	 *
	 * @var int
	 */
	protected $socket_id;

	/**
	 * Polls all connected sockets
	 * @param int $timeout			timeout in microseconds
	 */
	public static function poll($timeout = 0)
	{
		// Pre poll
		foreach(self::$sockets as $socket_id => $socket)
		{
			$socket->handlePrePollEvent();
		}
		
		// Polling
		
		$readers = array();

		foreach(self::$sockets as $socket_id => $socket)
		{
			if($socket->isConnected())
			{
				$readers[$socket_id] = $socket->getResource();
			}
		}

		if(socket_select($readers, $writers = null, $excepts = null, 0, $timeout))
		{			
			foreach($readers as $socket_id => $resource)
			{
				$c_socket = null;
				
				foreach(self::$sockets as $socket)
				{
					if($socket->getResource() === $resource)
					{
						$c_socket = $socket;
						break;
					}
				}
				
				assert("\$c_socket");

				$c_socket->handleSelectEvent();
			}
		}
		
		// Post poll
		foreach(self::$sockets as $socket_id => $socket)
		{
			$socket->handlePostPollEvent();
		}
	}	
	
	/**
	 * Constructs a socket object
	 */	
	public function __construct()
	{
		$this->socket_id = self::$socket_next_id;
		self::$sockets[$this->socket_id] = $this;
		self::$socket_next_id++;
		
		PF::log(PF::LOG_DEBUG, "Socket " . $this->socket_id . " created!");
	}
	
	/**
	 * Class destructor, just calls destroy
	 */
	public function __destruct()
	{
		$this->destroy();
	}
	
	/**
	 * Destroys the socket and closes it if still connected
	 */
	public function destroy()
	{
		if(array_key_exists($this->socket_id, self::$sockets))
		{
			if($this->isConnected())
			{
				$this->disconnect();
				unset(self::$sockets[$this->socket_id]);
				
				PF::log(PF::LOG_DEBUG, "Socket " . $this->socket_id . " destroyed!");
			}
		}
	}

	/**
	 * Returns the socket id
	 * @return int			the socket id
	 */	
	public function getSocketId()
	{
		return $this->socket_id;
	}
	
	/**
	 * Returns the socket's resource
	 * @return resource			the socket's resource
	 */	
	public function getResource()
	{
		return $this->resource;
	}
	
	/**
	 * Checks if the socket is connected
	 * @return boolean			true if connected
	 */		
	public function isConnected()
	{
		return $this->resource != null;
	}

	/**
	 * Disconnects a socket
	 */		
	public function disconnect()
	{
		$this->resource = null;
		PF::log(PF::LOG_INFO, "Socket " . $this->socket_id . " disconnected!");
	}
	
	/**
	 * Connects a socket
	 *
	 * @return boolean			true if successful
	 */	
	abstract public function connect();

	/**
	 * Handles a read event from socket_select
	 */		
	abstract public function handleSelectEvent();
	
	/**
	 * Handles a pre poll event
	 */		
	abstract public function handlePrePollEvent();

	/**
	 * Handles a post poll event
	 */
	abstract public function handlePostPollEvent();
	
	/**
	 * Returns the last socket error
	 * @return string			the last socket error
	 */	
	protected function getLastSocketError()
	{
		 $errno = socket_last_error($this->resource);
		 return "[Socket error " . $errno . ": " . socket_strerror($errno) . "]";
	}
}
?>
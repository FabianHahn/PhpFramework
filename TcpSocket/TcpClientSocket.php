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

namespace PhpFramework\TcpSocket;

use \PhpFramework\PhpFramework as PF;
use \PhpFramework\Socket\TransportSocket;

/**
 * Represents a TCP client socket that can connect to a port on an address
 */
class TcpClientSocket extends TransportSocket
{
	/**
	 * The IP address to bind to
	 * @var string
	 */	
	protected $address;

	/**
	 * The port to bind to
	 * @var int
	 */		
	protected $port;
	
	/**
	 * Constructs a socket object
	 * @override
	 * @param string $address		the address to bind to
	 * @param int $port				the port to bind to
	 */
	public function __construct($address, $port)
	{
		parent::__construct();
		
		$this->address = $address;
		$this->port = $port;
	}
	
	/**
	 * Connects a socket
	 *
	 * @return boolean			true if successful
	 */	
	public function connect()
	{
		if(!$this->isConnected())
		{
			if(($this->resource = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) !== false)
			{
				PF::log(PF::LOG_INFO, "Connecting TCP client socket " . $this->socket_id . " to " . $this->address . ":" . $this->port);
			
				if(socket_connect($this->resource, $this->address, $this->port) !== false)
				{
					assert("\$this->isConnected()");
					return true;
				}
				else
				{
					PF::log(PF::LOG_WARNING, "Failed to connect TCP client socket " . $this->socket_id . " to " . $this->address . ":" . $this->port . ". " . $this->getLastSocketError());
					return false;
				}
			}
			else
			{
				PF::log(PF::LOG_WARNING, "Creating TCP socket failed. ". $this->getLastSocketError());
				return false;
			}
		}
		else
		{
			PF::log(PF::LOG_WARNING, "Tried to connect already connected TCP client socket.");
			return false;
		}
	}
	
	/**
	 * Returns this server socket's address
	 * @return string			the address
	 */		
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * Returns this server socket's port
	 * @return int			the port
	 */	
	public function getPort()
	{
		return $this->port;
	}	
}
?>
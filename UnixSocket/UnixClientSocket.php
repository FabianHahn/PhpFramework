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

namespace PhpFramework\UnixSocket;

use \PhpFramework\PhpFramework as PF;
use \PhpFramework\Socket\TransportSocket;

/**
 * Represents an UNIX client socket that can connect to a socket inode
 */
class UnixClientSocket extends TransportSocket
{
	/**
	 * The IP address to bind to
	 * @var string
	 */	
	protected $address;
	
	/**
	 * Constructs a socket object
	 * @override
	 * @param string $address		the address to bind to
	 */
	public function __construct($address)
	{
		parent::__construct();
		
		$this->address = $address;
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
			if(($this->resource = socket_create(AF_UNIX, SOCK_STREAM, SOL_SOCKET)) !== false)
			{
				PF::log(PF::LOG_INFO, "Connecting UNIX client socket " . $this->socket_id . " to " . $this->address);
			
				if(socket_connect($this->resource, $this->address) !== false)
				{
					assert("\$this->isConnected()");
					return true;
				}
				else
				{
					PF::log(PF::LOG_WARNING, "Failed to connect UNIX client socket " . $this->socket_id . " to " . $this->address . ". " . $this->getLastSocketError());
					return false;
				}
			}
			else
			{
				PF::log(PF::LOG_WARNING, "Creating UNIX socket failed. " . $this->getLastSocketError());
				return false;
			}
		}
		else
		{
			PF::log(PF:LOG_WARNING, "Tried to connect already connected UNIX client socket.");
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
}
?>
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

/**
 * Listening server socket that is able to accept children
 */
abstract class ServerSocket extends Socket
{
	/**
	 * Event that gets triggered when the socket disconnects
	 * @var Event
	 */
	protected $disconnected_event;
	
	/**
	 * Event that gets triggered when the socket accepts a child socket
	 * @var Event
	 */
	protected $accept_event;
	
	/**
	 * Array of all ChildSockets this serversocket accepted
	 * @var array[ChildSocket]
	 */	
	protected $children;

	/**
	 * Constructs a socket object
	 * @override
	 */
	public function __construct()
	{
		parent::__construct();

		$this->disconnected_event = new Event();
		$this->accept_event = new Event();
		$this->children = array();
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
		}

		parent::disconnect();
	}
	
	/**
	 * Handles a read event from socket_select
	 */		
	public function handleSelectEvent()
	{
		if($child_resource = socket_accept($this->resource))
		{
			$child = new ChildSocket($child_resource);
			$this->children[$child->getSocketId()] = $child;
			
			$this->accept_event->triggerEvent($this, $child);
		}
		else
		{
			PF::log(PF::LOG_WARNING, "Accepting socket from parent " . $this->socket_id . " failed. " . $this->getLastSocketError());
		}
	}
	
	/**
	 * Handles a pre poll event
	 */		
	public function handlePrePollEvent()
	{
		foreach($this->children as $child_id => $child)
		{
			if(!$child->isConnected())
			{
				unset($this->children[$child_id]);
				PF::log(PF::LOG_DEBUG, "Disconnected child " . $child_id . " collected by parent socket " . $this->socket_id);
			}
		}
	}

	/**
	 * Handles a post poll event
	 */		
	public function handlePostPollEvent()
	{
		
	}

	/**
	 * Returns this socket's child sockets
	 * @return array[ChildSocket]		the chilren
	 */
	public function getChildren()
	{
		return $this->children;
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
	 * Returns this socket's accept event
	 * @return Event		the accept event
	 */
	public function getAcceptEvent()
	{
		return $this->accept_event;
	}	
}
?>
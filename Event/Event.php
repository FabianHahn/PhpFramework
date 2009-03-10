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

namespace PhpFramework\Event;

use \PhpFramework\PhpFramework as PF;

/**
 * Class that represents events by implementing a "third-person" observer pattern
 */
class Event
{
	/**
	 * List of all listeners for this event
	 * @var SplDoublyLinkedList
	 */
	protected $listeners;
	
	/**
	 * Constructs the event
	 */
	public function __construct()
	{
		$this->listeners = new \SplDoublyLinkedList();
	}
	
	/**
	 * Adds a listener to this event
	 * @param callback $listener		a listener callback function
	 */
	public function addListener($listener)
	{
		if(is_callable($listener))
		{
			$this->listeners->push($listener);
		}
		else
		{
			throw new Exception("Trying to add uncallable listener");
		}
	}

	/**
	 * Removes a listener from this event
	 * @param callback $del			the listener to get removed
	 * @return boolean				true if listener was removed
	 */
	public function delListener($del)
	{
		foreach($this->listeners as $i => $listener)
		{
			if($listener === $del)
			{
				$this->listeners->offsetUnset($i);
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Triggers the event and notifies all listeners
	 * @param array[mixed]			(optional) the arguments to the listener callbacks
	 */
	public function triggerEvent()
	{
		$params = func_get_args();
		
		foreach($this->listeners as $listener)
		{
			call_user_func_array($listener, $params);
		}
	}
}
?>
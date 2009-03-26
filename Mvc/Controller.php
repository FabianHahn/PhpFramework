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

namespace PhpFramework\Mvc;

use \PhpFramework\PhpFramework as PF;

/**
 * This represents an MVC page controller
 */
class Controller
{
	/**
	 * Returns an instance of the controller using late static binding
	 * @return Controller
	 */
	public static function getInstance()
	{
		return new static();
	}
	
	/**
	 * Returns this controller's name
	 * @return string			the controller's name
	 */
	public function getControllerName()
	{
		return get_class($this);
	}
	
	/**
	 * Executes this controller
	 * 
	 * @param string $action		the action function to call
	 */
	public function execute($action)
	{
		PF::log(PF::LOG_DEBUG, "Executing controller action " . $action);
		return call_user_func(array($this, $action . "Action"));
	}
	
	/**
	 * This method catches all undefined calls and will redirect them to the index action if there is an action call
	 * @param $name				the called method name
	 * @param $arguments		the called method's arguments
	 * @throws Exception		if the called method is not an action
	 */
	public function __call($name, $arguments)
	{
		if(strtolower(substr($name, -6, 6)) == "action")
		{
			PF::log(PF::LOG_INFO, "Redirecting action call " . $name . " to index action in controller " . $this->getControllerName());
			$this->indexAction();
		}
		else
		{
			throw new Exception("Called undefined method " . $name . " in controller " . $this->getControllerName() . "!");
		}
	}
	
	/**
	 * Default action
	 */
	public function indexAction()
	{
		echo "The controller " . $this->getControllerName() . " doesn't have an index action associated. You can add one by overwriting the actionIndex() method.";
	}
}
?>
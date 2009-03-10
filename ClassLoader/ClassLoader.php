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

namespace PhpFramework\ClassLoader;

use \PhpFramework\PhpFramework as PF;

/**
 * This class represents a class loader which is able to automatically load classes based on their namespace
 */
class ClassLoader
{
	/**
	 * The root path of the classes to be autoloaded
	 * @var string
	 */
	protected $root_path;
	/**
	 * The root namespace of the classes to be autoloaded
	 * @var array[string]
	 */
	protected $root_namespace;
	/**
	 * Is the class loader already registered?
	 * @var boolean
	 */
	protected $registered;
	
	/**
	 * Creates the class loader
	 * @param string $root_path				the root path where classes should be loaded from
	 * @param string $root_namespace		the fully qualified root namespace from which classes should be loaded
	 */
	public function __construct($root_path, $root_namespace)
	{
		$this->root_path = $root_path;
		$this->registered = false;
		
		if($this->root_path[strlen($this->root_path) - 1] != "/")
		{
			$this->root_path .= "/";
		}		
		
		$this->root_namespace = explode("\\", $root_namespace);
	}
	
	/**
	 * Registers this class loader
	 */
	public function register()
	{
		PF::log(PF::LOG_INFO, "Registering class loader for " . $this->getRootNamespace() . " in " . $this->root_path);

		if(!$this->registered)
		{
			spl_autoload_register(array($this, "loadClass"));
			$this->registered = true;
		}		
	}
	
	/**
	 * Unregisters this class loader
	 */
	public function unregister()
	{
		PF::log(PF::LOG_INFO, "Unregistering class loader for " . $this->getRootNamespace() . " in " . $this->root_path);
		
		if($this->registered)
		{
			spl_autoload_unregister(array($this, "loadClass"));
			$this->registered = false;
		}		
	}
	
	/**
	 * Load a previously undefined class
	 * @param $name					the fully qualified class name
	 */
	public function loadClass($name)
	{		
		$name_parts = explode("\\", $name);
		
		foreach($this->root_namespace as $root_namespace_part)
		{
			if(array_shift($name_parts) != $root_namespace_part)
			{
				return;
			}
		}
		
		PF::log(PF::LOG_DEBUG, "Autoloading " . $this->getRootNamespace() . " class " . $name);
		
		$path = $this->root_path . implode("/", $name_parts) . ".php";
		$class_name = array_pop($name_parts);

		if(file_exists($path))
		{
			require $path;
			PF::log(PF::LOG_INFO, "Loaded " . $this->getRootNamespace() . " class " . $name);
		}
	}
	
	/**
	 * Returns the root namespace of this class loader
	 * @return string		the root namespace
	 */
	public function getRootNamespace()
	{
		return implode("\\", $this->root_namespace);
	}
}
?>
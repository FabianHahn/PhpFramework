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
use \PhpFramework\ClassLoader\ClassLoader;

require "Controller.php";
require "View.php";

/**
 * Class that implements an MVC engine that handles site requests
 */
class Mvc
{
	/**
	 * Stores the document root of the site that uses this class. The default value is just a guess, use setDocumentRoot to set it to the correct location.
	 * @var string
	 */
	protected static $document_root = "../../";
	
	/**
	 * The index controller class
	 * @var string
	 */
	protected static $index_controller = "Index";
	
	/**
	 * The http root of the project that uses this MVC engine
	 * @var string
	 */
	protected static $http_root;
	
	/**
	 * The root namespace of the project that uses the MVC engine
	 * @var string
	 */
	protected static $project_namespace = "";
	
	/**
	 * The MVC engine's model class loader
	 * @var ClassLoader
	 */
	protected static $model_class_loader;
	
	/**
	 * Class can't be instatiated
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * Handles a page request by passing control to the correct controller
	 * @throws Exception		if no index controller can found
	 */
	public static function handleRequest()
	{
		$path_info = isset($_SERVER["PATH_INFO"]) ? $_SERVER["PATH_INFO"] : "/";
		
		if(preg_match("/^\\/(\\w*)(\\/(\\w+)(\\.\\w+)?)?$/", $path_info, $matches))
		{
			$controller_name = $matches[1] ? $matches[1] : self::$index_controller;
			$action = $matches[3] ? $matches[3] : "index";
		}
		else
		{
			$controller_name = self::$index_controller;
			$action = "index";
		}
		
		PF::log(PF::LOG_DEBUG, "Autoloading controller " . $controller_name);
		
		if(file_exists(self::$document_root . "Controllers/" . $controller_name . "Controller.php"))
		{
			require self::$document_root . "Controllers/" . $controller_name . "Controller.php";
			$controller = call_user_func(array(self::$project_namespace . "\\Controllers\\" . $controller_name . "Controller", "getInstance"));
		}
		else if(file_exists(self::$document_root . "Controllers/" . self::$index_controller . "Controller.php"))
		{
			PF::log(PF::LOG_INFO, "Redirecting controller call " . $controller_name . " to index controller " . self::$index_controller);			
			require self::$document_root . "Controllers/IndexController.php";
			$controller = call_user_func(array(self::$project_namespace . "\\Controllers\\IndexController", "getInstance"));
		}
		else
		{
			throw new Exception("Could not find index controller " . self::$index_controller . "!");
		}
		
		PF::log(PF::LOG_INFO, "Loaded controller " . $controller_name);
		
		PF::log(PF::LOG_DEBUG, "Executing controller action " . $action);
		call_user_func(array($controller, $action . "Action"));
		
		PF::log(PF::LOG_DEBUG, "Unloading controller " . $controller_name);
		unset($controller);
	}
	
	/**
	 * Enables model class autoloading. Do this AFTER you set your document root and your project namespace
	 */
	public static function enableModelAutoloading()
	{
		if(!self::$model_class_loader)
		{
			self::$model_class_loader = new ClassLoader(self::$document_root . "Models/", (empty(self::$project_namespace) ? "" : self::$project_namespace . "\\") . "Models");
		}
		
		self::$model_class_loader->register();
	}
	
	/**
	 * Disables model class autoloading 
	 */
	public static function disableModelAutoloading()
	{
		if(self::$model_class_loader)
		{
			self::$model_class_loader->unregister();
		}
	}
	
	/**
	 * Sets the index controller
	 * @param string $index_controller			the index controller's name (be sure not to include the Controller suffix)
	 */
	public static function setIndexController($index_controller)
	{
		self::$index_controller = $index_controller;
	}
	
	/**
	 * Sets the project namespace
	 * @param string $namespace			the namespace to use
	 */
	public static function setProjectNamespace($namespace)
	{
		self::$project_namespace = $namespace;
	}
	
	/**
	 * Sets the http root to a specific url
	 * @param string $http_root
	 */
	public static function setHttpRoot($http_root)
	{
		self::$http_root = $http_root;
	}
	
	/**
	 * Sets the document root to a specific location
	 * @param string $document_root			the document root to set
	 */
	public static function setDocumentRoot($document_root)
	{
		self::$document_root = $document_root;
		
		if($document_root[strlen($document_root) - 1] != "/")
		{
			self::$document_root .= "/";
		}
	}
	
	/**
	 * Returns the document root
	 * @return string
	 */
	public static function getDocumentRoot()
	{
		return self::$document_root;
	}
	
	/**
	 * Returns the current http root
	 * @return string		the http root
	 */
	public static function getHttpRoot()
	{
		if(self::$http_root)
		{
			return self::$http_root;
		}
		else
		{
			return "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["SCRIPT_NAME"] . "/";
		}
	}
}
?>
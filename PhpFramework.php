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

namespace PhpFramework;

class PhpFramework
{
	const VERSION = "0.2";
	
	/**
	 * Log level constants
	 */     
	const LOG_WARNING = 1;
	const LOG_INFO = 2;
	const LOG_DEBUG = 4;
	const LOG_ALL = 7;
	
	/**
	 * An array of loaded modules
	 * @var array
	 */
	static private $loaded_modules = array();
		
	/**
	 * The current log level
	 * @var int
	 */
	static private $log_level;
	
	/**
	 * The current log callback function
	 * @var callback
	 */
	static private $log_callback;
	
	/**
	 * Initializes PhpFramework
	 */	
	public static function init()
	{
		spl_autoload_register(array(__CLASS__, "loadClass"));
	}
	
	/**
	 * Prints out some information about the framework (just like phpinfo)
	 */
	public static function info()
	{
		echo "<h1>PHP Framework</h1>\n";
		echo "Running version " . self::VERSION . "<br>\n";
		echo "Please report any bugs you may encounter to <a href=\"mailto:esmf68@gmail.com\">&lt;esmf68@gmail.com&gt;</a><br>";
		echo "<h2>Available modules</h2>";
		echo "<ul>\n";
		$dir = dir(__DIR__);
		
		while(($entry = $dir->read()) !== false)
		{
			if($entry == "." || $entry === "..") continue;
			
			if(is_dir(__DIR__ . "/" . $entry) && file_exists(__DIR__ . "/" . $entry . "/" . $entry . ".php"))
			{
				$subdir = dir(__DIR__ . "/" . $entry);
				
				$files = 0;
				
				while(($subentry = $subdir->read()) !== false)
				{
					if(preg_match("/^\\w*\\.php$/", $subentry)) $files++;
				}			
				
				echo "<li><b>" . $entry . "</b>: " . $files . " " . ($files == 1 ? "file" : "files") . "</li>\n";
			}
		}
		
		echo "</ul>\n";
	}
	
	/**
	 * Loads a framework class
	 * @param string $class		the name of the class to load
	 */
	public static function loadClass($class)
	{				
		if(preg_match("/^PhpFramework\\\\(\\w*)\\\\(\\w*)$/", $class, $matches))
		{
			self::log(self::LOG_DEBUG, "Autoloading framework class " . $class);
			
			$module = $matches[1];
			$class_name = $matches[2];
				
			if(is_dir(__DIR__ . "/" . $module) && file_exists(__DIR__ . "/" . $module . "/" . $module . ".php") && file_exists(__DIR__ . "/" . $module . "/" . $class_name . ".php"))
			{
				require __DIR__ . "/" . $module . "/" . $module . ".php";
				$loaded_modules[] = $module;
				self::log(self::LOG_INFO, "Loaded framework module " . $module);
			}
			else
			{
				self::log(self::LOG_WARNING, "Module " . $module . " could not be loaded (triggered by class " . $class . ").");
			}
		}
	}
	
	/**
	 * Logs a message
	 * @param int $level		the log level of this message
	 * @param string $message	the log message
	 */
	public static function log($level, $message)
	{
		if($level & self::$log_level) // Am I logged?
		{
			if(is_callable(self::$log_callback))
			{
				call_user_func_array(self::$log_callback, array($level, $message));				
			}
		}
	}
	
	/**
	 * Returns the framework root path
	 * @return string		framework root path
	 */
	public static function getFrameworkRoot()
	{
		return __DIR__;
	}
	
	/**
	 * Checks if a module is loaded
	 * @param string $module		the name of the module to check
	 * @return boolean				true if loaded
	 */
	public static function isLoaded($module)
	{
		return in_array($module, self::$loaded_modules);
	}
	
	/**
	 * Enables logging
	 * @param int $level			the desired logging level
	 * @param callback $callback	the callback function to use for logging
	 */
	public static function setLogging($level, $callback)
	{
		self::$log_level = $level;
		self::$log_callback = $callback;
	}
}
?>
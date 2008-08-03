<?php
class PhpFramework
{
	const VERSION = "0.1";
	
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
	 * The cached framework root path
	 * @var string
	 */
	static private $framework_root;
	
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
	 * Prints out some information about the framework (just like phpinfo)
	 */
	public static function info()
	{
		echo "<h1>PHP Framework</h1>\n";
		echo "Running version " . self::VERSION . "<br>\n";
		echo "Please report any bugs you may encounter to <a href=\"mailto:esmf68@gmail.com\">&lt;esmf68@gmail.com&gt;</a><br>";
		echo "<h2>Available modules</h2>";
		echo "<ul>\n";
		$dir = dir(self::getFrameworkRoot());
		
		while(($entry = $dir->read()) !== false)
		{
			if($entry == "." || $entry === "..") continue;
			
			if(is_dir(self::getFrameworkRoot() . $entry))
			{
				$subdir = dir(self::getFrameworkRoot() . $entry);
				
				$files = 0;
				
				while(($subentry = $subdir->read()) !== false)
				{
					if(preg_match("/^\\w*\\.php$/", $subentry)) $files++;
				}			
				
				echo "<li><b>" . $entry . "</b>: " . $files . " class(es)</li>\n";
			}
		}
		
		echo "</ul>\n";
	}
	
	/**
	 * Loads a framework module
	 * @param string $module	the name of the module to load
	 * @return boolean			true if success
	 */
	public static function loadModule($module)
	{		
		if(is_dir(self::getFrameworkRoot() . $module) && file_exists(self::getFrameworkRoot() . $module . "/" . $module . ".php"))
		{
			require self::getFrameworkRoot() . $module . "/" . $module . ".php";
			$loaded_modules[] = $module;
			
			self::log(self::LOG_INFO, "Module " . $module . " loaded.");
			
			return true;
		}
		
		self::log(self::LOG_WARNING, "Module " . $module . " not found, could not be loaded.");
		
		return false;
	}
	
	/**
	 * Loads several framework modules
	 * @param array[string] $modules	an array of modules to load
	 * @return boolean					true if success
	 */
	public static function loadModules($modules)
	{
		$result = true;
		
		foreach($modules as $module)
		{
			$result = $result && self::loadModule($module);
		}
		
		return $result;
	}
	
	/**
	 * Tries to satisfy a module dependency
	 * @param array[string]|string $dependencies	a dependency or an array of dependencies
	 * @throws Exception							if a dependency couldn't be met
	 */
	public static function depends($dependencies)
	{
		if(is_array($dependencies))
		{
			foreach($dependencies as $dependency)
			{
				self::depends($dependency);
			}
		}
		else
		{
			$dependency = $dependencies;
			
			$trace = debug_backtrace();
			preg_match("/\\/(\\w*)\\.php$/", $trace[0]["file"], $matches);
			$caller = $matches[1];
			
			self::log(self::LOG_DEBUG, $caller . " depends on " . $dependency);
			
			if(!self::isLoaded($dependency))
			{
				// Try to load
				if(!self::loadModule($dependency))
				{					
					throw new Exception("Depencency on module " . $dependency . " could not be met (required by " . $caller . ")!");
				}
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
		if(!self::$framework_root)
		{
			preg_match("/^(.*\\/)\\w*\\.php$/", __FILE__, $matches);
			self::$framework_root = $matches[1];
		}
		
		return self::$framework_root;
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
<?php
/**
 * Class that represents a globally accessible config
 *
 */
class Config
{
	/**
	 * This can't be called
	 *
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * The config file's location
	 *
	 * @var string
	 */
	protected static $config_file = "config.ini";
	
	/**
	 * The actual config values
	 *
	 * @var array
	 */
	protected static $config;
	
	/**
	 * Loads the config
	 * 
	 * @throws Exception		if the config file could not be found
	 */
	protected static function loadConfig()
	{
		if(!file_exists(self::$config_file))
		{
			throw new Exception("Could not find config file " . self::$config_file);			
		}
		
		self::$config = parse_ini_file(self::$config_file, true);
	}
	
	/**
	 * Sets the config file name
	 *
	 * @param string $config_file
	 */
	public static function setFileName($config_file)
	{
		self::$config_file = $config_file;
	}
	
	/**
	 * Returns either a config section or a config value, depending on whether key is specified or not
	 *
	 * @param string $section
	 * @param string $key
	 * @return array|string
	 * @throws Exception		if the config file could not be found or invalid keys are specified
	 */
	public static function get($section, $key = "")
	{
		if(!is_array(self::$config)) // Not yet loaded
		{
			self::loadConfig();
		}
		
		if(!isset(self::$config[$section]))
		{
			throw new Exception("Could not find section " . $section . " in config!");
		}
		
		if(empty($key))
		{
			return self::$config[$section];
		}
		else
		{
			if(!isset(self::$config[$section][$key]))
			{
				throw new Exception("Could not find key "  . $key . " from section " . $section . " in config!");
			}
			
			return self::$config[$section][$key];
		}
	}
}
?>
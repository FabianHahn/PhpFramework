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

namespace PhpFramework\Config;

use \PhpFramework\PhpFramework as PF;

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
		PF::log(PF::LOG_INFO, "Loaded config file " . self::$config_file);
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
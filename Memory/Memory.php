<?php
/**
 * Class that represents a globally accessible memory
 *
 */
class Memory
{
	/**
	 * This can't be called
	 *
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * The actual memory values
	 *
	 * @var array
	 */
	protected static $memory = array();
		
	/**
	 * Returns a memory value
	 *
	 * @param string $key
	 * @return mixed
	 * @throws Exception		if invalid keys are specified
	 */
	public static function get($key)
	{
		if(!isset(self::$memory[$key]))
		{
			throw new Exception("Could not find key " . $section . " in memory!");
		}
		
		return self::$memory[$key];
	}
	
	/**
	 * Sets a memory value
	 *
	 * @param string $key
	 * @param string $value
	 */
	public static function set($key, $value)
	{
		self::$memory[$key] = $value;
	}
}
?>
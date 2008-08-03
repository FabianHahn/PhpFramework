<?php
/**
 * Simple logger that logs into a file
 */
class FileLogger
{
	/**
	 * Current log file handle
	 * @var resource
	 */
	private static $file;
	
	/**
	 * Cannot be called
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * Enable the logger
	 * @param string $file		file to log to
	 * @param int $level		[optional] the desired log level
	 * @throws Exception		if unable to write to $file
	 */
	public static function enable($file, $level = 3)
	{
		self::$file = fopen($file, "a");
		
		if(self::$file === false)
		{
			throw new Exception("Cannot write to log file " . $file . ", check path and permissions.");
		}
		
		PhpFramework::setLogging($level, array("FileLogger", "log"));
	}
	
	/**
	 * Logs a message
	 * @param int $level		the log level of this message
	 * @param string $message	the los message
	 * @throws Exception		if writing to log file failed
	 */
	public static function log($level, $message)
	{
		$ret = 0;
		$date = date("[d.m.Y-H:i:s]", time());
		
		switch($level)
		{
			case PhpFramework::LOG_WARNING:
				$ret = fwrite(self::$file, $date . " Warning: " . $message . "\n");
			break;
			case PhpFramework::LOG_INFO:
				$ret = fwrite(self::$file, $date . " Info: " . $message . "\n");
			break;
			case PhpFramework::LOG_DEBUG:
				$ret = fwrite(self::$file, $date . " Debug: " . $message . "\n");
			break;
		}
		
		if($ret === false)
		{
			throw new Exception("Writing to log file failed.");
		}
	}
}
?>
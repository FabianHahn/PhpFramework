<?php
/**
 * Simple logger that prints html directly to the page response
 */
class HtmlDebugLogger
{
	/**
	 * Cannot be called
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * Enable the logger
	 * @param int $level	[optional] the desired log level
	 */
	public static function enable($level = 3)
	{
		PhpFramework::setLogging($level, array("HtmlDebugLogger", "log"));
	}

	/**
	 * Logs a message
	 * @param int $level		the log level of this message
	 * @param string $message	the los message
	 */
	public static function log($level, $message)
	{
		$formatted = str_replace("\n", "<br>\n", $message);
		
		switch($level)
		{
			case PhpFramework::LOG_WARNING:
				echo "<span style=\"color:red;\"><b>Warning:</b> " . $formatted . "</span><br>\n";
			break;
			case PhpFramework::LOG_INFO:
				echo "<span style=\"color:green;\"><b>Info:</b> " . $formatted . "</span><br>\n";
			break;
			case PhpFramework::LOG_DEBUG:
				echo "<span style=\"color:blue;\"><b>Debug:</b> " . $formatted . "</span><br>\n";
			break;
		}
	}
}
?>
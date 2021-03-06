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

namespace PhpFramework\FileLogger;

use \PhpFramework\PhpFramework as PF;

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
		
		PF::setLogging($level, array(__NAMESPACE__ . "\\FileLogger", "log"));
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
			case PF::LOG_WARNING:
				$ret = fwrite(self::$file, $date . " Warning: " . $message . "\n");
			break;
			case PF::LOG_INFO:
				$ret = fwrite(self::$file, $date . " Info: " . $message . "\n");
			break;
			case PF::LOG_DEBUG:
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
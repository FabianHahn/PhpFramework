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

namespace PhpFramework\HtmlDebugLogger;

use \PhpFramework\PhpFramework as PF;

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
		PF::setLogging($level, array(__NAMESPACE__ . "\\HtmlDebugLogger", "log"));
	}

	/**
	 * Logs a message
	 * @param int $level		the log level of this message
	 * @param string $message	the los message
	 */
	public static function log($level, $message)
	{
		$formatted = str_replace("\n", "<br>\n", htmlentities($message, ENT_QUOTES, "UTF-8"));
		
		switch($level)
		{
			case PF::LOG_WARNING:
				echo "<div style=\"color:red;\"><b>Warning:</b> " . $formatted . "</div>\n";
			break;
			case PF::LOG_INFO:
				echo "<div style=\"color:green;\"><b>Info:</b> " . $formatted . "</div>\n";
			break;
			case PF::LOG_DEBUG:
				echo "<div style=\"color:blue;\"><b>Debug:</b> " . $formatted . "</div>\n";
			break;
		}
	}
}
?>
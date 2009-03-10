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

namespace PhpFramework\Memory;

use \PhpFramework\PhpFramework as PF;

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
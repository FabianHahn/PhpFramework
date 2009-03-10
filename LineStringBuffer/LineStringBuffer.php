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

namespace PhpFramework\LineStringBuffer;

use \PhpFramework\PhpFramework as PF;

/**
 * Implements a string buffer that can be popped linewise
 */	
class LineStringBuffer
{
	/**
	 * The string buffer
	 * @var string
	 */
	protected $buffer;
	
	/**
	 * The line ending of the buffer strings
	 * @var string
	 */
	protected $line_ending;
	
	/**
	 * Constructs a string line buffer
	 * @param string $line_ending		(optional) the line ending to use
	 */	
	public function __construct($line_ending = "\n")
	{
		$this->buffer = "";
		$this->line_ending = $line_ending;
	}
	
	/**
	 * Clears the buffer
	 */			
	public function clear()
	{
		$this->buffer = "";
	}

	/**
	 * Extends the string with a string
	 * @param string $head			the string the buffer should be extended with
	 */		
	public function extend($tail)
	{
		$this->buffer .= $tail;
	}
	
	/**
	 * Prepends a string to the buffer
	 * @param string $head			the string to prepend
	 */	
	public function prepend($head)
	{
		$this->buffer = $head . $this->buffer;
	}
	
	/**
	 * Checks whether there is a full line to read from the buffer
	 * @return int|boolean 			if yes, returns the length, otherwise false
	 */
	public function hasLine()
	{
		if(($linelen = strcspn($this->buffer, $this->line_ending)) !== strlen($this->buffer))
		{
			return $linelen;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Extracts a line from a buffer
	 * @return string|boolean			the line or false if there is no line
	 */
	public function popLine()
	{
		if(($linelen = strcspn($this->buffer, $this->line_ending)) !== strlen($this->buffer)) // If there is a line with ending in the buffer
		{
			// Extract the line
			$line = substr($this->buffer, 0, $linelen);
			
			// Extract the rest
			$rest = substr($this->buffer, $linelen);
			
			// Remove the line ending from the rest
			$this->buffer = substr($rest, min(strspn($rest, $this->line_ending), strlen($this->line_ending)));
			
			return $line;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns the current string buffer
	 * @return string			the current string buffer
	 */	
	public function getBuffer()
	{
		return $this->buffer;
	}
}
?>
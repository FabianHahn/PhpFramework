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

namespace PhpFramework\Template;

require "FileTemplate.php";
require "StringTemplate.php";

/**
 * A class that represents templates with placeholders
 */
abstract class Template
{
	/**
	 * The placeholder char used for all templates
	 * @var char
	 */
	protected static $placeholder_char = "%";
	
	/**
	 * The template data of this template
	 * @var string
	 */
	protected $data;
	
	/**
	 * The subtemplates of this template
	 * @var array[Template]
	 */
	protected $subs;
	
	/**
	 * Fills a placeholder with content
	 * @param string $placeholder		the placeholder
	 * @param string $content			the content to replace the placeholder with
	 */
	public function fill($placeholder, $content)
	{
		$this->data = str_replace(self::$placeholder_char . $placeholder . self::$placeholder_char, $content, $this->data);
	}

	/**
	 * Adds a subtemplate for a placeholder
	 * @param string $placeholder		the placeholder where a subtemplate should be added
	 * @param Template $template		the subtemplate to add
	 * @return Template					returns $template
	 */
	public function add($placeholder, Template $template)
	{
		if(!array_key_exists($placeholder, $this->subs))
		{
			$this->subs[$placeholder] = array();
		}

		array_push($this->subs[$placeholder], $template);
		return $template;
	}

	/**
	 * Purge subtemplates from a placeholder
	 * @param string $placeholder		the placeholder to purge
	 */
	public function purge($placeholder)
	{
		$this->subs[$placeholder] = array();
	}

	/**
	 * Converts this template into a string for output
	 * @return string		string represenation of this template
	 */
	public function __toString()
	{
		foreach($this->subs as $placeholder => $sub)
		{
			$sub_content = "";

			foreach($sub as $template)
			{
				$sub_content .= $template . "\n";
			}

			$this->fill($placeholder, $sub_content);
		}

		return $this->data;
	}
	
	/**
	 * Returns all subtemplates for a placeholder
	 * @param string $placeholder		the placeholder for the subtemplates
	 * @return array[Template]			an array of subtemplates for $placeholder
	 */
	public function getSubs($placeholder)
	{
		if(!array_key_exists($placeholder, $this->subs))
		{
			$this->subs[$placeholder] = array();
		}
		
		return $this->subs[$placeholder];
	}
	
	/**
	 * Sets the placeholder char for all templates
	 * @param char $char		the new placeholder char to use
	 */
	public static function setPlaceholderChar($char)
	{
		self::$placeholder_char = $char;
	}	
}
?>
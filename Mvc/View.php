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

namespace PhpFramework\Mvc;

use \PhpFramework\PhpFramework as PF;

/**
 * This class represents an MVC view
 */
class View
{
	/**
	 * This view's filename
	 * @var string
	 */
	protected $_filename;
	/**
	 * Specifies whether this view is buffered or not
	 * @var boolean
	 */
	protected $_buffered;
	/**
	 * This views property variables
	 * @var array[mixed]
	 */
	protected $_properties;
	
	/**
	 * Create the view by setting its filename and specifying if it should be buffered or not
	 * @param string $filename		the view's filename
	 * @param boolean $buffered		(optional) should the view be buffered?
	 */
	public function __construct($filename, $buffered = false)
	{
		$this->_filename = $filename;
		$this->_buffered = $buffered;
		$this->_properties = array();
	}
	
	/**
	 * Return a property variable
	 * @param $name			the property's name
	 * @return mixed		the property value, or null if it doesn't exist
	 */
	public function __get($name)
	{
		if(isset($this->_properties[$name]))
		{
			return $this->_properties[$name];
		}
		else
		{
			PF::log(PF::LOG_WARNING, "Trying to read unknown property " . $name . " from view with filename " . $this->_filename);
			return null;
		}
	}
	
	/**
	 * Shows this view
	 * @return string			the buffered view if buffering is enabled
	 */
	public function show()
	{
		if(file_exists(Mvc::getDocumentRoot() . "Views/" . $this->_filename))
		{
			if($this->_buffered)
			{
				PF::log(PF::LOG_INFO, "Showing view " . $this->_filename . " (buffered)");;
				ob_start();
			}
			else
			{
				PF::log(PF::LOG_INFO, "Showing view " . $this->_filename);
			}			
			
			require Mvc::getDocumentRoot() . "Views/" . $this->_filename;
			
			if($this->_buffered)
			{
				return ob_end_clean();
			}
		}
	}
	
	/**
	 * Set a property variable to be used in the view
	 * @param string $name
	 * @param mixed $value
	 */
	public function setProperty($name, $value)
	{
		$this->_properties[$name] = $value;
	}
}
?>
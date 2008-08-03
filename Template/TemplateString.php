<?php
/**
 * A template from a string
 */
class TemplateString extends Template
{
	/**
	 * Create the template from a string
	 * @param string $string		the template string
	 */
	public function __construct($string)
	{
		$this->subs = array();
		$this->data = $string;
	}
}
?>
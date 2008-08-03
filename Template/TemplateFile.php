<?php
/**
 * A template from a file
 */
class TemplateFile extends Template
{
	/**
	 * Load the template from a file
	 * @param string $filename			the location of the template file
	 */
	public function __construct($filename)
	{
		$this->subs = array();
		$this->data = file_get_contents($filename);
		
		if($this->data === false)
		{
			throw new Exception("Could not load template file " . $filename);
		}
		
		PhpFramework::log(PhpFramework::LOG_INFO, "Loaded template file " . $filename);
	}	
}
?>
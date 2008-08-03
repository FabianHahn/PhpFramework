<?php
/**
 * Bootstrapper class
 */
class Bootstrap
{
	/**
	 * Root path on this domain
	 *
	 * @var string
	 */
	private static $root_path = "";
	
	/**
	 * Bootstrapping rule to use
	 * @var BootstrapRule
	 */
	private static $bootstrap_rule;
	
	/**
	 * This can't be called
	 */
	private function __construct()
	{
		
	}
	
	/**
	 * Handles a request
	 *
	 * @param string $request_uri		the request uri from the webserver
	 * @throws Exception				if no bootstrap_rule is set
	 */
	public static function handleRequest($request_uri = null)
	{
		if(!$request_uri)
		{
			$request_uri = $_SERVER["REQUEST_URI"];
		}
		
		if(self::$bootstrap_rule)
		{
			self::$bootstrap_rule->handleRequest($request_uri);
		}
		else
		{
			throw new Exception("No BootstrapRule set. Please pass one over to Bootstrap::setBootstrapRule before handling requests!");
		}
	}
	
	/**
	 * Sets the root path
	 * @param string $root_path		the new root path
	 */
	public static function setRootPath($root_path)
	{
		self::$root_path = $root_path;
	}
	
	/**
	 * Sets the bootstrap rule
	 * @param string $rule			the new bootstrap rule
	 */
	public static function setBootstrapRule(BootstrapRule $rule)
	{
		self::$bootstrap_rule = $rule;
	}
}
?>
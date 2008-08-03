<?php
/**
 * Declaration of a BootstrapRule
 */
interface BootstrapRule
{
	/**
	 * Handles the request URI conforming to the rule the implementing class represents
	 * @param string $request_uri		the request uri passed from the bootstrapper
	 */
	public function handleRequest($request_uri);
}
?>
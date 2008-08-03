<?php
require_once "DatabaseInsertQuery.php";
require_once "DatabaseSelectQuery.php";
require_once "DatabaseUpdateQuery.php";

/**
 * Abstract class that represents a database query
 *
 */
abstract class DatabaseQuery
{
	/**
	 * The PDO database driver this query should use
	 *
	 * @var string
	 */
	protected $pdo_driver;
	
	/**
	 * Constructs the query by setting its driver
	 *
	 * @param string $pdo_driver
	 */
	public function __construct($pdo_driver)
	{
		$this->pdo_driver = $pdo_driver;
	}
	
	/**
	 * Converts the query into a string
	 *
	 */
	abstract public function __toString();
}
?>
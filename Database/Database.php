<?php
require_once "DatabaseException.php";
require_once "DatabaseRow.php";
require_once "DatabaseQuery.php";

/**
 * Class that represents a database connection
 *
 */
class Database
{
	/**
	 * The PDO instance representing the connection to the database
	 *
	 * @var PDO
	 */
	protected $pdo;
	
	/**
	 * The PDO Data Source Name this database connection uses
	 *
	 * @var string
	 */
	protected $pdo_dsn;
	/**
	 * The username that is used to connect to the database
	 *
	 * @var string
	 */
	protected $pdo_username;
	/**
	 * The password that is used to connect to the database
	 *
	 * @var string
	 */
	protected $pdo_password;
	
	/**
	 * The last query that was executed
	 *
	 * @var string
	 */
	protected $last_query = "";
	
	/**
	 * Creates the database object
	 *
	 * @param string $dsn			The Data Source Name to the database
	 * @param string $username		The username that is used to connect to the database
	 * @param string $password		The password that should be used to connect
	 */
	public function __construct($dsn, $username, $password)
	{
		$this->pdo_dsn = $dsn;
		$this->pdo_username = $username;
		$this->pdo_password = $password;
	}
	
	/**
	 * Attempts to establish the database connection
	 *
	 * @throws DatabaseException	If connection fails
	 */
	public function connect()
	{		
		try
		{
			$this->pdo = new PDO($this->pdo_dsn, $this->pdo_username, $this->pdo_password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			PhpFramework::log(PhpFramework::LOG_INFO, "Database connected");
		}
		catch(PDOException $e)
		{
			throw new DatabaseException("Establishing PDO connection failed: " . $e->getMessage(), $e->getCode(), "", $e->getTrace());
		}
	}

	/**
	 * Disconnects from the database server
	 *
	 */
	public function disconnect()
	{
		$this->pdo = null;
		PhpFramework::log(PhpFramework::LOG_INFO, "Database disconnected");
	}
	
	/**
	 * Returns a fresh select query
	 *
	 * @return DatabseSelectQuery
	 */
	public function select()
	{
		return new DatabaseSelectQuery($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
	}
	
	/**
	 * Executes a SQL query
	 *
	 * @param string $query			The query to execute
	 * @return PDOStatement			A PDOStatement object representing the result
	 * @throws DatabaseException	If the query fails
	 */
	public function query($query)
	{
		echo $query . "\n";
		
		$this->last_query = $query;
		
		try
		{
			$pdo_statement = $this->pdo->query($query);
			PhpFramework::log(PhpFramework::LOG_DEBUG, "Database query: " . $query);
		}
		catch(PDOException $e)
		{
			throw new DatabaseException("Query failed: " . $e->getMessage(), $e->getCode(), $query, $e->getTrace());
		}
		
		$error_info = $pdo_statement->errorInfo();
		
		if(!empty($error_info[2]))
		{
			throw new DatabaseException("Query failed: " . $error_info[2], $error_info[0], $query);
		}
		
		return $pdo_statement;
	}
	
	/**
	 * Fetches a row from a database result
	 *
	 * @param PDOStatement $statement
	 * @return DatabaseRow
	 */
	public function fetch(PDOStatement $statement)
	{
		if($rawrow = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT))
		{
			return new DatabaseRow($rawrow);;
		}
		
		return null;
	}
	
	/**
	 * Fetches all rows from a database result
	 *
	 * @param PDOStatement $statement
	 * @return array
	 */
	public function fetchAll(PDOStatement $statement)
	{
		$fetched = $statement->fetchAll(PDO::FETCH_ASSOC);
		$ret = array();
		
		foreach($fetched as $row)
		{
			$ret[] = new DatabaseRow($row);
		}
		
		return $ret;
	}
	
	/**
	 * Quotes a string according to the PDO driver setting
	 *
	 * @param string $unquoted
	 * @return string				quoted string
	 */
	public function quote($unquoted)
	{
		return $this->pdo->quote($unquoted);
	}
	
	/**
	 * Checks if this database object is connected or not
	 * 
	 * @return boolean
	 */
	public function isConnected()
	{
		return (boolean) $this->pdo;
	}
}
?>
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

namespace PhpFramework\Database;

use \PhpFramework\PhpFramework as PF;

require "DatabaseException.php";
require "DatabaseRow.php";
require "DatabaseStatement.php";
require "DatabaseConnective.php";
require "DatabaseConjunction.php";
require "DatabaseDisjunction.php";
require "DatabaseQuery.php";
require "DatabaseInsertQuery.php";
require "DatabaseSelectQuery.php";
require "DatabaseUpdateQuery.php";
require "DatabaseDeleteQuery.php";

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
			$this->pdo = new \PDO($this->pdo_dsn, $this->pdo_username, $this->pdo_password);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			PF::log(PF::LOG_INFO, "Database connected");
		}
		catch(\PDOException $e)
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
		PF::log(PF::LOG_INFO, "Database disconnected");
	}
	
	/**
	 * Returns a fresh select query
	 *
	 * @return DatabseSelectQuery
	 */
	public function select()
	{
		return new DatabaseSelectQuery($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME), $this);
	}
	
	/**
	 * Returns a fresh update query
	 *
	 * @return DatabseUpdateQuery
	 */
	public function update()
	{
		return new DatabaseUpdateQuery($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME), $this);
	}

	/**
	 * Returns a fresh insert query
	 *
	 * @return DatabseSelectQuery
	 */
	public function insert()
	{
		return new DatabaseInsertQuery($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME), $this);
	}
	
	/**
	 * Returns a fresh delete query
	 *
	 * @return DatabseDeleteQuery
	 */
	public function delete()
	{
		return new DatabaseDeleteQuery($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME), $this);
	}	
		
	/**
	 * Executes a SQL query
	 *
	 * @param string $query			The query to execute
	 * @return DatabaseStatement	A DatabaseStatement object representing the result
	 * @throws DatabaseException	If the query fails
	 */
	public function query($query)
	{
		$this->last_query = $query;
		
		try
		{
			$pdo_statement = $this->pdo->query($query);
			PF::log(PF::LOG_DEBUG, "Database query: " . $query);
		}
		catch(\PDOException $e)
		{
			throw new DatabaseException("Query failed: " . $e->getMessage(), $e->getCode(), $query, $e->getTrace());
		}
		
		$error_info = $pdo_statement->errorInfo();
		
		if(!empty($error_info[2]))
		{
			throw new DatabaseException("Query failed: " . $error_info[2], $error_info[0], $query);
		}
		
		return new DatabaseStatement($pdo_statement);
	}
	
	/**
	 * Returns the (most likely autoincremented) id of the last inserted row
	 * 
	 * @return int		the id of the last inserted row
	 */
	public function lastInsertId()
	{
		return $this->pdo->lastInsertId();
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
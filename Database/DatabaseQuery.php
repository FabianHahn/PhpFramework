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
	 * The PhpFramework database associated with this query
	 * 
	 * @var Database
	 */
	protected $database;
	
	/**
	 * Constructs the query by setting its driver
	 *
	 * @param string $pdo_driver		This query's PDO driver
	 * @param Database $database		(optional) A PhpFramework database to associate with the query
	 */
	public function __construct($pdo_driver, $database = null)
	{
		$this->pdo_driver = $pdo_driver;
		$this->database = $database;
	}
	
	/**
	 * Executes the query
	 * 
	 * @return DatabaseStatement	A DatabaseStatement object representing the result
	 * @throws DatabaseException	If no Database is associated with this query
	 */
	public function execute()
	{
		if($this->database)
		{
			return $this->database->query($this);
		}
		else
		{
			throw new DatabaseException("Tried to execute query without associated Database object");
		}
	}
	
	/**
	 * Converts the query into a string
	 *
	 */
	abstract public function __toString();
}
?>
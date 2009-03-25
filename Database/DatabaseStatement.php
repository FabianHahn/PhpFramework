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
 * Represents a database statement returned after the execution of a query
 *
 */
class DatabaseStatement
{
	/**
	 * This statement's internal PDO statement
	 * 
	 * @var PDOStatement
	 */
	protected $statement;
	
	/**
	 * Constructs the statement
	 * 
	 * @param PDOStatement $statement		the internal PDO statement
	 */
	public function __construct(\PDOStatement $statement)
	{
		$this->statement = $statement;
	}
	
	/**
	 * Fetches a row from the statement
	 *
	 * @return DatabaseRow
	 */
	public function fetch()
	{
		if($rawrow = $this->statement->fetch(\PDO::FETCH_ASSOC, \PDO::FETCH_ORI_NEXT))
		{
			return new DatabaseRow($rawrow);
		}
		
		return null;
	}
	
	/**
	 * Fetches all rows from a database result
	 *
	 * @return array[DatabaseRow]
	 */
	public function fetchAll()
	{
		$fetched = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
		$ret = array();
		
		foreach($fetched as $row)
		{
			$ret[] = new DatabaseRow($row);
		}
		
		return $ret;
	}
	
	/**
	 * Returns the number of rows in this statement (equivalent to mysql_num_rows etc)
	 * 
	 * @return int		number of rows in the statement
	 */
	public function numRows()
	{
		return $this->statement->rowCount();
	}
	
	/**
	 * Returns the statement's internal statement resource
	 * 
	 * @return PDOStatement
	 */
	public function getInternalStatement()
	{
		return $this->statement;
	}
}

?>
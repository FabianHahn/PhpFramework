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
 * Class that represends an SQL INSERT query
 *
 */
class DatabaseInsertQuery extends DatabaseQuery
{
	const INSERT_TABLE = 0;
	const INSERT_VALUES = 1;
	
	/**
	 * Components this query is made of
	 *
	 * @var array
	 */
	protected $query_components;

	/**
	 * Constructs the select query and initializes its components
	 * 
	 * @param string $pdo_driver		This query's PDO driver
	 * @param Database $database		(optional) A PhpFramework database to associate with the query
	 * @override
	 */
	public function __construct($pdo_driver, $database = null)
	{
		$this->query_components[self::INSERT_TABLE] = "";
		$this->query_components[self::INSERT_VALUES] = array();
		
		parent::__construct($pdo_driver, $database);
	}	

	/**
	 * Adds the target table
	 *
	 * @param string $table_name
	 * @return DatabaseInsertQuery				this query object instance
	 */	
	public function table($table_name)
	{
		$this->query_components[self::INSERT_TABLE] = $table_name;
		
		return $this;
	}
	
	/**
	 * Adds an array of columns as VALUES clause to the query
	 *
	 * @param array $columns					the columns to insert
	 * @return DatabaseInsertQuery				this query object instance
	 */
	public function valueArray($columns)
	{
		$this->query_components[self::INSERT_VALUES] = array_merge($this->query_components[self::INSERT_VALUES], $columns);
		
		return $this;
	}
	
	/**
	 * Adds an column-value pair to the VALUES clause of the query
	 *
	 * @param string $column					the column to insert
	 * @param string $value						the value to insert
	 * @return DatabaseInsertQuery				this query object instance
	 */
	public function value($column, $value)
	{
		$this->query_components[self::INSERT_VALUES][$column] = $value;
		
		return $this;
	}
	
	/**
	 * Converts the query into a string
	 *
	 * @throws DatabseException			if the query is incomplete or invalid
	 * @return string					the generated query
	 */
	public function __toString()
	{
		$query = "INSERT INTO\n";
		
		if(empty($this->query_components[self::INSERT_TABLE]))
		{
			throw new DatabaseException("Query incomplete: No table name for UPDATE set.");
		}
		else
		{
			$query .= "\t`" . $this->query_components[self::INSERT_TABLE] . "`\n";
		}
		
		if(!count($this->query_components[self::INSERT_VALUES]))
		{
			throw new DatabaseException("Query inclomplete: No VALUES clause set.");
		}
		else
		{
			$keys = "";
			$values = "";
			
			foreach($this->query_components[self::INSERT_VALUES] as $column_key => $column_value)
			{
				if(!empty($keys))
				{
					$keys .= ",\n";
				}
				
				if(!empty($values))
				{
					$values .= ",\n";
				}
				
				
				$keys .= "\t`" . $column_key . "`";
				$values .= "\t'" . $column_value . "'";
			}
			
			$query .= "(\n" . $keys . "\n)\nVALUES\n(\n" . $values . "\n)\n";
		}

		return trim($query) . ";";
	}
}
?>
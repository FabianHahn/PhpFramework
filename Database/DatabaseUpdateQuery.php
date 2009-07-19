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
 * Class that represends an SQL UPDATE query
 *
 */
class DatabaseUpdateQuery extends DatabaseQuery
{
	const UPDATE_TABLE = 0;
	const UPDATE_SET = 1;
	const UPDATE_WHERE = 2;
	
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
		$this->query_components[self::UPDATE_TABLE] = "";
		$this->query_components[self::UPDATE_SET] = array();
		$this->query_components[self::UPDATE_WHERE] = "";
		
		parent::__construct($pdo_driver, $database);
	}	

	/**
	 * Adds the target table
	 *
	 * @param string $table_name
	 * @return DatabaseUpdateQuery				this query object instance
	 */	
	public function table($table_name)
	{
		$this->query_components[self::UPDATE_TABLE] = $table_name;
		
		return $this;
	}
	
	/**
	 * Adds an array of columns as SET clause to the query
	 *
	 * @param array $columns					the columns to update
	 * @return DatabaseUpdateQuery				this query object instance
	 */
	public function setArray($columns)
	{
		$this->query_components[self::UPDATE_SET] = array_merge($this->query_components[self::UPDATE_SET], $columns);
		
		return $this;
	}
	
	/**
	 * Adds a column-value pair to the SET clause to the query
	 *
	 * @param string $column					the column to update
	 * @param string $value						the value to set for the column
	 * @return DatabaseUpdateQuery				this query object instance
	 */	
	public function set($column, $value)
	{
		$this->query_components[self::UPDATE_SET][$column] = $value;
		
		return $this;
	}
	
	/**
	 * Adds a WHERE condition to the query connected by a conjunction
	 *
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function whereAnd($condition)
	{
		$where = $this->query_components[self::UPDATE_WHERE];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseConjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::UPDATE_WHERE] = new DatabaseConjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::UPDATE_WHERE] = $condition;
		}
		
		return $this;
	}
	
	/**
	 * Adds a WHERE condition to the query connected by a disjunction
	 *
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function whereOr($condition)
	{
		$where = $this->query_components[self::UPDATE_WHERE];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseDisjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::UPDATE_WHERE] = new DatabaseDisjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::UPDATE_WHERE] = $condition;
		}
		
		return $this;
	}
	
	/**
	 * Shortcut for where
	 * 
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function where($condition)
	{
		return $this->whereAnd($condition);
	}

	/**
	 * Converts the query into a string
	 *
	 * @throws DatabseException			if the query is incomplete or invalid
	 * @return string					the generated query
	 */
	public function __toString()
	{
		$query = "UPDATE\n";
		
		if(empty($this->query_components[self::UPDATE_TABLE]))
		{
			throw new DatabaseException("Query incomplete: No table name for UPDATE set.");
		}
		else
		{
			$query .= "\t`" . $this->query_components[self::UPDATE_TABLE] . "`\n";
		}
		
		if(!count($this->query_components[self::UPDATE_SET]))
		{
			throw new DatabaseException("Query inclomplete: No SET clause set.");
		}
		else
		{
			$sets = "";
			
			foreach($this->query_components[self::UPDATE_SET] as $column_key => $column_value)
			{
				if(!empty($sets))
				{
					$sets .= ",\n";
				}
				
				$sets .= "\t`" . $column_key . "` = " . $column_value;
			}
			
			$query .= "SET\n" . $sets . "\n";
		}
		
		if($this->query_components[self::UPDATE_WHERE])
		{				
			$query .= "WHERE\n\t" . $this->query_components[self::UPDATE_WHERE] . "\n";
		}

		return trim($query) . ";";
	}
	
	/**
	 * Executes the query
	 * 
	 * @return int					the number of rows affected
	 * @throws DatabaseException	If no Database is associated with this query
	 */
	public function execute()
	{
		if($this->database)
		{
			return $this->database->exec($this);
		}
		else
		{
			throw new DatabaseException("Tried to execute query without associated Database object");
		}
	}
}
?>
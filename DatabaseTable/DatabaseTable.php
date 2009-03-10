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

namespace PhpFramework\DatabaseTable;

use \PhpFramework\PhpFramework as PF;
use \PhpFramework\Database;

require "DatabaseTableLink.php";
require "DatabaseTableConnection.php";
require "DatabaseTableConnectionRow.php";

/**
 * Class that represents a database table
 */
class DatabaseTable
{
	/**
	 * The database connection associated to this table
	 *
	 * @var Database
	 */
	protected $db;
	
	/**
	 * Specifies the table name
	 *
	 * @var string
	 */
	protected $tableName;
	/**
	 * Specifies the table columns
	 *
	 * @var array
	 */
	protected $columns;
	/**
	 * Specifies the primary key of the table
	 *
	 * @var string
	 */
	protected $primaryKey;
	
	/**
	 * Constructs the database table
	 *
	 * @param Database $db				the Database the table is loaded from
	 * @param string $tableName			the table name
	 * @param array $columns			a set of columns of this table
	 * @param string $primaryKey		the table's primary key column
	 */
	public function __construct(Database $db, $tableName = "", $columns = array(), $primaryKey = "")
	{
		$this->db = $db;
		
		if(!empty($tableName) && count($columns) && !empty($primaryKey))
		{
			$this->tableName = $tableName;
			$this->columns = $columns;
			$this->primaryKey = $primaryKey;
		}
	}
	
	/**
	 * Returns a select query for this table
	 *
	 * @param string $tableAlias		an alias for the table name
	 * @return DatabaseSelectQuery		a query that selects all columns from the table
	 */
	public function select($tableAlias = "")
	{
		$select = $this->db->select();
		
		if(!empty($tableAlias)) // Use alias rewriting
		{
			$select->from($this->tableName, $tableAlias);
		
			foreach($this->columns as $column)
			{
				$select->column($column, $tableAlias, $tableAlias . "." . $column);
			}					
		}
		else
		{
			$select->from($this->tableName);
		
			foreach($this->columns as $column)
			{
				$select->column($column, $this->tableName);
			}				
		}

		return $select;
	}
	
	/**
	 * Adds this table's columns to a SELECT query
	 *
	 * @param DatabaseSelectQuery $select		an existing select query
	 * @param string $tableAlias				an alias for the table name
	 * @return DatabaseSelectQuery				returns the modified select query with the added columns
	 */
	public function addSelectColumns(DatabaseSelectQuery $select, $tableAlias = "")
	{
		if(!empty($tableAlias)) // Use alias rewriting
		{		
			foreach($this->columns as $column)
			{
				$select->column($column, $tableAlias, $tableAlias . "." . $column);
			}					
		}
		else
		{		
			foreach($this->columns as $column)
			{
				$select->column($column, $this->tableName);
			}				
		}

		return $select;		
	}
	
	/**
	 * Finds a row from this table by a primary key value
	 *
	 * @param string $value			the primary key value
	 * @return Databaserow			the found row
	 */
	public function find($value)
	{
		$select = $this->select();
		$select->where($this->primaryKey . " = '" . $value . "'");
		
		return $this->db->query($select)->fetch();
	}
	
	/**
	 * Finds a row from this table by a given column and its value
	 *
	 * @param string $column		the column name by which search 
	 * @param string $value			the searched value
	 * @return DatabaseRow			the found row
	 */
	public function findBy($column, $value)
	{
		$select = $this->select();
		$select->where($column . " = '" . $value . "'");
		
		return $this->db->query($select)->fetch();
	}
	
	/**
	 * Fetches all rows for a given set of conditions
	 *
	 * @param array $wheres				a set of WHERE conditions
	 * @return array[DatabaseRow]		a set of all rows that match the condition
	 */
	public function fetchAll($wheres = array())
	{
		$select = $this->select();
		
		foreach($wheres as $where)
		{
			if(isset($where[DatabaseSelectQuery::KEY_CONDITION_CONNECTIVE]))
			{
				$select->where
				(
					$where[DatabaseSelectQuery::KEY_CONDITION_CONDITION],
					$where[DatabaseSelectQuery::KEY_CONDITION_CONNECTIVE]
				);	
			}
			else
			{
				$select->where($where[DatabaseSelectQuery::KEY_CONDITION_CONDITION]);	
			}			
		}
		
		return $this->db->query($select)->fetchAll();
	}
	
	/**
	 * Returns this table's table name
	 *
	 * @return string		the table name
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	 * Returns this table's columns
	 *
	 * @return array[string]		a set of column names
	 */
	public function getColumns()
	{
		return $this->columns;
	}
	
	/**
	 * Returns this table's primary key
	 *
	 * @return string			the primary key
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}
	
	/**
	 * Checks if this table has a certain column
	 *
	 * @param string $column		the column name to check
	 * @return boolean				true if the column exists
	 */
	public function hasColumn($column)
	{
		return in_array($this->columns, $column);
	}
	
	/**
	 * Returns this database table's database connection
	 *
	 * @return Database			the table's Database connection
	 */
	public function getDb()
	{
		return $this->db;
	}
}
?>
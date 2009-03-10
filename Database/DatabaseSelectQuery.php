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
 * Class that represents an SQL SELECT database query
 *
 */
class DatabaseSelectQuery extends DatabaseQuery
{
	const SELECT_COLUMNS = 0;
	const SELECT_FROM = 1;
	const SELECT_JOIN = 2;
	const SELECT_WHERE = 3;
	const SELECT_GROUP = 4;
	const SELECT_HAVING = 5;
	const SELECT_ORDER = 6;
	const SELECT_LIMIT = 7;
	
	const JOIN_INNER = 0;
	const JOIN_LEFT_OUTER = 1;
	const JOIN_RIGHT_OUTER = 2;
	const JOIN_CROSS = 3;
	
	const KEY_JOIN_TYPE = 0;
	const KEY_JOIN_TARGET = 1;
	const KEY_JOIN_CONDITIONS = 2;
	const KEY_JOIN_ALIAS = 3;
	const KEY_FROM_ALIAS = 0;
	const KEY_FROM_TABLE = 1;
	const KEY_ORDER_TYPE = 0;
	const KEY_ORDER_COLUMN = 1;
	
	const ORDER_ASC = 0;
	const ORDER_DESC = 1;
	
	/**
	 * Components this query is made of
	 *
	 * @var array
	 */
	protected $query_components;
	
	/**
	 * Constructs the select query and initializes its components
	 * 
	 * @param string $pdo_driver		The query's PDO driver
	 * @param Database $database		(optional) A PhpFramework database to associate with the query
	 * @override
	 */
	public function __construct($pdo_driver, $database = null)
	{
		$this->query_components[self::SELECT_COLUMNS] = array();
		$this->query_components[self::SELECT_FROM] = array();
		$this->query_components[self::SELECT_JOIN] = array();
		$this->query_components[self::SELECT_WHERE] = null;
		$this->query_components[self::SELECT_GROUP] = array();
		$this->query_components[self::SELECT_HAVING] = null;
		$this->query_components[self::SELECT_ORDER] = array();
		$this->query_components[self::SELECT_LIMIT] = "";
		
		parent::__construct($pdo_driver, $database);
	}
	
	/**
	 * Adds a column to the query
	 *
	 * @param string $column			(optional) the column to select
	 * @param string $table				the table name of the column to select
	 * @param string $alias				(optional) a result alias for this column
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function column($column, $table = "", $alias = "")
	{
		if(empty($alias))
		{
			$temp = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
			
			$this->query_components[self::SELECT_COLUMNS][$temp] = $temp;
		}
		else
		{
			$this->query_components[self::SELECT_COLUMNS][$alias] = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		}
		
		return $this;
	}
	
	/**
	 * Adds a COUNT aggregate function to the query
	 *
	 * @param string $column			the column to count
	 * @param string $table				the table name of the column to count
	 * @param string $alias				alias for the aggregate function's result
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function columnCount($column, $table, $alias)
	{
		$this->query_components[self::SELECT_COLUMNS][$alias] = "COUNT(" . (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`)";

		return $this;
	}
	
	/**
	 * Adds a MIN aggregate function to the query
	 *
	 * @param string $column			the column to count
	 * @param string $table				the table name of the column to minimize
	 * @param string $alias				alias for the aggregate function's result
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function columnMin($column, $table, $alias)
	{
		$this->query_components[self::SELECT_COLUMNS][$alias] = "MIN(" . (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`)";

		return $this;
	}
	
	/**
	 * Adds a MAX aggregate function to the query
	 *
	 * @param string $column			the column to count
	 * @param string $table				the table name of the column to maximize
	 * @param string $alias				alias for the aggregate function's result
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function columnMax($column, $table, $alias)
	{
		$this->query_components[self::SELECT_COLUMNS][$alias] = "MAX(" . (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`)";

		return $this;
	}	

	/**
	 * Adds a from clause to the query
	 *
	 * @param string $table				the table to select from
	 * @param string $alias				a table name alias
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function from($table, $alias = "")
	{
		if(empty($table)) throw new DatabaseException("Table name in FROM clause must not be empty");
		
		$this->query_components[self::SELECT_FROM][self::KEY_FROM_TABLE] = $table;
		
		if(!empty($alias))
		{
			$this->query_components[self::SELECT_FROM][self::KEY_FROM_ALIAS] = $alias;
		}
		
		return $this;
	}
	
	/**
	 * Adds a join clause to the query
	 *
	 * @param integer $type				a JOIN_ constant
	 * @param string $target			the join target table
	 * @param string $conditions		ON joining conditions
	 * @param string $alias				(optional) an alias for the joined table name
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function join($type, $target, $conditions = "", $alias = "")
	{		
		$join = array();
		$join[self::KEY_JOIN_TYPE] = $type;
		$join[self::KEY_JOIN_TARGET] = $target;
		
		if(!empty($conditions))
		{
			$join[self::KEY_JOIN_CONDITIONS] = $conditions;
		}
		
		if(!empty($alias))
		{
			$join[self::KEY_JOIN_ALIAS] = $alias;
		}
		
		$this->query_components[self::SELECT_JOIN][] = $join;
		
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
		$where = $this->query_components[self::SELECT_WHERE];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseConjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::SELECT_WHERE] = new DatabaseConjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::SELECT_WHERE] = $condition;
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
		$where = $this->query_components[self::SELECT_WHERE];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseDisjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::SELECT_WHERE] = new DatabaseDisjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::SELECT_WHERE] = $condition;
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
	 * Adds a GROUP BY clause to the query
	 *
	 * @param string $column			the column to group
	 * @param string $table				(optional) the table with the column to group 
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function group($column, $table = "")
	{
		$group = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		$this->query_components[self::SELECT_GROUP][] = $group;
		
		return $this;
	}
	
	/**
	 * Adds a HAVING condition to the query connected by a conjunction
	 *
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function havingAnd($condition)
	{
		$where = $this->query_components[self::SELECT_HAVING];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseConjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::SELECT_HAVING] = new DatabaseConjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::SELECT_HAVING] = $condition;
		}
		
		return $this;
	}
	
	/**
	 * Adds a WHERE condition to the query connected by a disjunction
	 *
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function havingOr($condition)
	{
		$where = $this->query_components[self::SELECT_HAVING];
		
		if(!empty($where))
		{
			if($where instanceof DatabaseDisjunction)
			{
				$where->addExpression($condition);
			}
			else
			{
				$this->query_components[self::SELECT_HAVING] = new DatabaseDisjunction(array($where, $condition));
			}
		}
		else
		{
			$this->query_components[self::SELECT_HAVING] = $condition;
		}
		
		return $this;
	}
	
	/**
	 * Shortcut for havingAnd
	 * 
	 * @param string $condition			the condition to add
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function having($condition)
	{
		return $this->havingAnd($condition);
	}
	
	/**
	 * Adds an ORDER BY clause to the query
	 *
	 * @param string $column			the ordering column
	 * @param string $table				(optional) the table in which the ordering column is found 
	 * @param integer $type				(optional) an ORDER_ constant
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function order($column, $table = "", $type = 0)
	{
		$order = array();
		$order[self::KEY_ORDER_COLUMN] = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		$order[self::KEY_ORDER_TYPE] = $type;

		$this->query_components[self::SELECT_ORDER][] = $order;
		
		return $this;
	}		
	
	/**
	 * Adds a LIMIT clause to the query
	 *
	 * @param integer $start			the starting rank of the limit
	 * @param integer $count			the limit count
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function limit($start, $count = null)
	{
		$limit = $start;
		
		if($count)
		{
			$limit .= "," . $count;
		}
		
		$this->query_components[self::SELECT_LIMIT] = $limit;
		
		return $this;
	}
	
	/**
	 * Converts the query into a string
	 *
	 * @throws DatabaseException		if the query is incomplete or invalid
	 * @return string					the generated query
	 */
	public function __toString()
	{
		
		$query = "SELECT\n";
		
		if(!count($this->query_components[self::SELECT_COLUMNS]))
		{
			$query .= "\t*\n";
		}
		else
		{
			$columns = "";
			foreach($this->query_components[self::SELECT_COLUMNS] as $alias => $column)
			{
				if(!empty($columns))
				{
					$columns .= ",\n";
				}
				
				$columns .= "\t" . $column;
				
				if($alias !== $column)
				{
					$columns .= " AS `" . $alias . "`";
				}
			}
			
			$query .= $columns . "\n";
		}
		
		$from = "";
		if(!count($this->query_components[self::SELECT_FROM]))
		{
			throw new DatabaseException("Query incomplete: No FROM clause set.");
		}
		else
		{
			$query .= "FROM\n\t`" . $this->query_components[self::SELECT_FROM][self::KEY_FROM_TABLE] . "`";
			
			if(isset($this->query_components[self::SELECT_FROM][self::KEY_FROM_ALIAS]))
			{
				$query .= " `" . $this->query_components[self::SELECT_FROM][self::KEY_FROM_ALIAS] . "`";
			}
			
			$query .= "\n";
		}
				
		foreach($this->query_components[self::SELECT_JOIN] as $join)
		{			
			switch($join[self::KEY_JOIN_TYPE])
			{
				case self::JOIN_INNER:
					$query .= "INNER JOIN\n"; 
				break;
				case self::JOIN_LEFT_OUTER:
					$query .= "LEFT OUTER JOIN\n";
				break;
				case self::JOIN_RIGHT_OUTER:
					$query .= "RIGHT OUTER JOIN\n";
				break;
				default:
					throw new DatabaseException("Query invalid: Received unsupported JOIN type " . $join[self::KEY_JOIN_TYPE]);
				break;
			}
			
			$query .= "\t`" . $join[self::KEY_JOIN_TARGET] . "`";
			
			if(isset($join[self::KEY_JOIN_ALIAS]))
			{
				$query .= " `" . $join[self::KEY_JOIN_ALIAS] . "`";
			}
			
			$query .= "\n";
			
			if($join[self::KEY_JOIN_CONDITIONS])
			{			
				$query .= "ON\n\t" . $join[self::KEY_JOIN_CONDITIONS] . "\n";
			}
		}
		
		if($this->query_components[self::SELECT_WHERE])
		{				
			$query .= "WHERE\n\t" . $this->query_components[self::SELECT_WHERE] . "\n";
		}
		
		$groups = "";
		foreach($this->query_components[self::SELECT_GROUP] as $group)
		{
			if(!empty($groups))
			{
				$groups .= ",\n";
			}
			
			$groups .= "\t" . $group;
		}
		if(!empty($groups))
		{
			$query .= "GROUP BY\n" . $groups . "\n";
		}
		
		if($this->query_components[self::SELECT_HAVING])
		{				
			$query .= "HAVING\n\t" . $this->query_components[self::SELECT_HAVING] . "\n";
		}
		
		$orders = "";
		foreach($this->query_components[self::SELECT_ORDER] as $order)
		{
			if(!empty($orders))
			{
				$orders .= ",\n";
			}
			
			$orders .= "\t" . $order[self::KEY_ORDER_COLUMN];

			if(isset($order[self::KEY_ORDER_TYPE]))
			{
				switch($order[self::KEY_ORDER_TYPE])
				{
					case self::ORDER_ASC:
						$orders .= " ASC";
					break;
					case self::ORDER_DESC:
						$orders .= " DESC";
					break;
					default:
						throw new DatabaseException
						(
							"Query invalid: Received unsupported ORDER type " . $order[self::KEY_ORDER_TYPE]
						);
					break;
				}
			}
		}
		if(!empty($orders))
		{
			$query .= "ORDER BY\n" . $orders . "\n";
		}
		
		if(!empty($this->query_components[self::SELECT_LIMIT]))
		{
				$query .= "LIMIT\n" . $this->query_components[self::SELECT_LIMIT] . "\n";
		}
		
		return trim($query) . ";";
	}
}
?>
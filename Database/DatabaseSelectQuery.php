<?php
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
	const KEY_CONDITION_CONNECTIVE = 0;
	const KEY_CONDITION_CONDITION = 1;
	
	const CONDITION_AND = 0;
	const CONDITION_OR = 1;
	
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
	 * @param string $pdo_driver
	 * @override
	 */
	public function __construct($pdo_driver)
	{
		$this->query_components[self::SELECT_COLUMNS] = array();
		$this->query_components[self::SELECT_FROM] = array();
		$this->query_components[self::SELECT_JOIN] = array();
		$this->query_components[self::SELECT_WHERE] = array();
		$this->query_components[self::SELECT_GROUP] = array();
		$this->query_components[self::SELECT_HAVING] = "";
		$this->query_components[self::SELECT_ORDER] = array();
		$this->query_components[self::SELECT_LIMIT] = "";
		
		parent::__construct($pdo_driver);
	}
	
	/**
	 * Adds a column to the query
	 *
	 * @param string $table
	 * @param string $column
	 * @param string $alias
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function column($table, $column, $alias = "")
	{
		if(!empty($alias))
		{
			$this->query_components[self::SELECT_COLUMNS][$alias] =
				(!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		}
		else
		{
			$temp = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
			$this->query_components[self::SELECT_COLUMNS][$temp] = $temp;
		}
		
		return $this;
	}

	/**
	 * Adds a from clause to the query
	 *
	 * @param string $table
	 * @param string $alias
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function from($table, $alias = "")
	{
		if(!empty($table) === false) throw new PreconditionViolation("table not empty");
		
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
	 * @param string $target
	 * @param array $conditions
	 * @param string $alias
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function join($type, $target, $conditions = array(), $alias = "")
	{		
		$join = array();
		$join[self::KEY_JOIN_TYPE] = $type;
		$join[self::KEY_JOIN_TARGET] = $target;
		
		if(count($conditions))
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
	 * Adds a WHERE condition to the query
	 *
	 * @param string $condition
	 * @param integer $connective		a CONDITION_ constant
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function where($condition, $connective = 0)
	{
		$where = array();
		$where[self::KEY_CONDITION_CONDITION] = $condition;
		$where[self::KEY_CONDITION_CONNECTIVE] = $connective;

		$this->query_components[self::SELECT_WHERE][] = $where;
		
		return $this;
	}
	
	/**
	 * Adds a GROUP BY clause to the query
	 *
	 * @param string $table
	 * @param string $column
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function group($table, $column)
	{
		$group = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		$this->query_components[self::SELECT_GROUP][] = $group;
		
		return $this;
	}
	
	/**
	 * Adds a HAVING condition to the query
	 *
	 * @param string $condition
	 * @param integer $connective		a CONDITION_ constant
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function having($condition, $connective = 0)
	{
		$having = array();
		$having[self::KEY_CONDITION_CONDITION] = $condition;
		$having[self::KEY_CONDITION_CONNECTIVE] = $connective;

		$this->query_components[self::SELECT_HAVING][] = $having;
		
		return $this;
	}
	
	/**
	 * Adds an ORDER BY clause to the query
	 *
	 * @param string $table
	 * @param string $column
	 * @param integer $type				an ORDER_ constant
	 * @return DatabaseSelectQuery		this query object instance
	 */
	public function order($table, $column, $type = 0)
	{
		$order = array();
		$order[self::KEY_ORDER_COLUMN] = (!empty($table) ? "`" . $table . "`." : "") . "`" . $column . "`";
		$order[self::KEY_ORDER_TYPE] = $type;

		$this->query_components[self::SELECT_HAVING][] = $having;
		
		return $this;
	}		
	
	/**
	 * Adds a LIMIT clause to the query
	 *
	 * @param integer $start
	 * @param integer $count
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
	 * @throws DatabseException			if the query is incomplete or invalid
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
			
			if(isset($join[self::KEY_JOIN_CONDITIONS]))
			{				
				$ons = "";
				foreach($join[self::KEY_JOIN_CONDITIONS] as $on)
				{
					if(!empty($ons))
					{
						switch($on[self::KEY_CONDITION_CONNECTIVE])
						{
							case self::CONDITION_AND:
								$ons .= " AND\n";
							break;
							case self::CONDITION_OR:
								$ons .= " OR\n";
							break;
							default:
								throw new DatabaseException
								(
									"Query invalid: Received unsupported condition connective " . 
									$on[self::KEY_CONDITION_CONNECTIVE]
								);
							break;
						}
					}
					
					$ons .= "\t" . $on[self::KEY_CONDITION_CONDITION];
				}
				if(!empty($ons))
				{
					$query .= "ON\n" . $ons . " ";
				}				 
			}
		}
		
		$wheres = "";
		foreach($this->query_components[self::SELECT_WHERE] as $where)
		{
			if(!empty($wheres))
			{
				switch($where[self::KEY_CONDITION_CONNECTIVE])
				{
					case self::CONDITION_AND:
						$wheres .= " AND\n";
					break;
					case self::CONDITION_OR:
						$wheres .= " OR\n";
					break;
					default:
						throw new DatabaseException
						(
							"Query invalid: Received unsupported condition connective " . $where[self::KEY_CONDITION_CONNECTIVE]
						);
					break;
				}
			}
			
			$wheres .= "\t" . $where[self::KEY_CONDITION_CONDITION];
		}
		if(!empty($wheres))
		{
			$query .= "WHERE\n" . $wheres . "\n";
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
		
		if(!empty($this->query_components[self::SELECT_HAVING]))
		{
				$query .= "HAVING " . $this->query_components[self::SELECT_HAVING] . " ";
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
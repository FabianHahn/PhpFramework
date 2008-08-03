<?php
/**
 * Class that represends an SQL UPDATE query
 *
 */
class DatabaseUpdateQuery extends DatabaseQuery
{
	const UPDATE_TABLE = 0;
	const UPDATE_SET = 1;
	const UPDATE_WHERE = 2;
	
	const KEY_CONDITION_CONNECTIVE = 0;
	const KEY_CONDITION_CONDITION = 1;
	
	const CONDITION_AND = 0;
	const CONDITION_OR = 1;
	
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
		$this->query_components[self::UPDATE_TABLE] = "";
		$this->query_components[self::UPDATE_SET] = array();
		$this->query_components[self::UPDATE_WHERE] = array();
		
		parent::__construct($pdo_driver);
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
	public function set($columns)
	{
		$this->query_components[self::UPDATE_SET] = $columns;
		
		return $this;
	}
	
	/**
	 * Adds a WHERE condition to the query
	 *
	 * @param string $condition
	 * @param integer $connective		a CONDITION_ constant
	 * @return DatabaseUpdateQuery				this query object instance
	 */
	public function where($condition, $connective = 0)
	{
		$where = array();
		$where[self::KEY_CONDITION_CONDITION] = $condition;
		$where[self::KEY_CONDITION_CONNECTIVE] = $connective;

		$this->query_components[self::UPDATE_WHERE][] = $where;
		
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
		$query = "UPDATE\n";
		
		if(empty($this->query_components[self::UPDATE_TABLE]))
		{
			throw new DatabaseException("Query incomplete: No table name for UPDATE set.");
		}
		else
		{
			$query .= "\t`" . $this->query_components[self::UPDATE_FROM] . "`\n";
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
				
				$sets .= "\t`" . $column_key . "` = '" . $column_value . "'";
			}
			
			$query .= "SET\n" . $sets . "\n";
		}
		
		$wheres = "";
		foreach($this->query_components[self::UPDATE_WHERE] as $where)
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

		return trim($query) . ";";
	}
}
?>
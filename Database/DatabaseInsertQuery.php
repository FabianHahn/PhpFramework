<?php
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
	 * @param string $pdo_driver
	 * @override
	 */
	public function __construct($pdo_driver)
	{
		$this->query_components[self::INSERT_TABLE] = "";
		$this->query_components[self::INSERT_VALUES] = array();
		
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
		$this->query_components[self::INSERT_TABLE] = $table_name;
		
		return $this;
	}
	
	/**
	 * Adds an array of columns as VALUES clause to the query
	 *
	 * @param array $columns					the columns to update
	 * @return DatabaseUpdateQuery				this query object instance
	 */
	public function values($columns)
	{
		$this->query_components[self::INSERT_VALUES] = $columns;
		
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
			$query .= "\t`" . $this->query_components[self::UPDATE_FROM] . "`\n";
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
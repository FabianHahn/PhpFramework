<?php
PhpFramework::depends("Database");

require_once "DatabaseTableConnection.php";
require_once "DatabaseTableConnectionRow.php";

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
	 * @param Database $db
	 * @param string $tableName
	 * @param array $columns
	 * @param string $primaryKey
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
	 * @param string $tableAlias
	 * @return DatabaseSelectQuery
	 */
	public function select($tableAlias = "")
	{
		$select = $this->db->select();
		
		if(!empty($tableAlias)) // Use alias rewriting
		{
			$select->from($this->tableName, $tableAlias);
		
			foreach($this->columns as $column)
			{
				$select->column($tableAlias, $column, $tableAlias . "." . $column);
			}					
		}
		else
		{
			$select->from($this->tableName);
		
			foreach($this->columns as $column)
			{
				$select->column($this->tableName, $column);
			}				
		}

		return $select;
	}
	
	/**
	 * Adds this table's columns to a SELECT query
	 *
	 * @param DatabaseSelectQuery $select
	 * @param string $tableAlias
	 * @return DatabaseSelectQuery
	 */
	public function addSelectColumns(DatabaseSelectQuery $select, $tableAlias = "")
	{
		if(!empty($tableAlias)) // Use alias rewriting
		{		
			foreach($this->columns as $column)
			{
				$select->column($tableAlias, $column, $tableAlias . "." . $column);
			}					
		}
		else
		{		
			foreach($this->columns as $column)
			{
				$select->column($this->tableName, $column);
			}				
		}

		return $select;		
	}
	
	/**
	 * Finds a row from this table by a primary key value
	 *
	 * @param string $value
	 * @return array
	 */
	public function find($value)
	{
		$select = $this->select();
		$select->where($this->primaryKey . " = '" . $value . "'");
		
		$ret = $this->db->query($select);
		
		return $this->db->fetch($ret);
	}
	
	/**
	 * Finds a row from this table by a given column and its value
	 *
	 * @param string $column
	 * @param string $value
	 * @return array
	 */
	public function findBy($column, $value)
	{
		$select = $this->select();
		$select->where($column . " = '" . $value . "'");
		
		$ret = $this->db->query($select);
		
		return $this->db->fetch($ret);		
	}
	
	/**
	 * Fetches all rows for a given set of conditions
	 *
	 * @param array $wheres
	 * @return array
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
		
		return $this->db->fetchAll($this->db->query($select));
	}
	
	/**
	 * Returns this table's table name
	 *
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}
	
	/**
	 * Returns this table's columns
	 *
	 * @return array
	 */
	public function getColumns()
	{
		return $this->columns;
	}
	
	/**
	 * Returns this table's primary key
	 *
	 * @return string
	 */
	public function getPrimaryKey()
	{
		return $this->primaryKey;
	}
	
	/**
	 * Checks if this table has a certain column
	 *
	 * @param string $column
	 * @return boolean
	 */
	public function hasColumn($column)
	{
		return in_array($this->columns, $column);
	}
	
	/**
	 * Returns this database table's database connection
	 *
	 * @return Database
	 */
	public function getDb()
	{
		return $this->db;
	}
}
?>
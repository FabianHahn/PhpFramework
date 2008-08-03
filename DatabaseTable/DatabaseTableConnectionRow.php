<?php
/**
 * Represents a root row returned by a database table connection
 */
class DatabaseTableConnectionRow extends DatabaseRow
{
	/**
	 * This database connection row's table connection
	 *
	 * @var DatabaseTableConnection
	 */
	protected $connection;
	
	/**
	 * This connection row's id
	 *
	 * @var integer
	 */
	protected $row_id;
	
	/**
	 * Constructs this database connection row
	 *
	 * @override
	 * @param array $data
	 * @param DatabaseTableConnection $connection
	 * @param integer $row_id
	 */
	public function __construct($data, DatabaseTableConnection $connection, $row_id)
	{
		$this->connection = $connection;
		$this->row_id = $row_id;
		
		parent::__construct($data);
	}
	
	/**
	 * Returns the rows from $nodeAlias connected to this row
	 *
	 * @param string $nodeAlias
	 * @return array
	 */
	public function getConnectedRows($nodeAlias)
	{
		return $this->connection->getConnectedRows($this, $nodeAlias);
	}
	
	/**
	 * Returns the row from $nodeAlias connected to this row
	 *
	 * @param string $nodeAlias
	 * @return DatabaseRow
	 */
	public function getConnectedRow($nodeAlias)
	{
		return $this->connection->getConnectedRow($this, $nodeAlias);
	}
	
	/**
	 * Returns this row's id
	 *
	 * @return integer
	 */
	public function getRowId()
	{
		return $this->row_id;
	}
	
	/**
	 * Returns a string representation of this row used for array comparison functions
	 *
	 * @return string
	 */
	public function __toString()
	{
		return print_r($this->data, true);
	}
}
?>
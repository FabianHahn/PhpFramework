<?php
/**
 * Class that represents a database row
 *
 */
class DatabaseRow
{
	/**
	 * The row data of this database row
	 *
	 * @var array
	 */
	protected $data;
	
	/**
	 * Constructs this database row
	 *
	 * @param array $data
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}
	
	/**
	 * Handler for key requests
	 *
	 * @param string $key
	 * @return string
	 * @throws DatabaseException				if key doesn't exist
	 */
	public function __get($key)
	{
		if(array_key_exists($key, $this->data))
		{
			return $this->data[$key];
		}
		else
		{
			throw new DatabaseException("Row doesn't contain requested key " . $key);
		}
	}
	
	/**
	 * Returns this row's raw data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}
}
?>
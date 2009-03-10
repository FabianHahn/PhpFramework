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
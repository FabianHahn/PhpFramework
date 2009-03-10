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
 * Class that represents a connection between a root database table and multiple node tables.
 * Each link to such a node table is represented by a DatabaseTableLink
 */
class DatabaseTableConnection
{
	const KEY_TABLE = 0;
	const KEY_ROOT_KEYS = 1;
	const KEY_NODE_KEYS = 2;
	const KEY_JOIN_TYPE = 3;
	const KEY_FETCH_MODE = 4;
	const KEY_ADDITIONAL_CONDITIONS = 5;
	
	const FETCH_LAZY = 0;
	const FETCH_EAGER = 1;
	
	/**
	 * The root table of this table connection
	 *
	 * @var DatabaseTable
	 */
	protected $rootTable;
	/**
	 * The connection nodes of this table connection
	 *
	 * @var array
	 */
	protected $nodeTables;
	
	/**
	 * Connected rows of a fetch result
	 *
	 * @var array
	 */
	protected $connectedRows;
	
	/**
	 * Constructs this database table connection
	 *
	 * @param DatabaseTable $rootTable
	 * @param array $nodeTables
	 */
	public function __construct(DatabaseTable $rootTable, $nodeTables)
	{
		$this->rootTable = $rootTable;
		$this->nodeTables = $nodeTables;
	}
	
	/**
	 * Fetches this table connection's data
	 *
	 * @return array				an array of DatabaseTableConnectionRow objects
	 */
	public function fetchAll()
	{
		$select = $this->rootTable->select("root");
		
		foreach($this->nodeTables as $nodeAlias => $nodeTable)
		{
			if($nodeTable[self::KEY_FETCH_MODE] == self::FETCH_EAGER) // Enable eager fetching
			{
				// Add columns
				$nodeTable[self::KEY_TABLE]->addSelectColumns($select, $nodeAlias);
				
				// Calculate conditions
				$conditions = isset($nodeTable[self::KEY_ADDITIONAL_CONDITIONS]) ?
					$nodeTable[self::KEY_ADDITIONAL_CONDITIONS] : array();
				
				foreach($nodeTable[self::KEY_ROOT_KEYS] as $i => $rootKey)
				{
					$c_string = sprintf
					(
						"`root`.`%s` = `%s`.`%s`",
						$rootKey,
						$nodeAlias,
						$nodeTable[self::KEY_NODE_KEYS][$i]
					);
					
					$condition = array
					(
						DatabaseSelectQuery::KEY_CONDITION_CONNECTIVE	=> DatabaseSelectQuery::CONDITION_AND,
						DatabaseSelectQuery::KEY_CONDITION_CONDITION	=> $c_string
					);
					
					$conditions[] = $condition;
				}
				
				// Add the JOIN
				$select->join
				(
					$nodeTable[self::KEY_JOIN_TYPE],
					$nodeTable[self::KEY_TABLE]->getTableName(),
					$conditions,
					$nodeAlias
				);		
			}
		}
		
		$statement = $this->rootTable->getDb()->query($select);
		
		$rootRows = array();
		$this->connectedRows = array();
		
		$i = 0;
		
		while($row = $this->rootTable->getDb()->fetch($statement))
		{	
			$fetched = array();
			$rowdata = $row->getData();
			
			foreach($rowdata as $key => $value)
			{
				$pos = strcspn($key, ".");
				$tableAlias = substr($key, 0, $pos);
				$column = substr($key, ++$pos);
				
				$fetched[$tableAlias][$column] = $value;
			}
			
			$connectionRow = new DatabaseTableConnectionRow($fetched["root"], $this, $i); 
			$rootRows[] = $connectionRow;
			
			$rootRows = array_unique($rootRows);
			$j = array_search((string) $connectionRow, $rootRows);
			
			foreach($fetched as $tableAlias => $tableRow)
			{
				if(!isset($this->connectedRows[$tableAlias][$j]))
				{
					$this->connectedRows[$tableAlias][$j] = array();
				}
				
				$this->connectedRows[$tableAlias][$j][] = new DatabaseRow($tableRow);
			}
			
			$i++;
		}
		
		return $rootRows;
	}
	
	/**
	 * Returns all connected rows by connection row and node alias
	 *
	 * @param DatabaseTableConnectionRow $rootRow
	 * @param unknown_type $nodeAlias
	 * @throws DatabaseException						if connection doesn't exist
	 * @return array									an array of DatabaseRow objects
	 */
	public function getConnectedRows(DatabaseTableConnectionRow $rootRow, $nodeAlias)
	{
		$nodeTable = $this->nodeTables[$nodeAlias];
		
		if($nodeTable[self::KEY_FETCH_MODE] == self::FETCH_EAGER) // Already fetched with eager mode
		{
			if(count($this->connectedRows[$nodeAlias][$rootRow->getRowId()]))
			{
				return $this->connectedRows[$nodeAlias][$rootRow->getRowId()];
			}
			
			throw new DatabaseException("Trying to access non existent connected row by node alias " . $nodeAlias);
		}
		else // Lazy fetching, do it now
		{
			$select = $nodeTable[self::KEY_TABLE]->select();
			$root_data = $rootRow->getData();
			
			foreach($nodeTable[self::KEY_ROOT_KEYS] as $i => $root_key)
			{
				$select->where
				(
					"`" . $nodeTable[self::KEY_NODE_KEYS][$i] . "` = '" . $root_data[$root_key] . "'",
					DatabaseSelectQuery::CONDITION_AND 
				);
			}
			
			$statement = $this->rootTable->getDb()->query($select);
			
			
			$ret = $this->rootTable->getDb()->fetchAll($statement);
			
			if(!count($ret))
			{
				$dummy_row = array();
				$columns = $nodeTable[self::KEY_TABLE]->getColumns();
				foreach($columns as $node_column)
				{
					$dummy_row[$node_column] = NULL;
				}
				
				return array(new DatabaseRow($dummy_row));
			}
			
			return $ret;
		}
	}
	
	/**
	 * Returns a connected row by connection row and node alias
	 *
	 * @param DatabaseTableConnectionRow $rootRow
	 * @param string $nodeAlias
	 * @throws DatabaseException						if connection doesn't exist
	 * @return DatabaseRow
	 */
	public function getConnectedRow(DatabaseTableConnectionRow $rootRow, $nodeAlias)
	{
		$nodeTable = $this->nodeTables[$nodeAlias];
		
		if($nodeTable[self::KEY_FETCH_MODE] == self::FETCH_EAGER) // Already fetched with eager mode
		{
			if(count($this->connectedRows[$nodeAlias][$rootRow->getRowId()]))
			{
				return $this->connectedRows[$nodeAlias][$rootRow->getRowId()][0];
			}
			
			throw new DatabaseException("Trying to access non existeng connected row by node alias " . $nodeAlias);
		}
		else // Lazy fetching, do it now
		{
			$select = $nodeTable[self::KEY_TABLE]->select();
			$root_data = $rootRow->getData();
			
			foreach($nodeTable[self::KEY_ROOT_KEYS] as $i => $root_key)
			{
				$select->where
				(
					"`" . $nodeTable[self::KEY_NODE_KEYS][$i] . "` = '" . $root_data[$root_key] . "'",
					DatabaseSelectQuery::CONDITION_AND 
				);
			}
			
			$statement = $this->rootTable->getDb()->query($select);
			
			$ret = $this->rootTable->getDb()->fetch($statement);
			
			if(!$ret)
			{
				$dummy_row = array();
				foreach($nodeTable[self::KEY_NODE_KEYS] as $node_key)
				{
					$dummy_row[$node_key] = NULL;
				}
				
				return new DatabaseRow($dummy_row);
			}
			
			return $ret;
		}
	}
}
?>
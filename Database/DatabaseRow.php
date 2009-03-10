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
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
 * Class that represents a link to a node table
 */
class DatabaseTableLink
{
	/**
	 * The node table this connective links to
	 * 
	 * @var DatabaseTable
	 */
	protected $nodeTable;
	
	/**
	 * An array of column connection
	 * One connection entry looks like rootCol -> array(nodeCol1, nodeCol2, ...)
	 * 
	 * @var array[array[string]]
	 */
	protected $connectedColumns;
	
	/**
	 * One of the DatabaseTableConnection FETCH_* constants
	 * 
	 * @var int
	 */
	protected $joinType;
	
	/**
	 * Additional connection conditions
	 * 
	 * @var array[DatabaseCondition]
	 */
	protected $additionalConditions;
}
?>
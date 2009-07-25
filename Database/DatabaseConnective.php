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
 * Class that represents a connective in a condition clause of a query
 */
abstract class DatabaseConnective
{
	/**
	 * The expressions to connect
	 * 
	 * @var array[string]
	 */
	protected $expressions;
	
	/**
	 * Constructs a connective
	 * 
	 * @param array[string] $expressions		the expressions to connect
	 */
	public function __construct($expressions)
	{
		$this->expressions = $expressions;
	}
	
	/**
	 * Add an expression to the list of expressions
	 * 
	 * @param $expression		the expression to add
	 */
	public function addExpression($expression)
	{
		$this->expressions[] = $expression;
	}
	
	/**
	 * Returns the string value of this connective
	 * 
	 * @return string			the string value of the connective
	 */
	public function __toString()
	{
		$ret = "";
		
		foreach($this->expressions as $expression)
		{
			if(!empty($ret))
			{
				$ret .= " " . $this->getConnective() . " ";
			}
			
			if($expression instanceof DatabaseConnective) // Check if the expression is a connective itself
			{
				$ret .= "(" . $expression . ")";
			}
			else
			{
				$ret .= $expression;
			}
		}
		
		return $ret;
	}
	
	/**
	 * Returns this class' connective
	 * 
	 * @return string
	 */
	abstract protected function getConnective();
}
?>
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

require "../PhpFramework.php";

use \PhpFramework\PhpFramework as PF;

PF::init();

use \PhpFramework\HtmlDebugLogger\HtmlDebugLogger;
use \PhpFramework\Database\Database;
use \PhpFramework\Database\DatabaseSelectQuery;
use \PhpFramework\Database\DatabaseStatement;
use \PhpFramework\Database\DatabaseException;

HtmlDebugLogger::enable(PF::LOG_WARNING | PF::LOG_INFO);
DatabaseException::enableHtmlOutput();

$db = new Database("mysql:dbname=test", "test", "test");

$db->connect();

/**
 * Assumes the following table:
 * CREATE TABLE `test`.`users` (
 * 		`user_id` INT UNSIGNED NOT NULL,
 * 		`name` VARCHAR (20),
 * 		`country` VARCHAR (200),
 * 		`last_login` INT UNSIGNED DEFAULT '0' NOT NULL,
 * 		PRIMARY KEY(`user_id`)
 * )
 */

$query = $db->select();
$query->from("users")->group("country")->column("country")->columnCount("user_id", "", "count")->columnMax("last_login", "", "latest_login")->order("latest_login", "", DatabaseSelectQuery::ORDER_DESC);

echo "<b>Query:</b><br><pre>" . $query . "</pre><br>\n";

try
{
	$statement = $query->execute();
	
	$rows = $statement->fetchAll();
	
	foreach($rows as $row)
	{
		echo $row->country . ": " . $row->count . " users (last activity: " . date("d.m.Y - H:i:s", $row->latest_login) . ")<br>";
	}
}
catch(DatabaseException $e)
{
	echo $e;
}

?>
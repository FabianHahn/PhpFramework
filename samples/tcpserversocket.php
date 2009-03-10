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

set_time_limit(30);

require "../PhpFramework.php";

use PhpFramework\PhpFramework as PF;

PF::init();

use PhpFramework\HtmlDebugLogger\HtmlDebugLogger;
use PhpFramework\Socket\Socket;
use PhpFramework\TcpSocket\TcpServerSocket;

HtmlDebugLogger::enable(PF::LOG_ALL);

function server_accept($socket, $child)
{
	$child->getReadEvent()->addListener("child_read");
	$child->writeLine("Hello! Write me a line and I'll relay it to the browser page.");
	echo "New incoming connection from " . $child->getPeerName() . "<br>";
}

function child_read($socket, $line)
{
	echo "Child connection " . $socket->getSocketId() . " wrote: " . htmlentities($line, ENT_QUOTES) . "<br>";
}

$socket = new TcpServerSocket("127.0.0.1", "4242");

$socket->getAcceptEvent()->addListener("server_accept");

if($socket->connect())
{
	echo "Server socket opened! I will now listen on port 4242 and relay everything to this page for the next 20 seconds.<br>";
	
	$start = time();
	
	while(time() - $start <= 20)
	{
		Socket::poll(100000);
		flush();
	}
	
	$socket->destroy();
}
else
{
	echo "Error: Could not open server socket!";
}

?>
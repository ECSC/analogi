<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

define ('DB_USER_O', 'ossecuser');
define ('DB_PASSWORD_O', 'abc123!!');
define ('DB_HOST_O', '127.0.0.1');
define ('DB_NAME_O', 'ossec');

$db_ossec = mysql_connect (DB_HOST_O, DB_USER_O, DB_PASSWORD_O) OR die ('Could not connect to SQL : ' . mysql_error() );
mysql_select_db (DB_NAME_O, $db_ossec) OR die ('Could not select the database : ' . mysql_error() );

?>

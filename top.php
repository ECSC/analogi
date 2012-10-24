<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */


$starttime = microtime();
$startarray = explode(" ", $starttime);
$starttime = $startarray[1] + $startarray[0];

include './config.php';
include './functions.php';
include './colours.php';

if (isset($_COOKIE['ossecdbjs']) && $glb_ossecdb[$_COOKIE['ossecdbjs']]<>'' && file_exists('./'.$glb_ossecdb[$_COOKIE['ossecdbjs']])){
	# If a database cookie is set AND it exists in $glb_ossecdb[] AND the database file exists 
	$useossecdb='./'.$glb_ossecdb[$_COOKIE['ossecdbjs']];
}else{
	# Otherwise just use the main one
	$useossecdb='./'.$glb_ossecdb['Main'];
}

include $useossecdb;


## This does work but I have commented as not everyone will have icinga installed.
## If you want to play uncomment this, and the link in index.php
## This part of the code currently only looks for hosts that came back up.
#include './db_icinga.php';

?>

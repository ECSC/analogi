<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

# see if database is populated correctly, if not then JS alert to user.


$query="SELECT count(id) as res_count
	FROM alert";
if($result=mysql_query($query, $db_ossec)){
	$row = @mysql_fetch_assoc($result);
	if(!$row['res_count']>0){
		echo "
		alert(\"Connected to database ok, but no alerts found. Ensure OSSEC is logging to your database.\");";
	}
}else{
		echo "
		alert(\"Problems checking database for information\");";
}


$query="SELECT count(id) as res_count
	FROM data";
if($result=mysql_query($query, $db_ossec)){
	$row = @mysql_fetch_assoc($result);
	if(!$row['res_count']>0){
		echo "
		alert(\"Connected to database ok, but no data found. Ensure OSSEC is logging to your database.\");";
	}
}else{
		echo "
		alert(\"Problems checking database for information\");";
}


$query="SELECT count(id) as res_count
	FROM location";
if($result=mysql_query($query, $db_ossec)){
	$row = @mysql_fetch_assoc($result);
	if(!$row['res_count']>0){
		echo "
		alert(\"Connected to database ok, but no locations found. Ensure OSSEC is logging to your database.\");";
	}
}else{
		echo "
		alert(\"Problems checking database for information\");";
}

?>

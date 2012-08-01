<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

# This code is taken directly from detail.php

require './top.php';

$where="";

# input<var> = the raw GET
# filter<var> = for repopulating the filter toolbar
# where = the cumulative sql command

## filter criteria 'level'
if(isset($_GET['level']) && is_numeric($_GET['level']) && $_GET['level']>0){
	$inputlevel=$_GET['level'];
	$where.="AND signature.level>=".$inputlevel." ";
}else{
	$inputlevel="";
	$where.="";
}


## filter from
if(isset($_GET['from']) && preg_match("/^[0-9\ ]+$/", $_GET['from'])){
	$inputfrom=$_GET['from'];
	$filterfrom=$inputfrom;
	$f=split(" ",$inputfrom);
	$sqlfrom=mktime(substr($f[0], 0, 2), substr($f[0], 2, 4), 0,substr($f[1], 2, 2),substr($f[1], 0, 2),substr($f[1], 4, 2));
	$where.="AND alert.timestamp>=".$sqlfrom." ";
}else{
	$inputfrom="";
	$filterfrom=$inputfrom;
	$where.="";
} 

## filter to
if(isset($_GET['to']) && preg_match("/^[0-9\ ]+$/", $_GET['to'])){
	$inputto=$_GET['to'];
	$filterto=$inputto;
	$t=split(" ",$inputto);
	$sqlto=mktime(substr($t[0], 0, 2), substr($t[0], 2, 4), 0,substr($t[1], 2, 2),substr($t[1], 0, 2),substr($t[1], 4, 2));
	$where.="AND alert.timestamp<=".$sqlto." ";
}else{
	$inputto="";
	$filterto=$inputto;
	$where.="";
} 

## filter criteria 'source'
if(isset($_GET['source']) && strlen($_GET['source'])>0){
	$inputsource=$_GET['source'];
	$where.="AND location.name like '%".$inputsource."%' ";
}else{
	$inputsource="";
	$where.="";
}

## filter criteria 'path'
if(isset($_GET['path']) && strlen($_GET['path'])>0){
	$inputpath=$_GET['path'];
	$where.="AND location.name like '%".$inputpath."%' ";
}else{
	$inputpath="";
	$where.="";
}


## filter rule_id
if(isset($_GET['rule_id']) && strlen($_GET['rule_id'])>0){
	$inputrule_id=$_GET['rule_id'];
	$filterule_id=$inputrule_id;
		
	$inputrule_id_array=preg_split('/,/', $inputrule_id);

	$where.="AND (1=0 ";
	foreach ($inputrule_id_array as $value){
		$where.="OR alert.rule_id=".$value." ";
	}
	$where.=")";

}else{
	$inputrule_id="";
	$filterule_id=$inputrule_id;
	$where.="";
	$noterule_id="";
}	



### filter input 'datamatch'
# Current opinion is that this does not have to be 'safe' as we trust users who can access this
if(isset($_GET['datamatch']) && strlen($_GET['datamatch'])>0){
	$inputdatamatch=$_GET['datamatch'];
	$filterdatamatch=$inputdatamatch;
	$where.="AND data.full_log like '%".quote_smart($inputdatamatch)."%' ";
}else{
	$inputdatamatch="";
	$filterdatamatch=$inputdatamatch;
}


### filter input 'rulematch'
# Current opinion is that this does not have to be 'safe' as we trust users who can access this
if(isset($_GET['rulematch']) && strlen($_GET['rulematch'])>0){
	$inputrulematch=$_GET['rulematch'];
	$filterrulematch=$inputrulematch;
	$where.="AND signature.description like '%".quote_smart($inputrulematch)."%' ";
}else{
	$inputrulematch="";
	$filterrulematch=$inputrulematch;

}


$querytable="SELECT alert.id as id, alert.rule_id as rule, signature.level as lvl, alert.timestamp as timestamp, location.name as loc, data.full_log as data
	FROM alert, location, signature, data
	WHERE 1=1
	and alert.location_id=location.id
	and alert.rule_id=signature.rule_id
	and alert.id=data.id
	".$where."
	ORDER BY alert.timestamp DESC";
$resulttable=mysql_query($querytable, $db_ossec);


header("Content-type: text/csv");  
header("Cache-Control: no-store, no-cache");  
header('Content-Disposition: attachment; filename="AnaLogI_output.csv"');  

echo "DatabaseID	";
echo "Rule	";
echo "Level	";
echo "Timestamp	";
echo "Location	";
echo "Data	";
echo "\n";



while($rowtable = @mysql_fetch_assoc($resulttable)){

	echo htmlspecialchars($rowtable['id'])."	";
	echo htmlspecialchars($rowtable['rule'])."	";
	echo htmlspecialchars($rowtable['lvl'])."	";
	echo date($glb_detailtimestamp, $rowtable['timestamp'])."	";
	echo $rowtable['loc']."	";
	echo $rowtable['data'];
	echo "\n";
}



















?>

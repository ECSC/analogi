<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$mainstring="";
$keyprepend="";
$notrepresented=array();

# counting in hours/days may get slow on larger databases, so grouping is done in blocks of 10^x seconds
if($inputhours<4){
	$substrsize=8;
	$zeros="00";	
}elseif($inputhours<48){
	$substrsize=7;
	$zeros="000";	
}else{
	$substrsize=6;
	$zeros="0000";	
}


# The graph data 'series' can be broken down in several ways
# graphheightmultiplier is just a tweak as some fields are generally longer than others.
if((isset($_GET['field']) && $_GET['field']=='path') || (!isset($_GET['field']) && $glb_graphbreakdown=="path") ){

	$graphheightmultiplier=5;
	$keyprepend="";
	$querychart="select concat(substring(alert.timestamp, 1, $substrsize), '$zeros') as res_time, count(alert.id) as res_cnt, SUBSTRING_INDEX(location.name, '->', -1) as res_field
		from alert, location, signature, signature_category_mapping, category
		where signature.level>=$inputlevel
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.rule_id=signature_category_mapping.rule_id
		and signature_category_mapping.cat_id=category.cat_id
		and alert.timestamp>".(time()-($inputhours*3600))."
		".$wherecategory." 
		".$glb_notrepresentedwhitelist_sql."
		group by substring(alert.timestamp, 1, $substrsize), SUBSTRING_INDEX(location.name, '->', -1)
		order by substring(alert.timestamp, 1, $substrsize), SUBSTRING_INDEX(location.name, '->', -1)";

}elseif((isset($_GET['field']) && $_GET['field']=='level') || (!isset($_GET['field']) && $glb_graphbreakdown=="level") ){
	$graphheightmultiplier=2;
	$keyprepend="Lvl: ";
	$querychart="select concat(substring(alert.timestamp, 1, $substrsize), '$zeros') as res_time, count(alert.id) as res_cnt, signature.level as res_field
		from alert, location, signature, signature_category_mapping, category
		where signature.level>=$inputlevel
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.rule_id=signature_category_mapping.rule_id
		and signature_category_mapping.cat_id=category.cat_id
		and alert.timestamp>".(time()-($inputhours*3600))."
		".$wherecategory." 
		".$glb_notrepresentedwhitelist_sql."
		group by substring(alert.timestamp, 1, $substrsize), signature.level
		order by substring(alert.timestamp, 1, $substrsize), signature.level";

}elseif((isset($_GET['field']) && $_GET['field']=='rule_id') || (!isset($_GET['field']) && $glb_graphbreakdown=="rule_id")){
	$graphheightmultiplier=9;
	$keyprepend="";
	$querychart="select concat(substring(alert.timestamp, 1, $substrsize), '$zeros') as res_time, count(alert.id) as res_cnt, CONCAT(alert.rule_id, ' ', signature.description) as res_field
		from alert, location, signature, signature_category_mapping, category
		where signature.level>=$inputlevel
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.rule_id=signature_category_mapping.rule_id
		and signature_category_mapping.cat_id=category.cat_id
		and alert.timestamp>".(time()-($inputhours*3600))."
		".$wherecategory." 
		".$glb_notrepresentedwhitelist_sql."
		group by substring(alert.timestamp, 1, $substrsize), alert.rule_id
		order by substring(alert.timestamp, 1, $substrsize), alert.rule_id";

}else{
	# Default is source

	$graphheightmultiplier=1;
	$keyprepend="";
	$querychart="select concat(substring(alert.timestamp, 1, $substrsize), '$zeros') as res_time, count(alert.id) as res_cnt, SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_field
		from alert, location, signature, signature_category_mapping, category
		where signature.level>=$inputlevel
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.rule_id=signature_category_mapping.rule_id
		and signature_category_mapping.cat_id=category.cat_id
		and alert.timestamp>".(time()-($inputhours*3600))."
		".$wherecategory." 
		".$glb_notrepresentedwhitelist_sql."
		group by substring(alert.timestamp, 1, $substrsize), SUBSTRING_INDEX(location.name, ' ', 1)
		order by substring(alert.timestamp, 1, $substrsize), SUBSTRING_INDEX(location.name, ' ', 1)";
}

$resultchart=mysql_query($querychart, $db_ossec);

$tmpdate="";
$timegrouping=array();
$arraylocations=array();
$arraylocationsunique=array();

echo "var chartData = [
	";

$first=0;

## Informal note, I hate this section of code, it will be rewritten.

while($rowchart = @mysql_fetch_assoc($resultchart)){

	# We have data, so empty the var on this load
	$glb_nodatastring="";

	# XXX Compile a list of all hosts, maybe a better way to do this than have an array the size of the alert table
	$fieldname=substr(preg_replace($glb_hostnamereplace,"",$rowchart['res_field']),0,35);
	if(strlen($fieldname)==35){
		$fieldname.="...";
	}

	array_push($arraylocations, $fieldname);


	# for the first run, this needs setting
	if($first==0){
		$first=1;
		$tmpdate=intval($rowchart['res_time']);
	}

	# This alert is a new time 'group'...
	if($tmpdate!=$rowchart['res_time'] && $rowchart['res_time']>1){
		# ...so what we have compiled needs to go to 'mainstring' (remember to use tmpdate, not the latest row time)
	        $mainstring.= "		{date: new Date(".date("Y", $tmpdate).", ".(date("m", $tmpdate)-1).", ".date("j", $tmpdate).", ".date("G", $tmpdate).", ".date("i", $tmpdate)."), ";
		
		foreach($timegrouping as $key=>$val){
			#append this location to array
			$mainstring.="'".$key."': ".$val.", ";
		}

		$mainstring=substr($mainstring, 0, -2);
	        $mainstring.= "},
	";

		# clear the array we have used to collect counts for a specific time 'group'
		unset($timegrouping);
		
		# reset the working time 'group' so the next if will be fired and we start collecting for the next time 'group'
		$tmpdate=$rowchart['res_time'];
	}
	
	# Oh look, this alert matches the time 'group' we are collecting for.
	if($rowchart['res_time']==$tmpdate){
		$timegrouping[$fieldname]=$rowchart['res_cnt'];

	}
}


# We have to run this cycle one more time to process the last row

if($tmpdate>1){
	$mainstring.= "		{date: new Date(".date("Y", $tmpdate).", ".(date("m", $tmpdate)-1).", ".date("j", $tmpdate).", ".date("G", $tmpdate).", ".date("i", $tmpdate)."), ";

	foreach($timegrouping as $key=>$val){
	
		#append this location to array
		$mainstring.="'".$key."': ".$val.", ";
	}
	$mainstring=substr($mainstring, 0, -2);
	$mainstring.= "},
	";

}


# dump what we have collected
$mainstring=substr($mainstring, 0, -3);
$mainstring.="
		];";



# See if there was data, if not then drop some test output to the main chartdiv, just for happiness
# mysql module isntalled?
# mysql connectable?
# database look like it has right schema?
# any data in there?
if(!preg_match("/date/", $mainstring)){

	$nodatastring="";
	$problem=0;

	if(function_exists('mysql_connect')){
		$sqlmodule="yes";
	}else{
		$problem=1;
		$sqlmodule="no!<br/>";
		$sqlmodule.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fix - https://www.google.co.uk/#q=php+mysql_connect";
	}

	if(mysql_connect (DB_HOST_O, DB_USER_O, DB_PASSWORD_O)){
		$mysqlconnect="yes";
	}else{
		$problem=1;
		$mysqlconnect="no!<br/>";
		$mysqlconnect.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fix - ";
	}

	if(mysql_query("SELECT 1 from agent", $db_ossec)
	&& mysql_query("SELECT 1 from alert", $db_ossec)
	&& mysql_query("SELECT 1 from category", $db_ossec)
	&& mysql_query("SELECT 1 from data", $db_ossec)
	&& mysql_query("SELECT 1 from location", $db_ossec)
	&& mysql_query("SELECT 1 from server", $db_ossec)
	&& mysql_query("SELECT 1 from signature", $db_ossec)
	&& mysql_query("SELECT 1 from signature_category_mapping", $db_ossec)){
		$databaseschema="yes";
	}else{
		$problem=1;
		$databaseschema="no!<br/>";
		$databaseschema.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fix - Import the MySQL schema that comes with OSSEC";
	}

	if(checktable('alert') && checktable('data') && checktable('location') && checktable('signature')){
		$anydata="yes";
	}else{
		$problem=1;
		$anydata="no!<br/>";
		$anydata.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fix - Ensure agents are logging data.";
	}

	$nodatastring="
	<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>No Data</div>
	<div style='padding-bottom:10px;'>There is no data available for this query, running diagnostics...</div>
	<div>Test 1 - Can PHP detect MySQL module? - ".$sqlmodule."</div>
	<div>Test 2 - Can PHP connect to your MySQL? - ".$mysqlconnect."</div>
	<div>Test 3 - Does your database have correct looking schema? - ".$databaseschema."</div>
	<div>Test 4 - Is there any data in your database? - ".$anydata."</div>";

	if($problem==0){
		$nodatastring.="<div>No problem found, likely your query is too specific.</div>";
	}

}


function checktable($table){
	$query="SELECT max(id) as cnt from ".$table.";";
	$result=mysql_query($query);
	$row = @mysql_fetch_assoc($result);
	if($row['cnt']>0){
		return 1;
	}else{
		return 0;
	}	
}

echo htmlspecialchars($mainstring);	

$arraylocationsunique = array_unique($arraylocations);
asort($arraylocationsunique);


## Right now define each series of data with a name and settings
$graphcount=0;
foreach ($arraylocationsunique as $i => $location){
	$graphcount++;
	$graphlines.='
		// GRAPHS
		// Graph '.$i.'
		var graph'.$i.' = new AmCharts.AmGraph();
		graph'.$i.'.title = "'.$keyprepend.$location.'";
		graph'.$i.'.valueField = "'.$location.'";
		graph'.$i.'.bullet = "round";
		graph'.$i.'.hideBulletsCount = 30;
		graph'.$i.'.balloonText = "'.$keyprepend.$location.' : [[value]]";
		chart.addGraph(graph'.$i.');
';
		$notrepresented[$location]=1;
}


# As I cannot see a way for amcharts to be in a dynamic height graph.... lets use PHP to adjust it on page load...
$graphheight="	document.getElementById('chartdiv').style.height='".(500+($graphcount*$graphheightmultiplier))."px';";

## Lets colour out of hours in a nice shade of 'glb_outofhourscolour'!
$workinghoursguide="";
$daysago=ceil($inputhours/24);
for($i=$daysago; $i>=0 ; $i--){
	$guidedate=date("j", strtotime('-'.$i.' days'));	
	$guidemonth=date("n", strtotime('-'.$i.' days'))-1;	


	if(date('N', strtotime('-'.$i.' days')) == 6 || date('N', strtotime('-'.$i.' days')) == 7){
		# If in here, then the day value (1-7) of $i days ago was a Sat or a Sun			
		$workinghoursguide.= "
		// GUIDE - Weekend
		var guide".$i." = new AmCharts.Guide();
		guide".$i.".date = new Date(2012, ".$guidemonth.", ".$guidedate.", 0, 0);
		guide".$i.".toDate = new Date(2012, ".$guidemonth.", ".$guidedate.", 23, 59);
		guide".$i.".fillColor = '".$glb_outofhourscolour."';
		guide".$i.".inside = false;
		guide".$i.".fillAlpha = 0.2;
		guide".$i.".lineAlpha = 0;
		guide".$i.".label = 'Weekend';
		guide".$i.".labelRotation = 90;
		categoryAxis.addGuide(guide".$i.");
		";

	}else{
		# If in here then the day value indicates this is a weekday
		$workinghoursguide.= "
		// GUIDE - Non working hours
		// day value = ".date('N', strtotime('-'.$i.' days'))." am
		var guide".$i."am = new AmCharts.Guide();
		guide".$i."am.date = new Date(2012, ".$guidemonth.", ".$guidedate.", 0, 1);
		guide".$i."am.toDate = new Date(2012, ".$guidemonth.", ".$guidedate.", ".$glb_outofhours_daystart.", 0);
		guide".$i."am.fillColor = '".$glb_outofhourscolour."';
		guide".$i."am.inside = false;
		guide".$i."am.fillAlpha = 0.2;
		guide".$i."am.lineAlpha = 0;
		categoryAxis.addGuide(guide".$i."am);
		// day value = ".date('N', strtotime('-'.$i.' days'))." pm
		var guide".$i."pm = new AmCharts.Guide(); 
		guide".$i."pm.date = new Date(2012, ".$guidemonth.", ".$guidedate.", ".$glb_outofhours_dayend.", 0);
		guide".$i."pm.toDate = new Date(2012, ".$guidemonth.", ".$guidedate.", 23, 59);
		guide".$i."pm.fillColor = '".$glb_outofhourscolour."';
		guide".$i."pm.inside = false;
		guide".$i."pm.fillAlpha = 0.2;
		guide".$i."pm.lineAlpha = 0;
		guide".$i."pm.label = 'Nighttime';
		guide".$i."pm.labelRotation = 90;
		categoryAxis.addGuide(guide".$i."pm);
		";
			
	}
}

?>

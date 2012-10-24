<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$hostsubstrdebugstring="";

$query="select                
		count(alert.id) as res_cnt,
		concat(substring(alert.timestamp, 1, 5), '00000') as res_time,
		SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_name
	from alert, location
	where alert.location_id=location.id
	and alert.timestamp>".(time()-($glb_mass_days * 24 * 3600))."
	group by res_time, res_name;";

if($glb_debug==1){
	echo "var chartData3 = []";
	$hostsubstrdebugstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	$hostsubstrdebugstring.=$query;
}else{


	if(!$result=mysql_query($query, $db_ossec)){
		echo "SQL Error:".$query;
	}
	
	
	$hostsubstrarray=array();
	while($row = @mysql_fetch_assoc($result)){
		# Loop results
		foreach($glb_mass_hostsubstr as $hostsubstr){
			#Loop substrings for hosts
			if(preg_match("/".$hostsubstr."/", $row['res_name'])){
				#Yes, sub string found in MySQL result, though amcharts wont handle the -, so safe the string
				$hostsubstr=ereg_replace("[^A-Za-z0-9]", "", $hostsubstr);
				$hostsubstrarray[$row['res_time']][$hostsubstr]+=$row['res_cnt'];		
		
			}
		}
	}
	
	echo "
	
		var chartData3 = [";
	$mainstring3="";
	$substrcount=0;
	$substrs=array();
	$substrs2=array();
	
	$i=0;
	foreach($hostsubstrarray as $key=>$val){
	
		$substrcount++;	
	
		if($i==1){
			# Not first time, add a ,
			$mainstring3.=",";
		}else{
	
			$i=1;
		}
	
		# $key = time
		$mainstring3.="
			 {date: new Date(".date("Y", $key).", ".(date("m", $key)-1).", ".date("j", $key).", ".date("G", $key).", ".(date("i", $key))."),"; 
		foreach($val as $k=>$v){
			# $k = substr
			# $v = count
			array_push($substrs,$k);
	
			$mainstring3.=" ".$k.":".$v.",";	
	
		}
		$mainstring3=eregi_replace(',$', '', $mainstring3); 
		$mainstring3.="}";
	}
	$mainstring3=eregi_replace(',$', '', $mainstring3); 
	$mainstring3.="
		];";
	
	echo $mainstring3;
	
	#################################
	$substrs2=array_unique($substrs);
	asort($substrs2);

	$graphsubstr="";
	
	foreach($substrs2 as $substr){
	$safe_substr=ereg_replace("[^A-Za-z0-9]", "", $substr);
		$graphsubstr.= "
			// Graph ".$substr."
	                // GRAPH
	                var graph$safe_substr = new AmCharts.AmGraph();
	                graph$safe_substr.type = \"smoothedLine\";
	                graph$safe_substr.title = \"$substr\";
	                graph$safe_substr.valueField = \"$safe_substr\";
	                graph$safe_substr.lineThickness = 2;
	                chart3.addGraph(graph$safe_substr);
		";
	
	}	

}

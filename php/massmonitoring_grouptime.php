<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$where_ignore="";
$grouptimedebugstring="";

foreach($glb_mass_groupignore as $ignore){
	$where_ignore.="AND category.cat_name NOT LIKE \"".$ignore."\" ";
}

$query="select 
		count(alert.id) as res_cnt,
		concat(substring(alert.timestamp, 1, 5), '00000') as res_time,
		category.cat_name as res_name
	from alert, signature_category_mapping, category
	where alert.timestamp>".(substr(time()-($glb_mass_days * 24 * 3600),0,5)."00000")."
	and alert.rule_id=signature_category_mapping.rule_id
	and signature_category_mapping.cat_id=category.cat_id
	".$where_ignore."
	group by res_time, res_name;";

if($glb_debug==1){
	echo "var chartData = []";
	$grouptimedebugstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	$grouptimedebugstring.=$query;
	
}else{

	if(!$result=mysql_query($query, $db_ossec)){
		echo "SQL Error:".$query;
	}

	while($row = @mysql_fetch_assoc($result)){
		$sourcelevel[$row['res_time']][$row['res_name']] = $row['res_cnt'];
	}
	
	echo "var chartData = [";
	$whilelocation="";
	$mainstring="";
	
	$i=0;
	$graphcount=0;
	$graphstring="";
	$groups=array();
	$groups2=array();
	$groups3=array();
	
	foreach($sourcelevel as $key=>$val){
	
		# $key = time
	
		$graphcount++;	
	
		if($i==1){
			# Not first time, add a ,
			$mainstring.=",";
		}else{
	
			$i=1;
		}
	
		$mainstring.="
			 {date: new Date(".date("Y", $key).", ".(date("m", $key)-1).", ".date("j", $key).", ".date("G", $key).", ".(date("i", $key))."),"; 
	
		foreach($val as $k=>$v){
			# $k = group
			# $v = count
			array_push($groups,$k);
	
			$mainstring.=" ".$k.":".$v.",";	
	
		}
		$mainstring=eregi_replace(',$', '', $mainstring); 
		$mainstring.="}";
	}
	$mainstring=eregi_replace(',$', '', $mainstring); 
	$mainstring.="
		];";
	
	echo $mainstring;
	
	#################################
	$groups2=array_unique($groups);
	asort($groups2);
	
	foreach($groups2 as $group){
		$graphstring.= "
			// Graph ".$group."
	                // GRAPH
        	        var graph$group = new AmCharts.AmGraph();
	                graph$group.type = \"smoothedLine\";
	                graph$group.title = \"$group\";
	                graph$group.valueField = \"$group\";
	                graph$group.lineThickness = 2;
			graph$group.lineColor = \"".$randomcolour[array_rand($randomcolour)]."\";
	                chart.addGraph(graph$group);
		";
	}

}
?>

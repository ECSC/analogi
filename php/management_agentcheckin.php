<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$query="SELECT MAX(alert.timestamp) as res_time, SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_name
	FROM alert, location
	WHERE alert.location_id=location.id
	GROUP by res_name
	ORDER BY res_time;";

$mainstring="";
if($glb_debug==1){
	$mainstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	$mainstring.=$query;
}else{
	if(!$result=mysql_query($query, $db_ossec)){
		echo "SQL Error:".$query;
	}
	$mainstring="
		<div style='max-height:500px;overflow:auto;'>
		<table>
			<tr>
			<th>Agent</th>
			<th>Last Alert</th>
			<th></th>
			</tr>";
	
	while($row = @mysql_fetch_assoc($result)){
		$hoursago=(time()-$row['res_time'])/3600;
	
		if($hoursago>$glb_management_checkin){
			$mainstring.= "<tr>
					<td  style=\"padding:8px\"><a href='./detail.php?source=".$row['res_name']."&from=0000 ".date("dmy", ($row['res_time'])-(7*24*3600))."'>".$row['res_name']."</a></td>
					<td  style=\"padding:8px\">".date("l jS F Y ga", $row['res_time'])."</td>
					<td  style=\"padding:8px\">".floor((time()-$row['res_time'])/86400)." days</td>
					</tr>";
		}
	}

	$mainstring.="</table>
			</div>";

}
echo $mainstring;

?>

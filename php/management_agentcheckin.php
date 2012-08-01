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


if(!$result=mysql_query($query, $db_ossec)){
	echo "SQL Error:".$query;
}

$mainstring="
	<table>
		<tr>
		<th>Agent</th>
		<th>Last Alert</th>
		<th></th>
		</tr>";

while($row = @mysql_fetch_assoc($result)){
	$mainstring.= "<tr>
			<td  style=\"padding:8px\">".$row['res_name']."</td>
			<td  style=\"padding:8px\">".date("l jS F Y ga", $row['res_time'])."</td>
			<td  style=\"padding:8px\">".floor((time()-$row['res_time'])/86400)." days</td>
			</tr>";
}

$mainstring.="</table>";
echo $mainstring;

?>

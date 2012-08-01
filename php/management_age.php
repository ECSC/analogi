<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

# Generates the graph line for the data over time graph on management.php

$query="SELECT count(alert.id) as res_cnt, SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_name, location.id as res_id
        FROM alert, location, signature
        WHERE alert.location_id = location.id
        AND alert.rule_id = signature.rule_id
        GROUP BY res_name
        ORDER BY res_cnt DESC
        LIMIT 10;";


if(!$result=mysql_query($query, $db_ossec)){
	echo "SQL Error:".$query;
}

echo "var chartData = [{";

$mainstring="";
while($row = @mysql_fetch_assoc($result)){
	$mainstring.="
		location: \"".preg_replace($glb_hostnamereplace,"",$row['res_name'])."\", value: ".$row['res_cnt']."
	}, {
	";
}
$mainstring=substr($mainstring, 0, -4);
$mainstring.="
		];";
echo $mainstring;	


?>

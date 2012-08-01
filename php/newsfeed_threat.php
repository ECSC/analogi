<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
	
	$query="SELECT alert.timestamp, alert.id, substring_index(substring_index(name, ' ', 1), '->', 1) as source, substring_index(name,'->',-1) as path, alert.rule_id, signature.level, signature.description, (alert.timestamp + (".$glb_threatbooster."*86400*signature.level)) as t1, data.full_log as data
		FROM alert, location, signature, data
		WHERE signature.level>4
		AND location.id = alert.location_id 
		AND signature.rule_id = alert.rule_id 
		AND alert.id=data.id
		AND alert.timestamp>".(time()-(86400*$glb_threatdays))." 
		ORDER BY t1 DESC 
		LIMIT ".$glb_threatlimit.";";

	$result=mysql_query($query, $db_ossec);
	
	
	$threatcount=0;


	echo "
	<table style='width:100%;'>
	<tr>
		<th class='big'>Date</th>
		<th class='big'>Location</th>
		<th class='big'>Rule</th>
		<th class='big'>Level</th>
		<th class='big'>Data</th>
	</tr>
	";


	while($row = @mysql_fetch_assoc($result)){
		$threatcount=1;
		
		echo "<tr>
			<td>".date("D M j G:i:s", $row['timestamp'])."</td>
			<td>".$row['source']."</td>
			<td>".substr($row['description'], 0, 25)."...</td>
			<td>".$row['level']."</td>
			<td><a class='tooltip_small 'href='detail.php?rule_id=".$row['rule_id']."&from=".date("Gi dmy", time()-(86400*30))."&source=".$row['source']."'>Link<span style='left:50px; width:450px'>".$row['data']."</span></a></td>
			</tr>";
	}
	if($threatcount==0){
		echo $glb_nodatastring;
	}	
	echo "</table>";
?>

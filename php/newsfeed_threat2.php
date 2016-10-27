<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
	
	$query="SELECT alert.timestamp, alert.id, substring_index(substring_index(name, ' ', 1), '->', 1) as source, substring_index(name,'->',-1) as path, alert.rule_id, signature.level, signature.description, (alert.timestamp + (".$glb_threatbooster."*86400*signature.level)) as t1, data.full_log as data
		FROM alert
		LEFT JOIN location ON location.id = alert.location_id 
		LEFT JOIN signature ON signature.rule_id = alert.rule_id 
		LEFT JOIN data ON alert.id=data.id
		WHERE signature.level>4
		AND alert.timestamp>".(time()-(86400*$glb_threatdays))." 
		ORDER BY t1 DESC 
		LIMIT ".$glb_threatlimit.";";

	$result=$mysqli->query($query);
	
	
	$threatcount=0;


	echo "
	<table>
		<tr>
			<th style='padding:3px'>Date</th>
			<th style='padding:3px'>Server</th>
			<th style='padding:3px'>Error</th>
			<th style='padding:3px'>Info</th>
		</tr>
	";


	while($row = $result->fetch_assoc()){
		$threatcount=1;
		
		echo "<tr>
			<td>".date("D M j G:i:s", $row['timestamp'])."</td>
			<td>".$row['source']."</td>
			<td>".substr($row['description'], 0, 35)."...</td>
			<td>".$row['level']."</td>
			<td><a class='tooltip_small' href='detail.php?rule_id=".$row['rule_id']."&from=".date("Gi dmy", time()-(86400*30))."&source=".$row['source']."'>Link<span style='left:50px; width:700px;'>".$row['data']."</span></a></td>
			</tr>";
	}
	echo "</table>";

	if($threatcount==0){
		echo $glb_nodatastring;
	}	
?>

<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */


echo"
		// GUIDES for icinga checks";


$query="SELECT Unix_Timestamp(start_time) as itime, output, name1 
FROM icinga_notifications, icinga_objects
WHERE long_output like '%uptime%' 
AND icinga_notifications.object_id=icinga_objects.object_id
AND long_output like '%0 hours%' 
AND Unix_Timestamp(start_time)>".(time()-($inputhours*3600)).";";

$i=0;

$result=mysql_query($query, $db_icinga);
while($row = @mysql_fetch_assoc($result)){

	        $itime= "new Date(".date("Y", $row['itime']).", ".(date("m", $row['itime'])-1).", ".date("d", $row['itime']).", ".date("H", $row['itime']).", ".date("i", $row['itime']).")";

	echo '

			var guide'.$i.' = new AmCharts.Guide();
			guide'.$i.'.date = '.$itime.';
			guide'.$i.'.lineColor = "#CC0000";
			guide'.$i.'.lineAlpha = 1;
			guide'.$i.'.dashLength = 2;
			guide'.$i.'.inside = true;
			guide'.$i.'.labelRotation = 90;
			guide'.$i.'.label = "'.$row['name1'].' : '.$row['output'].'";
			categoryAxis.addGuide(guide'.$i.');';
	$i++;
}

?>

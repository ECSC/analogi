<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */



$query="SELECT count(alert.id) as res_cnt, alert.rule_id as res_id, signature.description as res_desc, signature.rule_id as res_rule
	FROM alert 
	LEFT JOIN signature on alert.rule_id=signature.rule_id 
	LEFT JOIN location on alert.location_id=location.id
	WHERE alert.timestamp>'".(time()-($inputhours*60*60))."' 
	AND signature.level>=".$inputlevel."
	".$glb_notrepresentedwhitelist_sql." 
	GROUP BY res_id, res_desc, res_rule  
	ORDER BY count(alert.id) DESC
	LIMIT ".$glb_indexsubtablelimit; 

echo "<div class='top10header'>
	<a href='#' class='tooltip'><img src='./images/help.png' /><span>Busiest rules in given time period.</span></a>
	Top Rule_ID, ".$inputhours." Hrs (lvl ".($inputlevel)."+)</div>";

if(!$result=mysql_query($query, $db_ossec)){
	echo "SQL Error:".$query;
}

$mainstring="";

# Keep this in the same format that detail.php already uses
$from=date("Hi dmy", (time()-($inputhours*3600)));

while($row = @mysql_fetch_assoc($result)){
	$mainstring.="<div class='fleft top10data' style='width:60px'>".$row['res_cnt']."</div>
			<div class='fleft top10data'><a class='top10data' href='./detail.php?rule_id=".$row['res_rule']."&from=".$from."&breakdown=source'>".htmlspecialchars(substr($row['res_desc'], 0, 28))."...</a></div>
			<div class='clr'></div>";
}
$mainstring.="";

echo $mainstring;

?>

<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

if($glb_debug==1){
	$starttime_toprarechart = microtime();
	$startarray_toprarechart = explode(" ", $starttime_toprarechart);
	$starttime_toprarechart = $startarray_toprarechart[1] + $startarray_toprarechart[0];
}


# This will not be pretty.  A SQL command was made that worked, but due to indexing design flaws with the OSSEC MYSQL schema the command took 10 minutes to run on a relatively new/empty database.
# A better version of this interface is planned that will redesign the databse and made this nicer.

echo "<div class='top10header'>
	<a href='#' class='tooltip'><img src='./images/help.png' /><span>Alerts in this period, and the last time they were seen (oldest and rarest at the top)</span></a>
	Rare in <span class='tw'>".$inputhours."</span> Hrs, last seen (Lvl <span class='tw'>".$inputlevel."</span>+)</div>";

$query="select distinct(alert.rule_id)
	from alert, signature, signature_category_mapping, category
	where alert.timestamp>".(time()-($inputhours*3600))."
	and alert.rule_id=signature.rule_id
	and alert.rule_id=signature_category_mapping.rule_id
	and signature_category_mapping.cat_id=category.cat_id
	and signature.level>".$inputlevel."
	".$wherecategory."";


if(!$result=mysql_query($query, $db_ossec)){
	echo "SQL Error:".$query;
}	

$lastrare =  array();

while($row = @mysql_fetch_assoc($result)){

	$ruleid=$row['rule_id'];

	$querylast="select max(alert.timestamp) as time, signature.description as descr
		from alert, signature
		where alert.rule_id=".$ruleid."
		and alert.rule_id=signature.rule_id
		and alert.timestamp<".(time()-($inputhours*3600));
	$resultlast=mysql_query($querylast, $db_ossec);
	$rowlast = @mysql_fetch_assoc($resultlast);
	$lastrare[$ruleid]=$rowlast['time']."||".$rowlast['descr'];
}


if($glb_debug==1){
	$mainstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>";
	$mainstring.=$query;

	$endtime_toprarechart = microtime();
	$endarray_toprarechart = explode(" ", $endtime_toprarechart);
	$endtime_toprarechart = $endarray_toprarechart[1] + $endarray_toprarechart[0];
	$totaltime_toprarechart = $endtime_toprarechart - $starttime_toprarechart;
	$mainstring.="<br>Took ".round($totaltime_toprarechart,1)." seconds";
	
}else{

	asort($lastrare);

	$i=0;
	$mainstring="";
	
	foreach ($lastrare as $key => $val) {
		if($i<$glb_indexsubtablelimit && trim($val)!="||"){
			$display=explode("||", $val);
			if($display[0]==""){
				$displaydate="New";
			}else{
				$displaydate=date("dS M H:i", $display[0]);
			}

			$mainstring.="<div class='fleft top10data' style='width:95px;'>".$displaydate."</div>
					<div class='fright top10data' style='text-align:right; width:*' ><a class='top10data' href='./detail.php?rule_id=".$key."&breakdown=source'>".htmlspecialchars(substr($display[1], 0, 40))."...</a></div>
					<div class='clr'></div>";
			$i++;
		}
	}
}

if($mainstring==""){
	echo $glb_nodatastring;
}else{
	echo $mainstring;
}

?>

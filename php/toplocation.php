<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

if($glb_debug==1){
	$starttime_toplocchart = microtime();
	$startarray_toplocchart = explode(" ", $starttime_toplocchart);
	$starttime_toplocchart = $startarray_toplocchart[1] + $startarray_toplocchart[0];
}

# To filter on 'Category' (SSHD) extra table needs adding, but they slow down the query for other things, so lets only put them into the SQL if needed....
if(strlen($wherecategory)>1){
        $wherecategory_tables=", signature_category_mapping, category";
        $wherecategory_and="and alert.rule_id=signature_category_mapping.rule_id
        and signature_category_mapping.cat_id=category.cat_id";
}else{
        $wherecategory_tables="";
        $wherecategory_and="";
}


$query="SELECT count(alert.id) as res_cnt, SUBSTRING_INDEX(SUBSTRING_INDEX(location.name, ' ', 1), '->', 1) as res_name
	FROM alert, location, signature ".$wherecategory_tables."
	WHERE alert.location_id = location.id
	AND alert.rule_id = signature.rule_id
	".$wherecategory_and."
	AND signature.level>='".$inputlevel."'
	AND alert.timestamp>'".(time()-($inputhours*60*60))."'
	".$wherecategory."
	".$glb_notrepresentedwhitelist_sql."
	GROUP BY res_name 
	ORDER BY res_cnt DESC 
	LIMIT ".$glb_indexsubtablelimit;

echo "<div class='top10header' >
	<a href='#' class='tooltip'><img src='./images/help.png' /><span>Busiest locations in given time frame.</span></a>
	Top Loc, <span class='tw'>".$inputhours."</span> Hrs (Lvl <span class='tw'>".$inputlevel."</span>+)</div>";

$mainstring="";
$detailshours="";
if(!$result=mysql_query($query, $db_ossec)){
	$mainstring.= "SQL Error: ".$query;

}elseif($glb_debug==1){
	$mainstring="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>";
	$mainstring.=$query;

	$endtime_toplocchart = microtime();
	$endarray_toplocchart = explode(" ", $endtime_toplocchart);
	$endtime_toplocchart = $endarray_toplocchart[1] + $endarray_toplocchart[0];
	$totaltime_toplocchart = $endtime_toplocchart - $starttime_toplocchart;
	$mainstring.="<br>Took ".round($totaltime_toplocchart,1)." seconds";

}else{

	$from=date("Hi dmy", (time()-($inputhours*3600)));
	if(isset($_GET['level'])){
		$detailshours="&level=".$inputlevel;
	}


	while($row = @mysql_fetch_assoc($result)){
		$mainstring.="<div class='fleft top10data' style='width:60px'>".number_format($row['res_cnt'])."</div>
				<div class='fleft top10data'><a class='top10data' href='./detail.php?source=".$row['res_name']."&level=".$inputlevel."&from=".$from.$detailshours."&breakdown=rule_id'>".htmlspecialchars(preg_replace($glb_hostnamereplace, "", $row['res_name']))."</a></div>
				<div class='clr'></div>";
	}

}

if($mainstring==""){
	echo $glb_nodatastring;
}else{
	echo $mainstring;
}

?>

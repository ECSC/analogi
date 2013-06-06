<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

$glb_trendip_count=10;
$whereignore="";


# Construct the SQL code to ignore IPs (in preformated format to save on SQL speed)
$iprange=array();
foreach($glb_trendip_ignore as $ignoreips){

	if(preg_match('/^[0-9\.]*\/[0-9\.]*$/', $ignoreips)){

		$iprange=getIpRang($ignoreips);
		$whereignore.=" and (a.src_ip < ".$iprange[0]." or  a.src_ip > ".$iprange[1].") ";

	}elseif(preg_match('/^[0-9\.]*$/', $ignoreips)){

		$whereignore.=" and (a.src_ip <> '".$ignoreips.") ";

	}

}


# This was originally just a subquery, but to keep order results I need to order by the string used for the 'WHERE IN' which is dynamic, so I need to parse the string with PHP so I can use it twice :/
$query="select res_ip from 
	 ( 
	  SELECT a.src_ip as res_ip,
	  count(a.id) as res_count
	  from alert a
	  where a.timestamp>".(time()-($glb_threatdays*24*3600))."
	  and a.src_ip <> '0'
	  and a.src_ip <> ''
	  and
	    (		
	      1=1
	      ".$whereignore."
	    )
	  group by res_ip
	  order by res_count desc
	  limit ".$glb_trendip_top."
	 ) as snuff";
if($glb_debug==1){
	echo "<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	echo $query;
}else{
	$whereinorderby="";
	$result=mysql_query($query, $db_ossec);
	while($row = @mysql_fetch_assoc($result)){
		$whereinorderby.="'".$row['res_ip']."',";
	}
	$whereinorderby=preg_replace('/,$/','',$whereinorderby);

}


# Now the final query
$query="select 
	inet_ntoa(alert.src_ip) as res_ip,
	count(alert.id) as res_cnt,
	category.cat_name as res_name,
	category.cat_id as res_id
	from alert, signature_category_mapping, category
	where alert.timestamp>".(time()-($glb_threatdays*24*3600))."
	and alert.rule_id=signature_category_mapping.rule_id
	and signature_category_mapping.cat_id=category.cat_id
	and alert.src_ip in 
		(
			".$whereinorderby."
		)
	group by res_ip, res_name
	order by field (alert.src_ip, ".$whereinorderby.");";

echo "<div class='clr' style='padding-bottom:0px'></div>";


if($glb_debug==1){
	echo "<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	echo $query;

}else{	

	echo "<table>";
	echo "<tr><th>IP</th><th>Groups (count)</th></tr>";
	
	$result=mysql_query($query, $db_ossec);
	$tmpip=array();
	while($row = @mysql_fetch_assoc($result)){
	
		$tmpip[$row['res_ip']][$row['res_id']."|".$row['res_name']]=$row['res_cnt'];
	
	}
	
	$prevdate = date("Hi dmy", (time()-($glb_threatdays * 86400)));
	
	foreach($tmpip as $key => $val){
	
		# $key = ip
	
		echo "<tr><td><a href='ip_info.php?ip=".$key."'>".$key."</td><td>";
	
		arsort($val);
	
		foreach($val as $k => $v){
			
			# $k = '1|squid'
			# $v = 123
	
			$categorybits=preg_split('/\|/', $k);
	
			echo "<a href='detail.php?from=".$prevdate."&category=".$categorybits[0]."&ipmatch=".$key."'>".$categorybits[1]."</a> (".number_format($v)."), ";
	
		}
	
		echo "</tr>";
	
	}
		
	echo "</table>";
}
?>

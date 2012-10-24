<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
require './top.php';

if(!(isset($_GET['ip']) && $_GET['ip']) || !filter_var($_GET['ip'], FILTER_VALIDATE_IP)){
	$ip=$_SERVER['REMOTE_ADDR'];
}else{
	$ip=$_GET['ip'];
}



# Get GeoIP stuff into JSON format
$url="http://freegeoip.net/json/".$ip;
$content=get_content($url);
$jsoned = json_decode($content);
$jsonlat=$jsoned->{'latitude'};
$jsonlng=$jsoned->{'longitude'};
if($jsonlat==""){
	$jsonlat="0";
}
if($jsonlng==""){
	$jsonlng="0";
}

#var_dump($jsoned);

# Get AS and CIDR
$url="http://www.dshield.org/api/ip/".$ip;
$content=get_content($url);
$xml = simplexml_load_string($content); 
$ip_isp = $xml->asname;
$ip_range = $xml->network;
$ip_attacks = $xml->attacks;


#First Instance
$query="SELECT alert.timestamp as first
	FROM alert
	WHERE alert.src_ip='".ip2long($ip)."'
	ORDER BY alert.timestamp
	LIMIT 1";
$result=mysql_query($query, $db_ossec);
$row = @mysql_fetch_assoc($result);
$firstinstance = $row['first'];
	


# Seen at
$query="SELECT distinct(substring_index(substring_index(location.name, ' ', 1), '->', 1)) as loc_name
	FROM alert
	LEFT JOIN location ON alert.location_id = location.id
	WHERE alert.id IN (select data.id from data where full_log like '%".$ip."%');";
if($glb_debug==1){
	$seenat="<div style='font-size:24px; color:red;font-family: Helvetica,Arial,sans-serif;'>Debug</div>"; 
	$seenat.=$query;
	
}else{
	$result=mysql_query($query, $db_ossec);
	$seenat="";
	while($row = @mysql_fetch_assoc($result)){
		$seenat.="<a href='detail.php?datamatch=".$ip."&source=".$row['loc_name']."&level=7'>".$row['loc_name']."</a>, ";
	}
}






?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>AnaLogi - OSSEC WUI</title>

<?php
include "page_refresh.php";
?>

<link href="./style.css" rel="stylesheet" type="text/css" />
<script src="./amcharts/amcharts.js" type="text/javascript"></script>

    <script src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
    <script>
      var map;
      function initialize() {
        var mapOptions = {
          zoom: 5,
          center: new google.maps.LatLng(<?php echo $jsonlat.", ".$jsonlng  ?>),
	disableDefaultUI: true,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map_canvas'),
            mapOptions);
      }

      google.maps.event.addDomListener(window, 'load', initialize);
    </script>



</head>
<body onload="databasetest()">

<?php include './header.php'; ?>
<div class='clr'></div>

<div>
<form method="GET" action="./ip_info.php?">
	<input type='text' name='ip' />
	<input type='submit' value='Search' />
</form>
</div>

<div class='clr gap'></div>

<div class='top10header'>IP Address - <?php echo $ip ?></div>
<div style="width:50%" class='fleft'>



	<div class='wide fleft'>Hostname</div>
	<div class='fleft'><?php echo gethostbyaddr($ip) ?></div>
	<div class='clr gap'></div>

	<div class='wide fleft'>ISP</div>
	<div class='fleft'><?php echo $ip_isp ?></div>
	<div class='clr gap'></div>

	<div class='wide fleft'>Network Range</div>
	<div class='fleft'><?php echo $ip_range ?></div>
	<div class='clr gap'></div>
	
	<div class='wide fleft'><a href='http://www.dshield.org/ipinfo.html?ip=<?php echo $ip; ?>'>dshield</a> have counted</div>
	<div class='fleft'><?php echo $ip_attacks ?> attacks from this IP</div>
	<div class='clr gap'></div>

	<div class='wide fleft'>First Ossec Alert</div>
	<div class='fleft'><?php $x = (strlen($firstinstance)>0) ? date($glb_detailtimestamp, $firstinstance): "-"; echo $x;?></div>
	<div class='clr gap'></div>

	<div class='wide fleft'>Country</div>
	<div class='fleft'><?php echo $jsoned->{'country_name'} ?></div>
	<div class='clr gap'></div>

	<div class='wide fleft'>Detail Breakdown</div>
	<div class='fleft'><a href='detail.php?datamatch=<?php echo $ip ?>&from=<?php echo date("Hi dmy",$firstinstance)?>'>View</a></div>
	<div class='clr gap'></div>

	<div class='wide fleft'>Seen At</div>
	<div class='fleft' style='width:370px; height:80px; overflow:auto;'><?php echo $seenat; ?></div>
	<div class='clr'></div>


</div>
<div style="width:50%" class='fleft'>

	<div id="map_canvas" style="width:420px ; height:250px"></div>
	<div class='tiny'>Geo Location accuracy may vary</div>
</div>




<div class='clr'></div>
<div class='gap'></div>
<div class='top10header'>Useful Links</div>

<div><a href="http://www.dnsstuff.com/tools/ptr.ch?ip=<?php echo $ip ?>">DNS Stuff PTR</a></div>
<div><a href="http://www.dnsstuff.com/tools/whois.ch?ip=<?php echo $ip ?>">DNS Stuff Whois</a></div>
<div><a href="http://www.whois.sc/<?php echo $ip ?>">Whois</a></div>
<div><a href="http://www.dshield.org/ipinfo.php?ip=<?php echo $ip ?>&Submit=Submit">DShield</a></div>
<div><a href="http://www.trustedsource.org/query.php?q=<?php echo $ip ?>">Trusted Source</a></div>
<div><a href="http://isc.sans.org/ipinfo.html?ip=<?php echo $ip ?>">SANS</a></div>
<div><a href="http://www.mcafee.com/threat-intelligence/ip/default.aspx?ip=<?php echo $ip ?>">McAfee</a></div>
<div><a href="http://www.senderbase.org/senderbase_queries/detailip?search_string=<?php echo $ip ?>">Cisco Lookup</a></div>
<div><a href="http://www.robtex.com/ip/<?php echo $ip ?>.html">Robtex</a></div>
<div><a href="http://www.mxtoolbox.com/SuperTool.aspx?action=blacklist%3a<?php echo $ip ?>">MxToolBox</a></div>
<div></div>

<div class="clr"></div>
<div style='padding:40px ;width:95%; text-align:center;'>
	<a class='tiny' href='http://www.ecsc.co.uk/analogi.html'>ECSC | Vendor Independent Information Security Specialists</a>
</div>

<div class='clr'></div>

<?php
include 'footer.php';
?>

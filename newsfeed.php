<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
require './top.php';
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>AnaLogi - OSSEC WUI</title>

<meta http-equiv="refresh" content="<?php echo $glb_autorefresh; ?>" > 
<link href="./style.css" rel="stylesheet" type="text/css" />

</head>
<body>

<?php include './header.php'; ?>
		
<div class='clr'></div>	

<div style="width:50%" class='fleft'>	
	<div class='top10header'>Alert Threat Feed</div>
	<div class="introbody" style='padding-bottom:10px;'>List of alerts over the last <?php echo $glb_threatdays; ?> days, in chronological order.</div>
	<div class="introbody">Alerts are 'boosted up' depending on the Alert Level.</div>
	<div class="introbody">Alerts will recede over time.</div>
	<div style="padding-top:8px;"></div>	
	
	<?php include './php/newsfeed_threat.php' ?>
	
</div>
<div style="width:47%" class='fright'>	
	<div class='top10header'>Rule Trend Analysis</div>
	<div class="introbody">Looking at which alert Rule IDs are seeing the greatest change.</div>
	
	<?php  include './php/newsfeed_trend.php'; ?>

</div>


<div class='clr'></div>
<div style='padding:40px ;width:95%; text-align:center;'>
<a class='tiny' href='http://www.ecsc.co.uk/'>ECSC | Vendor Independent Information Security Specialists</a>
</div>

</body>
</html>

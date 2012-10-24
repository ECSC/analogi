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

<?php
include "page_refresh.php";
?>
<link href="./style.css" rel="stylesheet" type="text/css" />

</head>
<body>

<?php includeme("./header.php"); ?>

		
<div class='clr'></div>	

<div style="width:48%" class='fleft'>	
	<div class='top10header'>Rule Trend Analysis (~3.5 hours)</div>
	<?php 
	# Set vars for this trend analysis
	$trend_window=10000;
	$lastfullblock=intval(substr(time(), 0, 6)."0000");
	?>

	<div class="introbody" style='height:35px;'>Comparing Level <span class='tw'><?php echo $glb_trendlevel; ?></span>+ over Period <?php echo date("D G:ia",$lastfullblock-$trend_window)." -> ".date("D G:ia",$lastfullblock)." over the last <span class='tw'>".$glb_trendweeks?></span> weeks</div>
	
	<div style="max-height:300px; overflow:auto;">
		<?php include "php/newsfeed_trend.php"; ?>	
	</div>
</div>
<div style="width:48%" class='fright'>	
	<div class='top10header'>Rule Trend Analysis (~28 hours)</div>
	<?php 
	# Set vars for this trend analysis
	$trend_window=100000;
	$lastfullblock=intval(substr(time(), 0, 5)."00000");
	?>
	<div class="introbody" style='height:35px;'>Comparing Level <span class='tw'><?php echo $glb_trendlevel; ?></span>+ over Period <?php echo date("D G:ia",$lastfullblock-$trend_window)." -> ".date("D G:ia",$lastfullblock)." over the last <span class='tw'>".$glb_trendweeks?></span> weeks</div>
	
	<div style="max-height:300px; overflow:auto;">
		<?php include "php/newsfeed_trend.php"; ?>	
	</div>
</div>

<div class='clr' style='padding-bottom:10px;'></div>

<div style="width:48%" class='fleft'>	
	<div class='top10header'>Alert Threat Feed</div>
	<?php 
	# Set vars for this trend analysis
	$trend_window=1000000;
	$lastfullblock=intval(substr(time(), 0, 5)."000000");
	?>
	<div class="introbody" style='height:25px;padding-bottom:10px;'>Grouped list of most important alerts over the last <span class='tw'><?php echo $glb_threatdays; ?></span> days, level <span class='tw'><?php echo $glb_threatdays; ?></span>+.</div>
	
	<div style="max-height:300px; overflow:auto;">
		<?php include './php/newsfeed_threat.php'; ?>
	</div>
</div>

<div style="width:48%" class='fright'>	
	<div class='top10header'>IPs Trending</div>
	<?php 
	# Set vars for this trend analysis
	$trend_window=100000;
	$lastfullblock=intval(substr(time(), 0, 5)."00000");
	?>
	<div class="introbody" style='height:25px;padding-bottom:10px;'>Top <span class='tw'><?php echo $glb_trendip_top; ?></span> IPs appear most in the logs over the last <span class='tw'><?php echo $glb_threatdays ?></span> days<br>One alert may span multiple 'groups'</div>
	
	<div style="max-height:300px; overflow:auto;">
		<?php include './php/newsfeed_trendip.php'; ?>
	</div>
</div>


<div class='clr'></div>
<?php
include 'footer.php';
?>

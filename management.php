<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
require './top.php';


### Deleting Section
if(isset($_GET['action']) && $_GET['action']=='delete' && preg_match("/\/management.php/", $_SERVER['HTTP_REFERER'])){
	# Yes I know the referer is fakable, but this is to help reduce CSRF attacks from remote links, and not to prevent malicious browsers

	$where="";
	# delete ruleid
	if(isset($_GET['rule_id']) && is_numeric($_GET['rule_id']) && strlen($_GET['rule_id'])>0){
		$where.="alert.rule_id=".$_GET['rule_id']." AND ";
	}
	
	# deletelevel
	if(isset($_GET['level']) && is_numeric($_GET['level']) && $_GET['level']>0){
		$where.="signature.level=".$_GET['level']." AND ";
	}
	
	# deletebefore
	if(isset($_GET['before']) && is_numeric($_GET['before']) && $_GET['before']>0){
		$where.="alert.timestamp<".$_GET['before']." AND ";
	}
	# delete source
	if(isset($_GET['source']) && strlen($_GET['source'])>0){
		$where.="location.name like \"".$_GET['source']."%\" AND ";
	}
	# delete path
	if(isset($_GET['path']) && strlen($_GET['path'])>0){
		$where.="location.name like \"%".$_GET['path']."\" AND ";
	}
	# delete data
	if(isset($_GET['datamatch']) && strlen($_GET['datamatch'])>0){
		$where.="data.full_log like \"%".$_GET['datamatch']."%\" AND ";
	}
	
	$query="";
	# Only run if paramters set, do NOT empty the database!
	if(strlen($where) > 0){

		# remove the last 'AND '
		$where=substr($where,0,-4);

		$querydelete="DELETE alert, data FROM alert
			LEFT JOIN data ON alert.id=data.id
			LEFT JOIN signature ON alert.rule_id=signature.rule_id
			LEFT JOIN location ON alert.location_id=location.id
			WHERE ".$where;
		$resultdelete=$mysqli->query($querydelete);
		if($resultdelete==1){
			# MySQL version of vaccum... this actually removes the data
			$query="OPTIMIZE TABLE alert;";
			$mysqli->query($query);
			$query="OPTIMIZE TABLE data;";
			$mysqli->query($query);
		}
	
		if($glb_detailsql==1){
		#	For niceness show the SQL queries, just incase you want to dig deeper your self
			echo "<div class='clr' style='padding-bottom:20px;'></div>
				<div class='fleft top10header'>SQL (".$resultdelete.")</div>
				<div class='fleft tiny' style=''>".htmlspecialchars($querydelete)."</div>";
		}
	}	
}

### Removing a location
if(isset($_GET['action']) && $_GET['action']=='removelocation' && isset($_GET['source']) && strlen($_GET['source'])>0 && preg_match("/\/management.php/", $_SERVER['HTTP_REFERER'])){
	# Yes I know the referer is fakable, but this is to help reduce CSRF attacks from remote links, and not to prevent malicious browsers

	# Delete data
	$querydelete="DELETE alert, data FROM alert
		LEFT JOIN data ON alert.id=data.id
		LEFT JOIN signature ON alert.rule_id=signature.rule_id
		LEFT JOIN location ON alert.location_id=location.id
		WHERE location.name like \"".$_GET['source']."%\"";
	$resultdelete=$mysqli->query($querydelete);
	if($glb_detailsql==1){
		#For niceness show the SQL queries, just incase you want to dig deeper your self
		echo "<div class='fleft top10header'>SQL (".$resultdelete.")</div>
			<div class='fleft tiny' style=''>".htmlspecialchars($querydelete)."</div>
			<div class='clr' style='padding-bottom:20px;'></div>";
	}
	# MySQL version of vaccum... this actually removes the data
	$query="OPTIMIZE TABLE alert;";
	$mysqli->query($query);
	$query="OPTIMIZE TABLE data;";
	$mysqli->query($query);

	# Delete location
	$querydelete="DELETE FROM location
		WHERE location.name like \"".$_GET['source']."%\"";	
	$resultdelete=$mysqli->query($querydelete);
	if($glb_detailsql==1){
		#For niceness show the SQL queries, just incase you want to dig deeper your self
		echo "<div class='fleft top10header'>SQL (".$resultdelete.")</div>
			<div class='fleft tiny' style=''>".htmlspecialchars($querydelete)."</div>
			<div class='clr' style='padding-bottom:20px;'></div>";
	}


}


	

### Oldest alert
$query="SELECT alert.timestamp as age
	FROM alert
	ORDER BY timestamp
	LIMIT 1";
$result=$mysqli->query($query);
$row = $result->fetch_assoc();
$oldestalert=$row['age'];



# Get all clients for dropdown
$query="SELECT distinct(substring_index(substring_index(name, ' ', 1), '->', 1)) as dname FROM location ORDER BY dname";
$result=$mysqli->query($query);
$filtersource="";
while($row = $result->fetch_assoc()){
	$filtersource.="<option value='".$row['dname']."'>".$row['dname']."</option>";
}

# Get paths for dropdown
$query="SELECT distinct(substring_index(name,'->',-1)) as dname FROM location ORDER BY dname;";
$result=$mysqli->query($query);
$filterpath="";
while($row = $result->fetch_assoc()){
	$filterpath.="<option value='".$row['dname']."'>".$row['dname']."</option>";
}

# Get all levels for dropdowns
$query="SELECT distinct(level) FROM signature ORDER BY level";
$result=$mysqli->query($query);
$filterlevel="";
while($row = $result->fetch_assoc()){
	$filterlevel.="<option value='".$row['level']."'>".$row['level']."</option>";
}

# Make dropdown 'Before'
$filterbefore="";
for ($i = 0; $i < 48; $i++) {
	$timestamp = mktime(0, 0, 0, date('n') - $i, 1);
	$filterbefore.="<option value='".$timestamp."'>".date("M Y", $timestamp)."</option>";	
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

<script type="text/javascript">

	function databasetest(){
		<!--  If no data, alerts will be created in here  -->
		<?php include './databasetest.php' ?>

	}


	<?php
	if($glb_management_clientvslevel==1){
		# this can be turned off
		include './php/management_sourcelevel.php';
		echo "
		
		";
		include './php/management_timevolume.php';
	}else{
		# but the graph still needs a variable to not break the page
		echo "var chartData = []";
		echo "chartData_timemanagement = []";
	}

	?>

	timemanagementaverage = "<?php echo $graph_timemanagement_average; ?>";
	
	AmCharts.ready(function () {

		//#########################################################
		var chart

		// SERIALL CHART
		chart = new AmCharts.AmSerialChart();
		chart.dataProvider = chartData;
		chart.categoryField = "source";
		chart.plotAreaBorderAlpha = 0.2;
		chart.rotate = true;
		
		// AXES
		// Category
		var categoryAxis = chart.categoryAxis;
		categoryAxis.gridAlpha = 0.1;
		categoryAxis.axisAlpha = 0;
		categoryAxis.gridPosition = "start";

		// value                      
		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.stackType = "regular";
		valueAxis.gridAlpha = 0.1;
		valueAxis.axisAlpha = 0;
		chart.addValueAxis(valueAxis);

		<?php echo $graphstring; ?>

		// LEGEND
		 var legend = new AmCharts.AmLegend();
		legend.position = "right";
		legend.borderAlpha = 0.3;
		legend.horizontalGap = 10;
		chart.addLegend(legend);

		<?php echo $graphheight; ?>

		// WRITE
		chart.write("chartdiv");

		//#########################################################

		var chart_timemanagement;

		// SERIAL CHART    
		chart_timemanagement = new AmCharts.AmSerialChart();
		chart_timemanagement.dataProvider = chartData_timemanagement;
		chart_timemanagement.categoryField = "date";

		// AXES
		// category
		var categoryAxis_timemanagement = chart_timemanagement.categoryAxis;
		categoryAxis_timemanagement.parseDates = true; // as our data is date-based, we set parseDates to true
		categoryAxis_timemanagement.minPeriod = "hh"; // our data is daily, so we set minPeriod to DD
		categoryAxis_timemanagement.dashLength = 1;
		categoryAxis_timemanagement.gridAlpha = 0.15;
		categoryAxis_timemanagement.axisColor = "#DADADA";

		// value                
		var valueAxis_timemanagement = new AmCharts.ValueAxis();
		valueAxis_timemanagement.axisColor = "#DADADA";
		valueAxis_timemanagement.dashLength = 1;
		valueAxis_timemanagement.title = "Daily Alerts";
		chart_timemanagement.addValueAxis(valueAxis_timemanagement);
		// value                
		var valueAxis_timemanagement2 = new AmCharts.ValueAxis();
		valueAxis_timemanagement2.axisColor = "#DADADA";
		valueAxis_timemanagement2.dashLength = 1;
		valueAxis_timemanagement2.position = "right";
		valueAxis_timemanagement2.title = "Cumulative Alerts";
		chart_timemanagement.addValueAxis(valueAxis_timemanagement2);


		// GUIDE for average
		var guide_timemanagement = new AmCharts.Guide();
		guide_timemanagement.value = timemanagementaverage;
		guide_timemanagement.lineColor = "#CC0000";
		guide_timemanagement.dashLength = 4;
		guide_timemanagement.label = "average";
		guide_timemanagement.inside = true;
		guide_timemanagement.lineAlpha = 1;
		valueAxis_timemanagement.addGuide(guide_timemanagement);

		<?php echo $graph_timemanagement; ?>


                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.bulletType = "round";
                legend.equalWidths = false;
                legend.valueWidth = 120;
                legend.color = "#000000";
                chart_timemanagement.addLegend(legend);

		// CURSOR
		var chartCursor_timemanagement = new AmCharts.ChartCursor();
		chartCursor_timemanagement.cursorPosition = "mouse";
		chart_timemanagement.addChartCursor(chartCursor_timemanagement);

		// SCROLLBAR
		var chartScrollbar_timemanagement = new AmCharts.ChartScrollbar();
		chart_timemanagement.addChartScrollbar(chartScrollbar_timemanagement);

		// WRITE
		chart_timemanagement.write("chartdiv_timemanagement");

		});

</script>

</head>

<body onload="databasetest()">

<?php include './header.php'; ?>

<div class='clr' style="margin-top:10px;"></div>	


<div class="top10header">Contents</div>
<div style="padding:10px;">
	<div class="contents"><a href='./management.php#intro'>Intro</a></div>
	<div class="contents"><a href='./management.php#agents'>Last Agent Alert</a></div>
	<div class="contents"><a href='./management.php#ruletweaking'>Rule Tweaking</a></div>
	<div class="contents"><a href='./management.php#databasesummary'>Database Size Summary</a></div>
	<div class="contents"><a href='./management.php#databasecleanup'>Database cleanup</a></div>
	<div class="contents"><a href='./management.php#removelocation'>Remove Location (OSSEC client)</a></div>
</div>

<a name="intro"></a> 
<div class="top10header">Intro</div>
<div class="introbody">All of this reflects the data held in your SQL database and is not linked in anyway to the flat file logs written by OSSEC.</div>

<div class="introbody">This page is to help manage your OSSEC database.</div>

<div class="introbody">I advise you first look at 'Rule Tweaking', as prevention is better than a cure. This section will help identify which rules are taking the most space and might even help point to areas where you can improve the rules to your needs.</div>

<div class="introbody">The section 'Database Size Summary' helps identify which box is submitting the most data of a specific level.</div>

<div class="introbody">The section 'Database Cleanup' should only be used when the other sections have been exhausted. After tweaking your rules, and identifying where most space is used, this section will allow you to PERMANENTLY DELETE data from your database.</div>



<a name="agents"></a> 
<div class="top10header">Last Agent Alert</div>
<div class="introbody">Looking for Agents that have no alerts in the last  <span class='tw'><?php echo $glb_management_checkin; ?></span> Hours. If you have deleted Alerts this may give a misleading result. This will NOT display agents that have NEVER connected.</div>
<div style="padding:10px;">
<?php include './php/management_agentcheckin.php'  ?>
</div>

<a name="ruletweaking"></a>
<div class="top10header">Rule Tweaking</div>
<div class="introbody">These are the  <span class='tw'><?php echo $glb_managementtweaking; ?></span> most common rule hits, per system, in the database. Investigate to see if these rules can be further tuned to remove unnecessary alerting?</div>

<div style="padding:10px;">
<?php include './php/management_commonrules.php' ?>
</div>


<a name="databasesummary"></a>
<div class="top10header">Database Summary</div>
<?php include 'php/management_databasesize.php'; ?>

<div class='clr' style="margin-top:10px;"></div>	

<div class="top10header">Database Usage - Client vs Level</div>
<div class="introbody">In the case where there are to many hosts in the database that this graph becomes a hinderance disable <span class='tw'>$glb_management_clientvslevel</span> in config.php</div>
<div class='clr'></div>
<?php echo $clientvsleveldebugstring; ?>
<div id="chartdiv" class="fleft" style="width:90%; height:750px"></div>

<div class='clr' style="margin-top:10px;"></div>	

<div class="top10header" >Database Usage - Overtime</div>
<?php echo $timevolumedebugstring; ?>
<div class='clr'></div>
<div id="chartdiv_timemanagement" class="fleft" style="width:90%; height:450px"></div>

<div class='clr' style="margin-top:10px;"></div>	

<a name="databasecleanup"></a>
<div class="top10header">Database Cleanup</div>
<div class="introbody">Use this section to cleanse the database of old/unimportant alerts. Examples:</div>
<div class="introbody">
	<li>Delete alerts which are older than your retention requirements (6 months or older?)
	<li>Delete all alerts for 'Server XYZ'
	<li>Delete all alerts level 5 or below
	<li>Delete all rule 5104 that is older than 2 months
	<li>Delete all proxy logs that are older than 4 months 
</div>
<div style="padding:10px;">
	<form method='GET' action='./management.php?action=delete'>
		<input type='hidden' name='action' value='delete' >
		<div class='fleft filters'>
			RuleID<br/>
			<input type='text' size='6' name='rule_id' value='' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			Level<br/>
			<select name='level' style='font-size:12px' >
				<option value='0'>--</option>
				<?php echo $filterlevel ?>
			</select>
		</div>
		<div class='fleft filters'>
			Before <br/>
			<select name='before' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filterbefore ?>
			</select>
		</div>
		<div class='fleft filters'>
			Source<br/>
			<select name='source' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filtersource ?>
			</select>
		</div>
		<div class='fleft filters'>
			Path<br/>
			<select name='path' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filterpath ?>
			</select>
		</div>
		<div class='fleft filters'>
			Data Match<br/>
			<input type='text' size='6' name='datamatch' value='' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			<br/>
			<input type='submit' value='..delete' />
		</div>
	</form>
</div>

<div class='clr' style="margin-top:10px;"></div>	

<a name="removelocation"></a>
<div class="top10header">Remove Location (OSSEC client)</div>
<div class="introbody">Used to remove locations that no longer exist.</div>
<div class="introbody">
	<li>This will remove ALL traces of this location from the database
	<li>This should only be used on locations that are will no longer connect to OSSEC
	<li>If you accidentally remove a location, you will have to restart the OSSEC service to re import, though all SQL data will be lost (flat file logs will not be affected)
</div>
<div style="padding:10px;">
	<form method='GET' action='./management.php?'>
		<input type='hidden' name='action' value='removelocation' >
		<div class='fleft filters'>
			Source<br/>
			<select name='source' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filtersource; ?>
			</select>
		</div>
		<div class='fleft filters'>
			<br/>
			<input type='submit' value='..remove' />
		</div>
	</form>
</div>


<div class='clr'></div>

<?php
include 'footer.php';
?>

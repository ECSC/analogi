<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

require './top.php';


## filter criteria 'level'
if(isset($_GET['level']) && is_numeric($_GET['level']) && $_GET['level']>=0){
	$inputlevel=$_GET['level'];
}else{
	$inputlevel=$glb_level;
}
$query="SELECT distinct(level) FROM signature ORDER BY level";
$result=mysql_query($query, $db_ossec);
while($row = @mysql_fetch_assoc($result)){
	$selected="";
	if($row['level']==$inputlevel){
		$selected=" SELECTED";
	}
	$filterlevel.="<option value='".$row['level']."'".$selected.">".$row['level']." +</option>";
}


## filter from
if(isset($_GET['hours']) && preg_match("/^[0-9]+$/", $_GET['hours'])){
	$inputhours=$_GET['hours'];
}else{
	$inputhours=$glb_hours;
} 


## filter
$radiosource="";
$radiopath="";
$radiolevel="";
if(isset($_GET['field']) && $_GET['field']=='source'){
    $radiosource="checked";
}elseif(isset($_GET['field']) && $_GET['field']=='path'){

	$radiopath="checked";
}elseif(isset($_GET['field']) && $_GET['field']=='level'){
	$radiolevel="checked";
}elseif(isset($_GET['field']) && $_GET['field']=='rule_id'){
	$radiorule_id="checked";
}else{
	if($glb_graphbreakdown=="source"){
		$radiosource="checked";
	}elseif($glb_graphbreakdown=="path"){
		$radiopath="checked";
	}elseif($glb_graphbreakdown=="level"){
		$radiolevel="checked";
	}elseif($glb_graphbreakdown=="rule_id"){
		$radiorule_id="checked";
	}else{
		# default source
		$radiosource="checked";
	}
}


?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>AnaLogi - OSSEC WUI</title>

<meta http-equiv="refresh" content="<?php echo $glb_autorefresh; ?>" > 
<link href="./style.css" rel="stylesheet" type="text/css" />
<script src="./amcharts/amcharts.js" type="text/javascript"></script>

<script type="text/javascript">
	var chart;

	<?php
	include './php/index_graph.php';
	?>


	AmCharts.ready(function () {
		// SERIAL CHART
		chart = new AmCharts.AmSerialChart();
		chart.dataProvider = chartData;
		chart.categoryField = "date";
		chart.startDuration = 0.5;
		chart.balloon.color = "#000000";
		chart.zoomOutOnDataUpdate=true;
		chart.pathToImages = "./images/";
		chart.zoomOutButton = {
			backgroundColor: '#000000',
			backgroundAlpha: 0.15
		};

		// listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
		chart.addListener("dataUpdated", zoomChart);

		// AXES
		// category
		var categoryAxis = chart.categoryAxis;
		categoryAxis.fillAlpha = 1;
		categoryAxis.fillColor = "#FAFAFA";
		categoryAxis.gridAlpha = 0;
		categoryAxis.axisAlpha = 0;
		categoryAxis.gridPosition = "start";
		categoryAxis.position = "top";		
		categoryAxis.parseDates = true;
		categoryAxis.minPeriod = "mm";

		<?php
		## See top.php for more info
		#include './php/index_graph_icinga.php';
		?>

		// value
		var valueAxis = new AmCharts.ValueAxis();
		chart.addValueAxis(valueAxis);
		valueAxis.logarithmic = <?php echo $glb_indexgraphlogarithmic; ?>;
		valueAxis.title = "Alerts";

		// this method is called when chart is first inited as we listen for "dataUpdated" event
		function zoomChart() {
			// replaced by chart.zoomOutOnDataUpdate
		}

		// SCROLLBAR
		var chartScrollbar = new AmCharts.ChartScrollbar();
		chartScrollbar.graph = graph0;
		chartScrollbar.scrollbarHeight = 40;
		chartScrollbar.color = "#FFFFFF";
		chartScrollbar.autoGridCount = true;
		chart.addChartScrollbar(chartScrollbar);

		
		<?php
		if($glb_indexgraphbubbletext==1){
		echo "
		// chartCursor
		var chartCursor = new AmCharts.ChartCursor();
          	chartCursor.cursorPosition = 'mouse';
                chartCursor.categoryBalloonDateFormat = 'JJ:NN, DD MMMM';
		chart.addChartCursor(chartCursor);
		";
		}
		?>	

		// changes cursor mode from pan to select
		function setPanSelect() {
			if (document.getElementById("rb1").checked) {
				chartCursor.pan = false;
				chartCursor.zoomable = true;
			} else {
				chartCursor.pan = true;
			}
			chart.validateNow();
		}  



		<?php
		echo $graphlines; 
		echo $workinghoursguide
		?>

		<?php
		if($glb_indexgraphkey==1){
		echo "
		// LEGEND
		var legend = new AmCharts.AmLegend();
		legend.markerType = 'circle';
		chart.addLegend(legend);";
		}
		?>


		// WRITE
		chart.write("chartdiv");
	});
</script>
	

</head>
<body>

<?php include './header.php'; ?>
		
<div class='clr'></div>	

<div id="chartdiv" style="width:100%; height:500px;"></div>
	
<div class='top10header'>Filters</div>

<div>
	<form method='GET' action='./index.php'>
		<div class='fleft filters'>
			Level<br/>
			<select name='level'>
				<option value=''>--</option>
				<?php echo $filterlevel; ?>
			</select>
		</div>
		<div class='fleft filters'>
			Hours<br/>
			<input type='text' size='6' name='hours' value='<?php echo $inputhours; ?>' />
		</div>
		<div class='fleft filters'>
			Graph Breakdown<br/>
			<input type='radio' name='field' value='source' <?php echo $radiosource; ?> />Source
			<input type='radio' name='field' value='path' <?php echo $radiopath; ?> />Path
			<input type='radio' name='field' value='level' <?php echo $radiolevel; ?> />Level
			<input type='radio' name='field' value='rule_id' <?php echo $radiorule_id; ?> />Rule ID
		</div>
		<div class='fleft filters'>
			<br/>
			<input type='submit' value='..go..' />
		</div>
	</form>
</div>

<div class='clr' style='margin:10px;'>&nbsp;</div>

<div id="top10s" class="top10s">
	<div class='fleft maincol'>
		<?php include './php/topid.php'; ?>

	</div>
	<div class='fleft maincol'>
		<?php include './php/toplocation.php'; ?>

	</div>
	<div class='fleft maincol'>
		<?php include './php/toprare.php'; ?>

	</div>
</div>

<div class='clr'></div>

<div style='padding:40px ;width:95%; text-align:center;'>
	<a class='tiny' href='http://www.ecsc.co.uk/'>ECSC | Vendor Independent Information Security Specialists</a>
</div>

</body>
</html>

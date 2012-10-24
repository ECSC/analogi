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

<?php include 'page_refresh.php'; ?>

<link href="./style.css" rel="stylesheet" type="text/css" />
<script src="./amcharts/amcharts.js" type="text/javascript"></script>

<script type="text/javascript">

        var chart;
        var chart2;
        var chart3;

	<?php
	include './php/massmonitoring_grouptime.php';
	include './php/massmonitoring_locationtime.php';
	include './php/massmonitoring_hostsubstr.php';
	?>

            AmCharts.ready(function () {
                // SERIAL CHART    
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "date";

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis.minPeriod = "hh"; // our data is daily, so we set minPeriod to DD
                categoryAxis.dashLength = 1;
                categoryAxis.gridAlpha = 0.15;
                categoryAxis.axisColor = "#DADADA";

                // value                
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisColor = "#DADADA";
                valueAxis.dashLength = 1;
		valueAxis.logarithmic = <?php echo $glb_indexgraphlogarithmic;  ?> ;
                chart.addValueAxis(valueAxis);


                // GRAPH
		<?php echo $graphstring; ?>

                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.markerType = "circle";
                chart.addLegend(legend);

                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chart.addChartCursor(chartCursor);

                // WRITE
                chart.write("chartDiv");
		//////////////////////////////////////////////////////

                // SERIAL CHART    
                chart2 = new AmCharts.AmSerialChart();
                chart2.dataProvider = chartData2;
                chart2.categoryField = "date";

                // AXES
                // category
                var categoryAxis2 = chart2.categoryAxis;
                categoryAxis2.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis2.minPeriod = "hh"; // our data is daily, so we set minPeriod to DD
                categoryAxis2.dashLength = 1;
                categoryAxis2.gridAlpha = 0.15;
                categoryAxis2.axisColor = "#DADADA";

                // value                
                var valueAxis2 = new AmCharts.ValueAxis();
                valueAxis2.axisColor = "#DADADA";
                valueAxis2.dashLength = 1;
		valueAxis2.minimum=0;
                chart2.addValueAxis(valueAxis2);

                // GRAPH
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "smoothedLine";
                graph2.bulletColor = "#FFFFFF";
                graph2.bulletBorderColor = "#00BBCC";
                graph2.bulletBorderThickness = 2;
                graph2.bulletSize = 7;
                graph2.title = "Price";
                graph2.valueField = "location";
                graph2.lineThickness = 2;
                graph2.lineColor = "#00BBCC";
                chart2.addGraph(graph2);


                // WRITE
                chart2.write("chartDiv2");

		////////////////////////////////////////////////////////

                // SERIAL CHART    
                chart3 = new AmCharts.AmSerialChart();
                chart3.dataProvider = chartData3;
                chart3.categoryField = "date";

                // AXES
                // category
                var categoryAxis3 = chart3.categoryAxis;
                categoryAxis3.parseDates = true; // as our data is date-based, we set parseDates to true
                categoryAxis3.minPeriod = "hh"; // our data is daily, so we set minPeriod to DD
                categoryAxis3.dashLength = 1;
                categoryAxis3.gridAlpha = 0.15;
                categoryAxis3.axisColor = "#DADADA";

                // value                
                var valueAxis3 = new AmCharts.ValueAxis();
                valueAxis3.axisColor = "#DADADA";
                valueAxis3.dashLength = 1;
		valueAxis3.minimum=0;
                chart3.addValueAxis(valueAxis3);

                // LEGEND
                var legend3 = new AmCharts.AmLegend();
                legend3.markerType = "circle";
                chart3.addLegend(legend3);

                // GRAPH
		<?php echo $graphsubstr ?> 


                // WRITE
                chart3.write("chartDiv3");

            });


</script>

</head>

<body onload="">

<?php include './header.php'; ?>

<div class='clr'></div>	

<div style="width:66%" class='fleft'>	
	<div class='top10header'>Groups Activity Over Time (<span class='tw'><?php echo $glb_mass_days ?></span> days)</div>
	<span style='font-size:9px;'>* For interesting curves I recommend go back to index.php and search for that group specificily </span>
	<?php echo $grouptimedebugstring; ?>
	<div id="chartDiv" class="fleft" style="height:800px; width:100%"></div>
</div>


<div style="width:34%" class='fright'>	
	<div class='top10header'>Reporting Locations (<span class='tw'><?php echo $glb_mass_days ?></span> days)</div>
	<?php echo $locationtimedebugstring; ?>
	<div id="chartDiv2" class="" style="height:300px; width:100%"></div>

	<div class='top10header'>Actvity per Host Substring  (<span class='tw'><?php echo $glb_mass_days ?></span> days)</div>
	<?php echo $hostsubstrdebugstring; ?>
	<div id="chartDiv3" class="" style="height:300px; width:100%"></div>
</div>



<div class='clr'></div>
<?php
include 'footer.php';
?>

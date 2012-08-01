<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */
require './top.php';

###  Get the criteria from the URL, these are used to populate the graph, and to populate the filter options further down


$where="";

# input<var> = the raw GET
# filter<var> = for repopulating the filter toolbar
# where = the cumulative sql command

## filter criteria 'level'
if(isset($_GET['level']) && is_numeric($_GET['level']) && $_GET['level']>=0){
	$inputlevel=$_GET['level'];
	$where.="AND signature.level>=".$inputlevel." ";
}else{
	$inputlevel="";
	$where.="";
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
if(isset($_GET['from']) && preg_match("/^[0-9\ ]+$/", $_GET['from'])){
	$inputfrom=$_GET['from'];
	$filterfrom=$inputfrom;
	$f=split(" ",$inputfrom);
	$sqlfrom=mktime(substr($f[0], 0, 2), substr($f[0], 2, 4), 0,substr($f[1], 2, 2),substr($f[1], 0, 2),substr($f[1], 4, 2));
	$where.="AND alert.timestamp>=".$sqlfrom." ";
}else{
	$inputfrom="";
	$filterfrom=$inputfrom;
	$where.="";
} 

## filter to
if(isset($_GET['to']) && preg_match("/^[0-9\ ]+$/", $_GET['to'])){
	$inputto=$_GET['to'];
	$filterto=$inputto;
	$t=split(" ",$inputto);
	$sqlto=mktime(substr($t[0], 0, 2), substr($t[0], 2, 4), 0,substr($t[1], 2, 2),substr($t[1], 0, 2),substr($t[1], 4, 2));
	$where.="AND alert.timestamp<=".$sqlto." ";
}else{
	$inputto="";
	$filterto=$inputto;
	$where.="";
} 


## filter criteria 'source'
if(isset($_GET['source']) && strlen($_GET['source'])>0){
	$inputsource=$_GET['source'];
	$where.="AND location.name like '%".$inputsource."%' ";
}else{
	$inputsource="";
	$where.="";
}
$query="SELECT distinct(substring_index(substring_index(name, ' ', 1), '->', 1)) as dname FROM location ORDER BY dname";
$result=mysql_query($query, $db_ossec);
while($row = @mysql_fetch_assoc($result)){
	$selected="";
	if($row['dname']==$inputsource){
		$selected=" SELECTED";
	}
	$filtersource.="<option value='".$row['dname']."'".$selected.">".$row['dname']."</option>";
}

## filter criteria 'path'
if(isset($_GET['path']) && strlen($_GET['path'])>0){
	$inputpath=$_GET['path'];
	$where.="AND location.name like '%".$inputpath."%' ";
}else{
	$inputpath="";
	$where.="";
}
$query="SELECT distinct(substring_index(name,'->',-1)) as dname FROM location ORDER BY dname;";
$result=mysql_query($query, $db_ossec);
while($row = @mysql_fetch_assoc($result)){
	$selected="";
	if($row['dname']==$inputpath){
		$selected=" SELECTED";
	}
	$filterpath.="<option value='".$row['dname']."'".$selected.">".$row['dname']."</option>";
}


## filter rule_id
if(isset($_GET['rule_id']) && strlen($_GET['rule_id'])>0){
	$inputrule_id=$_GET['rule_id'];
	$filterule_id=$inputrule_id;
		
	$inputrule_id_array=preg_split('/,/', $inputrule_id);

	$where.="AND (1=0 ";
	foreach ($inputrule_id_array as $value){
		if(strlen($value)>0){
			$where.="OR alert.rule_id=".$value." ";
		}

		$query="select signature.description from signature where rule_id=".$value;
		$result=mysql_query($query, $db_ossec);
		$row = @mysql_fetch_assoc($result);
		$noterule_id.="<span style='font-weight:bold;' >Rule ".$value."</span>: ".$row['description']."<br/>";
	}
	$where.=")";

}else{
	$inputrule_id="";
	$filterule_id=$inputrule_id;
	$where.="";
	$noterule_id="";
}	



### filter input 'datamatch'
# Current opinion is that this does not have to be 'safe' as we trust users who can access this
if(isset($_GET['datamatch']) && strlen($_GET['datamatch'])>0){
	$inputdatamatch=$_GET['datamatch'];
	$filterdatamatch=$inputdatamatch;
	$where.="AND data.full_log like '%".quote_smart($inputdatamatch)."%' ";
}else{
	$inputdatamatch="";
	$filterdatamatch=$inputdatamatch;
}


### filter input 'rulematch'
# Current opinion is that this does not have to be 'safe' as we trust users who can access this
if(isset($_GET['rulematch']) && strlen($_GET['rulematch'])>0){
	$inputrulematch=$_GET['rulematch'];
	$filterrulematch=$inputrulematch;
	$where.="AND signature.description like '%".quote_smart($inputrulematch)."%' ";
}else{
	$inputrulematch="";
	$filterrulematch=$inputrulematch;

}


### filter limit
if(isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit']<1000){
	$inputlimit=$_GET['limit'];
}else{
	$inputlimit=$glb_detailtablelimit;
}




#function highlight($string, $term){
#	$term = preg_replace('/\s+/', ' ', trim($term));
#	$words = explode(' ', $term);
#	$highlighted = array();
#	foreach ( $words as $word ){
#	    $highlighted[] = "<span class='highlight'>".$word."</span>";
#	}
#
#	return str_replace($words, $highlighted, $string);
#}





?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="./style.css" rel="stylesheet" type="text/css" />
<script src="./amcharts/amcharts.js" type="text/javascript"></script>
<script src="./sortable.js" type="text/javascript"></script>

<script type="text/javascript">

	function databasetest(){
		<!--  If no data, alerts will be created in here  -->
		<?php include './databasetest.php' ?>

	}

	var chart;

	<?php
	include './php/detail_graph.php';
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
		categoryAxis.equalSpacing = false;

		// value
		var valueAxis = new AmCharts.ValueAxis();
		chart.addValueAxis(valueAxis);
		valueAxis.logarithmic = <?php echo $glb_indexgraphlogarithmic;  ?> ;
		//valueAxis.minimum = 0;

		// this method is called when chart is first inited as we listen for "dataUpdated" event
		function zoomChart() {
			// replaced by chart.zoomOutOnDataUpdate
		}

		// SCROLLBAR
		var chartScrollbar = new AmCharts.ChartScrollbar();
		chartScrollbar.scrollbarHeight = 40;
		chartScrollbar.color = "#FFFFFF";
		chartScrollbar.autoGridCount = true;
		chart.addChartScrollbar(chartScrollbar);

		// chartCursor
		var chartCursor = new AmCharts.ChartCursor();
		chartCursor.cursorPosition = "mouse";
		chartCursor.categoryBalloonDateFormat = "JJ:NN, DD MMMM";
		chart.addChartCursor(chartCursor);


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

		<?php echo $graphlines; ?>

		// LEGEND
		var legend = new AmCharts.AmLegend();
		legend.markerType = "circle";
		chart.addLegend(legend);

		// WRITE
		chart.write("chartdiv");
});

</script>
	

</head>
<body onload="databasetest()">

<?php include './header.php'; ?>
		
<div class='clr'></div>	

<div class='tiny' style='color:red'><a href='./' class='tinyblack'> &lt; Back to Main</a></div>

<div id="chartdiv" style="width:100%; height:400px;"></div>

<div class='top10header'>Filters</div>

<div>
	<form method='GET' action='./detail.php'>
		<div class='fleft filters'>
			RuleID
			<a href="#" class="tooltip"><img src='./images/help.png' /><span>Comma separated allowed, e.g. "503,504"</span></a>
			<br/>
			<input type='text' size='6' name='rule_id' value='<?php echo $filterule_id; ?>' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			Level<br/>
			<select name='level' style='font-size:12px' >
				<option value=''>--</option>
				<?php echo $filterlevel; ?>
			</select>
		</div>
		<div class='fleft filters'>
			From <span style='font-size:10px;'>(HHMM DDMMYY)</span><br/>
			<input type='text' size='11' name='from' value='<?php echo $filterfrom; ?>' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			To <span style='font-size:10px;'>(HHMM DDMMYY)</span><br/>
			<input type='text' size='10' name='to' value='<?php echo $filterto; ?>' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			Source<br/>
			<select name='source' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filtersource; ?>
			</select>
		</div>
		<div class='fleft filters'>
			Path<br/>
			<select name='path'  style='font-size:12px' style='font-size:12px'>
				<option value=''>--</option>
				<?php echo $filterpath; ?>
			</select>
		</div>
		<div class='fleft filters'>
			<a href="#" class="tooltip"><img src='./images/help.png' /><span>Look for keywords in the full log entry</span></a>
			Data Match
			<br/>
			<input type='text' size='7' name='datamatch' value='<?php echo $filterdatamatch; ?>' style='font-size:12px'/>
		</div>
		<div class='fleft filters'>
			<a href="#" class="tooltip"><img src='./images/help.png' /><span>Look for rules containing keywords, i.e. 'XSS' will look for all rules that target XSS</span></a>
			Rule Match
			<br/>
			<input type='text' size='7' name='rulematch' value='<?php echo $filterrulematch; ?>' style='font-size:12px' />
		</div>
		<div class='fleft filters'>
			<br/>
			<input type='submit' value='..go' />
		</div>
	</form>
</div>	
<div class='clr'></div>
<div><?php echo $noterule_id; ?></div>

<div class='clr' style='border-top:20px;'>&nbsp;</div>
<?php
	# Count the queries for the last line of the table.
	$querycounttable="SELECT alert.id
		FROM alert, location, signature, data
		WHERE 1=1
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.id=data.id
		".$where;
	$resultcounttable=mysql_query($querycounttable, $db_ossec);
	$resultablerows=mysql_num_rows($resultcounttable);
	
	# Fetch the actual rows of data for the table
	$querytable="SELECT alert.id as id, alert.rule_id as rule, signature.level as lvl, alert.timestamp as timestamp, location.name as loc, data.full_log as data
		FROM alert, location, signature, data
		WHERE 1=1
		and alert.location_id=location.id
		and alert.rule_id=signature.rule_id
		and alert.id=data.id
		".$where."
		ORDER BY alert.timestamp DESC
		LIMIT ".$inputlimit;		
	$resulttable=mysql_query($querytable, $db_ossec);


	echo "<table class='dump sortable' id='sortabletable'  style='width:100%' ><tr>
		<th>ID</th><th>Rule</th><th>Lvl</th><th>Timestamp</th><th>Location</th><th>Data</th>
		</tr>";
	
	$rowcount=0;

	# This sets up the ability to highlight keywords below
	$term = preg_replace('/\|+/', '|', trim($glb_autohighlight));
	$words = explode('|', $term);
	$highlighted = array();
	foreach ( $words as $word ){
	    $highlighted[] = "<span class='highlight'>".$word."</span>";
	}


	while($rowtable = @mysql_fetch_assoc($resulttable)){

		# Dump each line to the table, be careful, this data is fromt the logs and should not be trusted
		if(isset($_GET['datamatch']) && strlen($_GET['datamatch'])>0){
			$tabledata=preg_replace("/(".$_GET['datamatch'].")/i", '<span style="color:red">$1</span>', htmlspecialchars($rowtable['data']));
		}else{
			$tabledata=htmlspecialchars($rowtable['data']);
		}

		$rowcount++;
		echo "<tr>";
		echo "<td>".htmlspecialchars($rowtable['id'])."</td>";
		echo "<td>".htmlspecialchars($rowtable['rule'])."</td>";
		echo "<td>".htmlspecialchars($rowtable['lvl'])."</td>";
		echo "<td>".date($glb_detailtimestamp, $rowtable['timestamp'])."</td>";
		echo "<td>".htmlspecialchars($rowtable['loc'])."</td>";
		echo "<td>".str_replace($words, $highlighted, htmlspecialchars($rowtable['data']))."</td>";
		echo "</tr>";

	}
	echo "</table>";


	# This final line has to be a separate table for the 'sortable' to work
	echo "<table class='dump sortable' style='width:100%' >";
	if($rowcount==0){
		echo "<tr><td><span style='color:red'>No data found, is your database populated?</span>.</td><td></td><td></td><td></td><td></td><td></td></tr>";
	}elseif($rowcount==$glb_detailtablelimit){
		echo "<tr><td colspan='6'><span style='color:red'>Search limited</span> to latest ".$rowcount." (of ".$resultablerows.") results as per your global config. Please refine your search on increase the limit.</td></tr>";
	}else{
		echo "<tr><td colspan='6'>".$rowcount." records shown.</td></tr>";
	}

	$detail2csv_get=preg_replace("/.*php\?/","",$_SERVER["REQUEST_URI"]);
	echo "<tr><td><a href='./detail2csv.php?".$detail2csv_get."'>Download all ".$resultablerows." results as CSV</a></td></tr>";
	echo "</table>";
	

	if($glb_detailsql==1){
	#	For niceness show the SQL queries, just incase you want to dig deeper your self
		echo "<div class='clr' style='padding-bottom:20px;'></div>
			<div class='fleft top10header'>SQL (Chart)</div>
			<div class='fleft tiny' style=''>".htmlspecialchars($querychart)."</div>";
	
		echo "<div class='clr' style='padding-bottom:20px;'></div>
			<div class='fleft top10header'>SQL (Table)</div>
			<div class='fleft tiny' style=''>".htmlspecialchars($querytable)."</div>";
	}

	?>

</div>

<div class='clr'></div>

<div style='padding:40px ;width:95%; text-align:center;'>
	<a class='tiny' href='http://www.ecsc.co.uk/'>ECSC | Vendor Independent Information Security Specialists</a>
</div>

</body>
</html>


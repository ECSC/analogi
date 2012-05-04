<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */


### Overall

# Not represented whitelist
# A list of locations/servers that are no longer in use.
# implemented = yes
# Note : it may be best to remove alerts from the database, but this is just an option
# example $glb_notrepresentedwhitelist = array ("dummyserver1", "lanserver");
$glb_notrepresentedwhitelist = array ();


# Server name Regex
# REGEX string representing which characters are removed from server names when manipulating
# implemeneted = yes
$glb_hostnamereplace="/[^a-zA-Z0-9_\/-]/";

# Index Logarithmic
# Determines whether the index.php graph is logarithmic (useful for widely varying data sources)
# options "true" or "false"   -   NB  MUST be in double quotes
# implemented = yes
$glb_indexgraphlogarithmic="true";

# Auto page refresh
# auto refresh for main page (only), allows this to auto update for a semi live screen in seconds
# implemented = yes
$glb_autorefresh=600;



### Overviewpage / Index.php

# Default level
# Default alert level for the main graph
# implemented = yes
$glb_level=5;

# Default hours
# Defult hours count for the main graph
# implemeneted = yes
$glb_hours=72;

# Graph key
# Determines whether the index.php graph displays a key (keep in mind that you can hover over a dot to get the name, useful to disable for lots of servers
# implemented = yes
$glb_indexgraphkey=1;

# Index graph all bubbles
# Determins whether the graph should have hover text for all bubbles on the mouse line.  Can be a pain if dozens of servers
# implemented = yes
$glb_indexgraphbubbletext=1;

# Overview Table Size
# How many entries should the 'Top' subtables have
# implemented = yes
$glb_indexsubtablelimit=10;

# Out of hours colour
# The colour used by the main graph to indicate out of hours timeperiods
# implemented = yes
$glb_outofhourscolour="#F08080";

# Out of Hours times
# The times the graph uses to determine what is out of hours.  Only supports complete hours, not minutes
# implemented = yes
$glb_outofhours_daystart=8;
$glb_outofhours_dayend=18;

# Absent client 
# How many days can an agent not report in for before it's considered missing and be reporte on.
# implemented = !! NO !! - my next job
$glb_missingclientdays=7;



### Breakdown page / detail.php

# Detail.php table limit 
# Max size of the table in detail.php 
# implemented=yes
$glb_detailtablelimit=500;

# Debug SQL on detail.php
# Useful if you want to drill down in SQL more, or for debugging
# implemented = yes
$glb_detailsql=1;

# Detail timestamp format
# The format of the timestamp column on the detail page, useful if you copy/paste to spreadsheet/csv
# implemented = yes
# Reference : http://php.net/manual/en/function.date.php
#$glb_detailtimestamp="H:i, jS m/Y";
$glb_detailtimestamp="Y/m/d g:i:s";
#$glb_detailtimestamp="H:i, jS m/Y";


?>

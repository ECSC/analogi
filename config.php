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
$glb_hostnamereplace="/[^a-zA-Z0-9_\/\ \-\.]/";

# Index Logarithmic
# Determines whether the index.php graph is logarithmic (useful for widely varying data sources)
# options "true" or "false"   -   NB  MUST be in double quotes
# implemented = yes
$glb_indexgraphlogarithmic="true";

# Auto page refresh
# auto refresh for main page (only), allows this to auto update for a semi live screen in seconds
# implemented = yes
$glb_autorefresh=600;

# No Data String
# Two situations where there no data is present, a query that is too strict, or the database is not logging
# implemented = yes
$glb_nodatastring="No data found matching this query.";
 

### Overviewpage / Index.php

# Default level
# Default alert level for the main graph
# implemented = yes
$glb_level=7;

# Default hours
# Defult hours count for the main graph
# implemeneted = yes
$glb_hours=72;

# Default graphbreakdown
# Default graph breakdown on page load
# implemented = yes
# options = [source|path|level|rule_id]
$glb_graphbreakdown="rule_id";

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
# Functionality now covered in management.php


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

# Highlighted words
# This is merely meant to highlight keywords in the breakdown page, it is not mean to replace you actually creating rules!
# cheekily taken from $BAD_WORDS 
# implemented = yes
$glb_autohighlight="root|admin|core_dumped|failure|error|attack|bad |illegal |denied|refused|unauthorized|fatal|failed|Segmentation Fault|Corrupted";

### Newsfeed.php

# Threat Booster
# This modifies how much of a boost the list, higher level alerts get (0.0-1.0)
# e.g. 0 Gives level 15 alert no real boost, level 1 gives them a high boost
# implemented = yes
$glb_threatbooster=0.9;

# Threat Days
# The amount of days back in time the thread list will look
# implemented = yes
$glb_threatdays=15;

# Threat limit
# How many alerts shown in the table
# implemented = yes
$glb_threatlimit=40;

# Trend Warning High
# At what level of normal should the trend be considered high?
# 1 means that if rule is 100% of average it will be alerted, 2 means the trend has to be 200% of aveage to be alerted
#implemented = yes
$glb_trendlimithigh=1.5;

# Trend Warning Low
# At what level of normal should the trend be considered low?
# 0.5 means that if rule is less than 50% of average it will be alerted, 0.1 means the trend has to be less than 10% of aveage to be alerted
#implemented = yes
$glb_trendlimitlow=0.99;

# Trend cutoff
# Set the lower level at which trend does not apply, i.e. if an alert hits 3 times compared to 1, that's a 300% jump, but may not be important.  Setting this to 10 means that these rare rules do not get shown
#implemented = yes
$glb_trendcutoff=10;

# Trend Weeks
# How many weeks back should the Trend tool look. Higher numbers are better, but make the query slower
# implemented = yes
$glb_trendweeks=10;

### OSSEC Database

# List of databases
# implemented = yes
# 'Main' is the default database used if there are any problems, make this your standard database
$glb_ossecdb['Main']="db_ossec.php";
#$glb_ossecdb['Secondary']="db_ossec2.php";

?>

<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

# This file is just about using custom colours for graphs.


$levelcolours = array(
	"level1" => "#aadd00",
	"level2" => "#b9db11",
	"level3" => "#c9da22",
	"level4" => "#d9d834",
	"level5" => "#e9d745",
	"level6" => "#f9d657",
	"level7" => "#f7c352",
	"level8" => "#f6b04d",
	"level9" => "#f59d48",
	"level10" => "#f48a43",
	"level11" => "#f2773f",
	"level12" => "#f1643a",
	"level13" => "#f05135",
	"level14" => "#ef3e30",
	"level15" => "#ef3e30"
);


# Assign colours to certain types of device
# Add/amend/customise at will
$groupcolour = array(
	"firewall" => "#ef3e30",
	"proxy" => "#f1643a",
	"mailserver" => "#f48a43",
	"genericserver" => "#f6b04d",
	"switch" => "#e9d745"
);
# Assign types of device to host names
# Populate at will
$devicegroup = array(
	"switch_accounts" => "switch",
	"switch_dmz" => "switch",
	"exchange" => "server",
	"samba" => "server"
);

	
# Colours from wikipedia as amcharts seem to repeat a bit
# http://en.wikipedia.org/wiki/List_of_colors
$randomcolour = array(
	"Bdazzled" => "#2E5894",
        "AirForceblue" => "#5D8AA8",
        "AlabamaCrimson" => "#A32638",
        "AlloyOrange" => "#C46210",
        "Amethyst" => "#9966CC",
        "AndroidGreen" => "#A4C639",
        "AntiqueBrass" => "#CD9575",
        "Ao" => "#008000",
        "Aqua" => "#00FFFF",
        "Arsenic" => "#3B444B",
        "BabyBlueEyes" => "#A1CAF1",
        "Beaver" => "#9F8170",
        "BrightGreen" => "#66FF00",
        "BrightPink" => "#FF007F",
        "BubbleGum" => "#FFC1CC",
        "Dark blue" => "#00008B",
        "DarkOrange" => "#FF8C00",
        "Gray" => "#808080",
        "CandyAppleRed" => "#FF0800",
        "Fuchsia" => "#C154C1",
        "Lemon" => "#FFFF00",
);


?>

<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

include './config.php';
include './functions.php';
include './db_ossec.php';

## This does work but I have commented as not everyone will have icinga installed.
## If you want to play uncomment this, and the link in index.php
## This part of the code currently only looks for hosts that came back up.
#include './db_icinga.php';

?>

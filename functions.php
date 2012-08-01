<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

function quote_smart($value){
        // Stripslashes
        if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
        }

        // Escape shite
        $value = htmlentities($value);

        return $value;
}


# glb_notrepresentedwhitelist
$glb_notrepresentedwhitelist_sql="";
foreach ($glb_notrepresentedwhitelist as $i){
	$glb_notrepresentedwhitelist_sql.=" AND location.name NOT LIKE '(".$i."%'";


}

?>

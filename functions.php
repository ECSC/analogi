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

        // Escape stuff
        $value = htmlentities($value);

        return $value;
}


# glb_notrepresentedwhitelist
$glb_notrepresentedwhitelist_sql="";
foreach ($glb_notrepresentedwhitelist as $i){
	$glb_notrepresentedwhitelist_sql.=" AND location.name NOT LIKE '(".$i."%'";


}

function includeme($file){

	include $file;

#	Not working, can't figure out exactly why though
#	if(!file_exists($file)){
#		echo "<span style='color:red'>Error: File ".$file." not found!</span>";
#	}else{
#		echo "yes";
#	}

}


function get_content($url){
    ## curl the google address -> latlng thing
    $ch = curl_init();

    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_HEADER, 0);

    ob_start();

    curl_exec ($ch);
    curl_close ($ch);
    $string = ob_get_contents();

    ob_end_clean();

    return $string;
}


function getIpRang(  $cidr) {
	# Thanks admin at wudimei dot com (http://php.net/manual/en/function.ip2long.php)

	list($ip, $mask) = explode('/', $cidr);
 
	$maskBinStr =str_repeat("1", $mask ) . str_repeat("0", 32-$mask );      //net mask binary string
	$inverseMaskBinStr = str_repeat("0", $mask ) . str_repeat("1",  32-$mask ); //inverse mask
  
	$ipLong = ip2long( $ip );
	$ipMaskLong = bindec( $maskBinStr );
	$inverseIpMaskLong = bindec( $inverseMaskBinStr );
	$netWork = $ipLong & $ipMaskLong; 

	$start = $netWork;// ignore network ID(eg: 192.168.1.0)
 
	$end = ($netWork | $inverseIpMaskLong) ; // ignore brocast IP(eg: 192.168.1.255)
	return array( $start, $end );
}

?>

<?php
/*
 * Copyright (c) 2012 Andy 'Rimmer' Shepherd <andrew.shepherd@ecsc.co.uk> (ECSC Ltd).
 * This program is free software; Distributed under the terms of the GNU GPL v3.
 */

# Change wallboard mode cookie
if(isset($_GET['wallboard'])){

	if($_GET['wallboard']=="1"){
		setcookie("wallboard_mode", "1", time()+(86400*20));	
		$wallboard_mode=1;
	}else{
		setcookie("wallboard_mode", "0", time()+(86400*20));
		$wallboard_mode=0;
	}

}else{

	if(isset($_COOKIE['wallboard_mode']) && $_COOKIE['wallboard_mode']=="1"){
		$wallboard_mode=1;
	}else{
		$wallboard_mode=0;
	}

}


if($wallboard_mode==1){
	$wallboard_url="<span class='tiny fright'>Wallboard Mode [<a href='".preg_replace("/&wallboard=(0|1)/", "", $_SERVER["REQUEST_URI"])."&wallboard=0'>On</a>]</span>";

	if(isset($_GET['slide']) && preg_match("/^[0-9]*$/",$_GET['slide'])){
		$slide=$_GET['slide']+1;

		$slidemax=count($glb_slidehow_pages);

		if($slide>$slidemax){

			$slide=1;
	
		}		

		$newpage=$glb_slidehow_pages[$slide];

	}else{
		$slide=1;

		$newpage=$glb_slidehow_pages[$slide];
	}

	echo "<meta http-equiv=\"refresh\" content=\"".$glb_autorefresh.";URL='".$newpage."&slide=".$slide."'\">";


}else{
	$wallboard_url="<span class='tiny fright'>Wallboard Mode [<a href='".preg_replace("/&wallboard=(0|1)/", "", $_SERVER["REQUEST_URI"])."&wallboard=1'>Off</a>]</span>";
	echo "<meta http-equiv=\"refresh\" content=\"".$glb_autorefresh."\" >";
}

?>

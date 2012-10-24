<div style='padding:40px ;width:95%; text-align:center;'>
	<a class='tiny' href='http://www.ecsc.co.uk/analogi.html'>ECSC | Vendor Independent Information Security Specialists</a>
	<?php
	if($glb_debug==1){
		$endtime = microtime();
		$endarray = explode(" ", $endtime);
		$endtime = $endarray[1] + $endarray[0];
		$totaltime = $endtime - $starttime;
		$totaltime = round($totaltime,2);
		echo "&nbsp;|&nbsp;<span class='tiny'>".$totaltime."s</span>";
	}
	?>
</div>

</body>
</html>


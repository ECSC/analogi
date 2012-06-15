<div class="fleft top10header" style='margin-top:10px;margin-bottom:10px;'>
	<div style='font-weight: bold'>
		<span style='color:rgb(0,84,130)'>Ana</span><span style='color:rgb(237,28,36); font-style:italic'>Log</span><span style='color:rgb(0,84,130)'>i</span>
	</div>
	<div class='tiny'>
		<span class="tiny">a&#183;nal&#183;o&#183;gi [uh-nal-uh-jee] noun. a similarity between like features of two things, on which a comparison may be based</span>
	</div>
</div>



<div class="fright" style='margin-top:10px;margin-bottom:10px;' >
	<?php
	if(count($glb_ossecdb)>1){
		echo "
		<form action='./index.php'>
			<select name='glb_ossecdb' onchange='document.cookie=\"ossecdbjs=\"+glb_ossecdb.options[selectedIndex].value ; location.reload(true)'>";

			foreach ($glb_ossecdb as $name => $file){
				if($_COOKIE['ossecdbjs'] == $name){
					$glb_ossecdb_selected=" SELECTED ";
				}else{
					$glb_ossecdb_selected="";
				}
				$glb_ossecdb_option.="<option value='".$name."' ".$glb_ossecdb_selected." >".$name." (".DB_NAME_O.", ".DB_HOST_O.")</option>";
			}
			echo $glb_ossecdb_option;
		echo "</select>
		</form>";
	}
	?>
</div>


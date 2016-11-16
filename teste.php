<?php
	if(isset($_GET['ligne'])){
	$ligne = "10";	
		if(file_exists("base_conf.php")){
			include_once("base_conf.php");
			include_once("requetes.php");
			include_once("mod_traitement.php");
			echo "lol";

		}
		
	}
?>
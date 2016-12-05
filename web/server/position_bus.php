<?php
	// header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type, Authorization, X-Requested-With,Access-Control-Allow-Credentials,Access-Control-Allow-Origin');
	// 	header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
	// 	header('Access-Control-Allow-Credentials: true');
	// 	header('Access-Control-Allow-Methods: REQUEST, GET, POST');

//$_POST['ligne']="10";
	if(isset($_GET['ligne'])){
	$ligne = "10";	
		
		if(file_exists("base_conf.php")){
				include_once("base_conf.php");
				include_once("requetes.php");
				include_once("mod_traitement.php");


				$base = new BaseDD(HOSTNAME,BASENAME,USERNAME,PASSWORD);

				$resultats= array(
					'allee' => Controller::selectNearBusTerminus($base, $ligne, "A"),
					'retour' => Controller::selectNearBusTerminus($base, $ligne, "R") 
					
				);


				//$resultats['retour'] = "{'bus':{'id_bus':'$id_bus','matricule':'$matricule','ligne':'$nom_ligne'},'position':[$latitude,$longitude]}";
				

				echo json_encode($resultats);

				//echo "</div>";
			}
		
	}
?>

<?php
	// header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type, Authorization, X-Requested-With,Access-Control-Allow-Credentials,Access-Control-Allow-Origin');
	// header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
	// header('Access-Control-Allow-Credentials: true');
	// header('Access-Control-Allow-Methods: REQUEST, GET, POST');

	//$_POST['ligne'] = "10";

	if(isset($_GET['ligne'])){
		
		
		if(file_exists("base_conf.php")){
			include_once("base_conf.php");
			include_once("mod_traitement.php");

			$base = "-1";
			$req1 = "mysql:host=" . HOSTNAME . ";dbname=" . BASENAME . "";
			$req2 = "SET NAMES UTF8";

			try{
				$base = new PDO($req1, USERNAME, PASSWORD);
				$req = $base->prepare($req2);
				$req->execute();

			}catch(Exception $e){
			}

			if($base != "-1"){

				//rechercher tous les bus de la ligne
				$req = "SELECT * FROM ligne,arretLigne,arret WHERE ligne.nom_ligne=:ligne AND ligne.id_ligne=arretLigne.id_ligne AND arret.id_arret=arretLigne.id_arret";
				$req = $base->prepare($req);
				$req->execute(array(
					":ligne" => $_GET['ligne']
					));
				$bus = $req->fetchall(PDO::FETCH_NUM);
				//echo $_POST['ligne'];
				/*id_ligne
				nom_ligne
				terminus1
				terminus2
				id_arretligne
				id_arret
				id_ligne
				sens
				num_arretDansLigne
				id_arret
				nom_arret
				latitude_arret
				longitude_arret
				*/
				
				//print_r($bus);

				$resultats = "";
				for($i=0; $i<count($bus); $i++){
					if($i!=0)
						$resultats .= "*";

					for($ii=0; $ii<13; $ii++){
						$resultats .= $bus[$i][$ii] . "_";
					}
					$resultats .= $bus[$i][13];
					
				}
				echo $resultats;
			}
		}
	}
?>
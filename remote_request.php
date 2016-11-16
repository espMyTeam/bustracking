<?php
	/* controller de bus tracking*/

	//$_GET['donnees'] = "bus 10111dk ligne 10 14.681384 -17.466691 16.4000 2.037200 2 11502200 260416 773675372";


	if(isset($_GET['donnees']) && $_GET['donnees'] != "" && file_exists("base_conf.php") && file_exists("requetes.php")){
		
		include_once("base_conf.php");
		include_once("requetes.php");
		include_once("mod_traitement.php");

		$sms = explode(" ", $_GET['donnees']);
		$message = $_GET['donnees'];
		/*if(isset($_GET['num']) && $_GET['num'] != "")
			$numero = $_GET['num'];
		else*/
		$numero = $sms[count($sms)-1];
		
		$base = new BaseDD(HOSTNAME,BASENAME,USERNAME,PASSWORD);

		/* ajouter le sms dans la base */
		$base->addRecvSms($message, $numero);


		/* extraire les informations du message recu */
		$matricule = $sms[1];
		$id_bus = $base->selectBusByMatricule($matricule)[0][0];


		if($id_bus != -1){
			//ajouter nouvelle position dans la base de données
			$ladate = "20" . $sms[10][4] . $sms[10][5] . "-" . $sms[10][2] . $sms[10][3] . "-" . $sms[10][0] . $sms[10][1];
			$lheure = $sms[9][0] . $sms[9][1] . ":" . $sms[9][2] . $sms[9][3] . ":" . $sms[9][4] . $sms[9][5];
			
			$position = $base->addPositionBus($id_bus, $sms[4], $sms[5], $sms[6], $sms[7], $ladate, $lheure);

			//mettre à jour la position courante du bus
			$base->modifyBus($id_bus, $position);

			//mettre à jour le sens s'il le faut
			Controller::updateBusSens($base, $id_bus, doubleval($sms[4]), doubleval($sms[5]));

		}
		
		//echo "bien reussie";
		
	}

	
?>
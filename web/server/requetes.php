<?php

	/* classe de base pour les requetes aux bases de données */
	Class BaseDD{
		private $base;
		public $running = False;
		private $baseName;
		private $userName;
		private $password;
		private $hostName;


		public function __construct($hostName, $baseName, $userName, $password){
			$this->baseName = $baseName;
			$this->userName = $userName;
			$this->hostName = $hostName;
			$this->password = $password;
			$this->connect();
		}

		/*
			connexion à la base 
		*/
		private function connect(){

				$req1 = "mysql:host=" . $this->hostName . ";dbname=" . $this->baseName . "";
				$req2 = "SET NAMES UTF8";

				try{
					$this->base = new PDO($req1, $this->userName, $this->password);
					$req = $this->base->prepare($req2);
					$req->execute();
					$this->running = True;

				}catch(Exception $e){
					$this->running = False;
				} 
		}

		function deconnect(){
			try{
				$this->close();
				$this->running = False;
			}catch(Exception $e){

			}
			
		}

		/* requete de selection */
		private function select($req, $array_params, $methode=PDO::FETCH_NUM){
			$req = $this->base->prepare($req);
			$req->execute($array_params);
			return $req->fetchall($methode);
		}

		/* requete d'insertion */
		private function insert($req, $array_params){
			$req = $this->base->prepare($req);
			$req->execute($array_params);
			return $this->base->lastInsertId();
		}

		private function update($req, $array_params){
			$req = $this->base->prepare($req);
			$req->execute($array_params);
		}

		private function delete($req, $array_params){
			$req = $this->base->prepare($req);
			$req->execute($array_params);
		}

		/*
			fonctions associées à la base de données
		*/

		/* selectionner un arret */
		function selectArret($id_arret){
			$req = "SELECT * FROM arret WHERE id_arret=:id_arret";
			$array_params = array(
				"id_arret" => $id_arret
			);
			return $this->select($req, $array_params);
		}


		function selectArretLigne($id_ligne, $id_arret_ligne, $sens){
			$req = "SELECT arret.id_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.id_arretligne FROM arret,arretLigne WHERE arretLigne.sens=:sens AND arretLigne.id_ligne=:id_ligne AND arret.id_arret=arretLigne.id_arret AND arretLigne.num_arretDansLigne=:num";
			$array_params = array(
				":id_ligne" => $id_ligne,
				":sens" => $sens,
				":num" => $id_arret_ligne
			);
			return $this->select($req, $array_params);
		}

		function selectTerminus($id_ligne, $sens_bus){
			if($sens_bus == "A") //vers palais
				$sens = "R";
			else
				$sens = "A";

			$req = "SELECT arret.id_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.id_arretligne FROM arret,arretLigne WHERE arretLigne.sens=:sens AND arretLigne.id_ligne=:id_ligne AND arret.id_arret=arretLigne.id_arret AND arretLigne.num_arretDansLigne=0";
			$array_params = array(
				":id_ligne" => $id_ligne,
				":sens" => $sens
			);
			return $this->select($req, $array_params);
		}

		/* selectionner l'arret ESP correspondant au sens donne en parametre */
		function selectArretEsp($id_ligne, $sens){
			if($sens == "A")
				$num = 17;
			else
				$num = 11;

			$req = "SELECT arret.id_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.id_arretligne FROM arret,arretLigne WHERE arretLigne.sens=:sens AND arretLigne.id_ligne=:id_ligne AND arret.id_arret=arretLigne.id_arret AND arretLigne.num_arretDansLigne=:num";
			$array_params = array(
				":id_ligne" => $id_ligne,
				":sens" => $sens,
				":num" => $num
			);
			return $this->select($req, $array_params);
		}

		/* selectionner un bus */
		function selectBus($id_bus, $methode=PDO::FETCH_NUM){ 
			$req = "SELECT * FROM bus WHERE id_bus=:id_bus";
			$array_params = array(
				"id_bus" => $id_bus
			);
			return $this->select($req, $array_params, $methode);
		}

		function selectAllBusLigneSens($nom_ligne, $sens, $methode=PDO::FETCH_NUM){
			$req = "SELECT bus.id_bus,matricule_bus,nom_ligne,latitude,longitude,altitude,vitesse,ladate,heure,sens_bus,toterminus FROM bus,positionBus WHERE nom_ligne=:nom_ligne AND positionBus.id_positionBus=bus.position_courant AND bus.sens_bus=:sens";
				
			$array_params = array(
				":nom_ligne" => $nom_ligne,
				":sens" => $sens
			);
			return $this->select($req, $array_params, $methode);
		}

		/* selectionner le bus grace à son matricule */
		function selectBusByMatricule($matricule){
			$req = "SELECT * FROM bus WHERE matricule_bus=:mat";
			$array_params = array(
				":mat" => $matricule
			);
			return $this->select($req, $array_params);
		}

		function selectBusByLigneName($nom_ligne){
			$req = "SELECT bus.id_bus,matricule_bus,nom_ligne,latitude,longitude,altitude,vitesse,ladate,heure,sens_bus FROM bus,positionBus WHERE nom_ligne=:nom_ligne AND positionBus.id_positionBus=bus.position_courant";
				
			$array_params = array(
				":nom_ligne" => $nom_ligne
			);
			return $this->select($req, $array_params);
		}



		/* selectionner une ligne */
		function selectLigne($id_ligne){
			$req = "SELECT * FROM ligne WHERE id_ligne=:id_ligne";
			$array_params = array(
				"id_ligne" => $id_ligne
			);
			return $this->select($req, $array_params);
		}


		function selectArretsLigne($nom_ligne, $sens = "-1"){
			if($sens == "-1" or $sens == ""){
				$req = "SELECT * FROM arretLigne WHERE id_ligne=:id_ligne";
				$array_params = array(
					"id_ligne" => $id_ligne
				);
			}else{
				$req = "SELECT * FROM arretLigne WHERE id_ligne=:id_ligne AND sens=:sens";
				$array_params = array(
					":id_ligne" => $id_ligne,
					":sens" => $sens
				);
			}
			
			return $this->select($req, $array_params);
		}

		function selectArretsLigneRestant($id_ligne, $sens, $num_ref){
			if($sens == "A"){
				$req = "SELECT arret.id_arret,arret.nom_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.num_arretDansLigne,arretLigne.distance_tonext FROM arret, arretLigne WHERE arret.id_arret=arretLigne.id_arret AND id_ligne=:id_ligne AND sens='A' AND num_arretDansLigne>=:num_ref AND num_arretDansLigne<17";
				$array_params = array(
					"id_ligne" => $id_ligne,
					":num_ref" => $num_ref
				);
			}else{
				$req = "SELECT arret.id_arret,arret.nom_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.num_arretDansLigne,arretLigne.distance_tonext FROM arret, arretLigne WHERE arret.id_arret=arretLigne.id_arret AND id_ligne=:id_ligne AND sens='R' AND num_arretDansLigne>=:num_ref AND num_arretDansLigne<11";
				$array_params = array(
					":id_ligne" => $id_ligne,
					":num_ref" => $num_ref
				);
			}
			
			return $this->select($req, $array_params);
		}

		function selectArretsLigneToTerm($id_ligne, $sens, $num_ref){
			if($sens == "A"){
				$req = "SELECT arret.id_arret,arret.nom_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.num_arretDansLigne,arretLigne.distance_tonext FROM arret, arretLigne WHERE arret.id_arret=arretLigne.id_arret AND id_ligne=:id_ligne AND sens='A' AND num_arretDansLigne>=:num_ref";
				$array_params = array(
					"id_ligne" => $id_ligne,
					":num_ref" => $num_ref
				);
			}else{
				$req = "SELECT arret.id_arret,arret.nom_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.num_arretDansLigne,arretLigne.distance_tonext FROM arret, arretLigne WHERE arret.id_arret=arretLigne.id_arret AND id_ligne=:id_ligne AND sens='R' AND num_arretDansLigne>=:num_ref";
				$array_params = array(
					":id_ligne" => $id_ligne,
					":num_ref" => $num_ref
				);
			}
			
			return $this->select($req, $array_params);
		}

		function selectAllArret(){
			$req = "SELECT * FROM arret";
			$array_params = array(		
			);
			
			return $this->select($req, $array_params);
		}

		

		function selectAllArretBusAnc(){
			$req = "SELECT * FROM arretBus";
			$array_params = array(		
			);
			
			return $this->select($req, $array_params);
		}

		/* selectionner tous les bus dans la zone */
		function selectAllBusZones($delta_lat, $delta_lng, $ligne, $lat_ref, $lng_ref, $sens){

			$req = "SELECT bus.id_bus,positionBus.latitude,positionBus.longitude FROM bus,positionBus WHERE bus.position_courant=positionBus.id_positionBus AND bus.sens_bus=:sens AND bus.nom_ligne=:ligne AND positionBus.latitude<=:max_lat AND positionBus.latitude >=:min_lat AND positionBus.longitude<=:max_lng AND positionBus.longitude>=:min_lng";
			$array_params = array(	
				":ligne" => $ligne,
				":max_lat" => $lat_ref+$delta_lat,
				":min_lat" => $lat_ref-$delta_lat,
				":max_lng" => $lng_ref+$delta_lng,
				":min_lng" => $lng_ref-$delta_lng,
				":sens" => $sens
			);
			
			return $this->select($req, $array_params);
		}

		/* selectionner tous les arrets de la zone sur la ligne demandee */
		function selectAllArretZones($delta_lat, $delta_lng, $id_ligne, $lat_ref, $lng_ref, $sens){

			$req = "SELECT arret.id_arret,arret.nom_arret,arret.latitude_arret,arret.longitude_arret,arretLigne.num_arretDansLigne,arretLigne.distance_tonext FROM arretLigne, arret WHERE arret.id_arret=arretLigne.id_arret AND arretLigne.sens=:sens AND arretLigne.id_ligne=:id_ligne AND arret.latitude_arret<=:max_lat AND arret.latitude_arret >=:min_lat AND arret.longitude_arret<=:max_lng AND arret.longitude_arret>=:min_lng";
			$array_params = array(	
				":id_ligne" => $id_ligne,
				":max_lat" => $lat_ref+$delta_lat,
				":min_lat" => $lat_ref-$delta_lat,
				":max_lng" => $lng_ref+$delta_lng,
				":min_lng" => $lng_ref-$delta_lng,
				":sens" => $sens
			);
			
			return $this->select($req, $array_params);
		}

		function selectArretsProches($lat, $lng, $id_ligne){
			$req = "SELECT * FROM arretLigne WHERE id_ligne=:id_ligne AND ";
			$array_params = array(
				"id_ligne" => $id_ligne
			);
			return $this->select($req, $array_params);
		}

		/* supprimer un bus */
		function deleteBus($id_bus){
			$req = "DELETE FROM bus WHERE id_bus=:id_bus";
			$array_params = array(
				"id_bus" => $id_bus
			);
			$this->delete($req, $array_params);
		}

		/* supprimer une ligne */
		public function deleteLigne($id_ligne){
			$req = "DELETE FROM ligne WHERE id_ligne=:id_ligne";
			$array_params = array(
				"id_ligne" => $id_ligne
			);
			return $this->delete($req, $array_params);
		}

		/*
			jouter un bus
		*/


		function addBus($matricule_bus, $nom_ligne='10', $position_courant=0, $sens_bus='R', $toterminus = 0){
			$req = "INSERT INTO bus(matricule_bus,position_courant,nom_ligne,sens_bus, toterminus) VALUES(:matricule_bus,:position_courant,:nom_ligne,:sens_bus,:toterminus);";
			$array_params = array(
				":matricule_bus" => $matricule_bus,
				":position_courant" => $position_courant,
				":nom_ligne" => $nom_ligne,
				":sens_bus" => $sens_bus,
				":toterminus" => $toterminus
			);
			return $this->insert($req, $array_params);
		}

		/*
			ajouter une position de bus
		*/
		function addPositionBus($id_bus, $latitude, $longitude, $altitude, $vitesse, $lheure, $ladate){
			$req = "INSERT INTO positionBus(id_bus,latitude, longitude,altitude,vitesse,ladate,heure) VALUES(:id_bus, :latitude, :longitude, :altitude, :vitesse, :lheure, :ladate);";
			$array_params = array(
				":latitude" => $latitude,
				":longitude" => $longitude,
				":altitude" => $altitude,
				":vitesse" => $vitesse,
				":lheure" => $lheure,
				":ladate" => $ladate,
				":id_bus" => $id_bus
			);
			return $this->insert($req, $array_params);
		}

		/* ajouter une ligne */
		// function addLigne($nom, $prix_unitaire, $type_produit, $photo){
		// 	$req = "INSERT INTO produit VALUES(:nom,:prix_unitaire,:type_produit,:photo);";
		// 	$array_params = array(
		// 		":nom" => $nom,
		// 		":prix_unitaire" => $prix_unitaire,
		// 		":type_produit" => $type_produit,
		// 		":photo" => $photo
		// 	);
		// 	return $this->insert($req, $array_params);
		// }

		/* ajouetr un sms recu*/
		function addRecvSms($sms, $dest){
			$req = "INSERT INTO smsRecv(contenu, emetteur, ladate, heure) VALUES(:contenu, :emetteur, :ladate, :heure)";
			$dat = Date("Y-m-d");
			$heur = Date("H:i:s");
			$array_params = array(
				":contenu" 		=> $sms,
				":emetteur" 	=> $dest,
				":ladate"		=> $dat,
				":heure"		=> $heur
			);
			return $this->insert($req, $array_params);
		}

		/* modifier le bus */
		function modifyBus($id_bus, $position="-1", $sens="-1"){

			if($position != -1){
				if($sens != -1){
					$req = "UPDATE bus SET sens_bus=:sens, position_courant=:position WHERE id_bus=:id_bus";
					$array_params = array(
						":sens" => $sens,
						":position" => $position,
						":id_bus" => $id_bus
					);
				}else{
					$req = "UPDATE bus SET position_courant=:position WHERE id_bus=:id_bus";
					$array_params = array(
						":position" => $position,
						":id_bus" => $id_bus
					);
				}
			}else{
				if($sens != -1){
					$req = "UPDATE bus SET sens_bus=:sens WHERE id_bus=:id_bus";
					$array_params = array(
						":sens" => $sens,
						":id_bus" => $id_bus
					);
				}else{

				}
			}
			
			$this->update($req, $array_params);
		}

		/* modifier la distance restante entre le bus et le terminus */
		function modifyBusTermD($id_bus, $toterminus){

			$req = "UPDATE bus SET toterminus=:toterminus WHERE id_bus=:id_bus";
			$array_params = array(
						":toterminus" => $toterminus,
						":id_bus" => $id_bus
			);
			
			$this->update($req, $array_params);
		}

		function modifyArret($id_arret, $lat, $lng){
			$req = "UPDATE arret SET latitude_arret=:lat, longitude_arret=:lng WHERE id_arret=:id_arret";
			$array_params = array(
						":lat" => $lat,
						":lng" => $lng,
						":id_arret" => $id_arret
						
			);
				
			
			$this->update($req, $array_params);
		}

		function modifyArretByName($nom_arret, $lat, $lng){
			$req = "UPDATE arret SET latitude_arret=:lat, longitude_arret=:lng WHERE nom_arret=:nom_arret";
			$array_params = array(
						":lat" => $lat,
						":lng" => $lng,
						":nom_arret" => $nom_arret
						
			);
				
			
			$this->update($req, $array_params);
		}

		function getIdLigne($nom_ligne){
			$req = "SELECT * FROM ligne WHERE nom_ligne=:nom_ligne";
			$array_params = array(
				"nom_ligne" => $nom_ligne
			);
			$val = $this->select($req, $array_params);
			$val = $val[0][0];
			return intval($val);
		}



	}

?>

<?php

	class GPS{

		/* calculer la distance entre deux points GPS */
		static function distance($lat1, $lng1, $lat2, $lng2){
			$delta = deg2rad($lng1-$lng2);
			$sdlong = sin($delta);
			$cdlong = cos($delta);
			$lat1 = deg2rad($lat1); 
			$lat2 = deg2rad($lat2);
			$slat1 = sin($lat1);
			$clat1 = cos($lat1);
			$slat2 = sin($lat2);
			$clat2 = cos($lat2);
			$delta = ($clat1 * $slat2) - ($slat1 * $clat2 * $cdlong);
			$delta = pow($delta, 2);
			$delta += pow($clat2*$sdlong, 2);
			$delta = sqrt($delta);
			$denom = ($slat1 * $slat2) + ($clat1 * $clat2 * $cdlong);
			$delta = atan2($delta, $denom);
			return $delta * 6372795;
		}

		/* calcul de la direction */
		static function course($lat1, $lng1, $lat2, $lng2){
			$dlon = deg2rad($lng2-$lng1);
			$lat1 = deg2rad($lat1);
			$lat2 = deg2rad($lat2);
			$a1 = sin($dlon) * cos($lat2);
			$a2 = sin($lat1) * cos($lat2) * cos($dlon);
			$a2 = cos($lat1) * sin($lat2) - $a2;
			$a2 = atan2($a1, $a2);
			if($a2 < 0.0)
				$a2 += 2*M_PI;
		  	return rad2deg(a2);
		}
		


		static function convertirLat($deg_lat,$min_lat,$sec_lat, $orientation){
			$lat = $deg_lat + $min_lat/60.0 + $sec_lat/3600.0;
			if($orientation == "S") $lat = -1 * $lat;

			return $lat;
		}
		
		static function convertirLong($deg_lng,$min_lng,$sec_lng, $orientation){
			$lng = $deg_lng + $min_lng/60.0 + $sec_lng/3600.0;
			if($orientation == "W") $lng = (-1) * $lng;

			return $lng;
		}

		/*
				retourne la latitude et la longitude minimale à ajouter à un point pour avoir la distance donnée
				On prend un point de reference (latitude, longitude). On cherche la latitude a ajouter pour avoir un point de même longitude situé à distance distance_ref.
				Puis on cherche, la longitude à ajouter pour avoir un point de même latitude à une distance distance_ref
		*/
		static function getAreaLong($distance_ref, $precision = 0.000001){
			
			$lat_ref= 14.432479;
			$lng_ref = 17.273171;
			$delta_lng = 0;
			$temp = 0;
			while(1){
				$delta_lng += $precision;
				$temp = GPS::distance($lat_ref, $lng_ref, $lat_ref, $lng_ref+$delta_lng);
				if($temp >= $distance_ref)
					break;
			}
			return $delta_lng;
		}

		static function getAreaLat($distance_ref, $precision = 0.000001){
			$lat_ref= 14.432479;
			$lng_ref = 17.273171;
			$delta_lat = 0;
			$temp = 0;
			while(1){
				$delta_lat += $precision;
				$temp = GPS::distance($lat_ref, $lng_ref, $lat_ref+$delta_lat, $lng_ref);
				if($temp >= $distance_ref)
					break;
			}
			return $delta_lat;
		}
	}

	
	class Controller{


		static function separeMsg($msg, $separateur=" "){
			return explode($separateur, $msg);
		}

		static function formatteMsg($array_infos){

		}

		static function selectNearBusTerminus($base, $ligne, $sens){
				//les indicateurs pour la formulation du message
				$flag_nb_arrets_restants = -1;
				$flag_next_bus = false;
				$flag_un_autre_bus_arrive = false;

				//selectionner le bus le plus proche du terminus
				$bus = Controller::getNearBus($base, 1, $sens);
				

				if(empty($bus)){
					return array("message" => Controller::formuleMessage($flag_nb_arrets_restants, $flag_next_bus, $flag_un_autre_bus_arrive));
				}else{

					$flag_next_bus = true;

					$i=0;
					$id_bus = $bus[0];
					$matricule = $bus[1];
					$nom_ligne = $bus[2];
					$latitude = $bus[3];
					$longitude = $bus[4];
					$altitude = $bus[5];
					$vitesse = $bus[6];
					$ladate = $bus[7];
					$heure = $bus[8];
					$sens_bus = $bus[9];

					//determiner l'arret le plus proche
					$delta_lat = GPS::getAreaLat(MAX_DISTANCE);
					$delta_lng = GPS::getAreaLong(MAX_DISTANCE);
					$lat_ref = doubleval($latitude);
					$lng_ref = doubleval($longitude);

					$arrets_zone = $base->selectAllArretZones($delta_lat, $delta_lng, 1, $lat_ref, $lng_ref, $sens);

					//determine l'arret le plus proche
					if(!empty($arrets_zone)){
						$ds_inc = 30;
						$near_stop_id = -1;

						for($j=0; $j<=MAX_DISTANCE; $j = $j + $ds_inc){
							for($i=0; $i<count($arrets_zone); $i++){
								
								if(GPS::distance($lat_ref, $lng_ref, doubleval($arrets_zone
									[$i][2]), doubleval($arrets_zone[$i][3])) - $j <= 10){
									$near_stop_id = $i;

									break;
								}
							}
							
							if($near_stop_id != -1) break;

						}
					}
					
					$distance_restante = 0;
					$nb = -1;
					if($near_stop_id != -1){
						$nb = 0;
						$near_arret = $arrets_zone[$near_stop_id];

							
						//selectionner tous les arrets de la ligne
						$all_arrets = $base->selectArretsLigneRestant(1, $sens_bus, $near_arret[4]);

						//approximer la distante restante pour arriver par rapport 
						//on parcours les arrets. si un arret n'est pas encore traversé, alors ajouter la distance restante
						for($ii = 0; $ii<count($all_arrets); $ii++){
							//if(intval($all_arrets[$ii][4]) <= intval($near_arret[4])){
							//	echo "arret " . $arrets_allee[$ii][1] . "\n";
								$distance_restante += doubleval($all_arrets[$ii][5]); 
								$nb++;
							//}
						}
					}

					if(!isset($near_arret))
						$near_arret = "";
					else{
						//$nb = $nb-1;
						//if($nb==-1)
						//	$nb=0;


						if($nb == 0){ //voir si le bus a dépassé l'arret ESP ou non
							if($sens_bus == "A")
								$temp_val_bus = Controller::isBusBetween($base,$bus, 15, 16, "A");
							else if($sens_bus == "R")
								$temp_val_bus = Controller::isBusBetween($base,$bus, 10, 11, "R");


							if($temp_val_bus == false )
								$nb = -1;

						}


						$near_arret = array(
							"id_arret" => $near_arret[0],
							"nom" => $near_arret[1],
							"latitude" => $near_arret[2],
							"longitude" => $near_arret[3],
							"nb_arrets_restants" => $nb //nombre d'arrets restants
						);

						$flag_nb_arrets_restants = $nb;
					}			
					$autres_bus = $base->selectAllBusLigneSens($ligne, $sens);
					$flag_un_autre_bus_arrive = isset($autres_bus[1])?true:false;

					$resultats = array(
						"ligne" => $nom_ligne, //nom de la ligne
						"sens" => $sens_bus, //le sens
						"near_bus" => array( //le bus le plus proche de l'arret ESP dans le sens choisi
							"id_bus" => $id_bus, 
							"matricule" => $matricule,
							"position" => array(
								'latitude' => $latitude,
								'longitude' => $longitude,
								'altitude' => $altitude
							),
							"vitesse" => $vitesse, //vitesse du bus
							"distante_restante" => $distance_restante, //distance entre le bus et l'arret ESP
							"next_arret" => $near_arret //le prochain arret
						),
						"bus" => Controller::retireElement(0,$id_bus, $autres_bus), /* les autres bus sur la route */
						"arrets" => Controller::retireElement(0,$near_arret['id_arret'],$all_arrets), //les restants pour atteindre l'arret ESP,
						"arret_esp" => $base->selectArretEsp(1, $sens, PDO::FETCH_ASSOC), //arret esp
						"message" => Controller::formuleMessage($flag_nb_arrets_restants, $flag_next_bus, $flag_un_autre_bus_arrive)
					);
					
					return $resultats;

				}
		}

		static function updateSensBus($base, $ligne, $sens){
				//selectionner le bus le plus proche du terminus
				$bus = Controller::getNearBusTerminus($base, 1, $sens);
				
				if(empty($bus)){
					return [];
				}else{
					//$bus = $req->fetchall(PDO::FETCH_NUM);
					$i=0;
					$id_bus = $bus[0];
					$matricule = $bus[1];
					$nom_ligne = $bus[2];
					$latitude = $bus[3];
					$longitude = $bus[4];
					$altitude = $bus[5];
					$vitesse = $bus[6];
					$ladate = $bus[7];
					$heure = $bus[8];
					$sens_bus = $bus[9];

					//determiner l'arret le plus proche
					$delta_lat = GPS::getAreaLat(MAX_DISTANCE);
					$delta_lng = GPS::getAreaLong(MAX_DISTANCE);
					$lat_ref = doubleval($latitude);
					$lng_ref = doubleval($longitude);

					$arrets_zone = $base->selectAllArretZones($delta_lat, $delta_lng, 1, $lat_ref, $lng_ref, $sens);

					//determine l'arret le plus proche
					$ds_inc = 10;
					$near_stop_id = -1;

					for($j=0; $j<=MAX_DISTANCE; $j = $j + $ds_inc){
						for($i=0; $i<count($arrets_zone); $i++){
							
							if(GPS::distance($lat_ref, $lng_ref, doubleval($arrets_zone
								[$i][2]), doubleval($arrets_zone[$i][3])) - $j <= 10){
								$near_stop_id = $i;

								break;
							}
						}
						
						if($near_stop_id != -1) break;

					}
					
					$distance_restante = 0;
					$nb = 0;
					if($near_stop_id != -1){
						
						$near_arret = $arrets_zone[$near_stop_id];

							
						//selectionner tous les arrets de la ligne
						$all_arrets = $base->selectArretsLigneRestant(1, $sens_bus, $near_arret[4]);

						//approximer la distante restante pour arriver par rapport 
						//on parcours les arrets. si un arret n'est pas encore traversé, alors ajouter la distance restante
						for($ii = 0; $ii<count($all_arrets); $ii++){
							//if(intval($all_arrets[$ii][4]) <= intval($near_arret[4])){
							//	echo "arret " . $arrets_allee[$ii][1] . "\n";
								$distance_restante += doubleval($all_arrets[$ii][5]); 
								$nb++;
							//}
						}
					}

					if(!isset($near_arret))
						$near_arret = "";
					else{
						$nb = $nb-1;
						if($nb==-1)
							$nb=0;
						if(nb==0){
							if($sens_bus == "A")
								$base->modifyBus($id_bus, '-1', "R");
							else
								$base->modifyBus($id_bus, '-1', "A");
						}
					}			
				}
		}


		/*
		* selectionner le bus le plus proche de l'arret specifié . Si un arret n'est pas precisé, alors on prend le terminus
		* 
		*/
		static function getNearBus($base, $id_ligne, $sens){
			//selectionner le terminus
			
			$terminus = $base->selectArretEsp($id_ligne, $sens)[0];

			//selectionner tous les bus 
			$lat_ref = doubleval($terminus[1]);
			$lng_ref = doubleval($terminus[2]);

			$all_bus = $base->selectAllBusLigneSens("10", $sens);

			if(!empty($all_bus)){
				$ds_inc = 10;
				$near_bus_id = -1;
				for($j=0; $j<=20000; $j = $j + $ds_inc){
					for($i=0; $i<count($all_bus); $i++){
						
						if(GPS::distance($lat_ref, $lng_ref, doubleval($all_bus[$i][3]), doubleval($all_bus[$i][4])) - $j <= $ds_inc){
							$near_bus_id = $i;

							break;
						}
					}
					
					if($near_bus_id != -1) break;

				}
				if($near_bus_id != -1){
					return $all_bus[$near_bus_id];
				}else{
					return [];
				}

			}else
				return [];
			
		}

				/*
		* selectionner le bus le plus proche de l'arret specifié . Si un arret n'est pas precisé, alors on prend le terminus
		* 
		*/
		static function getNearBusToArret($base, $id_ligne, $sens, $arret=null){
			//selectionner le terminus
			$res = array(
				"message" => "Pas de bus proche vers l'ESP.",
				"statut" => -1,
				"proche" => null
			);

			if($arret == null){

			}
			else{
				$lat_ref = doubleval($arret['latitude_arret']);
				$lng_ref = doubleval($arret['longitude_arret']);
				$all_bus = $base->selectAllBusLigneSens("10", $sens);

				if(!empty($all_bus)){
					$ds_inc = 10;
					$near_bus_id = -1;
					for($j=0; $j<=20000; $j = $j + $ds_inc){
						for($i=0; $i<count($all_bus); $i++){
							
							if(GPS::distance($lat_ref, $lng_ref, doubleval($all_bus[$i][3]), doubleval($all_bus[$i][4])) - $j <= $ds_inc){
								$near_bus_id = $i;

								break;
							}
						}
						
						if($near_bus_id != -1) break;

					}
					if($near_bus_id != -1){
						$res["proche"] = $all_bus[$near_bus_id];
						$res["statut"] = $near_bus_id;
					}

				}
			}

			return $res;
		}

		static function getNearBusTerminus($base, $id_ligne, $sens){
			//selectionner le terminus
			$terminus = $base->selectTerminus($id_ligne, $sens)[0];

			//selectionner tous les bus 
			$lat_ref = doubleval($terminus[1]);
			$lng_ref = doubleval($terminus[2]);

			$all_bus = $base->selectAllBusLigneSens("10", $sens);

			if(!empty($all_bus)){
				$ds_inc = 10;
				$near_bus_id = -1;
				for($j=0; $j<=20000; $j = $j + $ds_inc){
						for($i=0; $i<count($all_bus); $i++){
							
							if(GPS::distance($lat_ref, $lng_ref, doubleval($all_bus[$i][3]), doubleval($all_bus[$i][4])) - $j <= $ds_inc){
								$near_bus_id = $i;

								break;
							}
						}
						
						if($near_bus_id != -1) break;

				}
				if($near_bus_id != -1){
					return $all_bus[$near_bus_id];
				}else{
					return [];
				}
			}else
				return [];

			
		}

		/**
		* Mettre à jour le sens du bus
		*/
		static function updateBusSens($base, $id_bus, $new_lat, $new_lng){
			//selectionner le bus
			$bus = $base->selectBus($id_bus, PDO::FETCH_ASSOC)[0];
			
			$sens_bus = $bus['sens_bus'];

			//selectionner le terminus
			/*$terminus = $base->selectTerminus($id_ligne, $sens_bus)[0];
			$lat_ref = doubleval($terminus[1]);
			$lng_ref = doubleval($terminus[2]);*/
			$lat_ref = $new_lat;
			$lng_ref = $new_lng;

			
			$old_distance = doubleval($bus['toterminus']);
			$new_distance = Controller::calculDistanceTerm($base, $lat_ref, $lng_ref, $sens_bus);

			if($old_distance==0 OR $old_distance>$new_distance){
				$base->modifyBusTermD($id_bus, $new_distance);


			}elseif ($old_distance<$new_distance) {
				//modifier le sens
				if($sens_bus == "A"){
					$base->modifyBus($id_bus, '-1', "R");
					$new_distance = Controller::calculDistanceTerm($base, $lat_ref, $lng_ref, "R");
					$base->modifyBusTermD($id_bus, $new_distance);
				}
				else{
					$base->modifyBus($id_bus, '-1', "A");
					$new_distance = Controller::calculDistanceTerm($base, $lat_ref, $lng_ref, "A");
					$base->modifyBusTermD($id_bus, $new_distance);
				}
			}


		}

		/* la distance séparant du terminus */
		static function calculDistanceTerm($base, $lat_ref, $lng_ref, $sens){
			//determiner l'arret le plus proche
					$delta_lat = GPS::getAreaLat(20000);
					$delta_lng = GPS::getAreaLong(20000);

					$arrets_zone = $base->selectAllArretZones($delta_lat, $delta_lng, 1, $lat_ref, $lng_ref, $sens);

					

					//determine l'arret le plus proche
					$ds_inc = 10;
					$near_stop_id = -1;

					for($j=0; $j<=MAX_DISTANCE; $j = $j + $ds_inc){
						for($i=0; $i<count($arrets_zone); $i++){
							
							if(GPS::distance($lat_ref, $lng_ref, doubleval($arrets_zone
								[$i][2]), doubleval($arrets_zone[$i][3])) - $j <= 10){
								$near_stop_id = $i;

								break;
							}
						}
						
						if($near_stop_id != -1) break;

					}
					
					$distance_restante = 0;
					$nb = 0;
					if($near_stop_id != -1){
						
						$near_arret = $arrets_zone[$near_stop_id];

							
						//selectionner tous les arrets de la ligne
						$all_arrets = $base->selectArretsLigneToTerm(1, $sens, $near_arret[4]);


						//approximer la distante restante pour arriver par rapport 
						//on parcours les arrets. si un arret n'est pas encore traversé, alors ajouter la distance restante
						for($ii = 0; $ii<count($all_arrets); $ii++){
							//if(intval($all_arrets[$ii][4]) <= intval($near_arret[4])){
							//	echo "arret " . $arrets_allee[$ii][1] . "\n";
								$distance_restante += doubleval($all_arrets[$ii][5]); 
								$nb++;
							//}
						}
					}
					return $distance_restante;			
			}


			/*
				Savoir si le bus est entre deux arrets donnés
			*/
			static function isBusBetween($base,$bus, $id_arret1, $id_arret2, $sens, $id_ligne=1){
				$arret1 = $base->selectArretLigne($id_ligne, $id_arret1, $sens)[0];
				$arret1 = $base->selectArretLigne($id_ligne, $id_arret2, $sens)[0];

				//distance du bus par rapport aux deux arrets
				$d1 = GPS::distance(doubleval($bus[3]), doubleval($bus[4]), doubleval($arret1[1]), doubleval($arret1[2]));
				$d2 = GPS::distance(doubleval($bus[3]), doubleval($bus[4]), doubleval($arret2[1]), doubleval($arret2[2]));

				//distance entre les deux arrets
				$d12 = GPS::distance(doubleval($arret1[1]), doubleval($arret1[2]), doubleval($arret2[1]), doubleval($arret2[2]));

				if($d1 <= $d12 && $d2 <= $d12)
					return true;
				else
					return false;
			}

			/*
				retire un element (tableau) d'un tableau: index (position dans l'element) et sa valeur doivent suffir pour identifiant l'element
			*/
			static function retireElement($index_elem, $val_elem, $tab){
				$res = array();
				$inc = 0;
				foreach ($tab as $key => $value) {

					if(isset($value[$index_elem])){
						if($value[$index_elem] != $val_elem){
							$res[$inc] = $value;
							$inc++;
						}
					}
					
				}

				return $res;
			}



			/* 
				formulation du message
			*/

			static function formuleMessage($nb_arrets_restants, $next_bus=true, $un_autre_bus_arrive=false){
				$message = "";
				if($next_bus){
					if($nb_arrets_restants == 0){
						$message = "Le bus est presque arrivé à l'arrêt à l'ESP...";
					}elseif ($nb_arrets_restants>0 && $nb_arrets_restants<=2) {
						$message = "Le bus est à l'approche!!! Il ne reste que $nb_arrets_restants arrêts avant l'arrêt de l'ESP...";
					}elseif ($nb_arrets_restants>2) {
						$message = "Le bus arrive dans $nb_arrets_restants arrêts à l'arrêt l'ESP...";
					}else{
						$message = "Le bus a dépassé l'arrêt ESP.";
						if($un_autre_bus_arrive){
							$message += "Attendez le prochain bus...";
						}else{
							$message += " Malheureusement il n'y a plus de bus ayant déjà pris le départ...";
						}
					}
				}else{
					$message = "Il n'y a de bus a l'approche!!!Veuillez prendre un bus autre d'une autre ligne ou un autre moyen de transport...";
				}

				return $message;
			}

			/**
			* Localiser l'arret et le bus les proches de la position de l'internaute vers l'ESP
			* lat: latitude
			* lng: longitude
			* id_ligne: ligne du bus . Par défaut ligne = 1 (ligne 10)
			* rayon: rayon de la zone à definir. Par défaut = 1 km
			*/
			static function serviceClientMonbus($lat, $lng, $id_ligne=1, $rayon=1000){
				/*
				* determiner le sens: 
				* <algo>:si la personne est entre le terminus liberte et l'arret 16 alors sens=allee sinon sens=retour
				*/
				 
				$lat_ref = doubleval($lat);
				$lng_ref = doubleval($lng);
				$near_arret = array();
				$message = "Pas d'arret proche vers l'ESP";
				$ds_inc = 30;
				$near_stop_id = -1;

				//on cherche d'abord dans le sens allee
				$arrets_allee = selectArretsLigneRestant($id_ligne, "A", 1, $methode=PDO::FETCH_ASSOC);
				for($j=0; $j<=$rayon; $j = $j + $ds_inc){
					for($i=0; $i<count($arrets_allee); $i++){
						
						if(GPS::distance($lat_ref, $lng_ref, doubleval($arrets_allee
							[$i]['latitude_arret']), doubleval($arrets_allee[$i]['longitude_arret'])) - $j <= 10){
							$near_stop_id = $i;
							$near_arret = $arrets_allee[$i];
							$message = "Un arret proche vers L'ESP (sens liberte vers palais)";

							break;
						}
					}
					
					if($near_stop_id != -1) break;
				}

				// si on ne l'a pas trouvé, on cherche dans le sens retour
				if($near_stop_id == -1){
					$arrets_retour = selectArretsLigneRestant($id_ligne, "R", 1, $methode=PDO::FETCH_ASSOC);
					for($j=0; $j<=$rayon; $j = $j + $ds_inc){
						for($i=0; $i<count($arrets_retour); $i++){
							
							if(GPS::distance($lat_ref, $lng_ref, doubleval($arrets_retour
								[$i]['latitude_arret']), doubleval($arrets_retour[$i]['longitude_arret'])) - $j <= 10){
								$near_stop_id = $i;
								$near_arret = $arrets_allee[$i];
								$message = "Un arret proche vers L'ESP (sens palais vers liberte)";

								break;
							}
						}
						
						if($near_stop_id != -1) break;
					}
				}

				return array(
						"arret" => array(
							"message" => $message,
							"statut" => $near_stop_id,
							"proche" => $near_arret
						),
						"bus" => Controller::getNearBusToArret($next_arret),
						"sens" => $near_arret['sens']
					);

			}
	}

	/***************testes***********************/
	//echo GPS::distance(15.162717,-17.516171,15.262717,-17.716171);
	//echo GPS::convertirLat(14,39,28.54, "N") . "\n";
	//echo GPS::convertirLong(17,26,07.94, "W") . "\n";
	/*echo GPS::getAreaLat(100)  . "\n";
	echo GPS::distance(15.162717,-17.516171,15.162717+0.0009,-17.516171) . "\n"; */
	//echo GPS::distance(14.68133,-17.466775,15.122314814814,-17.753958333333);

	//14.688140, -17.464442

	//teste
	// include_once("base_conf.php");
	// include_once("requetes.php");

	// $base = new BaseDD(HOSTNAME,BASENAME,USERNAME,PASSWORD);

	// //print_r(Controller::getNearBus($base, 1, "A"));
	// print_r(Controller::selectNearBusTerminus($base, "10", "R"));
	//print_r(Controller::selectNearBusTerminus($base, $_POST['ligne'], "A"));
//	print_r(Controller::selectNearBusTerminus($base, "10", "A"));

	//
//	print_r($base->selectTerminus(1, "A")[0]);
	//echo Controller::calculDistanceTerm($base, 14.681384, -17.466691, 'R');

?>
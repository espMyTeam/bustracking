
<!DOCTYPE html>
<html>
	<head>
		<title>NextBus-ESP</title>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="author" value="abdoulaye KAMA">
        <meta name="publisher" content="Abdoulaye KAMA">
        <meta name="keywords" content="ucad,esp,bus,tracking,gps, web, iot, cloud, arduino" />
        <meta name="reply-to" content="abdoulayekama@gmail.com">
        <meta name="category" content="internet">
        <meta name="robots" content="index, follow">
        <meta name="distribution" content="global">
        <meta name="Description" content="Bus tracking DIC2TR 2015/2016">
        <meta name="copyright" content="Genetics">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, user-scalable=no">

		<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/style.css" media="all" />
		<link rel="stylesheet" type="text/css" href="leaflet/leaflet.css" media="all" />
		<link rel="stylesheet" type="text/css" href="font-awesome/css/font-awesome.css" media="all" />
		<link rel="stylesheet" type="text/css" href="css/pulse.css" media="all" />



	</head>



	<body>

		<header>
			<!--barre de menu de l'entête-->
			<nav class="navbar navbar-default">
				<div class="container-fluid">
				    <div class="navbar-header">
					    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
					        <span class="sr-only">Toggle navigation</span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					        <span class="icon-bar"></span>
					    </button>
				      	<img src="images/esp.png" />
				    </div>
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					    <ul class="nav navbar-nav navbar-right">
					        <li class="page-scroll"><a href="index.php">Accueil</a></li>
		                    <li class="page-scroll"><a href="bus.php">Où est le bus?</a></li>
		                    <li class="page-scroll"><a href="contact.php">Contact</a></li>
					    </ul>
				    </div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
		</header>

		<table id="keywords" cellspacing="0" cellpadding="0" hidden>
									<thead>
										<tr>
											<th><span>Sens</span></th>
											<th><span>localisation</span></th>
											<th><span>Distance vers ESP(km)</span></th>
											<th><span>Prochain arret</span></th>
											<th><span>Arrets vers ESP</span></th>
										</tr>
									</thead>

									<tbody>
										<tr>
											<td class="lalign">Vers Palais</td>
											<td id="loc_bus_palais"></td>
											<td id="dist_bus_palais"></td>
											<td id="arret_bus_palais"></td>
											<td id="rest_bus_palais"></td>
										</tr>
										<tr>
											<td class="lalign">Vers Terminus Liberté 5</td>
											<td id="loc_bus_liberte"></td>
											<td id="dist_bus_liberte"></td>
											<td id="arret_bus_liberte"></td>
											<td id="rest_bus_liberte"></td>
										</tr>

								</tbody>
							</table>

							<!-- carte de localisation du bus -->
							<section id="section-map">
									<div class="row" >
										<div class="col-md-4" style="margin-bottom:5px;">
											<!-- les deux sens -->
											<div class="row">
												<div class="col-md-12">
													<table class="table borderless table-reflow">
													  	<tr>
													      <th>Départ</th>
													      <th>Votre arrêt</th>
													      <th>Terminus</th>
													    </tr>
													    <tr>
													      <td>Le départ</td>
													      <td>La position</td>
													      <td>Le terminus</td>
													    </tr>
													</table>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12" style="text-align:center;">
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="stat_bus_palais"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="loc_bus_palais"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="dist_bus_palais"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="arret_bus_palais"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="rest_bus_palais"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="loc_bus_liberte"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="dist_bus_liberte"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="arret_bus_liberte"></h2></p>
													<p style="color:rgb(70,70,70)"><h2 class="h3" id="rest_bus_liberte"></h2></p>
												</div>
											</div>
										</div>
										<div class="col-md-8" style="margin-bottom:5px;">
											<!--la carte--> <div id="map" frameborder="0"></div> <!--la carte-->
										</div>
									</div>
							</section>



		<!-- carte de localisation du bus
		<section id="section-map">
				<div class="row" >
					<div class="col-md-12">
						<div id="map" frameborder="0"></div>
					</div>
				</div>


				<!-- vers palais
				<div class="row">
					<div class="col-md-1">
						<h2 class="h2" style="color:red">Liberté 5</h2>
					</div>
					<div class="col-md-2">
						<marquee direction="right" scrolldelay="300"><h2 class="h2" id="marquee-pal-1" hidden><img src='images/bus_to.png' style='height: 50px;'></h2></marquee>
					</div>
					<div class="col-md-1">
						<h2 class="h2" style="color:blue">ESP</h2>
					</div>
					<div class="col-md-2">
						<marquee direction="right" scrolldelay="300"><h2 class="h2" id="marquee-pal-2" hidden><img src='images/bus_to.png' style='height: 50px;'></h2></marquee>
					</div>
					<div class="col-md-1">
						<h2 class="h2">Palais</h2>
					</div>
					<div class="col-md-4">
						<h2 class="h3" id="stat_bus_palais"></h2>
					</div>
				</div>

				<!-- vers liberte 5
				<div class="row">
					<div class="col-md-1">
						<h2 class="h2" >Liberté 5</h2>
					</div>
					<div class="col-md-2">
						<marquee scrolldelay="300"><h2 class="h2" id="marquee-lib-2" hidden><img src='images/bus_from.png' style='height: 50px;'></h2></marquee>
					</div>
					<div class="col-md-1">
						<h2 class="h2" style="color:blue">ESP</h2>
					</div>
					<div class="col-md-2">
						<marquee scrolldelay="300"><h2 class="h2" id="marquee-lib-1" hidden><img src='images/bus_from.png' style='height: 50px;'></h2></marquee>
					</div>
					<div class="col-md-1">
						<h2 class="h2" style="color:red">Palais</h2>
					</div>
					<div class="col-md-4">
						<h2 class="h3" id="stat_bus_liberte"></h2>
					</div>
				</div>


				<!--div class="row">
					<div class="col-md-12">
							<div  id="map-liberte">
								<p id="map-data-liberte"><img src="images/bus_from.png" style=" height: 50px;"> terminus Liberté 5</p>
								<b>
									<div class="span-bus">
										<span style="font-size:20px;">Position:</span><br/>
										<div id="dist_bus_liberte"></div><br/>
										<div id="rest_bus_liberte"></div>
									</div>
									<div class="span-bus">
										<div id="loc_bus_liberte"></div><br/>
										<span style="font-size:20px;">km</span><br/>
										<span style="font-size:20px;">arrêt(s) restant(s)</span>
									</div>
								</b>
							</div>
						</div>
				</div-->


				<!--div class="row">
					<div class="col-md-12">
							<div  id="map-liberte">
								<p id="map-data-liberte"><img src="images/bus_from.png" style=" height: 50px;"> terminus Liberté 5</p>
								<b>
									<div class="span-bus">
										<span style="font-size:20px;">Position:</span><br/>
										<div id="dist_bus_liberte"></div><br/>
										<div id="rest_bus_liberte"></div>
									</div>
									<div class="span-bus">
										<div id="loc_bus_liberte"></div><br/>
										<span style="font-size:20px;">km</span><br/>
										<span style="font-size:20px;">arrêt(s) restant(s)</span>
									</div>
								</b>
							</div>
						</div>
				</div-->

		</section>

		<div class="container">
			<section class="text-center">
				<h5> Suivie GPS de bus de la ligne 10 Dakar Dem Dikk</h5>
				<samp>
				<p class="text-left" style="text-align:justify;border:1px solid black;padding:10px;border-radius:5px;">
					Avec l'essor de l'Internet des objets (IoT : Internet of Things)  dans le domaine des  Technologies de l'Information et de la Communication (TICs), un dispositif de suivi GPS des bus DDD apportera des réponses concrètes aux problèmes soulevées. En effet , à partir des données GPS, le dispositif permet de localiser les bus de la ligne 10 sur leur itinéraire du Terminus Liberté 5 au Terminus Palais  mais aussi dans le sens inverse. Ainsi, les bus en circulation seront situés sur une carte par rapport à l'arrêt ESP selon le sens de parcours. Le nombre d'arrêts restants ainsi qu'une estimation du timing d'arrivée sont également fournis.
				</p>
			</samp>
			</section>
		</div>

		<footer>
			<div class="row navbar navbar-default" id="footer-1" >
			        <div class="col-md-3 col-md-offset-2">
			            <h2 class="h3 text-center" ><a href="">Objectifs</a></h2>
			            <h2 class="h3 text-center" ><a href="">Contact</a></h2>
			        </div>
			        <div class="col-md-3 ">
			            <h2 class="h3 text-center" ><a href="">Galerie</a></h2>
			            <h2 class="h3 text-center" ><a href="">RSS</a></h2>
			        </div>
			        <div class="col-md-3 ">
			        	<h2 class="h3 text-center" ><a href="#" class="fa fa-facebook-square fa-2x"></a></h2>
			            <h2 class="h3 text-center" ><a href="#" class="fa fa-twitter-square fa-2x"></a></h2>
			        </div>
			</div>
			<div class="row text-center navbar navbar-default" id="footer-2">
		        <nav class="" >
  					<div class="container-fluid">
		                     <p>&copy 2016-2017 nextbus-esp. All rights reserved | Design by DIC3TR/ESP/UCAD</p>
		        	</div>
		        </nav>
		    </div>

		</footer>

		<script  type="text/javascript" src="jquery/jquery.js"></script>
		<script  type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
		<script type="text/javascript" src="leaflet/leaflet-src.js"></script>
    	<script type="text/javascript" src="leaflet/leaflet-realtime.js"></script>
<<<<<<< Updated upstream
    	
    	<script type="text/javascript" src="js/mapping.js"></script>
    	
=======
    	<script type="text/javascript" src="js/mapping.js"></script><!--le script qui ajoute des donnees à la carte-->
>>>>>>> Stashed changes
	</body>
</html>

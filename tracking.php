
<!DOCTYPE html>
<html>
<head>
<title>Bus tracking</title>
<!-- for-mobile-apps -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="UCAD,ESP,bus tracking,gps," />
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false);
		function hideURLbar(){ window.scrollTo(0,1); } </script>
<!-- //for-mobile-apps -->
<link href="css/bootstrap.css" rel="stylesheet" type="text/css" media="all" />
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />

<!-- js -->
<script src="js/jquery-1.11.1.min.js"></script>
<!-- //js -->
<!--FlexSlider-->
		<link rel="stylesheet" href="css/flexslider.css" type="text/css" media="screen" />
		<script defer src="js/jquery.flexslider.js"></script>
		<script type="text/javascript">
		$(window).load(function(){
		  $('.flexslider').flexslider({
			animation: "slide",
			start: function(slider){
			  $('body').removeClass('loading');
			}
		  });
		});
	  </script>
<!--End-slider-script-->
<!-- pop-up-script -->
<script src="js/jquery.chocolat.js"></script>
		<link rel="stylesheet" href="css/chocolat.css" type="text/css" media="screen" charset="utf-8">
		<!--light-box-files -->
		<script type="text/javascript" charset="utf-8">
		$(function() {
			$('.img-top a').Chocolat();
		});
		</script>

<!-- //pop-up-script -->
<!-- start-smoth-scrolling -->
<script type="text/javascript" src="js/move-top.js"></script>
<script type="text/javascript" src="js/easing.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$(".scroll").click(function(event){		
			event.preventDefault();
			$('html,body').animate({scrollTop:$(this.hash).offset().top},1000);
		});
	});
</script>
<!-- start-smoth-scrolling -->
<link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
<link href='//fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
</head>

<link rel="stylesheet" href="leaflet/leaflet.css" />
	
<body>
<!-- header -->
	<?php

		include_once("header.php");
	?>
<!-- header -->

<table hidden>
        <tr>
            <td align="right" bgcolor=""><h5>Saisissez une ligne de bus</h5></td>
            <td align="center" width="5"><input type="number" id="bussearch" value="10"  name="search" width="500"/></td>
            <td align="center" width="5"><input type="submit" name="rechercher" value="Localiser" id="search-btn2"/></td>
        </tr>
    </table>

<!-- features -->
	<div id="features" class="features">
		<div class="container">
			<h3>Geo-Localisation ligne bus 10</h3>
			<p class="vel">Dakar dem dikk</p>
			<div class="services-grids">
				<div class="col-md-12 col-sm-12 col-xs-12 services-grid-left">
					<div class="col-md-8 col-sm-8 col-xs-12 services-grid-left1" style="height: 800px; left:0px; margin:0px; padding:0px;width:80%;">
						<div id="map" frameborder="0"></div>
					</div>
					
					<div class="col-xs-3 col-sm-3 col-xs-12 services-grid-left2" style="width:100px;">
						<div class="mask"></div>
							<div class="content">
												<span class="info" title="Full Image"> </span>
											
						<h4>Free Images</h4>
						<p>
							<table id="keywords" cellspacing="0" cellpadding="0">
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
							
						</p>
					</div>
					<div class="clearfix"> </div>
				</div>

				<div class="clearfix"> </div>
			</div>
			
		</div>
	</div>
	<!--div class="map"><br><br>
		<div class="container">
				<div border="2" class="col-md-8 col-sm-8 col-xs-12" style="height: 800px; left:0px; margin:0px; padding:0px;width:80%;">
					<div id="map" frameborder="0" style="border:0" allowfullscreen=""></div>
					<div class="clearfix"> </div>
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12" style="width:100px;">
					lol
				</div>
				<div class="col-md-2 col-sm-2 col-xs-12" style="width:100px;">
					lol
				</div>
				<div class="clearfix"> </div>
		</div>
		<br><br>
	</div-->
<!-- //features -->


<!-- footer -->
	<?php
		include_once("footer.php");
	?>
<!-- //footer -->
<!-- here stars scrolling icon -->
	<script type="text/javascript">
		$(document).ready(function() {
			/*
				var defaults = {
				containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
				};
			*/
								
			$().UItoTop({ easingType: 'easeOutQuart' });
								
			});
	</script>
<!-- //here ends scrolling icon -->
<!-- for bootstrap working -->
	<script src="js/bootstrap.js"></script>
<!-- //for bootstrap working -->
<script type="text/javascript" src="leaflet/leaflet-src.js"></script>
    <script type="text/javascript" src="leaflet/leaflet-realtime.js"></script>
    <script type="text/javascript" src="mapping.js"></script>
</body>
</html>

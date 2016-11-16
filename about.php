
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
	
<body>
<!-- header -->
	<?php

		include_once("header.php");
	?>
<!-- header -->


<!-- features -->
	<div id="features" class="features">
		<div class="container">
			<h3>Our Features</h3>
			<p class="vel">Some Key Points</p>
			<div class="services-grids">
				<div class="col-md-6 services-grid-left">
					<div class="col-xs-4 services-grid-left1">
						<i class="hovicon effect-2 sub-a feat"><span class="glyphicon glyphicon-picture" aria-hidden="true"></span></i>
					</div>
					<div class="col-xs-8 services-grid-left2">
						<h4>Free Images</h4>
						<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil 
							molestiae consequatur.</p>
					</div>
					<div class="clearfix"> </div>
				</div>
				<div class="col-md-6 services-grid-right">
					<div class="col-xs-4 services-grid-left1">
						<i class="hovicon effect-2 sub-a feat"><span class="glyphicon glyphicon-phone" aria-hidden="true"></span></i>
					</div>
					<div class="col-xs-8 services-grid-left2">
						<h4>Responsive</h4>
						<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil 
							molestiae consequatur.</p>
					</div>
					<div class="clearfix"> </div>
				</div>
				<div class="clearfix"> </div>
			</div>
			<div class="services-grids">
				<div class="col-md-6 services-grid-left">
					<div class="col-xs-4 services-grid-left1">
						<i class="hovicon effect-2 sub-a feat"><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span></i>
					</div>
					<div class="col-xs-8 services-grid-left2">
						<h4>Google Maps</h4>
						<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil 
							molestiae consequatur.</p>
					</div>
					<div class="clearfix"> </div>
				</div>
				<div class="col-md-6 services-grid-right">
					<div class="col-xs-4 services-grid-left1">
						<i class="hovicon effect-2 sub-a feat"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span></i>
					</div>
					<div class="col-xs-8 services-grid-left2">
						<h4>Easily Customizable</h4>
						<p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil 
							molestiae consequatur.</p>
					</div>
					<div class="clearfix"> </div>
				</div>
				<div class="clearfix"> </div>
			</div>
		</div>
	</div>
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
</body>
</html>


<!DOCTYPE html>
<html>
  <head>
    <title>Bus tracking</title>

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
    
    
  </head>


  
  <body>
  

    <header>

      <!-- menu -->
      <div class="row">
              <div class="well nav nav-pills" id="menu">
                      <div class="navbar-header page-scroll">
                          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#principale">
                              <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                          </button>
                          <div class="col-xs-2 col-sm-2 col-md-4 col-lg-4">
                              <img src="images/esp.png" />
                          </div>
                          <br/>
                      </div>
                      
                      <div class="collapse navbar-collapse" id="principale">
                          <div class="col-xs-10 col-sm-10 col-md-8 col-lg-8 text-left"><br/>
                              <ul class="nav navbar-nav navbar-right">

                                    <li class="page-scroll"><a href="index.php">Ou est le bus?</a></li>
                                    <li class="page-scroll"><a href="contact.php">Contact</a></li>

                                  
                              </ul>

                          </div>
                      </div>
                      <br/>
                  </div>
                  
          </div>
    </header>

    <div class="container">
        <section>
         <div class="container-fluid bg-grey">
          <h2 class="h2 text-center">CONTACT</h2><br/>
          <div class="row">
            <div class="col-sm-5">
              <p>Envoyez nous un message...</p>
              <p><span class="glyphicon glyphicon-map-marker"></span> Sénégal, Dakar, UCAD, ESP</p>
              <!--p><span class="glyphicon glyphicon-phone"></span> +00 1515151515</p-->
              <p><span class="glyphicon glyphicon-envelope"></span> dic2-tr@esp.sn</p> 
              <p>
                  <ul class="list-unstyled list-inline list-social-icons">
                            <li>
                                <a href="https://www.facebook.com/geneticsenegal/"><i class="fa fa-facebook-square fa-2x"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-linkedin-square fa-2x"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-twitter-square fa-2x"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-google-plus-square fa-2x"></i></a>
                            </li>
                        </ul>
              </p>
            </div>
            <div class="col-sm-7">
              <div class="row">
                <div class="col-sm-6 form-group">
                  <input class="form-control" id="name" name="name" placeholder="Name" type="text" required>
                </div>
                <div class="col-sm-6 form-group">
                  <input class="form-control" id="email" name="email" placeholder="Email" type="email" required>
                </div>
              </div>
              <textarea class="form-control" id="comments" name="comments" placeholder="Comment" rows="5"></textarea><br>
              <div class="row">
                <div class="col-sm-12 form-group">
                  <button class="btn btn-default pull-right" type="submit">Send</button>
                </div>
              </div> 
            </div>
          </div>

        </div>
      </section>
    </div>

    <div class="container">
              <!-- Embedded Google Map -->
              <iframe width="100%" height="400px" frameborder="0" scrolling="no" marginheight="0" marginwidth="10" src="http://maps.google.com/maps?hl=en&amp;ie=UTF8&amp;ll=14.681293, -17.467403&amp;spn=56.506174,79.013672&amp;t=m&amp;z=5&amp;output=embed"></iframe>

    </div>
    

    <footer>
      <div class="row navbar navbar-default" id="footer-1" >
              <div class="col-md-3 col-md-offset-2">
                  <h2 class="h3" ><a href="">Objectifs</a></h2>
                  <h2 class="h3" ><a href="">Contact</a></h2>
              </div>
              <div class="col-md-3 ">
                  <h2 class="h3" ><a href="">Galerie</a></h2>
                  <h2 class="h3" ><a href="">RSS</a></h2>
              </div>
              <div class="col-md-3 ">
                <div class="">
              <ul>
                <li><a href="#" class="fa fa-facebook-square fa-2x"></a></li>
                <li><a href="#" class="fa fa-twitter-square fa-2x"></a></li>
              </ul>
            </div>
            
              </div>
      </div>
      <div class="row text-center navbar navbar-default" id="footer-2">
            <nav class="" >
            <div class="container-fluid">
                         <p>&copy 2016 nextbus-esp. All rights reserved | Design by DIC2TR/ESP/UCAD</p>
              </div>
            </nav>  
        </div>

    </footer>

    <script  type="text/javascript" src="jquery/jquery.js"></script>
    <script  type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
    <script type="text/javascript" src="leaflet/leaflet-src.js"></script>
      <script type="text/javascript" src="leaflet/leaflet-realtime.js"></script>
      <script type="text/javascript" src="mapping.js"></script>
  </body>
</html>
  


<?php
	//controleur frontal
	try {

		print_r($_SERVER);
		//new Routeur($_SERVER['SCRIPT_FILENAME'])
		echo "<br>";
		echo trim($_SERVER['SCRIPT_FILENAME'], "/");
		switch ($_SERVER['REQUEST_METHOD']) {
			case 'GET':
				if(isset($_GET['search'])){
					
					if(isset($_GET['ligne'])){

						if(isset($_GET['bus'])){
							if(isset($_GET['client_lat']) && isset($_GET['client_lng'])){
								
							}else{

							}
						}
						elseif(isset($_GET['arret'])){
							if(isset($_GET['client'])){
							
							}else{
								
							}
						}
						else{
							if(isset($_GET['client'])){
							
							}else{
								
							}
						}

					}else{ //pas de ligne 

					}
					
				}
				else{ //pas de search

				}
				break;

			case 'POST':
				echo "rien!!!";
				break;

			case 'PUT':
				echo "rien!!!";
				break;

			case 'DELETE':
				echo "rien!!!";
				break; 

			default:
				echo "rien!!!";
				break;
		}
	}
	catch (Exception $e) {
	    erreur($e->getMessage());
	}


	class Routeur
	{
		private $path;
    	private $callable;
    	private $matches = [];
    	private $params = [];
		
		public function __construct(){
			$this->path = trim($path, '/');  // On retire les / inutils
        	$this->callable = $callable;
		}

		/**
	    * Permettra de capturer l'url avec les paramètre 
	    **/
	    public function match($url){
	        $url = trim($url, '/');
	        $path = preg_replace('#:([\w]+)#', '([^/]+)', $this->path);
	        $regex = "#^$path$#i";
	        if(!preg_match($regex, $url, $matches)){
	            return false;
	        }
	        array_shift($matches);
	        $this->matches = $matches;  // On sauvegarde les paramètre dans l'instance pour plus tard
	        return true;
	    }

		public function toString(){
			echo "PATH->$this->path\nCALLABLE->$this->callable\nPARAMS";
			print_r($this->params);
		}
	}
?>
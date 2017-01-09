
function fill_map(handle){
	//selectionner toutes les arrêts et bus


}

function select_bus(){
	//selectionner l'ensemble des bus
}

/*
* trouver l'emplacement correspondant à l'adresse latitude/longitude
*/
function reverseGeocoding(latitude, longitude, zoom){
	if(zoom != undefined && zoom > 0 && zoom <22){
		$.ajax({
        url: "http://nominatim.openstreetmap.org/reverse",
        method: "GET",
		data: { format:"json", lat:latitude, lon:longitude, zoom: zoom, addressdetails:1 },
		contentType: "application/json; charset=utf-8",
		dataType: "json"
    }).then(function(data) {
       	console.log(data);
       	document.getElementById("res_addr").innerHTML = data.display_name;
       	document.getElementById("res_city").innerHTML = data.address.city;
       	document.getElementById("res_state").innerHTML = data.address.state;
       	document.getElementById("res_footway").innerHTML = data.address.road;
       	document.getElementById("res_building").innerHTML = data.address.building;

       	document.getElementById("res_suburb").innerHTML = data.address.suburb;
       	
       	document.getElementById("res_country").innerHTML = data.address.country;

    	});
	}
	
}

function geocoding(adresse){
	var a=9;
}

/* geolocaliser l'internaute 
* retourne :
	res {
		lat,
		lng,
		message
	}
*/
function geolocaliseVisiteur(){
	res={
		"latitude" : null,
		"longitude" : null,
		"altitude": null,
		"vitesse" : null,
		"precision" : null,
		"direction": null,
		"message" : null
	}

	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position){
			res.latitude = position.coords.latitude;
			res.longitude = position.coords.longitude;
			res.altitude = position.coords.altitude;
			res.vitesse = position.coords.speed;
			res.precision = position.coords.accuracy;
			res.direction = position.coords.heading;
			res.message = "OK";
			reverseGeocoding(res.latitude, res.longitude, 18);
		},
		function(error){
			res.message = "Erreur lors de la géolocalisation : ";
		    switch(error.code) {
		    case error.TIMEOUT:
		    	res.message += "Timeout !";
		    break;
		    case error.PERMISSION_DENIED:
		    res.message += "Vous n’avez pas donné la permission";
		    break;
		    case error.POSITION_UNAVAILABLE:
		    	res.message += "La position n’a pu être déterminée";
		    break;
		    case error.UNKNOWN_ERROR:
		    	res.message += "Erreur inconnue";
		    break;

			}
		},
		{
			maximumAge:600000, //durée de la mise en cache
			enableHighAccuracy:true //obtient une valeur plus precise avec le gps
		});
	}

	return res;
}

window.onload=function(){
	setInterval(function(){
		console.log(geolocaliseVisiteur());
	}, 10000);
	
}

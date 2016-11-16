var val_zoom = 15;
var map;
var LeafIcon = L.Icon.extend({
	    options: {
	       // shadowAnchor: [1, 41],
	     /*   iconSize: [25, 41],
			iconAnchor: [12, 41],
			popupAnchor: [1, -34],
			shadowSize: [41, 41]*/
	    }
});

/*
	créer les icones
*/
var bus_icon_lib, bus_icon_pal;
var arret_icone;
var visitor_icon;
var all_arrets;

var marker_allee, marker_retour, marker_next_allee, marker_next_retour;

window.onload=function(){
	var delai = 10; //, delaimax;
	var ligne = document.getElementById("bussearch");
	bus_icon_lib = create_icone("images/bus_from.png");
	bus_icon_pal = create_icone("images/bus_to.png");
	arret_icon = create_icone("images/marker-icon.png", "images/marker-shadow.png");
	visitor_icon = create_icone("images/marker_visitor.png", "images/marker_visitor_shadow.png");

	map = L.map('map',{
			center: [14.681293, -17.467403],
    		zoom: val_zoom
		}),
    trail = {
			        type: 'Feature',
			        properties: {
			            id: 1
			        },
			        geometry: {
			            type: 'LineString',
			            coordinates: []
			        }
	},
	realtime = L.realtime(function(success, error) {
			    	var data;
			    	
			    	if(ligne == undefined || ligne.value == undefined || ligne.value == null){
			    		data = "10";
			    		
			    	}
			    	else{
			    		data = ligne.value;

					}

	init_carte(ligne);
	

    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { //http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
			    attribution: 'DIC2TRS-ESP'
	}).addTo(map);

	

	map.on('zoomend', function(e){
		val_zoom = map.getZoom();
	})

	L.Realtime.reqwest({
		url: 'position_bus.php',
		method: "post",
		crossOrigin: true,
		type: 'json',
		data: {'ligne': "10"},
		async: true
	}).then(function(data) {
			        	
		var trailCoords = trail.geometry.coordinates;
		console.log(data);

		if(data.allee.position){
			trailCoords.push([data.allee.position.lng, data.allee.position.lat]);
			trailCoords.splice(0, Math.max(0, trailCoords.length - 5));

			if(marker_allee)
				update_marker_bus_a(data.allee.position.lat, data.allee.position.lng, "Bus<br/>Matricule:"+data.allee.bus.matricule);
			else
	        	marker_allee = ajout_marker_bus(data.allee.position.lat, data.allee.position.lng, "Bus<br/>Matricule:"+data.allee.bus.matricule, bus_icon_pal);
	        //mettre à jour le tableau
	        document.getElementById("loc_bus_palais").innerHTML = "latitude:"+data.allee.position.lat+",longitude:"+data.allee.position.lng;
	        document.getElementById("dist_bus_palais").innerHTML = (parseInt(data.allee.position.reste)/1000.0)+"";
	        document.getElementById("arret_bus_palais").innerHTML = data.allee.arrets.nom//+",latitude:"+parseFloat(data.allee.arrets.lat).toFixed(6)+", longitude:"+parseFloat(data.allee.arrets.lng).toFixed(6);
	        document.getElementById("rest_bus_palais").innerHTML = data.allee.arrets.reste;
	        document.getElementById("stat_bus_palais").innerHTML = "Bus vers l'ESP à "+(parseInt(data.allee.position.reste)/1000.0)+" km de l'ESP, "+data.allee.arrets.reste+" arret(s) restant(s)";

	        document.getElementById("marquee-pal-1").hidden = false;
	        //document.getElementById("marquee-pal-2").hidden = true;
	        
	        marker_next_allee = ajout_marker_arret(data.allee.arrets.lat,data.allee.arrets.lng, "Arret le plus proche du bus de Palais -> ESP");		

	        //marker_next_allee = ajout_marker_arret(data.allee.arrets.lat,data.allee.arrets.lng, "arret allee le plus proche<br/>"+"Arret<br/>N°:"+data.allee.arrets.nom+"<br/>Latitude:"+parseFloat(data.allee.arrets.lat).toFixed(6)+"<br/>Longitude:"+parseFloat(data.allee.arrets.lng).toFixed(6));		
		}else{
			//marker_allee = "";
	        //mettre à jour le tableau
	        document.getElementById("loc_bus_palais").innerHTML = "x";
	        document.getElementById("dist_bus_palais").innerHTML = "x";
	        document.getElementById("arret_bus_palais").innerHTML = "x";
	        document.getElementById("rest_bus_palais").innerHTML = "x";

	        document.getElementById("stat_bus_palais").innerHTML = " Pas de bus disponible vers l'ESP...";

	        //document.getElementById("marquee-pal-2").hidden = false;
	        document.getElementById("marquee-pal-1").hidden = true;
		}

		if(data.retour.position){
			trailCoords.push([data.retour.position.lng, data.retour.position.lat]);
			trailCoords.splice(0, Math.max(0, trailCoords.length - 5));

			if(marker_retour)
				update_marker_bus_r(data.retour.position.lat, data.retour.position.lng, "Bus<br/>Matricule:"+data.retour.bus.matricule);
			else
	        	marker_retour = ajout_marker_bus(data.retour.position.lat, data.retour.position.lng, "Bus<br/>Matricule:"+data.retour.bus.matricule, bus_icon_lib);
	        //mettre à jour le tableau
	        document.getElementById("loc_bus_liberte").innerHTML = "latitude:"+data.retour.position.lat+",longitude:"+data.retour.position.lng;
	        document.getElementById("dist_bus_liberte").innerHTML = (parseInt(data.retour.position.reste)/1000.0)+"";
	        document.getElementById("arret_bus_liberte").innerHTML = data.retour.arrets.nom//+",latitude:"+parseFloat(data.retour.arrets.lat).toFixed(6)+", longitude:"+parseFloat(data.retour.arrets.lng).toFixed(6);
	        document.getElementById("rest_bus_liberte").innerHTML = data.retour.arrets.reste;

	        document.getElementById("stat_bus_liberte").innerHTML = "Bus vers l'ESP à "+(parseInt(data.retour.position.reste)/1000.0)+" km, "+data.retour.arrets.reste+" arret(s) restant(s)";

	        document.getElementById("marquee-lib-1").hidden = false;
	        //document.getElementById("marquee-lib-2").hidden = true;

	        
	        	        //marker_next_retour = ajout_marker_arret(data.retour.arrets.lat,data.retour.arrets.lng, "arret retour le plus proche<br/>"+"Latitude:"+parseFloat(data.retour.arrets.lat).toFixed(6)+"<br/>Longitude:"+parseFloat(data.retour.arrets.lng).toFixed(6));		

	        marker_next_retour = ajout_marker_arret(data.retour.arrets.lat,data.retour.arrets.lng, "Arret le plus proche du bus liberté 5 -> ESP");		
		}else{
			//marker_retour = "";
	        //mettre à jour le tableau
	        document.getElementById("loc_bus_liberte").innerHTML = "x";
	        document.getElementById("dist_bus_liberte").innerHTML = "x";
	        document.getElementById("arret_bus_liberte").innerHTML = "x";
	        document.getElementById("rest_bus_liberte").innerHTML = "x";

	        document.getElementById("stat_bus_liberte").innerHTML = "Pas de bus disponible vers l'ESP...";
	        //document.getElementById("marquee-lib-2").hidden = false;
	        document.getElementById("marquee-lib-1").hidden = true;
		}
		


        val_zoom = map.getZoom();
		success({
			                type: 'FeatureCollection',
			                features: [data, trail]
			            });
			        })
			        .catch(error);
		}, {
			interval: 10 * 1000
	}).addTo(map);
			    
}

function ajout_marker(lat, lng, libelle) {
	var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)]).addTo(map);
    marker.bindPopup(libelle+"<br/>latitude:"+lat+", longitude:"+lng)
        .on('mouseover', function(e){ marker.openPopup(); })
        .on('mouseout', function(e){ marker.closePopup(); });

    return marker;
}

function ajout_marker_client(lat, lng) {
	var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)], {icon: visitor_icon, iconSize: [45, 61]}).addTo(map);
    marker.bindPopup("Vous:<br/>latitude:"+lat+", longitude:"+lng)
        .on('mouseover', function(e){ marker.openPopup(); })
        .on('mouseout', function(e){ marker.closePopup(); });

    return marker;
}

function ajout_marker_arret(lat, lng, libelle) {
	var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)]).addTo(map);
    marker.bindPopup(libelle)
        .on('mouseover', function(e){ marker.openPopup(); })
        .on('mouseout', function(e){ marker.closePopup(); });

    return marker;
}

function ajout_marker_bus(lat, lng, libelle, bus_icon) {
	var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)], {icon: bus_icon, iconSize: [45, 61]}).addTo(map);
    marker.bindPopup(libelle+"<br/>latitude:"+lat+", longitude:"+lng)
        .on('mouseover', function(e){ marker.openPopup(); })
        .on('mouseout', function(e){ marker.closePopup(); });

    return marker;
}

function update_marker_bus_a(lat, lng, libelle) {
	marker_allee.setLatLng(L.latLng(lat, lng));
    marker_allee.bindPopup(libelle+"<br/>latitude:"+lat+", longitude:"+lng)
        .on('mouseover', function(e){ marker_allee.openPopup(); })
        .on('mouseout', function(e){ marker_allee.closePopup(); });

   // return marker;
}

function update_marker_bus_r(lat, lng, libelle) {
	//console.log(L);
	marker_retour.setLatLng(L.latLng(lat, lng));
    marker_retour.bindPopup(libelle+"<br/>latitude:"+lat+", longitude:"+lng)
        .on('mouseover', function(e){ marker_retour.openPopup(); })
        .on('mouseout', function(e){ marker_retour.closePopup(); });

   // return marker;
}

/*
	ajouter tous les arrets de la ligne
*/
function ajout_arretss(ligne){
	var xhr; 
	try {  
		xhr = new ActiveXObject('Msxml2.XMLHTTP');    
	}catch(e){
		try {   
		    xhr = new ActiveXObject('Microsoft.XMLHTTP'); 
		}catch (e2) {
		    try {  
		        xhr = new XMLHttpRequest();  
			}catch(e3){  
		           	xhr = false;
		 	}
		}
	}

	if(xhr){
		xhr.onreadystatechange  = function() { 
		       if(xhr.readyState  == 4){
			       	
			        if(xhr.status  == 200) {
			        	var valeur = xhr.responseText;
			        	

			           	var arrets = valeur.split("*");

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

						console.log(arrets);
			          
			           	for(var i=0; i<arrets.length; i++){
			           		var arret = arrets[i];
			           		arret = arret.split("_");
			           		//all_arrets[i] = arret;
			           		latitude = arret[arret.length-2];
			           		longitude = arret[arret.length-1];
			           		

			           		var desc = "Arret<br/>N°:"+arret[10]+",arret:"+arret[11]+"<br/>Latitude:"+parseFloat(latitude).toFixed(6)+"<br/>Longitude:"+parseFloat(longitude).toFixed(6);
			           		
			           		//ajout_marker_arret(parseFloat(latitude).toFixed(6), parseFloat(longitude).toFixed(6), desc);

			           }
			       	}
			        else{
			           // label.innerText ="Error code " + xhr.status;
			        }
		        }

		};

		xhr.open("POST", "arrets_ligne.php",  true); 
		xhr.setRequestHeader("Access-Control-Allow-Methods", "REQUEST,GET,HEAD,OPTIONS,POST,PUT");
		xhr.setRequestHeader("Access-Control-Allow-Headers","Content-Type, Access-Control-Allow-Headers,Access-Control-Allow-Origin, Authorization, X-Requested-With, Access-Control-Allow-Methods");
        xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhr.send("ligne=10"); 
	}
}

function ajout_arrets(ligne){
    $.ajax({
            type: 'GET',
            url: 'arrets_ligne.php?ligne=10',
            success: function(data) 
            {
                var valeur = data;
				var arrets = valeur.split("*");
				console.log(arrets);
			          
	           	for(var i=0; i<arrets.length; i++){
	           		var arret = arrets[i];
	           		arret = arret.split("_");
	           		//all_arrets[i] = arret;
	           		latitude = arret[arret.length-2];
	           		longitude = arret[arret.length-1];
	           		

	           		var desc = "Arret<br/>N°:"+arret[10]+",arret:"+arret[11]+"<br/>Latitude:"+parseFloat(latitude).toFixed(6)+"<br/>Longitude:"+parseFloat(longitude).toFixed(6);
	           		
	           		//ajout_marker_arret(parseFloat(latitude).toFixed(6), parseFloat(longitude).toFixed(6), desc);

	           }
            }, error: function() {
            alert('il y a problème'); 
        } 
    });
}

function geolocalise(){
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position){
			
			document.getElementById("lab_lat").innerHTML = parseFloat(position.coords.latitude).toFixed(6);
			document.getElementById("lab_lng").innerHTML = parseFloat(position.coords.longitude).toFixed(6);
			//document.getElementById("lab_alt").innerHTML = position.coords.altitude;
			ajout_marker_client(document.getElementById("lab_lat").innerHTML, document.getElementById("lab_lng").innerHTML);
		},
		function(error){
			 var info = "Erreur lors de la géolocalisation : ";
			    switch(error.code) {
			    case error.TIMEOUT:
			    	info += "Timeout !";
			    break;
			    case error.PERMISSION_DENIED:
			    info += "Vous n’avez pas donné la permission";
			    break;
			    case error.POSITION_UNAVAILABLE:
			    	info += "La position n’a pu être déterminée";
			    break;
			    case error.UNKNOWN_ERROR:
			    	info += "Erreur inconnue";
			    break;
			    document.getElementById("lab_lat").innerHTML = info;
			    document.getElementById("lab_lng").innerHTML = info;
				}
			},
			{maximumAge:600000,enableHighAccuracy:true});
		}
}


function init_carte(ligne){
	//geolocalise();
	//esp 14.681293, -17.467403
	//14.681335 -17.466754
    var centre = ajout_marker(14.681335,-17.466754, "Ecole Supérieure Polytechnique");
	//ajout_arrets(ligne);
}


function addArretsToMap(){

}

/*
	créer une icone sur la carte leaflet
*/
function create_icone(image, shadow){
	if(shadow)
		return new LeafIcon({iconUrl: image, shadowUrl: shadow, iconSize: [45, 61]});
	else
		return new LeafIcon({iconUrl: image,  iconSize: [45, 61]});
}

var UPDATE_TIME = 5;
var lat_test = 14.681325;
var lng_test = -17.467423;

window.onload = function(){
	var val_zoom = 15;
	var bus_marker_retour, bus_marker_allee,bus_marker_retour_ic, bus_marker_allee_ic, all_bus_marker_allee, all_bus_marker_retour;
	var busIcon_allee = L.icon.pulse({iconSize:[20,20],color:'blue'});
	var busIcon_retour = L.icon.pulse({iconSize:[20,20],color:'red'});

	var map = L.map('map',{
		center: [14.681293, -17.467403],
    	zoom: val_zoom
	});
    
    var trail = {
        type: 'Feature',
        properties: {},
        geometry: {
            type: 'Point',
            coordinates: []
        }
	};

	//ajouter le marqueur ESP
	ajout_marker(14.681335,-17.466754, "Ecole Supérieure Polytechnique");
	//geolocalise();

	var realtime = L.realtime(
		function(success, error) {
			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { //http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
					    attribution: 'NextBus-ESP'
			}).addTo(map);

			map.on('zoomend', function(e){
				val_zoom = map.getZoom();
			});

			

			L.Realtime.reqwest({
				url: 'server/position_bus.php',
				method: "get",
				crossOrigin: true,
				type: 'json',
				data: {'ligne': "10"},
				async: true
			}).then(function(data) {
				
				all_bus_marker_retour = [];
				all_bus_marker_allee = [];

				console.log(data);

				if(bus_marker_retour){
						
						map.removeLayer(bus_marker_retour);
						map.removeLayer(bus_marker_retour_ic);
				}

				if(bus_marker_allee){
						
						map.removeLayer(bus_marker_allee);
						map.removeLayer(bus_marker_allee_ic);
				}

				//console.log(data.allee.near_bus.position);

				trail.geometry.coordinates = [];
				var res = {};

				if(data.allee.near_bus && data.retour.near_bus){
					res.allee = data.allee;
					res.retour = data.retour;
					trail.geometry.coordinates.push([data.allee.near_bus.position.longitude, data.allee.near_bus.position.latitude]);
					trail.geometry.coordinates.push([data.retour.near_bus.position.longitude, data.retour.near_bus.position.latitude]);

					getAddresse(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, 18);
					bus_marker_allee = ajout_marker(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, "label", busIcon_allee);
					bus_marker_allee_ic = ajout_marker(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, "label");

					getAddresse(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, 18);
					bus_marker_retour = ajout_marker(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, "label", busIcon_retour);
					bus_marker_retour_ic =ajout_marker(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, "label");

					for (var i=0; i<data.allee.bus.length; i++) {
						
						var iii = ajout_marker(data.allee.bus[i].latitude, data.allee.bus[i].longitude, "label");
						all_bus_marker_allee.push(iii);
					}

					for (var i=0; i<data.retour.bus.length; i++) {
						
						var iii = ajout_marker(data.retour.bus[i].latitude, data.retour.bus[i].longitude, "label");
						all_bus_marker_retour.push(iii);
					}
					

				}else if(data.allee.near_bus){

					res.allee = data.allee;

					trail.geometry.coordinates = [lng_test, lat_test];
					lng_test += 0.00001;
					lat_test += 0.00001;

					trail.geometry.coordinates = [data.allee.near_bus.position.longitude, data.allee.near_bus.position.latitude];
					getAddresse(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, 18);
					bus_marker_allee = ajout_marker(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, "label", busIcon_allee);
					bus_marker_allee_ic = ajout_marker(data.allee.near_bus.position.latitude, data.allee.near_bus.position.longitude, "label");
					
					for (var i=0; i<data.allee.bus.length; i++) {
						
						var iii = ajout_marker(data.allee.bus[i].latitude, data.allee.bus[i].longitude, "label");
						all_bus_marker_allee.push(iii);
					}


				}else if(data.retour.near_bus){
					res.retour = data.retour;
					trail.geometry.coordinates = [data.retour.near_bus.position.longitude, data.retour.near_bus.position.latitude];

					getAddresse(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, 18);
					bus_marker_retour = ajout_marker(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, "label", busIcon_retour);
					bus_marker_retour_ic =ajout_marker(data.retour.near_bus.position.latitude, data.retour.near_bus.position.longitude, "label");
					
					for (var i=0; i<data.retour.bus.length; i++) {
						
						var iii = ajout_marker(data.retour.bus[i].latitude, data.retour.bus[i].longitude, "label");
						all_bus_marker_retour.push(iii);
					}
					
				}

				

		        val_zoom = map.getZoom();

		       	if(res.allee || res.retour){
		       		success({
		                type: 'FeatureCollection',
		                features: [res, trail]
		            });

		            
		       	}


				
	        }).catch(error);
		},
		{
			interval: UPDATE_TIME * 1000,
			//start: true
		}
	).addTo(map); //fin L.realtime();

	/*
		ajouter un marqueur
	*/

	function ajout_marker(lat, lng, libelle, icone) {
		if(icone)
			var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)], {icon: icone}).addTo(map);
		else
			var marker = L.marker([parseFloat(lat).toFixed(6), parseFloat(lng).toFixed(6)]).addTo(map);
		
		//var a = getAddresse(lat, lng, val_zoom, marker);
		//console.log(a);

	    marker.bindPopup(libelle+"<br/>latitude:"+lat+", longitude:"+lng)
	        .on('mouseover', function(e){ marker.openPopup(); })
	        .on('mouseout', function(e){ marker.closePopup(); });

	    return marker;
	}

	/*
		créer une icone sur la carte leaflet
	*/
	function create_icone(image, shadow){
		if(shadow)
			return new LeafIcon({iconUrl: image, shadowUrl: shadow, iconSize: [35, 31]});
		else
			return new LeafIcon({iconUrl: image,  iconSize: [35, 31]});
	}



} //fin window.onload


/*
	geolocaliser le visteur
*/
function geolocalise(){
	if(navigator.geolocation){
		navigator.geolocation.getCurrentPosition(function(position){
			
			console.log(position);
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
			    
				}
			},
			{maximumAge:600000,enableHighAccuracy:true});
		}
}

/*
	trouver l'emplacement (adresse) correspondant aux coordonnees geographiques
*/
function getAddresse(lat, lng, zoom){
	var zoomr = 18;
	
	if(zoom > 0)
		zoomr = zoom;

	$.ajax({
        url: "http://nominatim.openstreetmap.org/reverse",
        method: "GET",
		data: { format:"json", lat:lat, lon:lng, zoom: zoom },
		contentType: "application/json; charset=utf-8",
		dataType: "json"
    }).then(function(data) {
    	//console.log("data");
       //console.log(data);
       //console.log(data);
       	return data;
    });

}


L.Icon.Pulse = L.DivIcon.extend({

        options: {
            className: '',
            iconSize: [12,12],
            color: 'red',
            animate: true,
            heartbeat: 1,
        },

        initialize: function (options) {
            L.setOptions(this,options);

            // css
            
            var uniqueClassName = 'lpi-'+ new Date().getTime()+'-'+Math.round(Math.random()*100000);

            var before = ['background-color: '+this.options.color];
            var after = [

                'box-shadow: 0 0 3px 2px '+this.options.color,

                'animation: pulsate ' + this.options.heartbeat + 's ease-out',
                'animation-iteration-count: infinite',
                'animation-delay: '+ (this.options.heartbeat + .1) + 's',
            ];

            if (!this.options.animate){
                after.push('animation: none');
            }

            var css = [
                '.'+uniqueClassName+'{'+before.join(';')+';}',
                '.'+uniqueClassName+':after{'+after.join(';')+';}',
            ].join('');
 
            var el = document.createElement('style');
            if (el.styleSheet){
                el.styleSheet.cssText = css;
            } else {
                el.appendChild(document.createTextNode(css));
            }

            document.getElementsByTagName('head')[0].appendChild(el);

            // apply css class

            this.options.className = this.options.className+' leaflet-pulsing-icon '+uniqueClassName;

            // initialize icon
            
            L.DivIcon.prototype.initialize.call(this, options);
        
        }
    });

    L.icon.pulse = function (options) {
        return new L.Icon.Pulse(options);
    };


    L.Marker.Pulse = L.Marker.extend({
        initialize: function (latlng,options) {
            options.icon = L.icon.pulse(options);
            L.Marker.prototype.initialize.call(this, latlng, options);
        }
    });

    L.marker.pulse = function (latlng,options) {
        return new L.Marker.Pulse(latlng,options);
    };

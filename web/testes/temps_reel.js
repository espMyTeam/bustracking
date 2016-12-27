

window.onload = function(){
	var val_zoom = 15;

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

	var realtime = L.realtime(
		function(success, error) {
			L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', { //http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
					    attribution: 'DIC2TRS-ESP'
			}).addTo(map);

			map.on('zoomend', function(e){
				val_zoom = map.getZoom();
			});

			L.Realtime.reqwest({
				url: '../server/position_bus.php',
				method: "get",
				crossOrigin: true,
				type: 'json',
				data: {'ligne': "10"},
				async: true
			}).then(function(data) {
				
				console.log(data);

				trail.geometry.coordinates = [];
				var res = {};

				if(data.allee.position && data.retour.position){
					res.allee = data.allee;
					res.retour = data.retour;
					trail.geometry.coordinates.push([data.allee.position.lng, data.allee.position.lat]);
					trail.geometry.coordinates.push([data.retour.position.lng, data.retour.position.lat]);


				}else if(data.allee.position){
					res.allee = data.allee;
					trail.geometry.coordinates = [data.allee.position.lng, data.allee.position.lat];


				}else if(data.retour.position){
					res.retour = data.retour;
					trail.geometry.coordinates = [data.retour.position.lng, data.retour.position.lat];
					
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
			interval: 10 * 1000,
			//start: true
		}
	).addTo(map); //fin L.realtime();
}

function getDataFormat(datas){
	res = {};
	if(datas.allee.position.lat && datas.allee.position.lng){
		res.allee = datas.allee;
	}

	if(datas.retour.position.lat && datas.retour.position.lng){
		res.retour = datas.retour;
	}

	if(res = {}) return false;

	return res;
}
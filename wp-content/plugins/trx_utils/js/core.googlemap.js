function reisen_googlemap_init(dom_obj, coords) {
	"use strict";
	if (typeof REISEN_STORAGE['googlemap_init_obj'] == 'undefined') reisen_googlemap_init_styles();
	REISEN_STORAGE['googlemap_init_obj'].geocoder = '';
	try {
		var id = dom_obj.id;
		REISEN_STORAGE['googlemap_init_obj'][id] = {
			dom: dom_obj,
			markers: coords.markers,
			geocoder_request: false,
			opt: {
				zoom: coords.zoom,
				center: null,
				scrollwheel: false,
				scaleControl: false,
				disableDefaultUI: false,
				panControl: true,
				zoomControl: true, //zoom
				mapTypeControl: false,
				streetViewControl: false,
				overviewMapControl: false,
				styles: REISEN_STORAGE['googlemap_styles'][coords.style ? coords.style : 'default'],
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
		};
		
		reisen_googlemap_create(id);

	} catch (e) {
		
		dcl(REISEN_STORAGE['strings']['googlemap_not_avail']);

	};
}

function reisen_googlemap_create(id) {
	"use strict";

	// Create map
	REISEN_STORAGE['googlemap_init_obj'][id].map = new google.maps.Map(REISEN_STORAGE['googlemap_init_obj'][id].dom, REISEN_STORAGE['googlemap_init_obj'][id].opt);

	// Add markers
	for (var i in REISEN_STORAGE['googlemap_init_obj'][id].markers)
		REISEN_STORAGE['googlemap_init_obj'][id].markers[i].inited = false;
	reisen_googlemap_add_markers(id);
	
	// Add resize listener
	jQuery(window).resize(function() {
		if (REISEN_STORAGE['googlemap_init_obj'][id].map)
			REISEN_STORAGE['googlemap_init_obj'][id].map.setCenter(REISEN_STORAGE['googlemap_init_obj'][id].opt.center);
	});
}

function reisen_googlemap_add_markers(id) {
	"use strict";
	for (var i in REISEN_STORAGE['googlemap_init_obj'][id].markers) {
		
		if (REISEN_STORAGE['googlemap_init_obj'][id].markers[i].inited) continue;
		
		if (REISEN_STORAGE['googlemap_init_obj'][id].markers[i].latlng == '') {
			
			if (REISEN_STORAGE['googlemap_init_obj'][id].geocoder_request!==false) continue;
			
			if (REISEN_STORAGE['googlemap_init_obj'].geocoder == '') REISEN_STORAGE['googlemap_init_obj'].geocoder = new google.maps.Geocoder();
			REISEN_STORAGE['googlemap_init_obj'][id].geocoder_request = i;
			REISEN_STORAGE['googlemap_init_obj'].geocoder.geocode({address: REISEN_STORAGE['googlemap_init_obj'][id].markers[i].address}, function(results, status) {
				"use strict";
				if (status == google.maps.GeocoderStatus.OK) {
					var idx = REISEN_STORAGE['googlemap_init_obj'][id].geocoder_request;
					if (results[0].geometry.location.lat && results[0].geometry.location.lng) {
						REISEN_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = '' + results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng();
					} else {
						REISEN_STORAGE['googlemap_init_obj'][id].markers[idx].latlng = results[0].geometry.location.toString().replace(/\(\)/g, '');
					}
					REISEN_STORAGE['googlemap_init_obj'][id].geocoder_request = false;
					setTimeout(function() { 
						reisen_googlemap_add_markers(id); 
						}, 200);
				} else
					dcl(REISEN_STORAGE['strings']['geocode_error'] + ' ' + status);
			});
		
		} else {
			
			// Prepare marker object
			var latlngStr = REISEN_STORAGE['googlemap_init_obj'][id].markers[i].latlng.split(',');
			var markerInit = {
				map: REISEN_STORAGE['googlemap_init_obj'][id].map,
				position: new google.maps.LatLng(latlngStr[0], latlngStr[1]),
				clickable: REISEN_STORAGE['googlemap_init_obj'][id].markers[i].description!=''
			};
			if (REISEN_STORAGE['googlemap_init_obj'][id].markers[i].point) markerInit.icon = REISEN_STORAGE['googlemap_init_obj'][id].markers[i].point;
			if (REISEN_STORAGE['googlemap_init_obj'][id].markers[i].title) markerInit.title = REISEN_STORAGE['googlemap_init_obj'][id].markers[i].title;
			REISEN_STORAGE['googlemap_init_obj'][id].markers[i].marker = new google.maps.Marker(markerInit);
			
			// Set Map center
			if (REISEN_STORAGE['googlemap_init_obj'][id].opt.center == null) {
				REISEN_STORAGE['googlemap_init_obj'][id].opt.center = markerInit.position;
				REISEN_STORAGE['googlemap_init_obj'][id].map.setCenter(REISEN_STORAGE['googlemap_init_obj'][id].opt.center);				
			}
			
			// Add description window
			if (REISEN_STORAGE['googlemap_init_obj'][id].markers[i].description!='') {
				REISEN_STORAGE['googlemap_init_obj'][id].markers[i].infowindow = new google.maps.InfoWindow({
					content: REISEN_STORAGE['googlemap_init_obj'][id].markers[i].description
				});
				google.maps.event.addListener(REISEN_STORAGE['googlemap_init_obj'][id].markers[i].marker, "click", function(e) {
					var latlng = e.latLng.toString().replace("(", '').replace(")", "").replace(" ", "");
					for (var i in REISEN_STORAGE['googlemap_init_obj'][id].markers) {
						if (latlng == REISEN_STORAGE['googlemap_init_obj'][id].markers[i].latlng) {
							REISEN_STORAGE['googlemap_init_obj'][id].markers[i].infowindow.open(
								REISEN_STORAGE['googlemap_init_obj'][id].map,
								REISEN_STORAGE['googlemap_init_obj'][id].markers[i].marker
							);
							break;
						}
					}
				});
			}
			
			REISEN_STORAGE['googlemap_init_obj'][id].markers[i].inited = true;
		}
	}
}

function reisen_googlemap_refresh() {
	"use strict";
	for (id in REISEN_STORAGE['googlemap_init_obj']) {
		reisen_googlemap_create(id);
	}
}

function reisen_googlemap_init_styles() {
    "use strict";
	// Init Google map
	REISEN_STORAGE['googlemap_init_obj'] = {};
	REISEN_STORAGE['googlemap_styles'] = {
		'default': []
	};
	if (window.reisen_theme_googlemap_styles!==undefined)
		REISEN_STORAGE['googlemap_styles'] = reisen_theme_googlemap_styles(REISEN_STORAGE['googlemap_styles']);
}
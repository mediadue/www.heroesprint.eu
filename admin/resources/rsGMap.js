function rsMap(data) {
  var g_obj = this;
  var g_markerClusterer = null;
  var g_map = null;
  var g_imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png';
  var g_markers = [];
  var g_data=data;
  
  this.refreshMap=function() {
    if (g_.markerClusterer) {
    	g_markerClusterer.clearMarkers();
    }

    var markerImage = new google.maps.MarkerImage(g_imageUrl, new google.maps.Size(24, 32));

    for (var i = 0; i < g_data.length; ++i) {
      var latLng = new google.maps.LatLng(g_data[i].lat, g_data[i].lon);
      var marker = new google.maps.Marker({
        position: latLng,
        draggable: false,
        icon: markerImage
      });
      g_markers.push(marker);
    }

    var zoom = 10;
    var size = 50;

    g_markerClusterer = new MarkerClusterer(g_map, g_markers, {
      maxZoom: zoom,
      gridSize: size,
      styles: [{
          url: getPathRoot + 'css/images/marker-empty.png',
          height: 35,
          width: 35,
          anchor: [16, 0],
          textColor: '#ff00ff',
          textSize: 10
        }]
    });
  }

  this.initialize=function() {
    g_map = new google.maps.Map(document.getElementById('map'), {
      zoom: 2,
      center: new google.maps.LatLng(39.91, 116.38),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var refresh = document.getElementById('refresh');
    google.maps.event.addDomListener(refresh, 'click', refreshMap);

    var clear = document.getElementById('clear');
    google.maps.event.addDomListener(clear, 'click', clearClusters);

    g_obj.refreshMap();
  }

  this.clearClusters=function(e) {
    e.preventDefault();
    e.stopPropagation();
    g_markerClusterer.clearMarkers();
  }
}
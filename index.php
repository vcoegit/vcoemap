<?php
//MarkerCluster-Plugin
// https://github.com/Leaflet/Leaflet.markercluster#using-the-plugin

//Stw. CSRF
session_start();
$_SESSION['csrf_token'] = uniqid('', true);
require('myClasses\Vcoeoci.class.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
    integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
    crossorigin=""/>
    <link rel="stylesheet" href="css/stylesheet.css">
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
    integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
    crossorigin=""></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <!-- MarkerClusters... -->
    <!-- https://unpkg.com/leaflet.markercluster@1.4.1/dist/ -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css">
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    
    <!-- <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" /> -->

    <style>
        /* .circle {
            width: 52px;
            height: 52px;
            line-height: 55px;
            background-image: url('circle6.gif');
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        } */

        .mask {
            position: absolute;
            top: -1px;                     /* minus half the div size */
            left: -1px;                    /* minus half the div size */
            width: 100px;                   /* the div size */
            height: 100px;                  /* the div size */
            background-color: rgb(256, 256, 256, 0.7); 
            border-radius: 50px;   /*Stw.: rounded corners*/        
            border: 2px solid #3188b6;       
            pointer-events: none;           /* send mouse events beneath this layer */
            text-align: center;
            line-height: 49px;
            font-size: 14px;
            color: #3188b6;
            font-weight: bold;
            /* opacity:0.5 */
        }
	</style>
    
    <title>vcoemap</title>

</head>
<body>

    <div id="mapid"></div>

 
 <?php
    $arr = [];
    $query = "SELECT * FROM ENTRIES";

    $vcoe = New myClasses\Vcoeoci;
    $arr = $vcoe->ArrayFromDB($query);

    //print("<pre>".print_r($arr,true)."</pre>");
    // echo print_r($arr,true);
?>

<script>



// how to access elements in multi-dimensional array in JavaScript
//alert( products[0][1] ); // Chocolate Cake

var places = <?php echo json_encode( $arr ) ?>;

    // var places = [
    //         [ 47.312759, 12.420044, "Somewhere in A (001)" ],
    //         [ 48.629278, 12.090454, "Somewhere in A (002)" ],
    //         [ 48.432845, 10.283203, "Somewhere in A (003)" ],
    //         [ 48.239309, 15.441284, "Somewhere in A (004)" ],
    //         [ 48.136767, 14.320679, "Somewhere in A (005)" ],
    //         [ 47.077604, 15.435791, "Somewhere in A (006)" ], 
    //         [ 48.167001, 16.487732, "Somewhere in A (007)" ], 
    //         [ 48.225588, 16.354523, "Somewhere in A (008)" ], 
    //         [ 48.199049, 16.279678, "Somewhere in A (009)" ], 
    //         [ 48.169291, 16.389885, "Somewhere in A (010)" ], 
    //         [ 48.225102, 16.356969, "Somewhere in A (011)" ], 
    //         [ 48.223472, 16.347141, "Somewhere in A (012)" ], 
    //         [ 48.175931, 16.383705, "Somewhere in A (013)" ], 
    // ];



	var mbAttr = 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
	    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
		'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';


    var grayscale = L.tileLayer(mbUrl, 
                                {
                                    id: 'mapbox/light-v9', 
                                    tileSize: 512, 
                                    zoomOffset: -1, 
                                    attribution: mbAttr,
                                }
                                );

    var streets   = L.tileLayer(mbUrl, 
                                {
                                    id: 'mapbox/streets-v11', 
                                    tileSize: 512, 
                                    zoomOffset: -1, 
                                    attribution: mbAttr,
                                }
                                );


    <?php 
        if(key_exists('center', $_POST) && $_POST['center']>0){
            $center = $_POST['center'];
        }

        if(key_exists('zoom', $_POST) && $_POST['zoom']>0){
            $center = $_POST['zoom'];
        }
    ?>

    var mymap = L.map('mapid', {
        center: [47.661688,13.090210],
        zoom: 8,
        layers: [grayscale]
    });

    //Loop through the markers array
    //Alle Punkte aus dem Places-Array der Map bzw. MarkerGroup und dann der Map hinzufügen...
    
    var markerGroup = L.layerGroup();
    var markers = L.markerClusterGroup({
        iconCreateFunction: function (cluster) {
        var markers = cluster.getAllChildMarkers();
        var html = '<div>' + markers.length + '</div>';
        return L.divIcon({ html: html, className: 'mask', iconSize: L.point(50, 50)});
        },
        spiderfyOnMaxZoom: false, showCoverageOnHover: true, zoomToBoundsOnClick: false  
	});

    for (var i=0; i<places.length; i++) {
        
        var lon = places[i][0];
        var lat = places[i][1];
        var title = places[i][2];
        var body = places[i][3];
        
        var markerLocation = new L.LatLng(lon, lat);
        var marker = new L.Marker(markerLocation);
        marker.bindPopup('<h3>'+title+'</h3>'+
                '<img src="images/lowgo.jpg" alt="" height="189" width="189">'+
                '<br><p>'+body+'</p>');
        
        markers.addLayer(marker);

        // mymap.addLayer(marker);

        markerGroup.addLayer(marker);

    }

    mymap.addLayer(markers);
    // mymap.addLayer(markerGroup);

    var baseMaps = {
        "Grayscale": grayscale,
        "Streets": streets
    };  

    var overlayMaps = {
        "MarkerGroup": markerGroup,
        "MarkerCluster": markers
    };


    L.control.layers(baseMaps, overlayMaps).addTo(mymap);

    var baseMaps = {
    "<span style='color: gray'>Grayscale</span>": grayscale,
    "Streets": streets,
    "MarkerGroup": markerGroup
    };




    mymap.on('click', function(e) {
        //alert(e.latlng);
        
        //Marker
        L.marker(e.latlng).addTo(mymap);
        
        //Popup
        var popup = L.popup()
        .setLatLng(e.latlng)
        .setContent(
            '<h3>was mir hier aufgefallen ist...</h3><p>This is a nice popup at...' + e.latlng + '</p><form action="commit.php" method="post"><div class="form-group"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="lat" value="' + e.latlng.lat + '"><input type="hidden" name="lng" value="' + e.latlng.lng + '"><input type="hidden" name="center" value="' + mymap.getCenter() + '"><input type="email" class="form-control" id="email" name="email" placeholder="deine@email.mail"><br /><input type="text" class="form-control" id="title" name="title" placeholder="Titel"><br /><br /><textarea type="text" class="form-control" id="report" name="report" rows="3" placeholder="Beschreibung"></textarea><br /><br /><button type="submit" class="btn btn-primary mb-2">Eintrag bestätigen</button></div></form>'
        )
        .openOn(mymap);

    });

</script>



</body>
</html>
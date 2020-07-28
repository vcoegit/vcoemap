<?php
//MarkerCluster-Plugin
// https://github.com/Leaflet/Leaflet.markercluster#using-the-plugin

//Overlay inspired by:
//https://www.w3schools.com/howto/howto_js_fullscreen_overlay.asp


//Stw. CSRF
session_start();

$now = time();
if (isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after']) {
    // this session has worn out its welcome; kill it and start a brand new one
    session_unset();
    session_destroy();
    session_start();
}

// either new or old, it should live at most for another hour
$_SESSION['discard_after'] = $now + 100;

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
    <script src="jQuery/jquery-3.5.1.min.js"></script>

    <!-- https://github.com/sciactive/pnotify/blob/master/README.md#getting-started -->
    <script type="text/javascript" src="node_modules/@pnotify/core/dist/PNotify.js"></script>
        <link href="node_modules/@pnotify/core/dist/PNotify.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="node_modules/@pnotify/mobile/dist/PNotifyMobile.js"></script>
        <link href="node_modules/@pnotify/core/dist/BrightTheme.css" rel="stylesheet" type="text/css" />
        <link href="node_modules/@pnotify/mobile/dist/PNotifyMobile.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
        PNotify.defaultModules.set(PNotifyMobile, {});
    </script>

    <script>
        function notifyUser(message) {

                    PNotify.alert({
                        title: "Nachricht:",
                        text: message
                    });
        };

        function showStackBottomRight(message, type) {
            if (typeof window.stackBottomRight === 'undefined') {
                window.stackBottomRight = new PNotify.Stack({
                dir1: 'up',
                dir2: 'left',
                firstpos1: 25,
                firstpos2: 25
                });
            }
            const opts = {
                title: "Nachricht",
                text: message,
                stack: window.stackBottomRight
            };
            switch (type) {
                case 'error':
                opts.type = 'error';
                break;
                case 'info':
                opts.type = 'info';
                break;
                case 'success':
                opts.type = 'success';
                break;
            }
            PNotify.alert(opts);
        }

    </script>

    <?php 
    
    $message =  isset($_SESSION['notification']) ? $_SESSION['notification'] : 'Um einen Eintrag hinzuzufügen, klicken Sie doppelt an die betreffende Stelle in der Karte.'; 

    if(strlen($message)>3){
        echo "    
        <script>
            $(document).ready(function(e) {
                showStackBottomRight('$message', 'success');
            });
        </script>";
    }

    $_SESSION['notification'] = '';

    ?>

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

    body {
    font-family: verdana,arial,sans-serif;
    }

    .overlay {
    height: 100%;
    width: 0;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #3188b6;
    overflow-x: hidden;
    transition: 0.5s;
    z-index: 40;
    opacity: 0.9;
    }

    .overlay-content {
    position: relative;
    top: 25%;
    width: 100%;
    text-align: left;
    margin-top: 30px;
    }

    .overlay a, .overlay h1, .overlay h2, .overlay h3, .overlay p, .overlay li {
    text-decoration: none;
    color: #f1fff1;
    transition: 0.3s;
    }

    .overlay li{
        font-size: 14px;
    }

    .overlay a:hover, .overlay a:focus {
    color: #f1f1f1;
    }

    .overlay .closebtn {
    position: absolute;
    top: 20px;
    right: 45px;
    font-size: 60px;
    }

    @media screen and (max-height: 450px) {
    .overlay a {font-size: 20px}
    .overlay .closebtn {
    font-size: 40px;
    top: 15px;
    right: 35px;
    }
    }

	</style>
    
    <title>vcoemap</title>

</head>
<body>
    
    <!-- <div id="logodiv"><img id="logo" src="images/vcoe_logo_rotated_left.jpg" alt="VCÖ-Logo"></div> -->

    <div id="logodiv">
        <a href="https://www.vcoe.at">
            <img id="svg" src="lowgo.jpg" alt="VCÖ-Logo" srcset="images/svglogo.svg">
        </a>
    </div>

    <div id="mapid" class="mapid"></div>

    <button type="button" class="btn btn-primary btn-sm infobtn" onclick="openNav()" id="slide-toggle">
    <svg width="30px" height="30px" viewBox="5 2 12 12" class="bi bi-info" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M8.93 6.588l-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588z"/>
        <circle cx="8" cy="4.5" r="1"/>
    </svg>
    </button>

<?php
    $arr = [];
    $query = "SELECT * FROM ENTRIES WHERE MARKED_DEL = 0";

    $vcoe = New myClasses\Vcoeoci;
    $arr = $vcoe->ArrayFromDB($query);

    //print("<pre>".print_r($arr,true)."</pre>");
    // echo print_r($arr,true);
?>

<script>



// how to access elements in multi-dimensional array in JavaScript
//alert( products[0][1] ); // Chocolate Cake

var places = <?php echo json_encode( $arr ) ?>;

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

    

    var mymap = L.map('mapid', {
        center: [
                <?= key_exists('centerLat', $_SESSION) ? $_SESSION['centerLat'] : 47.661688; ?>,
                <?= key_exists('centerLng', $_SESSION) ? $_SESSION['centerLng'] : 13.090210; ?>
                ],
        zoom: <?= key_exists('zoom', $_SESSION) ? $_SESSION['zoom'] : 8; ?>,
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
        var filepath = places[i][4];
        
        var markerLocation = new L.LatLng(lon, lat);
        var marker = new L.Marker(markerLocation);
        marker.bindPopup('<h4>'+title+'</h4>'+
                '<img src="' + filepath + '" alt="" height=auto width=250>'+
                '<br><p>'+body+'</p>');
        
        markers.addLayer(marker);

        // mymap.addLayer(marker);

        markerGroup.addLayer(marker);

    }

    mymap.addLayer(markers);
    // mymap.addLayer(markerGroup);

    // var baseMaps = {
    //     "Grayscale": grayscale,
    //     "Streets": streets
    // };  

    // var overlayMaps = {
    //     "MarkerGroup": markerGroup,
    //     "MarkerCluster": markers
    // };


    // L.control.layers(baseMaps, overlayMaps).addTo(mymap);

    // var baseMaps = {
    // "<span style='color: gray'>Grayscale</span>": grayscale,
    // "Streets": streets,
    // "MarkerGroup": markerGroup
    // };


    //Das Standardverhalten bei Doppelklick (bzw. beim Handy: zweimal hintippen) will ich jetzt nicht...
    mymap.doubleClickZoom.disable();

    // mymap.on('click', function(e) {
    //     notifyUser('Dopelklicken Sie auf Karte, wenn sie einen Eintrag machen wollen.');
    // });

    mymap.on('dblclick', function(e) {

        // notifyUser('Bitte füllen Sie das Formular aus...');
        //alert(e.latlng);

        //Marker
        L.marker(e.latlng).addTo(mymap);
        
        //Popup
        var popup = L.popup()
        .setLatLng(e.latlng)
        .setContent(
            '<div class="container"><h4>was mir hier aufgefallen ist...</h4><form action="commit.php" method="post" enctype="multipart/form-data"><div class="form-group"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="lat" value="' + e.latlng.lat + '"><input type="hidden" name="lng" value="' + e.latlng.lng + '"><input type="hidden" name="centerLng" value="' + mymap.getCenter().lng + '"><input type="hidden" name="centerLat" value="' + mymap.getCenter().lat + '"><input type="hidden" name="zoom" value="' + mymap.getZoom() + '"><input type="email" class="form-control" id="email" name="email" placeholder="deine@email.mail" required><br /> <div class="form-group"><label for="notificationtype">Kategorie</label><select class="form-control" id="notificationtype" name="notificationtype"><option>Tempo Kfz-Verkehr</option><option>zu wenig Platz (zu Fuß)</option><option>zu wenig Platz (Rad)</option><option>Gefahrenstelle</option><option>Rad-Abstellplatz fehlt</option><option>Sonstiges...</option></select></div><input type="text" class="form-control" id="title" name="title" placeholder="Titel"><br /><br /><textarea type="text" class="form-control" id="body" name="body" rows="3" placeholder="Beschreibung"></textarea><br /><br /><input type="hidden" name="MAX_FILE_SIZE" value="1024000"><input type="file" class="form-control-file" name="watchthispix" id="watchthispix" accept="image/*"><br /><br /><div class="buttons"><button type="submit" class="btn btn-primary btn-sm" id="submit">Eintrag bestätigen</button></div></div></form></div>'
        )
        .openOn(mymap);

    });

    /**
     * jQuery...
     */


    function openNav() {
    document.getElementById("formPanel").style.width = "100%";
    }

    function closeNav() {
    document.getElementById("formPanel").style.width = "0%";
    }



</script>

<div id="formPanel" class="overlay">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
  <div class="overlay-content">
    <div class="container">
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-in" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                <path fill-rule="evenodd" d="M6.5 3a.5.5 0 0 1 .5.5V6h2.5a.5.5 0 0 1 0 1H7v2.5a.5.5 0 0 1-1 0V7H3.5a.5.5 0 0 1 0-1H6V3.5a.5.5 0 0 1 .5-.5z"/>
            </svg> In die Karte zoomen.</p>
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-mouse2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 5.188C3 2.341 5.22 0 8 0s5 2.342 5 5.188v5.625C13 13.658 10.78 16 8 16s-5-2.342-5-5.188V5.189zm4.5-4.155C5.541 1.289 4 3.035 4 5.188V5.5h3.5V1.033zm1 0V5.5H12v-.313c0-2.152-1.541-3.898-3.5-4.154zM12 6.5H4v4.313C4 13.145 5.81 15 8 15s4-1.855 4-4.188V6.5z"/>
            </svg> Doppelklick in die Karte.</p>      
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-input-cursor" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 5h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4v1h4a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4v1zM6 5V4H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v-1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h4z"/>
                <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13A.5.5 0 0 1 8 1z"/>
            </svg> Problem beschreiben.</p>     
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-paperclip" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
            </svg> Bild anhängen. (optional)</p>  
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path fill-rule="evenodd" d="M4.646 8.354a.5.5 0 0 0 .708 0L8 5.707l2.646 2.647a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 0 0 0 .708z"/>
                <path fill-rule="evenodd" d="M8 11.5a.5.5 0 0 0 .5-.5V6a.5.5 0 0 0-1 0v5a.5.5 0 0 0 .5.5z"/>
            </svg> Beitrag senden.</p>  
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
            </svg> Bestätigungslink im Email anklicken.</p>  
            </div>
        </div>
    </div>
  </div>
</div>

</body>
</html>
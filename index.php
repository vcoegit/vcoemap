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

include("./includes/header.php");

?>

    <script>

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
    
    // $message =  key_exists('notification', $_SESSION) ? $_SESSION['notification'] : 'Um einen Eintrag hinzuzufügen, klicken Sie doppelt an die betreffende Stelle in der Karte.'; 

    $noticode = 1; //default
    $message = 'Auf dieser Website können Sie Problemstellen auf Ihren Wegen zu Fuß oder mit dem Fahrrad eintragen. Bitte klicken Sie auf der Karte auf die Straße, wo sie eine Problemstelle sehen. Nutzen Sie die entsprechende Kategorie und beschreiben Sie bitte kurz das Problem. Sie erhalten dann an die angegebene E-Mail Adresse einen Bestätigungslink. Nach der Bestätigung wird der Beitrag frei geschaltet. Sie können selbstverständlich mehrere Problemstellen eintragen. Danke!';

    if(key_exists('noticode', $_GET)){
        $noticode = $_GET['noticode'];
        switch ($noticode) {
            case 1:
                $message = "Um einen Eintrag hinzuzufügen, klicken Sie doppelt an die betreffende Stelle in der Karte.";
                break;
            case 2:
                $message = "Wir haben Ihnen ein Email geschickt. Bitte bestätigen Sie ihre Email-Adresse indem Sie auf den darin enthaltenen Link klicken, damit wir ihren Beitrag freischalten können.";
                break;
            case 3:
                $message = "Vielen Dank, Ihre Email-Adresse wurde bestätigt, Ihr Eintrag erscheint auf unserer Karte! Wir freuen uns über weitere Beiträge.";
                break;
            case 4:
                $message = "Hier ist offenbar ein Fehler passiert! Wir konnten Sie leider nicht identifizieren.";
                break;    
            case 5:
                $message = "Vielen Dank! Ihr Eintrag wurde veröffentlicht! Wir freuen uns über weitere Beiträge.";    
                break;      
            default:
                $message = "Um einen Eintrag hinzuzufügen, klicken Sie doppelt an die betreffende Stelle in der Karte.";

        }
    } 

    if(in_array($noticode, array(1, 2))){
        $alertType = '';
    }elseif(in_array($noticode, array(3, 5))){
        $alertType = 'success';
    }elseif(in_array($noticode, array(4) )){
        $alertType = 'error';
    }else{
        $alertType = null;
    };

    if(strlen($message)>3){
        echo "    
        <script>
            $(document).ready(function(e) {
                Swal.fire({
                text: '" . $message . "',
                icon: '" . $alertType . "'
                })
            });
        </script>";
    }

    $_SESSION['notification'] = '';

    ?>
    
    <title>vcoemap</title>

</head>

<body>
    
<?php
    include('./includes/navigation.php');
?>

    <div id="mapid" class="mapid"></div>

<?php
    $arr = [];
    $query = "SELECT * FROM ENTRIES WHERE MARKED_DEL = 0";

    $vcoe = New myClasses\Vcoeoci;
    $arr = $vcoe->ArrayFromDB($query);
?>


<!-- <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script> -->
<script src="vendor/calvinmetcalf/catiline.js"></script>
<script src="vendor/calvinmetcalf/leaflet.shpfile.js"></script>
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
    
    // betrifft die Legende...
    var legend = L.control({position: 'bottomleft'});

    legend.onAdd = function (map) {

        var div = L.DomUtil.create('div', 'info legend');
        div.innerHTML = '<div style="background-color:#FFFFFF;border-color:#0082b2; border-style:solid;border-width:1px; padding:1em; border-radius:25px;"><h6 style="background-color:#FFFFFF;">Legende:</h6><table style="height:100%; width:100%;"><tbody><tr><td style="vertical-align:-25%"><img src="images/biking.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Gefahrenstelle / zu wenig Platz Fahrrad</p></td></tr><tr><td style="vertical-align:-25%"><img src="images/walking.svg" style="height:30px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Gefahrenstellt / zu wenig Platz Gehen</p</td></tr><tr><td style="vertical-align:-25%"><img src="images/car.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> zu hohes Tempo Kfz-Verkehr</p></td></tr><td style="vertical-align:-25%"><img src="images/exclamation.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Sonstige Problemstelle</p></td></tr></tbody></table></div>';

        return div;
    };

    legend.addTo(mymap);


    // mymap.setLayoutProperty('country-label', 'text-field', [
    //     'get',
    //     'name_de'
    // ]);

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
        var notificationtype = places[i][5];
        var plz = places[i][6];
        
        var iconurl;
        var iconsize;

        switch (notificationtype) {
            case 'Gefahrenstelle Gehen':
                iconurl = 'images/walking.svg';
                iconsize = 32;
                break;
            case 'Gefahrenstelle Rad':
                iconurl = 'images/biking.svg';
                iconsize = 32;
                break;
            case 'zu hohes Tempo Kfz':
                iconurl = 'images/car.svg';
                iconsize = 32;
                break;
            case 'zu wenig Platz Gehen':
                iconurl = 'images/walking.svg';
                iconsize = 40;
                break;
            case 'zu wenig Platz Rad':
                iconurl = 'images/biking.svg';
                iconsize = 32;
                break;
            case 'Sonstiges':
                iconurl = 'images/exclamation.svg';
                iconsize = 32;
                break;
            default:
                iconurl = 'images/exclamation.svg';
                iconsize = 32;
                break;
        }


        var iconOptions = {
            iconUrl: iconurl,
            iconSize: [iconsize, iconsize]
        }
        
        var customIcon = L.icon(iconOptions);

        var markerOptions = {
            icon: customIcon
        }

        var markerLocation = new L.LatLng(lon, lat);
        var marker = new L.Marker(markerLocation, markerOptions);
        marker.bindPopup('<h6>'+notificationtype+'</h6>'+
                '<img src="' + filepath + '" alt="" height=auto width=250>'+
                '<p>'+body+'</p>'+
                '<p>PLZ: '+plz+'</p>'
                );
        
        markers.addLayer(marker);

        markerGroup.addLayer(marker);

    }

    mymap.addLayer(markers);

    // var shpfile = new L.Shapefile('VGD-Oesterreich_gen_250.zip', {
    //     onEachFeature: function(feature, layer) {
    //         if (feature.properties) {
    //             layer.bindPopup(Object.keys(feature.properties).map(function(k) {
    //                 return k + ": " + feature.properties[k];
    //             }).join("<br />"), {
    //                 maxHeight: 200
    //             });
    //         }
    //     },
    //     style: function (feature) {
    //             return {fillColor: '#1D1061'};
    //     }
    // });
    // shpfile.addTo(mymap);
    // shpfile.once("data:loaded", function() {
    //     console.log("finished loaded shapefile");
    // });

    // mymap.addLayer(shpfile);

    // shpfile.setZIndex(50);

    //mymap.addLayer(markerGroup);

    var baseMaps = {
        "Grayscale": grayscale,
        "Streets": streets
    };  

    var overlayMaps = {
        "Karteneinträge": markers
    };

    L.control.layers(baseMaps, overlayMaps).addTo(mymap);

    // var baseMaps = {
    // "<span style='color: gray'>Grayscale</span>": grayscale,
    // "Streets": streets,
    // "MarkerGroup": markerGroup
    // };


    //Das Standardverhalten bei Doppelklick (bzw. beim Handy: zweimal hintippen) will ich jetzt nicht...
    mymap.doubleClickZoom.disable();

    mymap.on('click', function(e){
 
    });

    mymap.on('dblclick', function(e) {

        //jedenfalls muss jetzt mal die Legende Weg...
        mymap.removeControl(legend);

        Swal.fire({
            // title: 'Eintrag an dieser Stelle?',
            text: "Wollen sie an dieser Stelle einen Karteneintrag machen?",
            type: 'question',
            showCancelButton: true,
            // confirmButtonColor: '#3085d6',
            // cancelButtonColor: '#d33',
            confirmButtonText: 'Ja!',
            cancelButtonText: 'Nein, abbrechen!'
        }).then((result) => {
        if (result.value) {
            setEntry(e.latlng)
            // Swal.fire(
            // 'Deleted!',
            // 'Your file has been deleted.',
            // 'success'
            // )
            }
        })

    });

    function setEntry(latlng){

        //Marker
        //L.marker(latlng).addTo(mymap);

        //Popup
        var popup = L.popup()
        .setLatLng(latlng)
        .setContent(
            '<div class="container"><h4>was mir hier aufgefallen ist...</h4><form action="commit.php" method="post" enctype="multipart/form-data"><div class="form-group"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="lat" value="' + latlng.lat + '"><input type="hidden" name="lng" value="' + latlng.lng + '"><input type="hidden" name="centerLng" value="' + mymap.getCenter().lng + '"><input type="hidden" name="centerLat" value="' + mymap.getCenter().lat + '"><input type="hidden" name="zoom" value="' + mymap.getZoom() + '"><input type="email" class="form-control" id="email" name="email" placeholder="deine@email.mail" required><br /> <div class="form-group"><label for="notificationtype">Kategorie</label><select class="form-control" id="notificationtype" name="notificationtype"><option>Gefahrenstelle Gehen</option><option>Gefahrenstelle Rad</option><option>zu hohes Tempo Kfz</option><option>zu wenig Platz Rad</option><option>zu wenig Platz Gehen</option><option>Sonstiges</option></select></div><input type="text" class="form-control" id="plz" name="plz" placeholder="PLZ"><br /><br /><textarea type="text" class="form-control" id="body" name="body" rows="3" placeholder="Beschreibung"></textarea><br /><br /><input type="hidden" name="MAX_FILE_SIZE" value="1024000"><input type="file" class="form-control-file" name="watchthispix" id="watchthispix" accept="image/*"><br /><br /><div class="buttons"><button type="submit" class="btn btn-primary btn-sm" id="submit">Eintrag bestätigen</button></div></div></form></div>'
        )
        .openOn(mymap);

    }

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
            </svg> In die Karte zoomen</p>
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-mouse2" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M3 5.188C3 2.341 5.22 0 8 0s5 2.342 5 5.188v5.625C13 13.658 10.78 16 8 16s-5-2.342-5-5.188V5.189zm4.5-4.155C5.541 1.289 4 3.035 4 5.188V5.5h3.5V1.033zm1 0V5.5H12v-.313c0-2.152-1.541-3.898-3.5-4.154zM12 6.5H4v4.313C4 13.145 5.81 15 8 15s4-1.855 4-4.188V6.5z"/>
            </svg> Doppelklick in die Karte</p>      
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-input-cursor" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 5h4a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-4v1h4a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-4v1zM6 5V4H2a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h4v-1H2a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h4z"/>
                <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v13a.5.5 0 0 1-1 0v-13A.5.5 0 0 1 8 1z"/>
            </svg> Problem beschreiben</p>     
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-paperclip" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0V3z"/>
            </svg> Bild anhängen (optional)</p>  
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-up-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path fill-rule="evenodd" d="M4.646 8.354a.5.5 0 0 0 .708 0L8 5.707l2.646 2.647a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 0 0 0 .708z"/>
                <path fill-rule="evenodd" d="M8 11.5a.5.5 0 0 0 .5-.5V6a.5.5 0 0 0-1 0v5a.5.5 0 0 0 .5.5z"/>
            </svg> Beitrag senden</p>  
            </div>
        </div>
        <div class="row howto">
            <div class="col-sm-6">
            <p><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path fill-rule="evenodd" d="M10.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.236.236 0 0 1 .02-.022z"/>
            </svg> Bestätigungslink im Email anklicken</p>  
            </div>
        </div>
    </div>
  </div>
</div>

</body>
</html>
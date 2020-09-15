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
$_SESSION['discard_after'] = $now + 1000;

$_SESSION['csrf_token'] = uniqid('', true);

require('myClasses/Vcoeoci.class.php');

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
    $message = 'Hier können Sie Problemstellen auf Ihren Wegen zu Fuß oder mit dem Fahrrad eintragen. Bitte zoomen Sie je nach Gerät per Mausrad, den +/- Tasten oder mit den Fingern am Bildschirm zu der Stelle, wo Sie ein Problem wahrnehmen. Per Doppelklick oder Double Tap erzeugen Sie einen neuen Eintrag.';

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
            case 6:
                $message = "Oops, hier ist offenbar ein Fehler passiert! Wir konnten ihre Eingaben nicht verarbeiten.";    
                break;     
            default:
                $message = "Um einen Eintrag hinzuzufügen, klicken Sie doppelt an die betreffende Stelle in der Karte.";

        }
    } 

    if(in_array($noticode, array(1, 2))){
        $alertType = '';
    }elseif(in_array($noticode, array(3, 5))){
        $alertType = 'success';
    }elseif(in_array($noticode, array(4, 6) )){
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
    
    <title>Problemstellen, Bewegungsaktive Mobilität</title>

</head>

<body>
    
<?php
    include('./includes/navigation.php');
?>

    <div id="mapid" class="mapid"></div>

<?php
    $arr = [];
    $query = "SELECT * FROM entries WHERE MARKED_DEL = 0";

    $vcoe = New myClasses\Vcoeoci;
    $arr = $vcoe->EntriesArrayFromDB($query);
?>


<!-- <script src="http://cdn.leafletjs.com/leaflet-0.6.4/leaflet.js"></script> -->
<script src="vendor/calvinmetcalf/catiline.js"></script>
<script src="vendor/calvinmetcalf/leaflet.shpfile.js"></script>
<script>

    var places = <?php echo json_encode( $arr ) ?>;

    //Daten aus hit.php...
    var gemeinde;
    var bundesland;
    var staat;

	var mbAttr = 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
	    '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
		'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		mbUrl = 'https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';


    // var BasemapAT_basemap = L.tileLayer('https://maps{s}.wien.gv.at/basemap/geolandbasemap/{type}/google3857/{z}/{y}/{x}.{format}', {
	// maxZoom: 20,
	// attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
	// subdomains: ["", "1", "2", "3", "4"],
	// type: 'normal',
	// format: 'png',
	// bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    // });

    var BasemapAT_grau = L.tileLayer('https://maps{s}.wien.gv.at/basemap/bmapgrau/{type}/google3857/{z}/{y}/{x}.{format}', {
	maxZoom: 19,
	attribution: 'Datenquelle: <a href="https://www.basemap.at">basemap.at</a>',
	subdomains: ["", "1", "2", "3", "4"],
	type: 'normal',
	format: 'png',
	bounds: [[46.35877, 8.782379], [49.037872, 17.189532]]
    });

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
        layers: [grayscale, BasemapAT_grau],
        minZoom: 6,
        maxZoom: 20
    });
    
    // betrifft die Legende...
    var legend = L.control({position: 'topleft'});

    legend.onAdd = function (map) {

        var div = L.DomUtil.create('div', 'infolegend');
        div.innerHTML = '<div class="container"><div style="background-color:#FFFFFF;border-color:#0082b2; border-style:solid;border-width:1px; padding:1em; border-radius:5px;"><h6 style="background-color:#FFFFFF;">Legende:</h6><button type="button" class="close" id = "legendclose" onclick="toggleLegend();" aria-label="Close"><span aria-hidden="true">&times;</span></button><table style="height:100%; width:100%;"><tbody><tr><td style="vertical-align:-25%"><img src="images/biking.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Gefahrenstelle / Problemstelle Fahrrad</p></td></tr><tr><td style="vertical-align:-25%"><img src="images/walking.svg" style="height:30px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Gefahrenstelle / Problemstelle Gehen</p</td></tr><tr><td style="vertical-align:-25%"><img src="images/car_exclamation.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> zu hohes Tempo Kfz-Verkehr</p></td></tr><td style="vertical-align:-25%"><img src="images/exclamation.svg" style="height:24px; width:auto"></td><td style="vertical-align:-25%"><p style="vertical-align:-25%"> Sonstige Problemstelle</p></td></tr></tbody></table></div></div>';

        return div;
    };

    legend.addTo(mymap);

    //removeFrom( <Map> map )

    showLegend = true;  // default value showing the legend

    var toggleLegend = function(){
        if(showLegend === true){
        /* use jquery to select your DOM elements that has the class 'legend' */
           $('.infolegend').hide(); 
           showLegend = false; 
        }else{
           $('.infolegend').show();
           showLegend = true; 
        }
    }

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
        var toc = places[i][7];
        var entryid = places[i][8];
        
        var iconurl;
        var iconsize;

        switch (notificationtype) {
            case 'Gefahrenstelle Gehen':
                iconurl = 'images/walking.svg';
                iconsize = 32;
                break;
            case 'Gefahrenstelle Radfahren':
                iconurl = 'images/biking.svg';
                iconsize = 32;
                break;
            case 'zu hohes Tempo Kfz':
                iconurl = 'images/car_exclamation.svg';
                iconsize = 36;
                break;
            case 'zu wenig Platz Gehen':
                iconurl = 'images/walking.svg';
                iconsize = 32;
                break;
            case 'zu wenig Platz Radfahren':
                iconurl = 'images/biking.svg';
                iconsize = 32;
                break;
            case 'Problemstelle Radfahren':
                iconurl = 'images/biking.svg';
                iconsize = 32;
            case 'Problemstelle Gehen':
                iconurl = 'images/walking.svg';
                iconsize = 32;         
            case 'Sonstiges':
                iconurl = 'images/exclamation.svg';
                iconsize = 32;
                break;
            case 'Sonstige Problemstelle':
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

        var strimage = filepath.length > 0 ? '<img src="' + filepath + '" alt="" height=auto width=250>' : '';

        var strpopup = '<div style="background-color:#EAF3F8;"><h6 style="background-color:#EAF3F8; color:#3188b6">'+notificationtype+'</h6>'+ 
                strimage +
                '<p style="color: #040404";>Beschreibung:<br>'+body+'</p>'+
                '<p style="color: #040404";>PLZ: '+plz+'</p><br><br>'+
                '<p style="color:#3188b6">('+entryid+')</p></div>'

        var markerLocation = new L.LatLng(lon, lat);
        var marker = new L.Marker(markerLocation, markerOptions);
        marker.bindPopup(strpopup);
        
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
        "Basemap": BasemapAT_grau,
        "Karteneinträge": markers
    };

    L.control.layers(baseMaps, overlayMaps).addTo(mymap);

    mymap.on('dblclick', function(e){
        // mymap.removeControl(legend);
        
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

                $.post( "hit.php", { lat: e.latlng.lat, lng: e.latlng.lng })
                    .done(function( data ) {
                            let arr_hit = data.split('|');
                            // alert( "Data Loaded: " + jsondata.gemeinde);
                            gemeinde = arr_hit[0];
                            bundesland = arr_hit[1];
                            staat = arr_hit[2];
                    })
                    .fail(function() {
                            alert( "error" );
                    })
                    .always(function() {
                            // was auf jeden Fall passieren soll...
                            // alert( "finished" );
                            $('#exampleModal').modal('show');

                            //wichtig!!! - Die Keyboardeingaben müssen für Leaflet deaktiviert werden,
                            //damit es bei der Eingabe in das Formular nicht zu unerwartetem Verhalten kommt (z.B. Zooming out mit underscore, usw.)
                            mymap.keyboard.disable();    

                            $('#gemeinde').val(gemeinde);
                            $('#bundesland').val(bundesland);
                            $('#staat').val(staat);

                            $('#centerLng').val(mymap.getCenter().lng);
                            $('#centerLat').val(mymap.getCenter().lat);
                            $('#lng').val(e.latlng.lng);
                            $('#lat').val(e.latlng.lat);
                            $('#zoom').val(mymap.getZoom());
                    });
            

            }
        })

        // var button = $(event.relatedTarget) // Button that triggered the modal
        // var recipient = button.data('whatever') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        // var modal = $('#exampleModal')
        // modal.find('.modal-title').text('New message to ' + 'bla')
        // modal.find('.modal-body input').val('blabla')

    });

    //Das Standardverhalten bei Doppelklick (bzw. beim Handy: zweimal hintippen) will ich jetzt nicht...
    mymap.doubleClickZoom.disable();

    // mymap.on('dblclick', function(e) {

    //     Swal.fire({
    //         // title: 'Eintrag an dieser Stelle?',
    //         text: "Wollen sie an dieser Stelle einen Karteneintrag machen?",
    //         type: 'question',
    //         showCancelButton: true,
    //         // confirmButtonColor: '#3085d6',
    //         // cancelButtonColor: '#d33',
    //         confirmButtonText: 'Ja!',
    //         cancelButtonText: 'Nein, abbrechen!'
    //     }).then((result) => {
    //     if (result.value) {
    //         setEntry(e.latlng)
    //         }
    //     })

    // });

    // function setEntry(latlng){

    //     //Marker
    //     //L.marker(latlng).addTo(mymap);

    //     //Popup
    //     var popup = L.popup()
    //     .setLatLng(latlng)
    //     .setContent(
    //         '<div class="container" style="z-index:10000"><h4>was mir hier aufgefallen ist...</h4><form action="commit.php" method="post" enctype="multipart/form-data"><div class="form-group"><input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token']; ?>"><input type="hidden" name="lat" value="' + latlng.lat + '"><input type="hidden" name="lng" value="' + latlng.lng + '"><input type="hidden" name="centerLng" value="' + mymap.getCenter().lng + '"><input type="hidden" name="centerLat" value="' + mymap.getCenter().lat + '"><input type="hidden" name="zoom" value="' + mymap.getZoom() + '"><input type="email" class="form-control" id="email" name="email" placeholder="deine@email.mail" required><br /> <div class="form-group"><label for="notificationtype">Kategorie</label><select class="form-control" id="notificationtype" name="notificationtype"><option>Gefahrenstelle Gehen</option><option>Gefahrenstelle Rad</option><option>zu hohes Tempo Kfz</option><option>Problemstelle Rad</option><option>Problemstelle Gehen</option><option>Sonstiges</option></select></div><input type="text" class="form-control" id="plz" name="plz" placeholder="PLZ"><br /><br /><textarea type="text" class="form-control" id="body" name="body" rows="3" placeholder="Beschreibung"></textarea><br /><br /><input type="hidden" name="MAX_FILE_SIZE" value="1024000"><input type="file" class="form-control-file" name="watchthispix" id="watchthispix" accept="image/*"><br /><br /><div class="buttons"><button type="submit" class="btn btn-primary btn-sm" id="submit">Eintrag bestätigen</button></div></div></form></div>'
    //     )
    //     .openOn(mymap);

    // }

    // function openNav() {
    // document.getElementById("formPanel").style.width = "100%";
    // }

    // function closeNav() {
    // document.getElementById("formPanel").style.width = "0%";
    // }

</script>

<div id="info-svg"><img id="info" src="images/list.svg" alt="" onclick=toggleLegend()></div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Karteneintrag erstellen...</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>


            <form  action="commit.php" method="post" id="newmapentry" enctype="multipart/form-data">

                <div class="modal-body">
                    
                            <p>Wählen Sie hier bitte die passende Kategorie, geben Sie die Postleitzahl an und beschreiben Sie kurz das Problem. Sie können auch ein Foto der Problemstelle anfügen. Sie erhalten an die angegebene E-Mail Adresse einen Bestätigungslink. Nach der Bestätigung wird der Beitrag freigeschaltet. Sie können gerne mehrere Problemstellen eintragen. Danke!</p>

                            <div class="form-group">
                                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="lat" id="lat" class="form-control" value="">
                                <input type="hidden" name="lng" id="lng" class="form-control" value="">
                                <input type="hidden" name="centerLng" id="centerLng" class="form-control" value="">
                                <input type="hidden" name="centerLat" id="centerLat" class="form-control" value="">
                                <input type="hidden" name="zoom" id="zoom" class="form-control" value="">
                            </div>
                            
                            <div class="form-group">
                                <label for="vorname">Vorname*</label>
                                <input type="text" class="form-control" id="vorname" name="vorname" placeholder="dein Vorname" maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <label for="nachname">Nachname*</label>
                                <input type="text" class="form-control" id="nachname" name="nachname" placeholder="dein Nachname" maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email*</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="deine@email.mail" maxlength="50" required>
                            </div>

                            <div class="form-group">
                                <label for="notificationtype">Kategorie*</label>
                                <select class="form-control" id="notificationtype" name="notificationtype">
                                    <option>Gefahrenstelle Gehen</option>
                                    <option>Gefahrenstelle Radfahren</option>
                                    <option>zu hohes Tempo Kfz</option>
                                    <option>Problemstelle Radfahren</option>
                                    <option>Problemstelle Gehen</option>
                                    <option>Sonstige Problemstelle</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="plz">PLZ</label>
                                <input type="text" class="form-control" id="plz" name="plz" pattern="[0-9]*" placeholder="PLZ (Problemstelle)" required>
                            </div>

                            <div class="form-group">
                                <label for="gemeinde">Gemeinde der Problemstelle</label>
                                <input type="text" name="gemeinde" id="gemeinde" class="form-control" value="" readonly>
                            </div>

                            <div class="form-group">
                                <label for="bundesland">Bundesland der Problemstelle</label>
                                <input type="text" name="bundesland" id="bundesland" class="form-control" value="" readonly>
                            </div>

                            <div class="form-group">
                                <label for="body">Beschreibung*</label>
                                <textarea type="text" class="form-control" id="body" name="body" rows="3" placeholder="Beschreibung" required></textarea>
                            </div>

                            <div class="form-group">
                                <input type="hidden" name="MAX_FILE_SIZE" value="1024000">
                                <label for="file">Hier können Sie ein Bild hochladen...</label>
                                <input type="file" id="file" class="form-control-file" name="watchthispix" id="watchthispix" accept="image/*">
                            </div>

                            <div class="form-group">
                                <label for="toc">Ich akzeptiere die <a href="impressum.php#nutzungsbedingungen" target="_blank" rel="noopener noreferrer">Nutzungsbedingungen*</a></label>
                                <input type="checkbox" name="toc" id="toc" required>
                            </div>
                    
                </div> <!--Modal Body-->

                <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                        <button type="submit" class="btn btn-primary" onclick="submit_entry()" id="submit">Eintrag bestätigen</button>
                </div> <!--Modal Footer-->

            </form> <!-- damit die Front-End-Validierung funktioniert muss der Submit-Button innerhalb des Forms sein-->

        </div>
    </div>
</div>

<script>

function submit_entry(){
    $('#newmapentry').submit();
}

</script>

</body>
</html>
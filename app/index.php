<!DOCTYPE html>
<html>

	<head>	
	
		<title>Disaster Master | MAP</title>
		
		<link rel="stylesheet" href="main.css" />
		
		<script language="javascript" type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBwsJveZEYGK03cSrusB4vgMTcEIWGbkiw"></script>
		
		<script language="javascript" type="text/javascript" src="disasterRec.js"></script>
		
		<script type="text/javascript" src="markerclusterer.js"></script>
        <script>
        
        /*
        window.searched_markers_lat = [];
        window.searched_markers_long = [];
        
        
        function searchBar() {
            
            searched_markers_lat = [];
            searched_markers_long = [];
            
            var submitted_text = (document.getElementById("text_input").value).toLowerCase();
            for(f in datasetReturn){
                var current_json_array = datasetReturn[f];
                var current_json_array_lat = current_json_array.lat;
                var current_json_array_lon = current_json_array.lon;
                if (current_json_array.title == null){
                    
                }
                else{
                    search_title = current_json_array.title.toLowerCase();
                    var searching_logic = search_title.search(submitted_text);
                
                    if (searching_logic >= 0) {
                        searched_markers_lat.push(current_json_array_lat);
                        searched_markers_long.push(current_json_array_lon);
                    }
                }
                initialize();
                
            }
            //DON'T TOUCH THIS!!
            return false;
        }
        */
        
        function initialize() {

            var myLatlng = new google.maps.LatLng(-29.113775,134.296875);
            
            var apiKey = "8TvT6h8adR3HU2g";
            
            var all_marker_locations = [];
            
            var markers_lat = [];
            var markers_long = [];
            window.markers_title = [];
            var description = [];
            var start_date = [];
            var end_date = [];
            var regions = [];
            var deaths = [];
            var injuries = [];
            var damages = [];
            
            var sentinal_lat = [];
            var sentinal_long = [];
            
            var lastSelectedMarker;
            
            var datasetSource = 'http://cgscomputing.com/jcdcjk/GovHack2015/api/data/aemkDisasterEvent.csv';

            var datasetUrl = "http://cgscomputing.com/jcdcjk/GovHack2015/api/?source="+datasetSource+'&apiKey='+apiKey;
            
            var datasetReq = new XMLHttpRequest();
            datasetReq.open("GET", datasetUrl , false);
            datasetReq.send();
            
            var datasetReturn = datasetReq.responseText; // This is the JSON response
            
            datasetReturn = JSON.parse(datasetReturn);
            
            var sentinal_data = "http://cgscomputing.com/jcdcjk/GovHack2015/api/?source=http://cgscomputing.com/jcdcjk/GovHack2015/api/data/hotspot_current.csv&apiKey=8TvT6h8adR3HU2g";
            
            var datasetReq = new XMLHttpRequest();
            datasetReq.open("GET", sentinal_data , false);
            datasetReq.send();
            
            var sentinal_json_response = datasetReq.responseText; // This is the JSON response
            
            sentinal_json_response = JSON.parse(sentinal_json_response);
            
            for (i in datasetReturn){
                
                var item = datasetReturn[i];
                
                markers_title.push(datasetReturn[i].title);
                markers_lat.push(parseFloat(datasetReturn[i].lat));
                markers_long.push(parseFloat(datasetReturn[i].lon));
                description.push(datasetReturn[i].description);
                start_date.push(datasetReturn[i].startDate);
                end_date.push(datasetReturn[i].endDate);
                regions.push(datasetReturn[i].regions);
                deaths.push(datasetReturn[i].Deaths);
                injuries.push(datasetReturn[i].Injuries);
                damages.push(item["Insured Cost"]);
    
            }
            
            /*
            for (h in sentinal_json_response){
                var targetItem = sentinal_json_response[h]
                sentinal_lat.push(parseFloat(targetItem["130.454056"]));
                sentinal_long.push(parseFloat(targetItem["_11.407911000000013"]));
            }
            
            console.log(sentinal_lat);
            console.log(sentinal_long);
            */
            
            var mapCanvas = document.getElementById('full_map_canvas');
          
            var mapOptions = {
                zoom: 5,
                //Map Styles Taken from https://snazzymaps.com/
                styles: [{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#e0efef"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"hue":"#1900ff"},{"color":"#c0e8e8"}]},{"featureType":"road","elementType":"geometry","stylers":[{"lightness":100},{"visibility":"simplified"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry","stylers":[{"visibility":"on"},{"lightness":700}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#7dcdcd"}]}],
                center: myLatlng,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_LEFT
                },
                panControl: false,
                streetViewControl: false,
                zoomControl: false,
                zoomControlOptions: {
                  position: google.maps.ControlPosition.TOP_RIGHT
                }

            
            }
            
            var map = new google.maps.Map(mapCanvas, mapOptions);

            var markers = [];
            
            for (i in markers_lat) {
                
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(markers_lat[i], markers_long[i]),
                    //map: map,
                    title: markers_title[i],
                    icon: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
                });
                
                all_marker_locations.push(marker);

                google.maps.event.addListener(marker, "click", function (event) {
                    
                    if (map.getZoom() < 11) {
                        map.setZoom(11);
                        map.setCenter(this.getPosition());
                    }
                    
                    else{
                        map.setCenter(this.getPosition());
                    }
                    
                    var clicked_lat = this.position.lat();
                    var clicked_long = this.position.lng();
                    
                    window.correct_data = "TEST";
                    
                    for(e in datasetReturn){
                                
                        if(datasetReturn[e].lat == clicked_lat){
                            correct_data = e;
                        }
                              
                        e++;
                    }
                    
                    var disaster_stats = document.getElementById("disaster_stats");
                    disaster_stats.setAttribute('class', 'show_information');  
                    
                    var info_title = document.getElementById("disaster_title");
                    var info_description = document.getElementById("disaster_description");
                    
                    
                    var injury = document.getElementById("injuries");
                        
                    var confirmed_deaths = "0";
                    var confirmed_injuries = "0";
                    var confirmed_damages = "0";
                    var confirmed_title = "UNKNOWN";
                        
                    if (deaths[correct_data] == undefined) {
                        var confirmed_deaths = "0";
                    }
                        
                    else {
                        confirmed_deaths = deaths[correct_data];
                    }
                        
                    if (injuries[correct_data] == undefined) {
                        var confirmed_injuries = "0";
                    }
                        
                    else {
                        confirmed_injuries = injuries[correct_data];
                    }
                    
                    if (damages[correct_data] == undefined){
                        var confirmed_damages = "N/A";
                    }
                    
                    else {
                        confirmed_damages = "$" + damages[correct_data];
                    }
                    if (markers_title[correct_data] == null){
                        var confirmed_title = "UNKNOWN";
                    }
                    
                    else {
                        confirmed_title = markers_title[correct_data];
                    }
                        
                    
                    info_title.innerHTML = confirmed_title;
                    info_description.innerHTML = description[correct_data];
                    document.getElementById("start_date").innerHTML = "START DATE: " + start_date[correct_data].slice(0,10);
                    document.getElementById("end_date").innerHTML = "END DATE: " + end_date[correct_data].slice(0,10);
                    document.getElementById("region").innerHTML = "REGION AFFECTED: " + regions[correct_data];
                    document.getElementById("deaths").innerHTML = "DEATHS: " + confirmed_deaths;
                    document.getElementById("injuries").innerHTML = "INJURIES: " + confirmed_injuries;
                    document.getElementById("damages").innerHTML = "INSURED COST (DAMAGES): " + confirmed_damages;
                    
                    //Selected Map marker colour change completed with help and assistance from "Mind the Gap" - Matthew Purcell.
                    if (lastSelectedMarker != undefined) {
                        lastSelectedMarker.setIcon("http://maps.google.com/mapfiles/ms/icons/red-dot.png");
                    }

                    this.setIcon('http://www.google.com/mapfiles/marker_green.png');
                    
                    lastSelectedMarker = this;
                    
                    var target_title = markers_title[correct_data].toLowerCase();
                    
                    var advice_types = ["fire", "storm", "earthquake", "epidemic", "flood", "cyclone", "tsunami", "drought", "hail", "transport", "shipwreck", "criminal act", "industrial", "heatwave"];
                    
                    var current_advice_type = "";
                    var word_detection_title = 0;
                    
                    var word_in_title = "unknown";
                    
                    for (a in advice_types) {
                        current_advice_type = advice_types[a];
                        word_detection_title = target_title.search(current_advice_type);
                        
                        if (word_detection_title >= 0) {
                            word_in_title = current_advice_type;
                            break;
                        }
                    }
                    
                    if (word_in_title == "unknown"){
                        var disaster_stats = document.getElementById("advice_div");
                        disaster_stats.setAttribute('class', 'hide_advice');  
                    }
                    else{
                        var disaster_stats = document.getElementById("advice_div");
                        disaster_stats.setAttribute('class', 'show_advice');  
                        
                        var advice_title = "";
                        
                        var first_letter_title_word = word_in_title.charAt(0);
                        
                        var a_or_an = "a ";
                        
                        if (first_letter_title_word == 'a' || first_letter_title_word == 'e' || first_letter_title_word == 'i' || first_letter_title_word == 'o' || first_letter_title_word == 'u') {
                            a_or_an = "an "
                        }
                        
                        if (word_in_title == "hail"){
                            advice_title = "How to prepare for " + a_or_an + word_in_title.charAt(0).toUpperCase() + word_in_title.substring(1,word_in_title.length) + "strom";
                        }
                        else if (word_in_title == "transport"){
                            advice_title = "How to prepare for " + a_or_an + word_in_title.charAt(0).toUpperCase() + word_in_title.substring(1,word_in_title.length) + "ation Disaster";
                        }
                        else if (word_in_title == "industrial"){
                              advice_title = "How to prepare for " + a_or_an + word_in_title.charAt(0).toUpperCase() + word_in_title.substring(1,word_in_title.length) + " Disaster";
                        }
                        else{
                            advice_title = "How to prepare for " + a_or_an + word_in_title.charAt(0).toUpperCase() + word_in_title.substring(1,word_in_title.length);
                        }
                        
                        var advice_fire_content = "Before a fire (wildfire) you should:<br/> <ul><li>Build an emergency Kit with First Aid essentials included</li><li>Design your home to be fire repellant - taking into consideration plants and materials</li><li>Regularily clean gutters and roof of flamable material such as sticks.</li><li>Install Smoke Alarms (forboth household and Wild fires.)</li></ul> during the fire stay away from the front and arrange temporary housing elseware if instructed by authorities.";
                        var advice_storm_content = "Before a storm you should:<br/> <ul><li>Build an emergency Kit with First Aid essentials included</li><li>Position yourself inside a home, building or hardtop automobile (not a convertible!)</li><li>Unplug any electronic equiptment well before the strom hits your region</li></ul> During the storm makesure that you stay inside and in a sheltered area and avoid contact with plugged in electronics";
                        var advice_earthquake_content = "Before an earthquake you should probably: <ul><li>Secure items that could fall and cause injuries</li><li>Store critical supplies and documents</li></ul>During an Earthquake you need to:<ul><li>Stay where you are until the shaking stops</li><li>Do not run outside</li><li>Drop down onto your hands and knees</li></ul>";
                        var advice_epidemic_content = "To prepare for an epidemic:<ul><li>Store a 2 week supply of water and food</li><li>Make sure you have a plentiful supply of other important items, like personal and prescription drugs</li></ul>";
                        var advice_flood_content = "During a flood you should: <br/><ul><li>Secure your home, if you have time.</li><li>Turn off utilities at the main switches or valves</li><li>Do not walk through moving water. Only six inches of moving water can make you fall</li>";
                        var advice_cyclone_content = "To prepare for a cyclone, you should take the following measures: <br/><ul><li>Know your surroundings</li><li>Identify hazards such as trees and water bodies and determined if they pose a threat to you</li><li>Cover your houses windows</li></ul> During a cyclone:<br/><ul><li>Listen to the radio/TV for information</li><li>Secure your home and outside objects</li><li>Avoid using the phone, except for emergencies</li></ul>";
                        var advice_tsunami_content = "During a Tsunami you should:<ul><li>Follow the evacuation order issued by authorities and evacuate immediately &mdash; take animals with you</li><li>Move inland to higher ground immediately</li><li>Stay away from the beach</li><li>Save yourself &mdash; not your possessions</li></ul>";
                        var advice_drought_content = "Before a drought you can prepare by: <ul><li>Conserving water - meaning don't waste it if there is no immediate use</li><li>Instal a water tank to help harvest natural water</li></ul>During a drought:<ul><li>Avoid water waste (such as don't flush the toilet if not required)</li><li>Get help if needed</li></ul>";
                        var advice_hail_content = "Before a Hailsorm you should:<ul><li>check you natural surroundings for trees with large branches, if you find some cut them off, because hail brings a higher risk of branches falling</li><li>Inspect you roof for vulnrabilities that could lead to hail damage</li></ul>If you see a hailstorm make sure that you:<ul><li>Put all exposed items undercover before the storm</li><li>Close all doors and windows in your home that leads to outside</li><li>Take shelter away from Windows.</li></ul>";
                        var advice_transport_content = "To prepare for an automobile disaster you should always:<ul><li>Make sure your seatbelt is clipped</li><li>Make sure that the driver is sober and fit for the job of driving</li></ul>To prepare for a flying disaster, you should:<ul><li>Always read the safety instructions ask about how an evacuation will occure (if not already supplied).</li></ul>For other transportation - just ensure that you think smart and are ready to make fast desisitions.";
                        var advice_shipwreck_content = "To prepare for a shipwreck you should:<ul><li>Always carry a lifevest with a wistle and light onboard a ship/water goign vessel</li><li>Ask about the evacuation plan and location of the life boats or bouys aboard the ship</li></ul> ";
                        var advice_crime_content = "There isn't much you can do to prepare for Criminal Acts - but during a criminal act you shoudl:<ul><li>Avoid the streets, stay behind a locked door.</li><li>Avoid contact with strangers</li><li>Listen and respond appropriately to all safety information released by the government</li></ul>";
                        var advice_industrial_content = "To prepare:<ul><li>Make sure you are wearing all required safety equiptment and know the evacuation precedures</li></ul>";
                        var advice_heatwave_content = "TO begin to prepre you shoud:<ul><li>Cover windows or light enterences that capture morning, midday or afternoon sun - helping to reduce heat</li><li>Listen to local weather forcasts </li><li>Drink water - dont get dehydrated!</li></ul>";
                        
                        document.getElementById("advice_title").innerHTML = advice_title;
                        
                        if (word_in_title == "fire") {
                            document.getElementById("button_link").href = "http://www.ready.gov/wildfires";
                            document.getElementById("advice_content").innerHTML = advice_fire_content;
                        }
                        else if (word_in_title == "storm") {
                            document.getElementById("button_link").href = "http://www.ready.gov/severe-weather";
                            document.getElementById("advice_content").innerHTML = advice_storm_content;
                        }
                        else if (word_in_title == "earthquake") {
                            document.getElementById("button_link").href = "http://www.ready.gov/earthquakes";
                            document.getElementById("advice_content").innerHTML = advice_earthquake_content;
                        }
                        else if (word_in_title == "epidemic") {
                            document.getElementById("button_link").href = "http://www.ready.gov/pandemic";
                            document.getElementById("advice_content").innerHTML = advice_epidemic_content;
                        }
                        else if (word_in_title == "flood") {
                            document.getElementById("button_link").href = "http://www.ready.gov/floods";
                            document.getElementById("advice_content").innerHTML = advice_flood_content;
                        }
                        else if (word_in_title == "cyclone") {
                            document.getElementById("button_link").href = "http://www.ready.gov/hurricanes";
                            document.getElementById("advice_content").innerHTML = advice_cyclone_content;
                        }
                        else if (word_in_title == "tsunami") {
                            document.getElementById("button_link").href = "http://www.ready.gov/tsunamis";
                            document.getElementById("advice_content").innerHTML = advice_tsunami_content;
                        }
                        else if (word_in_title == "drought") {
                            document.getElementById("button_link").href = "http://www.ready.gov/drought";
                            document.getElementById("advice_content").innerHTML = advice_drought_content;
                        }
                        else if (word_in_title == "hail") {
                            document.getElementById("button_link").href = "https://knowrisk.com.au/insight/articles/preparing-for-storms-and-hail";
                            document.getElementById("advice_content").innerHTML = advice_hail_content;
                        }
                        else if (word_in_title == "transport") {
                            document.getElementById("button_link").href = "https://www.atsb.gov.au/";
                            document.getElementById("advice_content").innerHTML = advice_transport_content;
                        }
                        else if (word_in_title == "shipwreck") {
                            document.getElementById("button_link").href = "http://www.watersafety.nsw.gov.au/boating-safety/boating-safety-tips.html";
                            document.getElementById("advice_content").innerHTML = advice_shipwreck_content;
                        }
                        else if (word_in_title == "criminal act") {
                            document.getElementById("button_link").href = "http://www.ncpc.org/topics/home-and-neighborhood-safety";
                            document.getElementById("advice_content").innerHTML = advice_crime_content;
                        }
                        else if (word_in_title == "industrial") {
                            document.getElementById("button_link").href = "http://www.indsh.com.au/occupational-health-and-safety-info.html";
                            document.getElementById("advice_content").innerHTML = advice_industrial_content;
                        }
                        else if (word_in_title == "heatwave") {
                            document.getElementById("button_link").href = "http://www.ready.gov/heat";
                            document.getElementById("advice_content").innerHTML = advice_heatwave_content;
                        }
                    
                    }

                });
            
                
                
                
            }
            
            var markerCluster = new MarkerClusterer(map, all_marker_locations);
            
            /*
            console.log(sentinal_lat[9]);
            console.log(sentinal_long[9]);
            
            var sentinel_position = new google.maps.LatLng(119.78878000000002, -20.95260600000001);
            
            console.log(sentinel_position);
            
            var sentinel_marker = new google.maps.Marker({
                position: sentinel_position,
                map: map,
                icon: "http://www.google.com/mapfiles/marker_green.png"
            });
            */
            /*
            if (searched_markers_lat.length > 0){
                for (g in searched_markers_lat) {
                    console.log(searched_markers_lat[g]);
                    console.log(searched_markers_long[g]);
                }
            }
            else {
                //do nothing!!!!
            }
            */

        }
        
        google.maps.event.addDomListener(window, 'load', initialize);
        
    </script>
			
	</head>
	
	<body>
	    
	    <!--
	    <form onsubmit="return searchBar();">
	        <input type="text" id="text_input" placeholder="Search Keyword"/>
	    </form>
	    -->
	    
	    <div id="disaster_info" class="">
	        <h1 id="disaster_title">Welcome to Disaster Master.</h1>
	        <p id="disaster_description">Disasters plague our world - whether it be floods, fires, droughts or storms. There is nothing you can do to fully avoid the risk of disaster, but wouldn't it be better is you were prepared for them? The map provides the location of all disasters related to Australians since the early 1800's. Providing detailed overviews and recounts of the events. 
	        
	        <br/><br/>
	        
	        Coupled with this the map provides live statistics of Sentinal Hotspots around the country - which can give you a heads-up warning for potential bushfires. 
	        
	        <br/><br/>
	        
	        To get started <span class="red_text">try clicking on the markers</span> to take a look at the event, descriptions and statistics. </p>
	        
	        
	    </div>
	    
	    <div id="disaster_stats" class="">
	        <p id="start_date"></p>
	        <p id="end_date"></p>
	        <p id="region"></p>
	        <p id="deaths"></p>
	        <p id="injuries"></p>
	        <p id="damages"></p>
	    </div>
	    
	    <div id="advice_div">
	        <h1 id="advice_title"></h1>
	        <p id="advice_content"></p>
	        <a id = "button_link" href="#" target="_blank">
	            <div id="advice_button"><p>Click here for more information</p></div>
	        </a>
	    </div>
	    
	    <div id="full_map_canvas"></div>
	
	</body>
	
</html>
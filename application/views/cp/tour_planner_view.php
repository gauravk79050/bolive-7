<style type="text/css">
#sidebar, #sidebarSet{
	display:none;
}
</style>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script>
	var location1, location2,response;
	var stops = [];
	var waypoints = [];
	function initialize(){
		var from_hr = parseInt($("#time_from_hr").val());
		var from_min = parseInt($("#time_from_min").val());
		var to_hr = parseInt($("#time_to_hr").val());
		var to_min = parseInt($("#time_to_min").val());

		if(from_hr > to_hr || (from_hr == to_hr && from_min > to_min) ){
			alert("<?php echo _("Time range is not valid. Please check Hours and Minutes")?>");
		}else{
			$("#progress").show();
			$.ajax({
				url: base_url+'cp/tour_planner/find_address',
				type: "post",
				dataType: "json",
				data: {
					'from_hr': 	from_hr,
					'to_hr': 	to_hr,
					'from_min': from_min,
					'to_min': 	to_min
				},
				success: function(result){
					if(result.error){
						alert(result.message);
						$("#progress").hide();
					}else{
						var locations = result.data;
						var address1 = locations.origin; 
						var address2 = locations.destination;
						waypoints = locations.waypoints;
						/*waypoints.push({
	            location: "Haandorpweg 1 Kallo 9130 BELGIE",
	            stopover: true
	        });

			waypoints.push({
	            location: "Kreek 100 Kieldrecht 9130 BELGIE",
	            stopover: true
	        });

			waypoints.push({
	            location: "Haandorpweg p35 Kallo 9130 BELGIE",
	            stopover: true
	        });

			waypoints.push({
	            location: "Koningsdijk 32 Meerdonk 9170 BELGIE",
	            stopover: true
	        });*/
						geocoder = new google.maps.Geocoder();
						if (geocoder)
						{
							geocoder.geocode( { 'address': address1}, function(results, status)
						   	{
						    	if (status == google.maps.GeocoderStatus.OK)
						      	{
						         //location of first address (latitude + longitude)
						        	location1 = results[0].geometry.location;

						         	geocoder.geocode( { 'address': address2}, function(results, status)
				  			   		{
				  			      		if (status == google.maps.GeocoderStatus.OK)
				  			      		{
				  			         		//location of second address (latitude + longitude)
				  			         		location2 = results[0].geometry.location;

				  			         		// calling the showMap() function to create and show the map
				  			         		showMap();
					  			      	} else
				  			      		{
				  			        		alert("Geocode was not successful for the following reason: " + status);
				  			      		}
				  			   		});
						      	} else
						      	{
						        	alert("Geocode was not successful for the following reason: " + status);
						      	}
						   	});
						}
						//showMap();
					}
				}
			});
		}
	}
	
	/* function showMap(){

		directionsDiv = document.getElementById("directions");
		
		latlng = new google.maps.LatLng((location1.lat()+location2.lat())/2,(location1.lng()+location2.lng())/2);


		var mapOptions =
		{
		   zoom: 8,
		   center: latlng,
		   mapTypeId: google.maps.MapTypeId.HYBRID
		};

		map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

		directionsService = new google.maps.DirectionsService();
		directionsDisplay = new google.maps.DirectionsRenderer();
		directionsDisplay.setMap(map);
		directionsDisplay.setPanel(directionsDiv);
alert(waypoints.toSource());
		var request = {
		   origin:location1,
		   destination:location2,
		   waypoints: waypoints, //an array of waypoints
           optimizeWaypoints: true,
		   travelMode: google.maps.DirectionsTravelMode.DRIVING/*,	// Options: DRIVING, WALKING, BICYCLING, TRANSIT
		   transitOptions: {
			    departureTime: new Date()
			}*comment it
		};

		directionsService.route(request, function(response, status)
		{
			alert(status);
			alert(response.toSource());
		   	if (status == google.maps.DirectionsStatus.OK)
		   	{
		   		directionsDiv.innerHTML = "";
		   		$("#progress").hide();
				directionsDisplay.setDirections(response);

		   		// calculate total distance and duration
	        	var distance = 0;
	            var time = 0;
	            var theRoute = response.routes[0];
	            for (var i=0; i<theRoute.legs.length; i++) {
	              var theLeg = theRoute.legs[i];
	              distance += theLeg.distance.value;
	              time += theLeg.duration.value;
	            }

	            var distance = "The distance between the two points on the chosen route is: "+getDistance(distance);
			   	document.getElementById("distance_road").innerHTML = distance;
			    var time_driving = "The aproximative driving time is: "+Math.round(time/60);
			    document.getElementById("time_driving").innerHTML = time_driving;
		   	}
		});
	} */

	function showMap(){

		directionsDiv = document.getElementById("directions");
		
		latlng = new google.maps.LatLng((location1.lat()+location2.lat())/2,(location1.lng()+location2.lng())/2);


		var mapOptions =
		{
		   zoom: 8,
		   center: latlng,
		   mapTypeId: google.maps.MapTypeId.HYBRID
		};

		map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

		directionsService = new google.maps.DirectionsService();
		directionsDisplay = new google.maps.DirectionsRenderer();
		directionsDisplay.setMap(map);
		directionsDisplay.setPanel(directionsDiv);
alert(waypoints.toSource());
		console.log(waypoints);
		
		$.each(waypoints,function(index,value){
				geocoder.geocode( { 'address': value.location}, function(results, status)
			   	{
			    	if (status == google.maps.GeocoderStatus.OK)
			      	{
				      	console.log(typeof results[0].geometry.location);
			        	stops.push(results[0].geometry.location);
			        	//console.log(Object.keys(stops));
			        	//console.log(stops);
			         	//console.log(stops.length);
			      	}
			   	});
		});

		console.log(stops);
		console.log(stops.length);
		console.log('test');
		var batches = [];
		var itemsPerBatch = 10; // google API max - 1 start, 1 stop, and 8 waypoints
		var itemsCounter = 0;
		var test_array = $.map(stops, function(n, i) { alert(n+' '+i);return i; })
		var wayptsExist = stops.length > 0;

		//console.log(typeof stops);
		//console.log(Object.keys(stops).length);
		//console.log($(stops).toArray());
		//console.log(jQuery.makeArray( stops ));
		//console.log(stops);
		while (wayptsExist) {
		    var subBatch = [];
		    var subitemsCounter = 0;
		 
		    for (var j = itemsCounter; j < stops.length; j++) {
		        subitemsCounter++;
		        subBatch.push({
		            location: new window.google.maps.LatLng(stops[j].Geometry.Latitude, stops[j].Geometry.Longitude),
		            stopover: true
		        });
		        if (subitemsCounter == itemsPerBatch)
		            break;
		    }
		 
		    itemsCounter += subitemsCounter;
		    batches.push(subBatch);
		    wayptsExist = itemsCounter < stops.length;
		    // If it runs again there are still points. Minus 1 before continuing to
		    // start up with end of previous tour leg
		    itemsCounter--;
		}
		calcRoute(batches, directionsService, directionsDisplay);
	}
	
	function getDistance(distance) {
		/*if ($('#directionUnits').val() == "Miles")
			return Math.round((distance*0.621371192)/100) / 10 + " miles";
		else*/
			return Math.round(distance/100) / 10 + " km";		
	}

	function calcRoute(batches, directionsService, directionsDisplay) {
	    var combinedResults;
	    var unsortedResults = [{}]; // to hold the counter and the results themselves as they come back, to later sort
	    var directionsResultsReturned = 0;
	     
	    for (var k = 0; k < batches.length; k++) {
	        var lastIndex = batches[k].length - 1;
	        var start = batches[k][0].location;
	        var end = batches[k][lastIndex].location;
	         
	        // trim first and last entry from array
	        var waypts = [];
	        waypts = batches[k];
	        waypts.splice(0, 1);
	        waypts.splice(waypts.length - 1, 1);
	         
	        var request = {
	            origin : start,
	            destination : end,
	            waypoints : waypts,
	            travelMode : window.google.maps.TravelMode.WALKING
	        };
	        (function (kk) {
	            directionsService.route(request, function (result, status) {
	                if (status == window.google.maps.DirectionsStatus.OK) {
	                     
	                    var unsortedResult = {
	                        order : kk,
	                        result : result
	                    };
	                    unsortedResults.push(unsortedResult);
	                     
	                    directionsResultsReturned++;
	                     
	                    if (directionsResultsReturned == batches.length) // we've received all the results. put to map
	                    {
	                        // sort the returned values into their correct order
	                        unsortedResults.sort(function (a, b) {
	                            return parseFloat(a.order) - parseFloat(b.order);
	                        });
	                        var count = 0;
	                        for (var key in unsortedResults) {
	                            if (unsortedResults[key].result != null) {
	                                if (unsortedResults.hasOwnProperty(key)) {
	                                    if (count == 0) // first results. new up the combinedResults object
	                                        combinedResults = unsortedResults[key].result;
	                                    else {
	                                        // only building up legs, overview_path, and bounds in my consolidated object. This is not a complete
	                                        // directionResults object, but enough to draw a path on the map, which is all I need
	                                        combinedResults.routes[0].legs = combinedResults.routes[0].legs.concat(unsortedResults[key].result.routes[0].legs);
	                                        combinedResults.routes[0].overview_path = combinedResults.routes[0].overview_path.concat(unsortedResults[key].result.routes[0].overview_path);
	                                         
	                                        combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getNorthEast());
	                                        combinedResults.routes[0].bounds = combinedResults.routes[0].bounds.extend(unsortedResults[key].result.routes[0].bounds.getSouthWest());
	                                    }
	                                    count++;
	                                }
	                            }
	                        }
	                        directionsDisplay.setDirections(combinedResults);
	                    }
	                }
	            });
	        })(k);
	    }
	}
</script>
		
<div id="main" style="text-transform:none;">		
    <div id="main-header">
	   <h2><?php echo _('Tour Planner')?></h2>
    </div>
	
	<div id="content" style="width: 100%;">
		<div id="content-container">
	     
		 	<div class="box">
				<h3><?php echo _('Select Time Range')?></h3>
				<div class="table">
               
                	<form method="post" action="">
                   		<table id="ftp-settings" cellspacing="0" cellpadding="0" border="0">
			       			<tbody>
                      			<tr>
                         			<td>
                         				<?php $hr = 0; $min = 0;?>
			                         	<?php echo _('From'); ?> :
			                         	<div>
			                         		<span><?php echo _("Hour");?></span>
			                         		<select id="time_from_hr" name="time_from_hr">
			                         		<option value="">--<?php echo _("Select");?>--</option>
			                         		<?php while($hr < 24){?>
			                         		<option value="<?php echo ( (strlen($hr) == 1)?"0".$hr:$hr );?>"><?php echo ( (strlen($hr) == 1)?"0".$hr:$hr );?></option>
			                         		<?php $hr++;?>
			                         		<?php }?>
			                         	</select>
			                         	</div>
			                         	
			                         	<div>
			                         		<span><?php echo _("Min");?></span>
			                         		<select id="time_from_min" name="time_from_min">
				                         		<option value="">--<?php echo _("Select");?>--</option>
				                         		<?php while($min < 55){?>
				                         		<option value="<?php echo ( (strlen($min) == 1)?"0".$min:$min );?>"><?php echo ( (strlen($min) == 1)?"0".$min:$min );?></option>
			                         			<?php $min = $min + 5;?>
				                         		<?php }?>
				                         	</select>
			                         	</div>
			                         	
                         			</td>	
                         			<td>
                         				<?php $hr = 0; $min = 0;?>
			                         	<?php echo _('To'); ?> :
			                         	<div>
			                         		<span><?php echo _("Hour");?></span>
			                         		<select id="time_to_hr" name="time_to_hr">
			                         		<option value="">--<?php echo _("Select");?>--</option>
			                         		<?php while($hr < 24){?>
			                         		<option value="<?php echo ( (strlen($hr) == 1)?"0".$hr:$hr );?>"><?php echo ( (strlen($hr) == 1)?"0".$hr:$hr );?></option>
			                         		<?php $hr++;?>
			                         		<?php }?>
			                         	</select>
			                         	</div>
			                         	
			                         	<div>
			                         		<span><?php echo _("Min");?></span>
			                         		<select id="time_to_min" name="time_to_min">
				                         		<option value="">--<?php echo _("Select");?>--</option>
				                         		<?php while($min < 55){?>
				                         		<option value="<?php echo ( (strlen($min) == 1)?"0".$min:$min );?>"><?php echo ( (strlen($min) == 1)?"0".$min:$min );?></option>
			                         			<?php $min = $min + 5;?>
				                         		<?php }?>
				                         	</select>
			                         	</div>
			                         	
                         			</td>
                         			
                         			<td>
                         				<input type="button" id="plan" name="plan" value="<?php echo _("Get tour");?>" onclick="javascript: initialize();" />
                         			</td>
                      			</tr>
                   			</tbody>
                   		</table>
                	</form>
            	</div>
		 	</div>
		 	
		 	<div class="box">
		 		<h3><?php echo _('Tour')?></h3>
				<div class="table">
					<div id="progress" style="display: none;"><?php echo _("Creating Report");?> <img src="<?php echo base_url();?>assets/cp/images/20122139137.GIF" alt="..."/></div>
					<div id="distance_road"></div>
					<div id="time_driving"></div>
					<div id="map_canvas" style="width:90%; height:80%"></div>
					<div id="directions" style="width:90%; height:80%"></div>
				</div>
		 	</div>
		</div>
	</div>
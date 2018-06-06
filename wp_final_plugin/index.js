jQuery(document).ready(function(){
  if(jQuery("#loggedInUserCallback")[0].innerText.length > 0){
    jQuery("#goToMyAlumniPage").removeAttr('style');
  }
  var loggedInUser;
  var markers = [];
  var in_area = [];
  var alumni;
  var geocoder = new google.maps.Geocoder();
  let locations = [];
  var dataForMap;
    var data = {
      action: 'get_json_data',
    };
   jQuery.get(the_ajax_script.ajaxurl, data, function(response) { // Get data from endpoint
      var dataForMap = JSON.parse(response[0].body); 
      var courses = JSON.parse(response[1].body);
      if(response[2] != "")
      {
        loggedInUser = response[2];
      }
      console.log(courses);

      dataForMap.forEach(  (item) => { // Push all data to array
           geocoder.geocode({
          'address': item.address.postal_code + " " + item.address.city
        }, function(results, status) {

          var phone = "";
          var middleName ="";
            if(item.phone != null)
            {
              phone = item.phone;
            }
            if(item.middle_name != null)
            {
              middleName = item.middle_name;
            }
                  locations.push([(item.first_name +" "+ middleName +" "+item.last_name),results[0].geometry.bounds.f.b,results[0].geometry.bounds.b.b, item.address.address, item.address.postal_code, item.address.city, item.email, item.id, phone]);
                var latlng = new google.maps.LatLng(results[0].geometry.bounds.f.b, results[0].geometry.bounds.b.b);
                var marker = new google.maps.Marker({
                  position: latlng,
                  map: map,
                  icon: 'http://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5b%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5c000000%27tC%5c000000%27eC%5cLauto%27f%5c&ext=.png'
                });
                markers.push(marker);
        });
      });
    });
  function add_marker(arraymarkers, i) {
    var latlng = new google.maps.LatLng(arraymarkers[i][1], arraymarkers[i][2]);
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: 'http://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5b%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5c000000%27tC%5c000000%27eC%5cLauto%27f%5c&ext=.png'
    });
    markers[i] = marker;
  }
   function excecuteAddMarkersLoop(locations)
   {
     console.log(locations);
      for (var i = 0; i < locations.length; i++) {
        add_marker(locations, i); // Add markers on the google maps
      }
   }
    var input = document.getElementById('searchTextField');
    var options = {
         types: ['(cities)'],
    componentRestrictions: {country: "nl"} // Only the netherlands
             };
    
    new google.maps.places.Autocomplete(input, options);

  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 52.224196,
      lng: 5.678053
    },
    zoom: 6,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  });


  google.maps.event.addDomListener(window, "Centreren", function() {
    centermap();
  });
   
  
  
  jQuery('#locator button').on('click',function(e) {
    e.preventDefault();
    var address = jQuery('#searchTextField').val();
    var radiusmiles = parseInt(jQuery('select').val());
    var radiusmetric = radiusmiles * 0.62137; //km -> mi
      radiusmetric = radiusmetric /0.00062137; // mi -> meters
    in_area = [];
  
    geocoder.geocode({
      'address': address
    }, function(results, status) {
      if (status === google.maps.GeocoderStatus.OK) {

        var searchedlocation = results[0].geometry.location;
        var circle = new google.maps.Circle({
          radius: radiusmetric,
          center: searchedlocation
        });
        map.setCenter(searchedlocation);
        map.fitBounds(circle.getBounds());
        for (var i = 0; i < locations.length; i++) {
          var position = new google.maps.LatLng(locations[i][1], locations[i][2]);
          var distance = google.maps.geometry.spherical.computeDistanceBetween(searchedlocation, position);

          if (distance <= radiusmetric) {
            markers[i].setMap(map);
            in_area.push([locations[i][0], locations[i][3], locations[i][6], (distance / 1000).toFixed(1), locations[i][7]]); // distance/1000 van meters -> kilometers
          } else {
            markers[i].setMap(null);
          }

        }
        resultstotal(in_area.length, radiusmiles, address, circle);
      }
    });

  });

  function centermap() {
    var center = map.getCenter();
    google.maps.event.trigger(map, "Centreren");
    map.setCenter(center);
  }



  function resultstotal(resultstotal, radius, search, circlearea) {
    jQuery('#results').empty().append('<p class="col" style="margin-left: 20px;">' + resultstotal + ' therapeut(en) binnen ' + radius + 'km rond ' + search + '</p><ol class="col"></ol>');
    in_area.sort(function(a, b) {
      return a[3] - b[3];
    });
    for (var i = 0; i < in_area.length; i++) {

      var dealername = in_area[i][0];
      var dealeraddress = in_area[i][1];
      var dealeremail = in_area[i][2];
      var distanceround = in_area[i][3];

      jQuery('#results ol').append('<li class = "therapist" value = "'+in_area[i][4]+'">' + // Append results to dom
        '<h4 id = "heading">' + dealername + ' </h4>' +
        '<p id = "text">' + dealeraddress + '</p>' +
        '<a href="mailto:' + dealeremail + '">' + dealeremail + '</a>' +
        '</li>');
    }
    jQuery('#results ol .therapist').on('click', function(){
      alumni = searchForCorrespondingAlumnibyID(jQuery(this).val());

     fillAlumniPage(alumni);
    });

    jQuery('#results, #map').addClass('active');
    jQuery('#results').on('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function(e) {
      centermap();
      map.fitBounds(circlearea.getBounds());
    });

  }

  function searchForCorrespondingAlumnibyID(id)
  {
    for(var i = 0; i < locations.length; i++)
    {
      if(locations[i][7] == id)
      {
        return locations[i];
      }
    }
    return "";
  }

function searchForCorrespondingAlumniByUsername(username)
{
     for(var i = 0; i < locations.length; i++)
    {
      if(locations[i][6] == username)
      {
        return locations[i];
      }
    }
    return "";
}

  function fillAlumniPage(alumni)
  {
    jQuery('.alumnicontainer .sidebar .sidebar-top .profile-basic .name').text(alumni[0]);
    jQuery('.alumnicontainer .sidebar .profile-info #email').text(alumni[6]);
    jQuery('.alumnicontainer .sidebar .profile-info #address').text(alumni[3] +" "+alumni[4] + ", "+ alumni[5]);
    jQuery('.alumnicontainer .sidebar .profile-info #tel').text(alumni[8]);
              
              var data = {
                action: 'get_alumni_content',
                id: alumni[7]
              };
              jQuery.post(the_ajax_script.ajaxurl, data, function(response) {
                
                var jsonResponse = JSON.parse(response);
                if(jsonResponse != "" && jsonResponse != "false")
                {

                  if(jsonResponse.length > 1)
                  {
                    console.log(jsonResponse);
                     jQuery('.alumnicontainer .sidebar .sidebar-top .profile-image ')
                    .attr('src', jsonResponse[1]);
                  }
                  else{
                  jQuery('.alumnicontainer .sidebar .sidebar-top .profile-image ')
                    .attr('src', "https://i.stack.imgur.com/l60Hf.png");
                  }
                  jQuery('.alumnicontainer .content .info').text(jsonResponse[0].post_content);
                  jQuery('.alumnicontainer .content .heading').text(jsonResponse[0].post_title);
                }
                else{
                  jQuery('.alumnicontainer .content .info').text('Heeft nog geen biografie toegevoegd');
                  jQuery('.alumnicontainer .content .heading').text("Nog geen expertise toegevoegd");
                   jQuery('.alumnicontainer .sidebar .sidebar-top .profile-image ')
                    .attr('src', "https://i.stack.imgur.com/l60Hf.png");
                }
                     toggleViews();
                  if(loggedInUser == alumni[6]) // Give the person rights to edit page if it is the user
                  {
                      jQuery('#editText').removeAttr('style');
                      jQuery('#fileToUpload').removeAttr('style');
                      jQuery('#editText').on('click', function(){
                      jQuery('.alumnicontainer .content .heading').attr('contentEditable',true);
                      jQuery('.alumnicontainer .content .info').attr('contentEditable',true);
                      jQuery('#saveChanges').removeAttr('style');
                    });
                  }
              });

}
jQuery('.alumnicontainer #goBack').on('click', function(){
  toggleViews();
  jQuery('#saveChanges').css("display", "none");
  jQuery('#fileToUpload').css("display", "none")
  jQuery('#editText').css("display", "none");
  jQuery('.alumnicontainer .content .heading').attr('contentEditable',false);
  jQuery('.alumnicontainer .content .info').attr('contentEditable',false);
});

   function toggleViews()
   {
      jQuery('header').toggle();
      jQuery('#results').toggle();
      jQuery('#map').toggle();
      jQuery('.alumnicontainer').toggle();
      //jQuery('#main').toggle();
   }

           jQuery('#saveChanges').on('click', function(){
             if(loggedInUser == alumni[6])
             {
               var fd = new FormData();
               if( jQuery('#fileToUpload')[0].files.length > 0)
               {
                  fd.append( "image", jQuery('#fileToUpload')[0].files[0]);
               }
               fd.append( "action", 'post_alumni_data');      
               fd.append("text", jQuery('.alumnicontainer .content .info').text());
               fd.append("title", jQuery('.alumnicontainer .content .heading').text());
               fd.append("id", alumni[7]);
               fd.append("user", alumni[6]);
              jQuery.ajax({
                  type: 'POST',
                  url: the_ajax_script.ajaxurl,
                  data: fd, 
                  processData: false,
                  contentType: false,

                  success: function(data, textStatus, XMLHttpRequest) {
                      alert(data);
                  },

                  error: function(MLHttpRequest, textStatus, errorThrown) {
                      console.log(errorThrown);
                  }

              });
             }
        });

jQuery('#fileToUpload').on('change', function() { // Change tumbnail to uploaded image
    jQuery('#saveChanges').removeAttr('style');
    var input = jQuery(this)[0];
    console.log(input);
    if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                    jQuery('.alumnicontainer .sidebar .sidebar-top .profile-image ')
                    .attr('src', e.target.result)
  
            };

            reader.readAsDataURL(input.files[0]);
        }
});

  jQuery('#logIn').on('click', function(){
    if(jQuery("#loggedInUserCallback")[0].innerText.length == 0)
    {
      var ticket = getQueryVariable("ticket");
      if(ticket)
      {
        var oldURL = window.location.href
        var index = 0;
        var newURL = oldURL;
        index = oldURL.indexOf('?');
        if(index == -1){
            index = oldURL.indexOf('#');
        }
        if(index != -1){
            newURL = oldURL.substring(0, index);
        }
        window.location.href = "http://total-health.testing.edufra.me/cas/login?service="+ newURL;
      }
      else{
        
        window.location.href = "http://total-health.testing.edufra.me/cas/login?service="+ window.location.href
      }
    }
    else{
      alert("Je bent al ingelogd");
    }

  });

function getQueryVariable(variable)
{
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

  jQuery('#goToMyAlumniPage').on('click', function(){
        var data = {
      action: 'get_username',
    };
         jQuery.get(the_ajax_script.ajaxurl, data, function(response) { 
            loggedInUser = response
            alumni = searchForCorrespondingAlumniByUsername(loggedInUser);
            fillAlumniPage(alumni);
         });

  }); 

});

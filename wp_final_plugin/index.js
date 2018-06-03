jQuery(document).ready(function(){
  loggedInUser = 'floris@dynamixpixel.nl'//loggedInUser.trim();
  var markers = [];
  var in_area = [];
  var alumni;
  var geocoder = new google.maps.Geocoder();
  let locations = [];
  var dataForMap;
    var data = {
      action: 'get_json_data',
    };
   jQuery.get(the_ajax_script.ajaxurl, data, function(response) {
      var dataForMap = JSON.parse(response[0].body); 
      var courses = JSON.parse(response[1].body);
      //console.log(courses);
     // var enrollments = JSON.parse(response[2].body);

function delay()
{
  return new Promise(resolve => setTimeout(resolve, 300));
}
async function delayedLog(item)
{
  await delay();
}

async function processArray(array)
{
  const promises = array.map(delayedLog);
  await Promise.all(promises);
  excecuteAddMarkersLoop();
}
processArray()


      dataForMap.forEach( async (item) => {
          await geocoder.geocode({
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
                if(item == dataForMap[dataForMap.length -1])
                {
                  processArray(locations);
                }
        });
      });
    });
   function excecuteAddMarkersLoop()
   {
     console.log(locations);
      for ( i = 0; i < locations.length; i++) {
        add_marker(locations, i);
      }
              //console.log(markers);

   }
    var input = document.getElementById('searchTextField');
    var options = {
         types: ['(cities)'],
    componentRestrictions: {country: "nl"} // alleen plaatsnamen Nederland
             };
    
    new google.maps.places.Autocomplete(input, options);

  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: 52.224196,
      lng: 5.678053
    },
    zoom: 8,
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
        for (i = 0; i < locations.length; i++) {
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

  function add_marker(arraymarkers, i) {
    var latlng = new google.maps.LatLng(arraymarkers[i][1], arraymarkers[i][2]);
    var marker = new google.maps.Marker({
      position: latlng,
      map: map,
      icon: 'http://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5b%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5c000000%27tC%5c000000%27eC%5cLauto%27f%5c&ext=.png'
    });
    markers[i] = marker;
  }

  function resultstotal(resultstotal, radius, search, circlearea) {
    jQuery('#results').empty().append('<p class="col" style="margin-left: 20px;">' + resultstotal + ' therapeut(en) binnen ' + radius + 'km rond ' + search + '</p><ol class="col"></ol>');
    in_area.sort(function(a, b) {
      return a[3] - b[3];
    });
    for (i = 0; i < in_area.length; i++) {

      var dealername = in_area[i][0];
      var dealeraddress = in_area[i][1];
      var dealeremail = in_area[i][2];
      var distanceround = in_area[i][3];

      jQuery('#results ol').append('<li class = "therapist" value = "'+in_area[i][4]+'">' +
        //'<h4>' + dealername + ' (' + distanceround + ' km afstand)</h4>' +
        '<h4 id = "heading">' + dealername + ' </h4>' +
        '<p id = "text">' + dealeraddress + '</p>' +
        '<a href="mailto:' + dealeremail + '">' + dealeremail + '</a>' +
        '</li>');
    }
    jQuery('#results ol .therapist').on('click', function(){
      alumni = searchForCorrespondingAlumni(jQuery(this).val());
     fillAlumniPage(alumni);
    });

    jQuery('#results, #map').addClass('active');
    jQuery('#results').on('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function(e) {
      centermap();
      map.fitBounds(circlearea.getBounds());
    });

  }

  function searchForCorrespondingAlumni(id)
  {
    for(i = 0; i < locations.length; i++)
    {
      if(locations[i][7] == id)
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
              // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
              jQuery.post(the_ajax_script.ajaxurl, data, function(response) {
                
                var jsonResponse = JSON.parse(response);
                if(jsonResponse != "" && jsonResponse != "false")
                {
                  if(jsonResponse.length > 1)
                  {
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
              });
        if(loggedInUser == alumni[6]) // for test purpose condition is set to != 
        {
            jQuery('#editText').removeAttr('style');
            jQuery('#fileToUpload').removeAttr('style');
            jQuery('#editText').on('click', function(){
            jQuery('.alumnicontainer .content .heading').attr('contentEditable',true);
            jQuery('.alumnicontainer .content .info').attr('contentEditable',true);
            jQuery('#saveChanges').removeAttr('style');
          });
        }
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
                fd.append( "action", 'test_response');      
               fd.append("text", jQuery('.alumnicontainer .content .info').text());
               fd.append("title", jQuery('.alumnicontainer .content .heading').text());
               fd.append("id", alumni[7]);
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

jQuery('#fileToUpload').on('change', function() {
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
});
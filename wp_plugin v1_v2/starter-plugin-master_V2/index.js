  
var jsonData = JSON.parse(jsonData.body);
loggedInUser = loggedInUser.trim();
console.log(loggedInUser);
  var markers = [];
  var in_area = [];
var alumni;
  // var locations2 = [
  //   ['Rijks museum', 52.360135, 4.885498, 'Museumstraat 1, 1071 XX Amsterdam', 'Rijks@gmail.com'],
  //   ['Van Abbe museum', 51.434547, 5.482056, 'Bilderdijklaan 10, 5611 NH Eindhoven', 'Abbe@gmail.com'],
  //   ['Groninger museum', 53.212270, 6.566100, 'Museumeiland 1, 9711 ME Groningen', 'Groninger@gmail.com'],
  //   ['Spoorweg museum', 52.088005, 5.131232, 'Maliebaanstation 16, 3581 XW Utrecht', 'Spoorweg@gmail.com'],
  //   ['Naturalis', 52.164900, 4.475174, 'Pesthuislaan 7, 2333 BA Leiden', 'Naturalis@gmail.com'],
  //   ['Maritiem museum', 51.917552, 4.482216, 'Leuvehaven 1, 3011 EA Rotterdam', 'Maritiem@gmail.com'],
  // ];
 // ['Rijks museum', 'Museumstraat 1, 1071 XX, Amsterdam', 'Rijks@gmail.com', 43],
  var geocoder = new google.maps.Geocoder();
  var locations = [];

    jsonData.forEach(async (item) => {
        geocoder.geocode({
        'address': item.address.postal_code + " " + item.address.city
      }, function(results, status) {
        var phone = "";
          if(item.phone != null)
          {
            phone = item.phone;
          }
                locations.push([(item.first_name +" "+ item.last_name),results[0].geometry.bounds.f.b,results[0].geometry.bounds.b.b, item.address.address, item.address.postal_code, item.address.city, item.email, item.id, phone]);
              if(item == jsonData[jsonData.length -1])
              {
                console.log('done');
                excecuteAddMarkersLoop();
              }
      });
    })
   function excecuteAddMarkersLoop()
   {
    for ( i = 0; i < locations.length; i++) {
      console.log(locations);
      add_marker(locations, i);
    }
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
   
  
      
  
  $('#locator button').on('click',function(e) {
    e.preventDefault();
    var address = $('#searchTextField').val();
    var radiusmiles = parseInt($('select').val());
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
    $('main').empty().append('<p class="col">' + resultstotal + ' therapeut(en) binnen ' + radius + 'km rond ' + search + '</p><ol class="col"></ol>');
    in_area.sort(function(a, b) {
      return a[3] - b[3];
    });
    for (i = 0; i < in_area.length; i++) {

      var dealername = in_area[i][0];
      var dealeraddress = in_area[i][1];
      var dealeremail = in_area[i][2];
      var distanceround = in_area[i][3];

      $('main ol').append('<li class = "therapist" value = "'+in_area[i][4]+'">' +
        //'<h4>' + dealername + ' (' + distanceround + ' km afstand)</h4>' +
        '<h4>' + dealername + ' </h4>' +
        '<p>' + dealeraddress + '</p>' +
        '<a href="mailto:' + dealeremail + '">' + dealeremail + '</a>' +
        '</li>');
    }
    $('main ol .therapist').on('click', function(){
      alumni = searchForCorrespondingAlumni($(this).val());
     fillAlumniPage(alumni);
     toggleViews();
      //console.log($(this).val());
    });

    $('main, #map').addClass('active');
    $('main').on('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function(e) {
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
    $('.alumnicontainer .sidebar .sidebar-top .profile-basic .name').text(alumni[0]);
    $('.alumnicontainer .sidebar .profile-info #email').text(alumni[6]);
    $('.alumnicontainer .sidebar .profile-info #address').text(alumni[3] +" "+alumni[4] + ", "+ alumni[5]);
    $('.alumnicontainer .sidebar .profile-info #tel').text(alumni[8]);
                  var data = {
                action: 'get_alumni_content',
                id: alumni[7]
              };
              // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
              $.post(the_ajax_script.ajaxurl, data, function(response) {
                
                var jsonResponse = JSON.parse(response);
                  console.log(typeof response);
                if(jsonResponse != "")
                {
                  $('.alumnicontainer .content .info').text(jsonResponse.post_content);
                  $('.alumnicontainer .content .heading').text(jsonResponse.heading);
                }
                else{
                  $('.alumnicontainer .content .info').text('Heeft nog geen biografie toegevoegd');
                  $('.alumnicontainer .content .heading').text("Nog geen expertise toegevoegd");
                }

              });
    if(loggedInUser != alumni[6]) // for test purpose condition is set to != 
    {
        $('#editText').removeAttr('style');
      $('#editText').on('click', function(){
        $('.alumnicontainer .content .info').attr('contentEditable',true);
          $('#saveChanges').removeAttr('style');
      });
    }
}
$('.alumnicontainer #goBack').on('click', function(){
  toggleViews();
});

   function toggleViews()
   {
      $('header').toggle();
      $('#results').toggle();
      $('#map').toggle();
      $('.alumnicontainer').toggle();
      $('#main').toggle();
   }

           $('#saveChanges').on('click', function(){
               console.log(alumni);

              var data = {
                action: 'test_response',
                text: $('.alumnicontainer .content .info').text(),
                title: 'Masseuse',
                id: alumni[7]
              };
              // the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
              $.post(the_ajax_script.ajaxurl, data, function(response) {
                alert(response);
              });
        });
jQuery(document).ready(function(){
  if(jQuery("#alumni_loggedInUserCallback")[0].innerText.length > 0){
    jQuery("#alumni_goToMyAlumniPage").removeAttr('style');
    jQuery("#alumni_logIn").text("log uit");
  }
  var loggedInUser, alumni, cas_url, dataForMap;
  var markers = [];
  var in_area = [];
  var searchFlag = false;
  var geocoder = new google.maps.Geocoder();
  let locations = [];
  var data = {
    action: 'get_json_data',
  };
  jQuery.get(the_ajax_script.ajaxurl, data, function(response) { // Get data from endpoint
    var dataForMap = JSON.parse(response[0].body); 
    cas_url = response[1];
    if(typeof response[2] != 'undefined')
    {
      loggedInUser = response[2];
    }
    console.log(cas_url);
    dataForMap.forEach(  (item) => { // Push all data to array
      geocoder.geocode({
        'address': item.address.postal_code + " " + item.address.city
      }, 
      function(results, status) {
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
          clickable: false,
          icon: 'http://chart.apis.google.com/chart?cht=d&chdp=mapsapi&chl=pin%27i%5c%27%5b%27-2%27f%5chv%27a%5c%5dh%5c%5do%5c0099FF%27fC%5c000000%27tC%5c000000%27eC%5cLauto%27f%5c&ext=.png'
        });
        markers.push(marker);
        });
      });
      jQuery('#alumni_logIn').on('click', //add login button handler
      function(){
        if(loggedInUser == null)
        {
          var ticket = getQueryVariable("ticket");
          if(ticket)
          {
            console.log(cas_url);
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
            window.location.href = cas_url+"/cas/login?service="+ newURL;
          }
          else{
            window.location.href = cas_url+"/cas/login?service="+ window.location.href
          }
        }
        else{
          var data = {
            action: 'log_out',
          };
          jQuery.get(the_ajax_script.ajaxurl, data, function(response) {
             window.location.href = cas_url + '/cas/logout';
             loggedInUser = null;
          });
        }
      });
    });
    var input = document.getElementById('alumni_searchTextField');
    var options = {
      types: ['(cities)'],
      componentRestrictions: {country: "nl"} // Only the netherlands
    };
    
    new google.maps.places.Autocomplete(input, options);

    var map = new google.maps.Map(document.getElementById('alumni_map'), {
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
   
  
  
  jQuery('#alumni_locator button').on('click',function(e) { // search button click handler
    e.preventDefault();
    searchFlag = true;
    jQuery('#alumni_results').addClass('active');
    var address = jQuery('#alumni_searchTextField').val();
    var radiusmiles = parseInt(jQuery('select').val());
    var radiusmetric = radiusmiles * 0.62137; //km -> mi
    radiusmetric = radiusmetric /0.00062137; // mi -> meters
    in_area = [];
    geocoder.geocode({
      'address': address
    }, 
    function(results, status) {
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
    jQuery('#alumni_results').empty().append('<p class="col" style="margin-left: 20px;">' + resultstotal + ' therapeut(en) binnen ' + radius + 'km rond ' + search + '</p><ol class="col"></ol>');
    in_area.sort(function(a, b) {
      return a[3] - b[3];
    });
    for (var i = 0; i < in_area.length; i++) {

      var dealername = in_area[i][0];
      var dealeraddress = in_area[i][1];
      var dealeremail = in_area[i][2];
      var distanceround = in_area[i][3];

      jQuery('#alumni_results ol').append('<li class = "alumni_therapist" value = "'+in_area[i][4]+'">' + // Append results to dom
        '<h4 id = "alumni_heading">' + dealername + ' </h4>' +
        '<p id = "alumni_text">' + dealeraddress + '</p>' +
        '<a href="mailto:' + dealeremail + '">' + dealeremail + '</a>' +
        '</li>');
    }
    jQuery('#alumni_results ol .alumni_therapist').on('click', function(){
      alumni = searchForCorrespondingAlumnibyID(jQuery(this).val());
     fillAlumniPage(alumni);
    });

    jQuery('#alumni_results, #alumni_map').addClass('active');
    jQuery('#alumni_results').on('transitionend webkitTransitionEnd oTransitionEnd otransitionend MSTransitionEnd', function(e) {
      centermap();
      map.fitBounds(circlearea.getBounds());
    });

  }

  function searchForCorrespondingAlumnibyID(id){
    for(var i = 0; i < locations.length; i++)
    {
      if(locations[i][7] == id)
      {
        return locations[i];
      }
    }
    return "";
  }

  function searchForCorrespondingAlumniByUsername(username){
      for(var i = 0; i < locations.length; i++)
      {
        if(locations[i][6] == username)
        {
          return locations[i];
        }
      }
      return "";
  }

  function fillAlumniPage(alumni){
    jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_sidebar-top .alumni_profile-basic .alumni_name').text(alumni[0]);
    jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_profile-info #email').text(alumni[6]);
    jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_profile-info #address').text(alumni[3] +" "+alumni[4] + ", "+ alumni[5]);
    jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_profile-info #tel').text(alumni[8]);
    var data = {
      action: 'get_alumni_content',
      id: alumni[7]
    };
    jQuery.post(the_ajax_script.ajaxurl, data, function(response) {
      var jsonResponse = JSON.parse(response);
      if(jsonResponse != "" && jsonResponse != "false"){
        if(jsonResponse.length > 1){
          jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_sidebar-top .alumni_profile-image ').attr('src', jsonResponse[1]);
        }
        else{
          jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_sidebar-top .alumni_profile-image ').attr('src', "https://i.stack.imgur.com/l60Hf.png");
        }
        jQuery('.alumni_alumnicontainer .alumni_content .alumni_info').text(jsonResponse[0].post_content);
        jQuery('.alumni_alumnicontainer .alumni_content .alumni_heading').text(jsonResponse[0].post_title);
      }
      else{
        jQuery('.alumni_alumnicontainer .alumni_content .alumni_info').text('Heeft nog geen biografie toegevoegd');
        jQuery('.alumni_alumnicontainer .alumni_content .alumni_heading').text("Nog geen expertise toegevoegd");
        jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_sidebar-top .alumni_profile-image ').attr('src', "https://i.stack.imgur.com/l60Hf.png");
      }
        toggleViews('alumni');
      if(loggedInUser == alumni[6]){ // Give the person rights to edit page if it is the user
        jQuery('#alumni_editText').removeAttr('style');
        jQuery('#alumni_fileToUpload').removeAttr('style');
        jQuery('#alumni_editText').on('click', 
        function(){
          jQuery('.alumni_alumnicontainer .alumni_content .alumni_heading').attr('contentEditable',true);
          jQuery('.alumni_alumnicontainer .alumni_content .alumni_info').attr('contentEditable',true);
          jQuery('#alumni_saveChanges').removeAttr('style');
        });
      }
    });
  }
  jQuery('.alumni_alumnicontainer #alumni_goBack').on('click', 
  function(){
    toggleViews('search');
    jQuery('#alumni_saveChanges').css("display", "none");
    jQuery('#alumni_fileToUpload').css("display", "none")
    jQuery('#alumni_editText').css("display", "none");
    jQuery('.alumni_alumnicontainer .alumni_content .alumni_heading').attr('contentEditable',false);
    jQuery('.alumni_alumnicontainer .alumni_content .alumni_info').attr('contentEditable',false);
  });

  function toggleViews(state){
    console.log(state);
      if(state == "alumni")
      {
        jQuery('#alumni_zoekfunctie').hide();
        if(searchFlag)
        {
          jQuery('#alumni_results').hide();
        }
        jQuery('#alumni_map').hide();
        jQuery('.alumni_alumnicontainer').show();
      }
      if(state == "search")
      {
        jQuery('#alumni_zoekfunctie').show();
        if(searchFlag)
        {
          jQuery('#alumni_results').show();
        }
        jQuery('#alumni_map').show();
        jQuery('.alumni_alumnicontainer').hide();
      }
   }

   jQuery('#alumni_saveChanges').on('click', 
   function(){
     if(loggedInUser == alumni[6])
     {
        var fd = new FormData();
        if( jQuery('#alumni_fileToUpload')[0].files.length > 0){
          fd.append( "image", jQuery('#alumni_fileToUpload')[0].files[0]);
        }
        fd.append( "action", 'post_alumni_data');      
        fd.append("text", jQuery('.alumni_alumnicontainer .alumni_content .alumni_info').text());
        fd.append("title", jQuery('.alumni_alumnicontainer .alumni_content .alumni_heading').text());
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

  jQuery('#alumni_fileToUpload').on('change', 
  function() { // Change tumbnail to uploaded image
    jQuery('#alumni_saveChanges').removeAttr('style');
    var input = jQuery(this)[0];
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        jQuery('.alumni_alumnicontainer .alumni_sidebar .alumni_sidebar-top .alumni_profile-image ').attr('src', e.target.result)
      };
      reader.readAsDataURL(input.files[0]);
    }
  });

  function getQueryVariable(variable){
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    for (var i=0;i<vars.length;i++) {
      var pair = vars[i].split("=");
      if(pair[0] == variable){return pair[1];}
    }
    return(false);
  }

  jQuery('#alumni_goToMyAlumniPage').on('click', function(){
      var data = {
        action: 'get_username',
      };
      jQuery.get(the_ajax_script.ajaxurl, data, function(response) {
        loggedInUser = response
        alumni = searchForCorrespondingAlumniByUsername(loggedInUser);
        if(alumni != "")
        {
          fillAlumniPage(alumni);
        }
        else
        {
          alert("Je hebt geen alumnipagina");
        }
      });
    }); 
  });

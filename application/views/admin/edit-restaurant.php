<link rel="stylesheet" href="<?php echo base_url()?>assets/css/jquery.timepicker.css">
<style type="text/css">

   .error {

   color: red;

   }

</style>

<style type="text/css">

   .error {

   color: red;

   }

   #map_canvas {         

   height: 400px;         

   width: 400px;         

   margin: 0.6em;       

   }

   input[type="file"] {

   display: block;

   }

   .imageThumb {

   max-height: 75px;

   border: 2px solid;

   padding: 1px;

   cursor: pointer;

   }

   .pip {

   display: inline-block;

   margin: 10px 10px 0 0;

   }

   .remove {

   display: block;

   background: #444;

   border: 1px solid black;

   color: white;

   text-align: center;

   cursor: pointer;

   }

   .remove:hover {

   background: white;

   color: black;

   }

</style>

<body class="hold-transition skin-blue sidebar-mini">

   <div class="wrapper">

      <?php $this->load->view('admin/navbar') ?>

      <?php $this->load->view('admin/sidebar') ?>

      <!-- Content Wrapper. Contains page content -->

      <div class="content-wrapper">

         <!-- Content Header (Page header) -->

         <section class="content-header">

            <h1>

               Update Restaurant

               <!-- <small> Preview</small> -->

            </h1>

            <ol class="breadcrumb">

               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>

               <li><a href="<?php echo site_url('admin/restaurants');?>">Restaurants</a></li>

               <li class="active">Update Restaurant</li>

            </ol>

         </section>

         <!-- Main content -->

         <section class="content">

            <div class="row">

               <div class="col-md-12">

                  <?php  if(isset($error)){ echo $error; }

                     echo $this->session->flashdata('success_req'); ?>

                  <!-- general form elements -->

                  <div class="box box-primary">

                     <div class="box-header">

                        <h3 class="box-title">Contact Detail </h3>

                     </div>

                     <!-- /.box-header -->

                     <!-- form start -->

                     <form action="<?php echo base_url()?>Admin/update_restaurant/<?= $datas->id; ?>" method="post" enctype="multipart/form-data" onsubmit="return validation();">

                      <input type="hidden" id="lat" name="lat">

                      <input type="hidden" id="lang" name="lng">

                        <div class="box-body">

                           <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Restaurant Name:<span class="text-danger">*</span></label>

                                    <input type="text" name="restaurant_name" value="<?php echo set_value('restaurant_name', $datas->restaurant_name); ?>" class="form-control"/>

                                    <?php echo form_error('restaurant_name', '<div class="error">', '</div>'); ?>

                                 </div>

                              </div>

                           </div>
                           <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Restaurant Official Email:<span class="text-danger">*</span></label>

                                    <input type="text" name="restaurant_email" value="<?php echo set_value('restaurant_email', $datas->restaurant_email); ?>" class="form-control"/>

                                    <?php echo form_error('restaurant_email', '<div class="error">', '</div>'); ?>

                                 </div>

                              </div>

                           </div>

                           <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Location:<span class="text-danger">*</span></label>

                                    <input type="text" name="location" id="location" value="<?php echo set_value('location', $datas->location); ?>" class="form-control"/>

                                    <?php echo form_error('location', '<div class="error">', '</div>'); ?>

                                    <div id="map_canvas" style="display: none;"></div>

                                 </div>

                              </div>

                           </div>

                           <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Restaurant image:<span class="text-danger">*</span></label>

                                    <input type="file" name="image" class="form-control"/>

                                 <img height="150" width="100" src="<?= site_url('assets/uploaded/restaurants/'.$datas->image); ?>">

                                 </div>

                              </div>

                            </div>
                             <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="">Number of Table:<span class="text-danger"></span></label>
                                    <input type="number" name="number_of_table" value="<?php echo set_value('number_of_table', $datas->number_of_table); ?>" class="form-control"/>
                                    <?php echo form_error('number_of_table', '<div class="error">', '</div>'); ?>
                                 </div>
                              </div>
                           </div>

                            <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Mobile number:<span class="text-danger">*</span></label>
                                    <input type="text" id="freemobile"  name="mobile" maxlength="13" value="<?php echo set_value('mobile', $datas->mobile); ?>" class="form-control"/>
                                    <span class="error" id="errormobile"></span>
                                    <?php echo form_error('mobile', '<div class="error">', '</div>'); ?>

                                 </div>

                              </div>

                           </div>
                            <div class="row">
                              <div class="col-md-3">
                                 <div class="form-group">
                                    <label class="">Open Time:<span class="text-danger">*</span></label>
                                    <input type="text" id="openTime" name="openTime" value="<?php echo set_value('openTime', $datas->openTime); ?>" class="form-control basicExample"/>
                                    <?php echo form_error('openTime', '<div class="error">', '</div>'); ?>
                                 </div>
                              </div>
                              <div class="col-md-3">
                                 <div class="form-group">
                                    <label class="">Close Time:<span class="text-danger">*</span></label>
                                    <input type="text" id="closeTime" name="closeTime" value="<?php echo set_value('closeTime', $datas->closeTime); ?>" class="form-control basicExample"/>
                                    <?php echo form_error('closeTime', '<div class="error">', '</div>'); ?>
                                 </div>
                              </div>
                           </div>

                           <div class="row">

                              <div class="col-md-6">

                                <div class="form-group">

                                  <label>Status</label>

                                  <select class="form-control" name="status">

                                    <option <?= $datas->status == 'Active'?'selected':''; ?> value="Active">Active</option>

                                    <option <?= $datas->status =='Inactive'?'selected':''; ?> value="Inactive">Inactive</option>

                                  </select>

                                  <?php echo form_error('status', '<div class="error">', '</div>'); ?>

                                </div>

                              </div>

                           </div>

                           

                           <h4 class="box-title">Login Detail </h4>

                           <br>

                           <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label> Username :<span class="text-danger">*</span></label>

                                    <input type="text" name="user_name" value="<?php echo set_value('user_name', $datas->user_name); ?>" class="form-control"/>

                                    <?php echo form_error('user_name', '<div class="error">', '</div>'); ?>

                                 </div>

                              </div>

                            </div>

                            <div class="row">

                              <div class="col-md-6">

                                 <div class="form-group">

                                    <label class="">Password:<span class="text-danger">*</span></label>

                                    <input type="text" name="password" value="<?php echo set_value('password', $datas->password); ?>" class="form-control"/>

                                    <?php echo form_error('password', '<div class="error">', '</div>'); ?>

                                 </div>

                              </div>

                           </div>

                        </div>

                        <div class="box-footer">

                           <input type="submit" class="btn btn-primary" name="submit" value="Update"/>

                        </div>

                     </form>

                  </div>

                  <!-- /.box -->

               </div>

               <!-- </div> -->

            </div>

            <!-- Main row -->

         </section>

         <!-- /.content -->

         </aside><!-- /.right-side -->

      </div>

      <?php $this->load->view('admin/footer') ?>

      <div class="control-sidebar-bg"></div>

   </div>

</body>

</html>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-DnLv-o5tVV7l06DtmukXTJViCgyC1h4&libraries=places"></script>
<script src="<?php echo base_url()?>assets/js/jquery.timepicker.js"></script>
<script type="text/javascript">

$(document).ready(function() {

  $('.basicExample').timepicker();

    var lat = -33.8688,

        lng = 151.2195,

        latlng = new google.maps.LatLng(lat, lng),

        image = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png'; 

         

    var mapOptions = {           

            center: new google.maps.LatLng(lat, lng),           

            zoom: 13,           

            mapTypeId: google.maps.MapTypeId.ROADMAP         

        },

        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions),

        marker = new google.maps.Marker({

            position: latlng,

            map: map,

            icon: image

         });



        var options = {

          componentRestrictions: {country: "in"}

         };

     

    var input = document.getElementById('location');         

    var autocomplete = new google.maps.places.Autocomplete(input,options);   



    var place = autocomplete.getPlace();

   //document.getElementById('city2').value = place.name;

          

    

    autocomplete.bindTo('bounds', map); 

    var infowindow = new google.maps.InfoWindow(); 

 

    google.maps.event.addListener(autocomplete, 'place_changed', function() {

        infowindow.close();

        var place = autocomplete.getPlace();

         document.getElementById('lat').value = place.geometry.location.lat();

         document.getElementById('lang').value = place.geometry.location.lng();

        if (place.geometry.viewport) {

            map.fitBounds(place.geometry.viewport);

        } else {

            map.setCenter(place.geometry.location);

            map.setZoom(17);  

        }

        

        moveMarker(place.name, place.geometry.location);

    });  

    

    $("#location").focusin(function () {

        $(document).keypress(function (e) {

            if (e.which == 13) {

                infowindow.close();

                var firstResult = $(".pac-container .pac-item:first").text();

                

                var geocoder = new google.maps.Geocoder();

                geocoder.geocode({"address":firstResult }, function(results, status) {

                    if (status == google.maps.GeocoderStatus.OK) {

                        var lat = results[0].geometry.location.lat(),

                            lng = results[0].geometry.location.lng(),

                            placeName = results[0].address_components[0].long_name,

                            latlng = new google.maps.LatLng(lat, lng);

                           

                        moveMarker(placeName, latlng);

                        $("#location").val(firstResult);

                        

                    }

                });

            }

        });

    });

     

   function moveMarker(placeName, latlng){

      marker.setIcon(image);

      marker.setPosition(latlng);

      infowindow.setContent(placeName);

      infowindow.open(map, marker);

   }  

});

</script>
<script>
   function validation(){ 

 

   var freemobile = document.getElementById("freemobile").value;
   if(freemobile.length=="")
   {
   
   document.getElementById('errormobile').innerHTML = "Please Enter the Mobile Number!";
   return false;
   }
   if(isNaN(freemobile))
   {
   
   document.getElementById('errormobile').innerHTML = "Enter the valid Mobile Number!";
   return false;
   }
   if((freemobile.length < 10) || (freemobile.length > 10))
   {
   
   document.getElementById('errormobile').innerHTML = " Your Mobile Number must be 10 digit";
   return false;
   }
   
   if (!(freemobile.charAt(0)=="9" ||freemobile.charAt(0)=="8"||freemobile.charAt(0)=="7"||freemobile.charAt(0)=="6"))
   {
   
   document.getElementById('errormobile').innerHTML = "Mobile No. should start with 6,7,8 or 9";
   return false;
   } 
}
</script>
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
               Banners
               <!-- <small> Preview</small> -->
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li><a href="#">Banners</a></li>
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
                     
                     <?php 
                     if(isset($data))
                     { ?>
                      <form action="<?php echo base_url()?>Admin/update_banner" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                          <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="">Banner image:<span class="text-danger">*</span></label>
                                    <input type="file" name="image" class="form-control"/>
                                    <img height="150" width="100" src="<?= site_url('assets/uploaded/restaurants/'.$data->image); ?>">
                                 </div>
                              </div>
                            </div>
                        </div>
                        <div class="box-footer">
                           <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
                        </div>
                     </form>
                      <?php
                     }else{ ?>
                        <form action="<?php echo base_url()?>Admin/save_banner" method="post" enctype="multipart/form-data">
                          <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                      <label class="">Banner image:<span class="text-danger">*</span></label>
                                      <input type="file" name="image" class="form-control"/>
                                   </div>
                                </div>
                              </div>
                          </div>
                          <div class="box-footer">
                             <input type="submit" class="btn btn-primary" name="submit" value="Submit" />
                          </div>
                       </form>
                     <?php
                   }
                     ?>
                     
                  </div>
                  <!-- /.box -->
               </div>
               <!-- </div> -->
            </div>
            <div class="row">
               <div class="col-xs-12">
                  <?php if( $this->session->flashdata('message') ) : ?> 
                    <div class="alert alert-success" role="alert">
                      <?php echo $this->session->flashdata('message'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="box box-primary">
                     <!-- /.box-header -->
                     <div class="box-body" style="width: 70%;">
                        <div class="table-responsive" style="overflow-x:auto;">
                           <table id="example1" class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                    <th class="text-center">S.no</th>
                                    <th class="text-center">Banner</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $a=1;  foreach ($banners as $row) {
                                  ?>
                                 <tr>
                                    <td class="text-center"><?php echo $a++;?></td>
                                    <td class="text-center">
                                      <img height="100" width="130"  src="<?= site_url('assets/uploaded/banner/'.$row['image']); ?>">
                                     <!-- <img src="<?php echo base_url('assets/uploaded/banner/'. $row['image']);?>" height="100" width="130"> -->
                                    </td>

                                    <td class="text-center">
                                       <!-- <?php echo anchor('admin/banners/'.$row['banner_id'], '<i class="fa fa-edit"></i>', array("class"=>"btn btn-success")); ?>   -->

                                       <?php echo anchor('admin/delete_banner/'.$row['banner_id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
                                    </td>
                                 </tr>
                                 <?php }?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
               </div>
               <!-- /.col -->
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


<script type="text/javascript">
$(document).ready(function(){
  $('.table').DataTable({
      // "processing": true,
      // "serverSide": true,
      // "ajax": "<?= base_url('Admin/banners');?>",
    });
});
</script>
<!-- <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-DnLv-o5tVV7l06DtmukXTJViCgyC1h4&libraries=places"></script>
<script type="text/javascript">
  $(document).ready(function() {
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
</script> -->
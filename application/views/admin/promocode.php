<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css" integrity="sha256-yMjaV542P+q1RnH6XByCPDfUFhmOafWbeLPmqKh11zo=" crossorigin="anonymous"/>

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
    
      <div class="content-wrapper">
     
         <section class="content-header">
            <h1>
              Promocode
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li><a href="#">Promocode</a></li>
            </ol>
         </section>
   
         <section class="content">
            <div class="row">
              <div class="col-md-12">
                <?php  if(isset($error)){ echo $error; }
                  echo $this->session->flashdata('success_req'); ?>
                <div class="box box-primary">
               <div class="box-header">
               </div>
               <?php if(isset($data)) { ?>
                <form action="<?php echo base_url()?>Admin/update_promocode/<?= $data->id; ?>" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">promocode:<span class="text-danger">*</span></label>
                              <input type="text" name="promoCode" value="<?php echo set_value('promoCode',$data->promoCode); ?>" class="form-control"/>
                                 <?php echo form_error('promoCode', '<div class="error">', '</div>'); ?>
                           </div>
                         </div>
                          <div class="col-md-3">
                           <div class="form-group">
                              <label class="">Offer at PromoCode:<span class="text-danger">*</span></label>
                              <input type="text" name="promoCode_offer" value="<?php echo set_value('promoCode_offer',$data->promoCode_offer); ?>" class="form-control"/>
                                 <?php echo form_error('promoCode_offer', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">Start Date:<span class="text-danger">*</span></label>
                              <input type="text" name="startDate"  value="<?php echo set_value('startDate',$data->startDate );?>" class="form-control datetimepicker1"/>
                               <?php echo form_error('startDate', '<div class="error">','</div>'); ?>
                           </div>
                        </div>
                        

                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">Expiry Date:<span class="text-danger">*</span></label>
                              <input type="text" name="expiryDate" value="<?php echo set_value('expiryDate',$data->expiryDate);?>" class="form-control datetimepicker1"/>
                               <?php echo form_error('expiryDate', '<div class="error">','</div>'); ?>
                           </div>
                        </div>

                     </div>
                  </div>
                  <div class="box-footer">
                     <input type="submit" class="btn btn-primary" value="Update" />
                  </div>
               </form>
                <?php
               }else{ ?>
                <form action="<?php echo base_url()?>Admin/Add_PromoCode" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">PromoCode:<span class="text-danger">*</span></label>
                              <input type="text" name="promoCode" value="<?php echo set_value('promoCode'); ?>" class="form-control"/>
                                 <?php echo form_error('promoCode', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group">
                              <label class="">Offer at PromoCode:<span class="text-danger">*</span></label>
                              <input type="number" name="promoCode_offer" value="<?php echo set_value('promoCode_offer'); ?>" class="form-control"/><p style="margin: -25px 250px 10px">%</p>
                                 <?php echo form_error('promoCode_offer', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                      </div>
                      <div class="row">
                     
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">start Date:<span class="text-danger">*</span></label>
                              <input type="text" name="startDate"  value="<?php echo set_value('startDate');?>" class="form-control datetimepicker1"/>
                               <?php echo form_error('startDate', '<div class="error">','</div>'); ?>
                           </div>
                        </div>
                    

                      
                        <div class="col-md-6">
                           <div class="form-group">
                              <label class="">Expiry Date:<span class="text-danger">*</span></label>
                              <input type="text" name="expiryDate" value="<?php echo set_value('expiryDate');?>" class="form-control datetimepicker1"/>
                               <?php echo form_error('expiryDate', '<div class="error">','</div>'); ?>
                           </div>
                        </div>

                     </div>
                  </div>
                  <div class="box-footer">
                     <input type="submit" class="btn btn-primary" value="Submit" />
                  </div>
               </form>
               <?php } ?>
            </div>
              </div>
            </div>
          </section>

      <!-- <div class="content-wrapper">
        <section class="content"> -->
            <div class="row">
               <div class="col-xs-12">
                  <?php if( $this->session->flashdata('message') ) : ?> 
                    <div class="alert alert-success" role="alert">
                      <?php echo $this->session->flashdata('message'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="box box-primary">
                   
                     <div class="box-body">
                        <div class="table-responsive" style="overflow-x:auto;">
                           <table id="example1" class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Promo Code</th>                                   
                                    <th>Promo Offer Discount</th>                                   
                                    <th>start Date</th>                                   
                                    <th>expiry Date</th>                                   
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $a=1;  foreach ($promo as $row) {

                                  ?>
                                 <tr>
                                    <td><?php echo $a++;?></td>
                                    <td><?php echo $row['promoCode']?></td>
                                    <td><?php echo $row['promoCode_offer']?>%</td>
                                    <td><?php echo $row['startDate']?></td>
                                    <td><?php echo $row['expiryDate']?></td>
                                    
                                    <td class="text-center">
                                       <?php echo anchor('admin/promoCode/'.$row['id'], '<i class="fa fa-edit"></i>', array("class"=>"btn btn-success")); ?>  
                                       
                                       <?php echo anchor('admin/delete_PromoCode/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
                                    </td>
                                 </tr>
                                 <?php }?>
                              </tbody>
                           </table>
                        </div>
                     </div>
            
                  </div>
               
               </div>
            
            </div>
           
         </section>
    
         </aside>
      </div>
      <?php $this->load->view('admin/footer') ?>
      <div class="control-sidebar-bg"></div>
   </div>
</body>
</html>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
  <script type="text/javascript">
            $(function () {
                $('.datetimepicker1').datetimepicker();
            });
        </script>
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
              QR Codes
               <!-- <small> Preview</small> -->
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li><a href="<?php echo site_url('admin/restaurants');?>">Restaurants</a></li>
               <li class="active"> QR Codes</li>
            </ol>
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <div class="col-md-12">
                  <?php  if(isset($error)){ echo $error; }
                     echo $this->session->flashdata('message'); ?>
                  <!-- general form elements -->
                  <div class="box box-primary">
                     <!-- <div class="box-header">
                        <h3 class="box-title">Contact Detail </h3>
                     </div> -->
                     <!-- /.box-header -->
                     <!-- form start -->
                     <form action="<?php echo base_url()?>Admin/generate_qr_code/<?= $datas->id; ?>" method="post" enctype="multipart/form-data">
                        <div class="box-body">
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="">Number Of Table:<span class="text-danger">*</span></label>
                                    <input type="text" name="tables" value="<?php echo set_value('tables', $datas->number_of_table); ?>" class="form-control"/>
                                    <?php echo form_error('tables', '<div class="error">', '</div>'); ?>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="box-footer">
                           <input type="submit" class="btn btn-primary" name="submit" value="Generate QR"/>
                           
                        </div>
                     </form>
                  </div>
                  <div class="box box-primary">
                    <div class="row">
                      <div class="col-md-3">
                       <?php if(count($qr_codes))
                            {
                              ?>
                                <iframe src="<?php echo base_url()?>Admin/generate_pdf/<?= $datas->id; ?>" name="frame" style="display:none;"></iframe>
                                 <button class="btn btn-primary right" onclick="frames['frame'].print()">Print Code</button> 
                              <?php
                            } ?>
                            </div>
                    </div>
                     <div class="row">
                      <?php
                      if(count($qr_codes))
                      {
                        foreach ($qr_codes as $key => $value) 
                        {
                          ?>
                            <div class="col-md-3 col-sm-6 col-xs-12">
                              <div class="info-box">
                                <!-- <span class="info-box-icon bg-aqua"><i class="fa fa-envelope-o"></i></span> -->
                                <img height="200" width="210" src="<?= base_url('assets/qr_codes/').$value['qr_code'].".png"; ?>">
                                <div class="info-box-content">
                                  <span class="info-box-text">Table No. <?= $value['table_no']; ?></span>
                                </div>
                              </div>
                            </div>
                          <?php
                        }
                      }
                        ?>
                    </div>

                  </div>
               </div>
            </div>
         </section>
         <!-- /.content -->
         </aside><!-- /.right-side -->
      </div>
      <?php $this->load->view('admin/footer') ?>
      <div class="control-sidebar-bg"></div>
   </div>
</body>
<script>
function myFunction() {
  window.print();
}
</script>
</html>

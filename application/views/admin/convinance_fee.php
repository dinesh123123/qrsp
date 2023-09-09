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
               Convenience Fee
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li><a href="#">Convenience Fee</a></li>
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
                      <form action="<?php echo base_url()?>Admin/update_convenience" method="post" enctype="multipart/form-data">
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
                        <form action="<?php echo base_url()?>Admin/save_convenience_fee" method="post" enctype="multipart/form-data">
                          <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                   <div class="form-group">
                                      <label class="">Amount:<span class="text-danger">*</span></label>
                                      <input type="number" name="amount" class="form-control"/>
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
                      <?php echo $this->session->flashdata('sucesscate'); ?>
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
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $a=1;  foreach ($convenience as $row) {
                                  ?>
                                 <tr>
                                    <td class="text-center"><?php echo $a++;?></td>
                                    <td class="text-center">
                                      <?= $row['amount']; ?>
                                    </td>
                                    <td class="text-center">
                                       <?php echo anchor('admin/delete_convenience/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
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

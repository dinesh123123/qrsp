<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <?php $this->load->view('admin/navbar') ?>
  <?php $this->load->view('admin/sidebar') ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Categories
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo site_url('Admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Categories</li>

      </ol>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-md-6">
            <?php  if(isset($error)){ echo $error; }
               echo $this->session->flashdata('success_req'); ?>
            <div class="box box-primary">
               <div class="box-header">
               </div>
               <?php if(isset($data)) { ?>
                <form action="<?php echo base_url()?>Admin/update_categories/<?= $data->id; ?>" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                  	<div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                            <select class="form-control" name="al_non_al_type">
                              <option value="">Select Type</option>
                              <option <?= $data->al_non_al_type=='Non Alcoholic'?'selected':''; ?> value="Non Alcoholic">Non Alcoholic</option>
                              <option <?= $data->al_non_al_type=='Alcoholic'?'selected':''; ?> value="Alcoholic">Alcoholic</option>
                            </select>
                            <?php echo form_error('al_non_al_type', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                           	<select class="form-control" name="type">
                           		<option value="">Select Food Type</option>
                           		<option <?= $data->type=='Non Alcoholic'?'selected':''; ?> value="Non Alcoholic">Non Alcoholic</option>
                           		<option <?= $data->type=='Alcoholic'?'selected':''; ?> value="Alcoholic">Alcoholic</option>
                           	</select>
                           	<?php echo form_error('type', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                              <label class="">Name:<span class="text-danger">*</span></label>
                              <input type="text" name="name" value="<?php echo set_value('name',$data->name); ?>" class="form-control"/>
                                 <?php echo form_error('name', '<div class="error">', '</div>'); ?>
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
                <form action="<?php echo base_url()?>Admin/save_categories" method="post" enctype="multipart/form-data">
                  <div class="box-body">
              		<div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                           	<select class="form-control" name="al_non_al_type">
                           		<option value="">Select Type</option>
                           		<option value="Non Alcoholic">Non Alcoholic</option>
                           		<option value="Alcoholic">Alcoholic</option>
                           	</select>
                           	<?php echo form_error('al_non_al_type', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                            <select class="form-control" name="type">
                              <option value="">Select Food Type</option>
                              <option value="1">Veg</option>
                              <option value="2">Non Veg</option>
                            </select>
                            <?php echo form_error('type', '<div class="error">', '</div>'); ?>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-10">
                           <div class="form-group">
                              <label class="">Name:<span class="text-danger">*</span></label>
                              <input type="text" name="name" value="<?php echo set_value('name'); ?>" class="form-control"/>
                                 <?php echo form_error('name', '<div class="error">', '</div>'); ?>
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
         <div class="col-md-8">

            <div class="box box-primary">
               <div class="box-body">
                  <div class="table-responsive" style="overflow-x:auto;">
                     <table id="example1" class="table table-bordered table-striped">
                        <thead>
                           <tr>
                              <th>S.no</th>
                              <th>Type</th>
                              <th>Name</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php 
                              foreach ($categories as $key => $row) {
                             ?>
                           <tr>
                              <td><?php echo $key+1;?></td>
                              <td><?php echo $row['al_non_al_type']; ?></td>
                              <td><?php echo $row['name']; ?></td>
                              <td class="text-center">
                                 <div class="btn-group">
                                    <?php echo anchor('Admin/categories/'.$row['id'], '<i class="fa fa-edit"></i>', array("class"=>"btn btn-success")); ?>

                                    <?php echo anchor('Admin/delete_category/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
                                 </div>
                                 
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
      // "ajax": "<?= base_url('Admin/restaurants_data');?>",
    });
});
</script>

<body class="hold-transition skin-blue sidebar-mini">
   <div class="wrapper">
      <?php $this->load->view('admin/navbar') ?>
      <!-- Left side column. contains the logo and sidebar -->
      <?php $this->load->view('admin/sidebar') ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
         <!-- Content Header (Page header) -->
         <section class="content-header">
            <h1>
               <?= $restaurant_name; ?> Orders
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li>Restaurant Orders Management</li>
               <li class="active">Restaurant Orders </li>
            </ol>
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <div class="col-xs-12">
                  <div class="box box-primary">
                     <!-- <div class="box-header with-border">
			          <h3 class="box-title"><?= $restaurant_name; ?> Orders</h3>
			        </div> -->
                     <!-- /.box-header -->
                     <div class="box-body">
                          <form action="<?php echo base_url()?>Admin/restaurant_orders/<?= $this->uri->segment(3, 0); ?> " method="get" enctype="multipart/form-data">
			                  <div class="box-body">
			                     <div class="row">
			                        <div class="col-md-6">
			                           <div class="form-group">
			                           	<div class="col-md-4">
			                              <label class="">Date:<span class="text-danger">*</span></label>
			                          	</div>
			                          	<div class="col-md-6">
			                              <input type="text" name="date" id="datepicker" value="<?php echo set_value('name'); ?>" class="form-control"/>
			                          	</div>
			                           </div>
			                        </div>
			                        <div class="col-md-6">
			                        	<div class="form-group">
			                              <input type="submit" class="btn btn-primary" value="Search"/>
			                           </div>

			                           	
			                        </div>
			                     </div>
			                  </div>
			               </form>
                     </div>
                     <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
               </div>
               <!-- /.col -->
            </div>
            <!-- /.row -->
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <div class="col-xs-12">
                  <?php if( $this->session->flashdata('message') ) : ?> 
                    <div class="alert alert-success" role="alert">
                      <?php echo $this->session->flashdata('message'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="box box-primary">
                     <div class="box-header with-border">
			          <h3 class="box-title">Orders of <?= date("d-m-Y", strtotime($current_date)); ?> </h3>
			        </div>
                     <!-- /.box-header -->
                     <div class="box-body">
                        <div class="table-responsive" style="overflow-x:auto;">
                           <table id="example1" class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Order Id</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php 
                                 $total = 0.0;
                                 $a=1;  
                                 foreach ($orders as $row) {
                                 	$total +=  $row['total_price'];  
                                  ?>
                                 <tr>
                                    <td><?php echo $a++;?></td>
                                    <td><?php echo $row['order_id']?></td>
                                    <td><?php echo  date("d-m-Y", strtotime($current_date)); ?></td>
                                    <td><?php echo $row['total_price']?></td>
                                    <td class="text-center">
                                       <?php echo anchor('admin/order_details/'.$row['order_id'], '<i class="fa fa-search"></i>', array("class"=>"btn btn-success")); ?>  
                                    </td>
                                 </tr>
                                 <?php }
                                 ?>
                              </tbody>
                           </table>
                        </div>
                     </div>
                     <div class="box-header with-border">
			         <b> <h3 class="box-title">Total Amount of  <i class="fa fa-inr" aria-hidden="true"></i> <?= $total.".00"; ?> on date  <?= date("d-m-Y", strtotime($current_date)); ?> </h3></b>
			        </div>
                     <!-- /.box-body -->
                  </div>

                  <!-- /.box -->
               </div>
               <!-- /.col -->
            </div>
            <!-- /.row -->
         </section>
         <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
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
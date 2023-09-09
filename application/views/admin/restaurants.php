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
               Restaurants
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li>Restaurants Management</li>
               <li class="active">Restaurants </li>
            </ol>
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
                     <div class="box-header">
                        <a href="<?php echo base_url()?>admin/add_restaurant" class="btn btn-info btn-sm pull-right ">Add Restaurant</a>
                     </div>
                     <!-- /.box-header -->
                     <div class="box-body">
                        <div class="table-responsive" style="overflow-x:auto;">
                           <table id="example1" class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                    <th>S.no</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Image</th>
                                    <th>Mobile</th>
                                    <th>Restaurant Email</th>
                                    <!-- <th>QR Code</th> -->
                                    <th>Status</th>
                                    <th>Action</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php $a=1;  foreach ($restaurant as $row) {
                                  switch ( $row['status']) {
                                     case 'Inactive':
                                        $status_cls = 'danger';
                                        break;
                                     default:
                                        $status_cls = 'success';
                                        break;
                                  }
                                  ?>
                                 <tr>
                                    <td><?php echo $a++;?></td>
                                    <td><?php echo $row['restaurant_name']?></td>
                                    <td><?php echo $row['location']?></td>
                                    <td><img height="100" width="130"  src="<?= site_url('assets/uploaded/restaurants/'.$row['image']); ?>"></td>
                                    <td><?php echo $row['mobile']?></td>
                                     <td><?php echo $row['restaurant_email']?></td>

                                   

                                     <td><button class="btn btn-<?= $status_cls; ?>"><?php echo $row['status']; ?></button></td>
                                    <td class="text-center">
                                       <?php echo anchor('admin/edit_restaurant/'.$row['id'], '<i class="fa fa-edit"></i>', array("class"=>"btn btn-success")); ?>  
                                       <?php echo anchor('admin/qr_codes/'.$row['id'], '<i class="fa fa-qrcode"></i>', array("class"=>"btn btn-info")); ?>  
                                       <?php echo anchor('admin/delete_restaurant/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
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
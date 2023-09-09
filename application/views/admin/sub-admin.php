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
        User 
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo site_url('Admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>User Management</li>
        <li class="active">User </li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
        <?php  if(isset($error)){ echo $error; }
        echo $this->session->flashdata('success_req'); ?>
          <div class="box box-primary">
            <div class="box-header">
              <h3 class="box-title">All User</h3>
              <div class="float-right">
                <a href="<?php echo site_url('Admin/add_sub_admin');?>" class="btn btn-success"><i class="fa fa-plus"></i> Add</a>
            </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="table-responsive" style="overflow-x:auto;">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S.no</th>
                   <th>Image</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Password</th>
                   <th>Action</th>  
                </tr>
                </thead>
                <tbody>
        
              <?php $a=1;  foreach ($user as $row) { ?>
                <tr>

                  <td><?php echo $a++;?></td>
                  <td class="center">
                    <?php if(!empty($row['s_image'])){ ?>
                    <img src="<?php echo base_url()?>upload/sub-admin/<?php echo $row['s_image'];?>" height=80px width=100px>
                  <?php } else{ ?>
                    <img src="<?php echo base_url()?>assets/images/users/userdemo.jpg" height=80px width=100px>
                <?php  } ?>
                  </td>
                  <td><?php echo ucwords($row['s_name']);?></td>
                  <td><?php echo $row['s_email'];?></td>
                  <td><?php echo $row['password'];?></td>
                 
                
                   <td class="text-center">
                    <div class="btn-group">
                      <?php echo anchor('admin/subAdminDelete/'.$row['s_id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
                    </div>
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
<!-- ./wrapper -->


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

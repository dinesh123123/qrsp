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
        All User Feedback
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
              <!-- <h3 class="box-title">All User Feedback</h3> -->
              <!-- <a href="<?php echo base_url()?>admin/UserAdd" class="btn btn-info btn-sm pull-right ">Add</a> -->
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="table-responsive" style="overflow-x:auto;">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S.no</th>
                  <th>Image</th>
                  <!--<th>User ID</th>-->
                  <th>Link</th>
               <!-- <th>User Mobile</th> -->
                  <!-- <th>Phone No</th> -->
                  <!-- <th>Question</th> -->
                  <th>Date</th> 
                  <!-- <th>Image</th> -->
                  <!-- <th>Mobile Status</th> -->
                  <!-- <th>Action</th>  -->
                </tr>
                </thead>
                <tbody>
        
              <?php $a=1;  foreach ($gallery as $row) { ?>
                <tr>

                  <td><?php echo $a++;?></td>
                  <td class="center">
                    <?php if(!empty($row['image'])){ ?>
                      <a href="<?php echo base_url()?>assets/gallary/<?php echo $row['image'];?>">
                    <img src="<?php echo base_url()?>assets/gallary/<?php echo $row['image'];?>" height=80px width=100px>
                  <?php } else{ ?>
                    <img src="<?php echo base_url()?>assets/images/users/userdemo.jpg" height=80px width=100px>
                <?php  } ?>
                  </td>
                 
                  <td>
                     <a href="<?php echo base_url()?>assets/gallary/<?php echo $row['image'];?>">
                    <?php echo base_url()?>assets/gallary/<?php echo $row['image'];?>
                      
                    </td>
                  <td><?php echo $row['date']?></td>
                 
                  <!-- <td class="center">
                    <?php if(!empty($row['image'])){ ?>
                    <img src="<?php echo base_url()?>assets/images/users/<?php echo $row['image'];?>" height=80px width=100px>
                  <?php } else{ ?>
                    <img src="<?php echo base_url()?>assets/images/users/userdemo.jpg" height=80px width=100px>
                <?php  } ?>
                  </td> -->
                  <!-- <td><?php echo $row['mobile_status']?></td> -->
                 
                
                <!--   <td class="text-center">
                    <div class="btn-group">
                      <?php echo anchor('admin/UserDelete/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
                    </div>
                  </td> -->
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

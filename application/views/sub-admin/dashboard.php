<body class="skin-blue sidebar-mini wysihtml5-supported" style="height: auto; min-height: 100%;">

<div class="wrapper" style="height: auto; min-height: 100%;">



  <!-- Left side column. contains the logo and sidebar -->

  <?php $this->load->view('sub-admin/navbar') ?>

  <?php $this->load->view('sub-admin/sidebar') ?>



  <!-- Content Wrapper. Contains page content -->

  <div class="content-wrapper" style="min-height: 960px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard<!-- <small>Control panel</small> --></h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Dashboard</li>
      </ol>
    </section>
    <section class="content">
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h4>Total</h4>
              <p>QR Code Reset List</p>
            </div>
            <div class="icon">
              <i class="fa fa-qrcode"></i>
            </div>
            <a href="<?php echo site_url('SubAdmin/qr_code_reset_list');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
               <?php if($qr_code)
                {
                  echo $qr_code;
                }else{
                  echo '0';
                } 
                ?> 
              </i>
            </a>
          </div>
        </div><!-- ./col -->
        
        <!-- /col -->

     
        <!-- /col -->
        
        
       
       
      </div>

   <!--    <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-orange">
            <div class="inner">
              <h4>Total</h4>
              <p>Banner</p>
            </div>
            <div class="icon">
              <i class="fa fa-picture-o"></i>
            </div>
            <a href="<?php  echo base_url('admin/banners'); ?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
                <?php if($banner)
                {
                  echo $banner;
                }else{
                  echo '0';
                } 
                ?>
              </i>
            </a>
          </div>
        </div>

        <!-- <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h4>Total<sup style="font-size: 20px"></sup></h4>
              <p>User</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="<?php echo site_url('Admin/users');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">

                <?php 
                if($user){
                  echo $user;
                }else{
                  echo '0';
                }

                ?>
                  
              </i>
            </a>
          </div>
        </div> -->
      </div>
    </section>


  </div>

  <!-- /.content-wrapper -->

  <?php $this->load->view('sub-admin/footer') ?>
  <div class="control-sidebar-bg"></div>
</div>



</body>

</html>
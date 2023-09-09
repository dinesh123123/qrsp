<body class="skin-blue sidebar-mini wysihtml5-supported" style="height: auto; min-height: 100%;">

<div class="wrapper" style="height: auto; min-height: 100%;">



  <!-- Left side column. contains the logo and sidebar -->

  <?php $this->load->view('admin/navbar') ?>

  <?php $this->load->view('admin/sidebar') ?>



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
          
        
        
        <!-- ./col -->
       
        <!-- /col -->

        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-green">
            <div class="inner">
              <h4>Total<sup style="font-size: 20px"></sup></h4>
              <p>User</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-circle"></i>
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
        </div>
        
        
        
        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-orange">
            <div class="inner">
              <h4>Total<sup style="font-size: 20px"></sup></h4>
              <p>Sub Admin</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="<?php echo site_url('Admin/sub_admin');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">

                <?php 
                if($sub_admin){
                  echo $sub_admin;
                }else{
                  echo '0';
                }

                ?>
                  
              </i>
            </a>
          </div>
        </div>
        <!-- /col -->
        
        
        
        
        
       
      
      
    
        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h4>Total</h4>
              <p>QR Code One List</p>
            </div>
            <div class="icon">
              <i class="fa fa-qrcode"></i>
            </div>
            <a href="<?php echo site_url('Genrate/qr_code_one_list');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
               <?php if($qr_code_one)
                {
                  echo $qr_code_one;
                }else{
                  echo '0';
                } 
                ?> 
              </i>
            </a>
          </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-red">
            <div class="inner">
              <h4>Total</h4>
              <p>QR Code Two List</p>
            </div>
            <div class="icon">
              <i class="fa fa-qrcode"></i>
            </div>
            <a href="<?php echo site_url('Genrate/qr_code_two_list');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
               <?php if($qr_code_two)
                {
                  echo $qr_code_two;
                }else{
                  echo '0';
                } 
                ?> 
              </i>
            </a>
          </div>
        </div>
        
        
        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-blue">
            <div class="inner">
              <h4>Total</h4>
              <p>QR Code Reset List</p>
            </div>
            <div class="icon">
              <i class="fa fa-qrcode"></i>
            </div>
            <a href="<?php echo site_url('Genrate/qr_code_reset_list');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
               <?php if($qr_code_reset)
                {
                  echo $qr_code_reset;
                }else{
                  echo '0';
                } 
                ?> 
              </i>
            </a>
          </div>
        </div>
        
        
        
         <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-green">
            <div class="inner">
              <h4>Total</h4>
              <p>QR Code Infinite List</p>
            </div>
            <div class="icon">
              <i class="fa fa-qrcode"></i>
            </div>
            <a href="<?php echo site_url('Genrate/qr_code_infinite_list');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">
               <?php if($qr_code_infinite)
                {
                  echo $qr_code_infinite;
                }else{
                  echo '0';
                } 
                ?> 
              </i>
            </a>
          </div>
        </div>
        
        
        
        
        
        
        
        
        
        
        
        
          <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h4>Total<sup style="font-size: 20px"></sup></h4>
              <p>Feedback</p>
            </div>
            <div class="icon">
             <i class="fa fa-comments-o"></i>
            </div>
            <a href="<?php echo site_url('Admin/feedback');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">

               <?php 
                if($feed){
                  echo $feed;
                }else{
                  echo '0';
                }

                ?> 
                  
              </i>
            </a>
          </div>
        </div>
        
        
        
        <div class="col-lg-3 col-xs-4">
          <div class="small-box bg-purple">
            <div class="inner">
              <h4>Total<sup style="font-size: 20px"></sup></h4>
              <p>Scan History</p>
            </div>
            <div class="icon">
              <i class="fa fa-history"></i>
            </div>
            <a href="<?php echo site_url('Admin/users');?>" class="small-box-footer">More info 
              <i class="fa fa-arrow-circle-right">

                <?php 
                if($scan){
                  echo $scan;
                }else{
                  echo '0';
                }

                ?>
                  
              </i>
            </a>
          </div>
        </div>
        
        
        
        
        
        
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

  <?php $this->load->view('admin/footer') ?>
  <div class="control-sidebar-bg"></div>
</div>



</body>

</html>
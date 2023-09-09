<aside class="main-sidebar">
   <section class="sidebar">
      <div class="user-panel">
         <div class="pull-left image">
            <img src="<?php echo base_url()?>upload/sub-admin/<?php echo $this->session->userdata('s_image');?>" class="img-circle" alt="Admin Image">
         </div>
         <div class="pull-left info">
            <p><?php echo $this->session->userdata('s_name');?></p>
         </div>
      </div>
      <ul class="sidebar-menu" data-widget="tree">
         <li class="<?php  if($this->uri->segment(1)=='SubAdmin' && ($this->uri->segment(2)=='dashboard')) {echo 'active';}?>">
            <a href="<?php echo base_url() ?>SubAdmin/dashboard">
               <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
         </li>
         
         <li class="<?php  if($this->uri->segment(1)=='SubAdmin' && ($this->uri->segment(2)=='profile')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>SubAdmin/profile">
           <i class="fa fa-user-circle"></i> <span>Profile</span>
            </a>
         </li>
         
         <li class="<?php  if($this->uri->segment(1)=='SubAdmin' && ($this->uri->segment(2)=='qr_code_list')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>SubAdmin/qr_code_reset_list">
            <i class="fa fa-qrcode"></i> <span>QR Reset List</span>
            </a>
         </li>

         <!--<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>Genrate/qr_list">
            <i class="fa fa-qrcode"></i> <span>QR Code List</span>
            </a>
         </li>
         
          <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/scan_history">
            <i class="fa fa-history"></i> <span>Scan History</span>
            </a>
         </li>

          <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/feedback">
            <i class="fa fa-comments-o" ></i> <span>Feedback</span>

            </a>
         </li>

<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/add_terms_condition">
            <i class="fa fa-gavel"></i> <span>Terms Condtions</span>
            </a>
         </li>


         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/add_privacy_policy">
            <i class="fa fa-shield"></i>  <span>Privacy Policy</span>
            </a>
         </li>

         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/add_contact">
            <i class="fa fa-address-book"></i><span>Contact Us</span>
            </a>
         </li>

         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/add_about_us">
           <i class="fa fa-info-circle"></i><span>About Us</span>
            </a>
         </li>-->




         
      </ul>
   </section>
   <!-- /.sidebar -->
</aside>
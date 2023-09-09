
<aside class="main-sidebar">
   <section class="sidebar">
      <div class="user-panel">
         <div class="pull-left image">
            <img src="<?php echo base_url()?>upload/admin/<?php echo $this->session->userdata('admin_image');?>" class="img-circle" alt="Admin Image">
         </div>
         <div class="pull-left info">
            <p><?php echo $this->session->userdata('admin_name');?></p>
         </div>
      </div>
      <ul class="sidebar-menu" data-widget="tree">
         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='dashboard')) {echo 'active';}?>">
            <a href="<?php echo base_url() ?>admin/dashboard">
               <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            </a>
         </li>
          <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='users')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/users">
           <i class="fa fa-user-circle"></i> <span>Users</span>
            </a>
         </li>
         
         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='sub_admin')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/sub_admin">
           <i class="fa fa-users"></i><span>Sub Admin</span>
            </a>
         </li>
        <!--  <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/restaurants">
            <i class="fa fa-cutlery"></i> <span>Restaurants</span>
            </a>
         </li> -->

         <!--<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">-->
         <!--   <a href="<?php  echo base_url()?>Genrate/qr_list">-->
         <!--   <i class="fa fa-qrcode"></i> <span>QR Code List</span>-->
         <!--   </a>-->
         <!--</li>-->
         
         <!--<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">-->
         <!--   <a href="<?php  echo base_url()?>Genrate/qr_code_list">-->
         <!--   <i class="fa fa-qrcode"></i> <span>QR Management List</span>-->
         <!--   </a>-->
         <!--</li>-->
         
         
    <!--      <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">-->
         
    <!--        <div class="dropdown" style='margin-top: 8px;'>-->
    <!--     <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">-->
    <!--     <i style='margin-left: 18px;' class="fa fa-qrcode"></i> <span style='margin-left: 10px;'>QR Genrate </span>-->
    <!--    <ul class="dropdown-menu " style="background-color: #222d32; margin-left: 17px; border:none; margin-top:10px;">-->
    <!--<li><a href="<?php echo base_url()?>Genrate/one_time_scan"> <i style='' class="fa fa-qrcode"></i>One Time Genrate</a></li>-->
    <!--<li><a href="<?php echo base_url()?>Genrate/two_time_scan"><i style='' class="fa fa-qrcode"></i>Two Time Genrate</a></li>-->
    <!--<li><a href="<?php echo base_url()?>Genrate/reset_time_scan"><i style='' class="fa fa-qrcode"></i>Reset Genrate</a></li>-->
 
    <!-- </ul>-->
    <!-- </div>-->
         
    <!--  </li>-->
         
         
           <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <!--<a href="">-->
            <!--<i class="fa fa-qrcode"></i> <span>QR Code Genrate</span>-->
            <div class="dropdown" style='margin-top: 10px;'>
    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">
       <i style='margin-left: 18px;' class="fa fa-qrcode"></i> <span style='margin-left: 10px;'>QR List  </span>
  <ul class="dropdown-menu " style="background-color: #222d32; margin-left: 17px; border:none; margin-top:10px;">
    <li><a href="<?php echo base_url()?>Genrate/one_time_list"> <i style='' class="fa fa-qrcode"></i>One Time List</a></li>
    <li><a href="<?php echo base_url()?>Genrate/two_time_list"><i style='' class="fa fa-qrcode"></i>Two Time List</a></li>
    <li><a href="<?php echo base_url()?>Genrate/reset_time_list"><i style='' class="fa fa-qrcode"></i>Reset List</a></li>
    <li><a href="<?php echo base_url()?>Genrate/infinite_time_list"><i style='' class="fa fa-qrcode"></i>Infinite List</a></li>
 
  </ul>
</div>
          
            <!--</a>-->
           
         </li>
         
         
         
           <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <!--<a href="">-->
            <!--<i class="fa fa-qrcode"></i> <span>QR Code Genrate</span>-->
            <div class="dropdown" style='margin-top: 20px;'>
    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">
       <i style='margin-left: 18px;' class="fa fa-qrcode"></i> <span style='margin-left: 10px;'>QR Generation </span>
  <ul class="dropdown-menu " style="background-color: #222d32; margin-left: 17px; border:none; margin-top:10px;">
    <li><a href="<?php  echo base_url()?>Genrate/qr_code_one_list"> <i style='' class="fa fa-qrcode"></i>One Generation</a></li>
    <li><a href="<?php  echo base_url()?>Genrate/qr_code_two_list"><i style='' class="fa fa-qrcode"></i>Two Generation</a></li>
    <li><a href="<?php  echo base_url()?>Genrate/qr_code_reset_list"><i style='' class="fa fa-qrcode"></i>Reset Generation</a></li>
    <li><a href="<?php  echo base_url()?>Genrate/qr_code_infinite_list"><i style='' class="fa fa-qrcode"></i>Infinite Generation</a></li>
 
  </ul>
</div>
          
            <!--</a>-->
           
         </li>
         
         
          <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
         
            <div class="dropdown" style='margin-top: 20px;'>
    <a aria-expanded="false" aria-haspopup="true" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">
       <i style='margin-left: 18px;' class="fa fa-image"></i> <span style='margin-left: 10px;'>Gallery </span>
  <ul class="dropdown-menu " style="background-color: #222d32; margin-left: 17px; border:none; margin-top:10px;">
    <li><a href="<?php  echo base_url()?>Genrate/add_image_multiple"> <i style='' class="fa fa-image"></i>Gallery Add</a></li>
    <li><a href="<?php  echo base_url()?>Genrate/gallary_list"><i style='' class="fa fa-image"></i>Gallery List</a></li>

  </ul>
</div>
         
         </li>
         
         
         
         
         
       

       
       
      
         
         <!--<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?> " style='margin-top: 8px;'>-->
         <!--   <a href="<?php  echo base_url()?>Genrate/add_image_multiple">-->
         <!--   <i class="fa fa-image"></i> <span>Gallery Add</span>-->
         <!--   </a>-->

         <!--</li>-->
         <!--<li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">-->
         <!--   <a href="<?php  echo base_url()?>Genrate/gallary_list">-->
         <!--   <i class="fa fa-image" ></i> <span>Gallery List</span> -->
          

         <!--   </a>-->
         <!--</li>-->
        
          <li style='margin-top: 7px;' class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/scan_history">
            <i class="fa fa-history"></i> <span>Scan History</span>
            </a>
         </li>
          
          <li  style='margin-top: -5px;'class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='restaurants')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/feedback">
            <i class="fa fa-comments-o" ></i> <span>Feedback</span> 
            <!--<i class="fa fa-material-icons"></i> <span>Feedback</span>-->

            </a>
         </li>
         
         <!-- <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/banners">
            <i class="fa fa-image"></i> <span>Banners</span>
            </a>
         </li> -->
         <!-- <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='categories')) {echo 'active';}?>">
            <a href="<?php  echo base_url()?>admin/categories">
            <i class="fa fa-list-alt"></i> <span>Categories </span>
            </a>
         </li> -->
        
         <!-- <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='promoCode')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/promoCode">
            <i class="fa fa-tag  fa-inverse"></i> <span>Promo Codes</span>
            </a>
         </li> -->
         <!-- <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='billing')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/billings">
            <i class="fa fa-dollar"></i> <span>Billings</span>
            </a>
         </li> -->
         <!-- <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='payments')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/payments">
            <i class="fa fa-dollar"></i> <span>Payments</span>
            </a>
         </li> -->
         


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
         </li>
         
         
         
         
       
         

<!-- 
         <li class="<?php  if($this->uri->segment(1)=='admin' && ($this->uri->segment(2)=='banners')) {echo 'active';}?>">
            <a href="<?php echo base_url()?>admin/banners">
            <i class="fa fa-image"></i> <span>Faq</span>
            </a>
         </li>
 -->

         
      </ul>
      
   </section>
   <!-- /.sidebar -->
</aside>

<script>
    //dropdown on  click //
$(".dropdown_click .selected").on('click', function() {
    $(".dropdown_click .drop-content ul").slideToggle();
});

$(".dropdown_click .drop-content ul li span").on('click', function() {
    // var bindText = $(this).html();
    $(".dropdown_click .selected  span").html($(this).html());
    $(".dropdown_click .drop-content ul").slideUp();
}); 

//dropdown on  hover //
$(".dropdown_hover ").on({
    mouseenter: function () {
       $(".drop-content .drop-hover").slideDown();
    },
    mouseleave: function () {
       $(".drop-content .drop-hover").slideUp();
    }
});

$(".dropdown_hover .drop-content .drop-hover li span").on('click', function() {
    $(".dropdown_hover .selected  span").html($(this).html());
    $(".dropdown_hover .drop-content .drop-hover").slideUp();
}); 

$(document).bind('click', function(e) {
    var $clickhide = $(e.target);
    if (! $clickhide.parents().hasClass("dropdown_c"))
        $(".dropdown_c .drop-content ul").slideUp();
});

</script>

<script>
    document.addEventListener("DOMContentLoaded", function(){
  document.querySelectorAll('.sidebar .nav-link').forEach(function(element){
    
    element.addEventListener('click', function (e) {

      let nextEl = element.nextElementSibling;
      let parentEl  = element.parentElement;	

        if(nextEl) {
            e.preventDefault();	
            let mycollapse = new bootstrap.Collapse(nextEl);
            
            if(nextEl.classList.contains('show')){
              mycollapse.hide();
            } else {
                mycollapse.show();
                // find other submenus with class=show
                var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                // if it exists, then close all of them
                if(opened_submenu){
                  new bootstrap.Collapse(opened_submenu);
                }
            }
        }
    }); 
  }) 
}); 
</script>

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
        <?php echo $title; ?> 
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo site_url('Admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>User Management</li>
        <li class="active">User </li>
      </ol>
    </section>
    <!-- Main content -->
   <section class="content">
	<div class="page-heading">
		<!-- <h1><?php echo $title; ?></h1> -->
		<!-- <ol class="breadcrumb">
			<li><a href="<?php echo base_url(); ?>dashboard">Home</a></li>
			<li><a href="javascript:void(0);">Forms</a></li>
			<li class="active"><?php echo $title; ?></li>
		</ol> -->
	</div>
		<?php

				if($this->session->flashdata('item')) {
				$message = $this->session->flashdata('item');
				?>
				<div class="<?php echo $message['class'] ?>"><?php echo $message['message']; ?>

				</div>
				<?php
				}

				?>
	<div class="page-body clearfix">
	<!-- Basic Validation -->
	<div class="panel panel-default">
	<!-- <div class="panel-heading"><?php echo $title; ?></div> -->
	<div class="panel-body">
		<div class="card-body">
			<form action="<?php echo base_url(); ?>admin/update_contact_us/<?php echo $contacts['id']; ?>" method="post">
			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label>Name</label>
						<input type="text" name="name" value="<?php echo $contacts['name']; ?>" class="form-control" placeholder="Enter your Name">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Email</label>
						<input type="text" name="email" value="<?php echo $contacts['email']; ?>" class="form-control" placeholder="Enter your Email">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Mobile</label>
						<input type="text" name="mobile" value="<?php echo $contacts['mobile']; ?>" class="form-control" placeholder="Enter your Mobile">
					</div>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label>Address</label>
						<input type="text" value="<?php echo $contacts['address']; ?>" name="address" class="form-control" placeholder="Enter your Date Of Birth">
					</div>
				</div>
				<div class="col-md-12">
					<div class="form-group">
						<label>Subject</label>
						<textarea type="text" name="subject" class="form-control" placeholder="Enter your Date Of Birth"><?php echo $contacts['subject']; ?></textarea>
					</div>
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Update</button>
		</div>
	</form>
	</div>
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

<script src="https://cdn.ckeditor.com/4.20.1/standard-all/ckeditor.js"></script>
		 <script>
    CKEDITOR.replace('editor1', {
      fullPage: true,
      extraPlugins: 'docprops',
      allowedContent: true,
      height: 200,
      removeButtons: 'PasteFromWord'
    });
  </script>
  <script type="application/javascript">
/** After windod Load */
$(window).bind("load", function() {
  window.setTimeout(function() {
    $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove();
    });
}, 4000);
});
</script>



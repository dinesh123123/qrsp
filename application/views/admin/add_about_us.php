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
				
				<!-- <ol class="breadcrumb">
					<li><a href="<?php echo base_url(); ?>admin/dashboard">Dashboard</a></li>
					<li class="active">Home</li>
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
						<form action="<?php echo base_url(); ?>admin/update_about_us/<?php echo $terms['id'] ;?>" method="post">
							<div class="row">
							<div class="col-md-12">
							<div class="form-group">
								<label>Title</label>
								<input style="border-radius: 7px;" type="text" class="form-control" name="title" value="<?php echo $terms['title'] ;?>"  placeholder="Your Title" required />
							</div>
						</div>
						</div>
							<div class="form-group">
								<label>Description</label>
							<textarea cols="80" id="editor1" name="description" rows="10"><?php echo $terms['description'] ;?>
        </textarea>
							</div>

							<div class="form-group">
								<button type="submit" class="btn btn-sm btn-success">Update</button>
							</div>
						</form>
					</div>
				</div>
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



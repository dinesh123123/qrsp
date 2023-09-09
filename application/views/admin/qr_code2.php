<style type="text/css">
	.error {
	color: red;
	}
</style>
<style type="text/css">
	.error {
	color: red;
	}
	#map_canvas {         
	height: 400px;         
	width: 400px;         
	margin: 0.6em;       
	}
	input[type="file"] {
	display: block;
	}
	.imageThumb {
	max-height: 75px;
	border: 2px solid;
	padding: 1px;
	cursor: pointer;
	}
	.pip {
	display: inline-block;
	margin: 10px 10px 0 0;
	}
	.remove {
	display: block;
	background: #444;
	border: 1px solid black;
	color: white;
	text-align: center;
	cursor: pointer;
	}
	.remove:hover {
	background: white;
	color: black;
	}
</style>
<body class="hold-transition skin-blue sidebar-mini">
	<div class="wrapper">
		<?php $this->load->view('admin/navbar') ?>
		<?php $this->load->view('admin/sidebar') ?>
		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<h1>
				  QR Codes Genrate
					<!-- <small> Preview</small> -->
				</h1>
				<ol class="breadcrumb">
					<li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
					<li><a href="<?php echo site_url('admin/feedback');?>">Feedback</a></li>
					<li class="active"><a href="<?php echo site_url('Genrate/qr_list');?>" > QR Code List</a></li> 


				</ol>
			</section>
			<!-- <a href="<?php echo base_url()?>Genrate/qr_code_list" class="btn btn-primary btn-sm pull-right ">Back</a> -->
			<!-- Main content -->
			<section class="content">
				<div class="row">
					<div class="col-md-12">

						<?php  if(isset($error)){ echo $error; }
							echo $this->session->flashdata('message'); ?>
						<!-- general form elements -->
						<div class="box box-primary">
							<!-- <div class="box-header">
								<h3 class="box-title">Contact Detail </h3>
							</div> -->
							<!-- /.box-header -->
							<!-- form start -->
							<form action="<?php echo base_url()?>Genrate/save_qr_code" method="post" enctype="multipart/form-data">
								<div class="box-body">
									<div class="row">
										<div class="col-md-6">

										<?php

										if($this->session->flashdata('item')) {
										$message = $this->session->flashdata('item');
										?>
										<div class="<?php echo $message['class'] ?>"><?php echo $message['message']; ?>

										</div>
										<?php
										}

										?>
										
										
											<div class="form-group">
												<label class="">Unique ID : <span class="text-danger">*</span></label>
												<input type="number" id="numberField" name="unique_id" class="form-control" placeholder="Enter Unique Number Only" required />
												<?php echo form_error('tables', '<div class="error">', '</div>'); ?>
											</div>
										</div>




										<!--<div class="col-md-6">
											<div class="form-group">
												<label class="">Type : <span class="">*</span></label>
												<input type="text" name="type" class="form-control" placeholder="Enter Type" />
												<?php echo form_error('tables', '<div class="error">', '</div>'); ?>
											</div>
										</div>





										<div class="col-md-6">
											<div class="form-group">
												<label class="">Unit : <span class="">*</span></label>
												<input type="number"  id="numberunit" name="unit" class="form-control" placeholder="Enter Unit Number " />
												<?php echo form_error('tables', '<div class="error">', '</div>'); ?>
											</div>
										</div>




										<div class="col-md-6">
											<div class="form-group">
												<label class="">Image : <span class="">*</span></label>
												<input type="file" name="image" class="form-control" />
												<?php echo form_error('tables', '<div class="error">', '</div>'); ?>
											</div>
										</div>


							<div class="col-md-12">
							<div class="form-group">
						<label class="">Title : <span class="">*</span></label>
						<textarea class="form-control" name="title" placeholder="Enter Title" rows="3"  ></textarea>
							</div>
						</div>


						<div class="col-md-12">
							<div class="form-group">
						<label class="">Information / Link : <span class="">*</span></label>
						<textarea class="form-control" name="information" placeholder="Enter Information /Link " rows="5" ></textarea>
							</div>
						</div>-->




									
									</div>
								</div>
								<div class="box-footer">
									<input type="submit" class="btn btn-primary" name="submit" value="Generate QR"/>
									
								</div>
							</form>
						</div>
						<div class="box box-primary">
						  <div class="row">
							 <div class="col-md-3">
							 <!-- 
										  <iframe src="" name="frame" style="display:none;"></iframe>
											<button class="btn btn-primary right" onclick="frames['frame'].print()"></button>  -->
									
									 </div>
						  </div>
							<!--<div class="row">
									<?php if(!empty($qr_code)) { foreach ($qr_code as $value) { ?>
							<div class="col-md-3">
							
									
								 <img height="200" width="210" src="<?php echo base_url()?><?php echo $value['qr_code']; ?>">
							</div>
					
								<?php } } ?>
									 
						</div>-->
					</div>
				</div>
			</section>
			<!-- /.content -->
			</aside><!-- /.right-side -->
		</div>
		<?php $this->load->view('admin/footer') ?>
		<div class="control-sidebar-bg"></div>
	</div>
</body>
<script>
function myFunction() {
  window.print();
}
</script>

<script type="text/javascript">
	function restrictNumber(e) {
  var newValue = this.value.replace(new RegExp(/[^\d]/, 'ig'), "");
  this.value = newValue;
}

var userName = document.querySelector('#numberField');
userName.addEventListener('input', restrictNumber);
var userName = document.querySelector('#numberunit');
userName.addEventListener('input', restrictNumber);
</script>


In Your View File Set this
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

</html>

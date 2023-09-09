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
<script src="https://code.jquery.com/jquery-2.1.3.js"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
<script
	src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
.description {
    font-size: 2em;
    line-height: 4;
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
				  QR Codes
					<!-- <small> Preview</small> -->
				</h1>
				<ol class="breadcrumb">
					<li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
					<li><a href="<?php echo site_url('admin/feedback');?>">Feedback</a></li>
					<li class="active"><a href="<?php echo site_url('Genrate/qr_list');?>"> QR Code List</li>

				</ol>
			</section>
			<!-- <a href="<?php echo base_url()?>Genrate/qr_list" class="btn btn-primary btn-sm pull-right ">Back</a> -->
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
						
						</div>
						
						  <div class="row">
							 <div class="col-md-3">
							 <!-- 
										  <iframe src="" name="frame" style="display:none;"></iframe>
											<button class="btn btn-primary right" onclick="frames['frame'].print()"></button>  -->
									
									 </div>
						  </div>
							<div class="row">
								 <!-- <a href="<?php echo base_url()?>Genrate/qr_list" class="btn btn-primary btn-sm pull-right ">Back</a>  -->
									<?php if(!empty($qr_code)) { foreach ($qr_code as $value) { ?>
							<div class="col-md-4 outer-content">
							<div id="pdf-content" class="">
							<!-- <div id="pdf-content" class="vertical-center"> -->
									
								 <img  height="200" width="200" src="<?php echo base_url()?><?php echo $value['qr_code']; ?>">
							</div>
						 <!-- <div class="text-primary" style="margin-left: 40px;">
									<span class="info-box-text">UNIQUE ID   :  <?php echo $value['unique_id']; ?></span>
										  </div> -->
<div class="" style="margin-left: 40px;">
									<!-- <input type="submit" class="btn btn-primary" name="submit" value="PDF"/> -->
									<button id="btn-generate" class="btn btn-primary">Generate PDF</button>
									
								</div>
										  
							</div>
							
					
								<?php } } ?>
									 
			
					</div>
<!-- 
					<div id="content">
     <img  height="200" width="210" src="<?php echo base_url()?><?php echo $value['qr_code']; ?>">
</div> -->
<!-- <div id="editor"></div>
<button id="cmd">Generate PDF</button> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
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

<script>
	let print = () => {
    	let objFra = document.getElementById('myFrame');
        objFra.contentWindow.focus();
        objFra.contentWindow.print();
    }
    
    // Using regular js features.
    
    function print() {
        var objFra = document.getElementById('myFrame');
        objFra.contentWindow.focus();
        objFra.contentWindow.print();
    }
</script>
<script type="text/javascript">
	var doc = new jsPDF();
    var specialElementHandlers = {
        '#editor': function (element, renderer) {
            return true;
        }
    };

    $('#cmd').click(function () {
        doc.fromHTML($('#content').html(), 15, 15, {
            'width': 170,
                'elementHandlers': specialElementHandlers
        });
        doc.save('sample-file.pdf');
    });

</script>
<script>
	var buttonElement = document.querySelector("#btn-generate");
	buttonElement.addEventListener('click', function() {
		var pdfContent = document.getElementById("pdf-content").innerHTML;
		var windowObject = window.open();

		windowObject.document.write(pdfContent);

		windowObject.print();
		windowObject.close();
	});
</script>
<script>
$(document).ready(function(){
	$("#btn-generate").click(function(){
		var htmlWidth = $("#pdf-content").width();
		var htmlHeight = $("#pdf-content").height();
		var pdfWidth = htmlWidth + (15 * 2);
		var pdfHeight = (pdfWidth * 1.5) + (15 * 2);
		
		var doc = new jsPDF('p', 'pt', [pdfWidth, pdfHeight]);
	
		var pageCount = Math.ceil(htmlHeight / pdfHeight) - 1;
	
	
		html2canvas($("#pdf-content")[0], { allowTaint: true }).then(function(canvas) {
			canvas.getContext('2d');
	
			var image = canvas.toDataURL("image/png", 1.0);
			doc.addImage(image, 'PNG', 15, 15, htmlWidth, htmlHeight);
	
	
			for (var i = 1; i <= pageCount; i++) {
				doc.addPage(pdfWidth, pdfHeight);
				doc.addImage(image, 'PNG', 15, -(pdfHeight * i)+15, htmlWidth, htmlHeight);
			}
			
		doc.save("output.pdf");
		});
	});
});
</script>



</html>

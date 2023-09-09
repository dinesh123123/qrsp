<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

      <?php $this->load->view('sub-admin/navbar') ?>

      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.5/css/buttons.dataTables.min.css">
  <!-- Left side column. contains the logo and sidebar -->
  
    <?php $this->load->view('sub-admin/sidebar') ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?=$title;?>
      </h1>

      <ol class="breadcrumb">

        <li><a href="<?php echo site_url('SubAdmin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><?=$title;?></li>
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
              <!-- <h3 class="box-title">All User</h3> -->
              <!--<a href="<?php echo base_url()?>Genrate/qr_code" class="btn btn-primary btn-sm pull-right ">QR Codes Genrate</a>-->
              <!--<div class="float-right">
                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('importFrm');"><i class="fa fa-plus"></i> Import</a>
            </div>
            <div id="importFrm" style="display: none;">
                <h3 class="box-title">Upload excel file</h3>
              <form class="form-inline" action="<?php echo base_url();?>Genrate/import2" method="post" enctype="multipart/form-data">
                  <div class="form-group">
<input type="file" name="file" value="" />
</div>
<input class="btn btn-primary" type="submit" name="submit" value="Upload" />
</form>
</div>-->


<?php echo anchor('SubAdmin/reseset_qr_code_all/', 'All Reset <i class="fa fa-refresh"> </i>', array("class"=>"btn btn-success", "onclick"=>"return confirm('Are You Sure Resest All QR Code ?')")); ?>



            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="table-responsive" style="overflow-x:auto;">
                
              <table id="example1" class="table table-bordered table-striped">
                  
                <thead>
                    
                <tr>
                  <th>S.no</th>
                  <th>Image</th>
                  <th>UniqueID</th>
                  <!--<th>Type</th>-->
                  <!--<th>Unit</th>-->
                  <th>Scan Status</th>
                  <!--<th>Title</th>-->
                  <!--<th>Information</th>-->
                  <!--<th>Date</th>-->
                  <!--<th>View</th>-->
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
        
              <?php $a=1;  foreach ($qr_code as $row) { ?>
                <tr>

                  <td><?php echo $a++;?></td>
                  <td class="center">
										<?php if(!empty($row['image'])){ ?>
												<a href="<?php echo $row['image'];?>">
										<img src="<?php echo $row['image'];?>" height=80px width=100px>
									<?php } else{ ?>
										<a href="<?php echo base_url()?>assets/qr_image/qrimage.jpg">
										<img src="<?php echo base_url()?>assets/qr_image/qrimage.jpg" height=80px width=100px>
								<?php  } ?>
									</td>
                  <td><?php echo ucwords($row['unique_id']);?></td>
                  <!--<td><?php echo $row['type'];?></td>-->
                  <!--<td><?php echo $row['unit']?></td>-->
                  <td><?php echo $row['scan_status']?></td>
                  <!--<td><?php echo character_limiter($row['title'],25)?></td>-->
                  <!--<td><?php if (!filter_var($row['information'], FILTER_VALIDATE_URL) === false) { echo $row['information'];} else { echo character_limiter($row['information'],25);}?></td>-->
                 <!--<td> <?php echo $row['created_date']?></td>-->

                 <!--<td>
                   <?php echo anchor('Genrate/qrcodeview/'.$row['id'], '<i class="fa fa-qrcode"></i>', array("class"=>"btn btn-success")); ?>
                 </td>-->
                 
                 <!--<td>
                      <?php echo anchor('Genrate/QrDelete/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are You Sure delete?')")); ?>

                      <a class="btn btn-warning" href="<?php echo base_url(); ?>Genrate/qr_code_edit/<?php echo $row['id'] ; ?>"><i class="fa fa-edit"></i></a>


                 
                  </td>--> 
                  
                  <td class="text-center">
                    <div class="btn-group">
                      <?php echo anchor('SubAdmin/reseset_qr_code/'.$row['id'], '<i class="fa fa-refresh"></i>', array("class"=>"btn btn-success", "onclick"=>"return confirm('Are You Sure Resest QR Code ?')")); ?>
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

    <?php $this->load->view('sub-admin/footer') ?>
    <div class="control-sidebar-bg"></div>
</div>
<!-- ./wrapper -->


</body>
</html>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.print.min.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#example1').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    } );
} );
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


 <script type="text/javascript">
        $("body").on("click", "#btnExport", function () {
            html2canvas($('#example1')[0], {
                onrendered: function (canvas) {
                    var data = canvas.toDataURL();
                    var docDefinition = {
                        content: [{
                            image: data,
                            width: 500
                        }]
                    };
                    pdfMake.createPdf(docDefinition).download("customer-details.pdf");
                }
            });
        });
    </script>
<!--<script>
function formToggle(ID){
    var element = document.getElementById(ID);
    if(element.style.display === "none"){
        element.style.display = "block";
    }else{
        element.style.display = "none";
    }
}
</script>-->
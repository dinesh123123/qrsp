<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

      <?php $this->load->view('admin/navbar') ?>

      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.3.5/css/buttons.dataTables.min.css">
  <!-- Left side column. contains the logo and sidebar -->
  
    <?php $this->load->view('admin/sidebar') ?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
     <?php echo $title; ?>
      </h1>
      <div class="row">
          <div class="col-md-3">
         <?php if($qr_code){ ?>
                                <iframe src="<?php echo base_url()?>/Genrate/generate_pdf_code_reset" name="frame" style="display:none;"></iframe>
                                 <button class="btn btn-primary right" onclick="frames['frame'].print()">Print Code</button> 
                       
                           
                            <?php }else{ ?>
                                <button class="btn btn-primary right ">Print Code</button> 
                        <?php     } ?>
                         </div>
                    </div>

      <ol class="breadcrumb">

        <li><a href="<?php echo site_url('Admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo site_url('admin/feedback');?>">Feedback</a></li>
        <!--<li><a href="<?php echo base_url()?>Genrate/qr_code2" class="btn btn-primary btn-sm pull-right ">QR Codes Genrate</a></li>-->
        <!-- <li class="active"><?php echo $title ; ?></li> -->
        <!-- <button style="font-size: 10px;" class="btn btn-primary" id="btnExport" type="button" > PDF</button > -->
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
        <?php  if(isset($error)){ echo $error; }
        echo $this->session->flashdata('success_req'); ?>
        <?php if(!empty($success_msg)){ ?>
    <div class="col-xs-12">
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    </div>
    <?php } ?>
    <?php if(!empty($error_msg)){ ?>
    <div class="col-xs-12">
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    </div>
    <?php } ?>
          <div class="box box-primary">
<!--            <div class="box-header head">-->
                
              <!--<a href="<?php echo base_url()?>Genrate/qr_code" class="btn btn-primary btn-sm pull-right ">QR Codes Genrate</a>-->
<!--              <div class="float-right">-->
<!--                <a href="javascript:void(0);" class="btn btn-success" onclick="formToggle('importFrm');"><i class="fa fa-plus"></i> QR Generate</a>-->
<!--            </div>-->
<!--            <div id="importFrm" style="display: none;">-->
<!--                <h3 class="box-title">Upload excel file</h3>-->
<!--              <form class="form-inline" action="<?php echo base_url();?>Genrate/import" method="post" enctype="multipart/form-data">-->
<!--                  <div class="form-group">-->
<!--<input type="file" name="file" value="" />-->
<!--</div>-->
<!--<input class="btn btn-primary" type="submit" name="submit" value="Upload" />-->
<!--</form>-->
<!--</div>-->
<!--            </div>-->
            <!-- /.box-header -->
            <div class="box-body">
            <div class="table-responsive" style="overflow-x:auto;">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>S.no</th>
                  <th>QR TYPE</th>
                  
                  <th>UniqueID</th>
                  <th>Image</th>
                  <!--<th>View</th>-->
                  <th>Date</th>
                  <th>Time</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
        
              <?php $a=1;  foreach ($qr_code as $row) { ?>
                <tr>

                  <td><?php echo $a++;?></td>
                  <td><?php echo $row['qr_type'];?></td>
                  <td><?php echo ucwords($row['unique_id']);?></td>
                   <td class="center">
                    <?php if(!empty($row['qr_code'])){ ?>
                    <img src="<?php echo base_url().$row['qr_code'];?>" height=100px width=100px>
                  <?php } else{ ?>
                    <img src="<?php echo base_url()?>assets/qr_image/qrimage.jpg" height=80px width=100px>
                <?php  } ?>
                <!--  </td>-->
                 <!-- <td>-->
                 <!--  <?php echo anchor('Genrate/qrcodeview/'.$row['id'], '<i class="fa fa-qrcode"></i>', array("class"=>"btn btn-success")); ?>-->
                 <!--</td>-->
                 <td><?php echo ($row['created_date']);?> </td>
                 <td><?php echo ($row['created_time']);?> </td>
                  <td>
                      <?php echo anchor('Genrate/reset_time_delete/'.$row['id'], '<i class="fa fa-times"></i>', array("class"=>"btn btn-danger", "onclick"=>"return confirm('Are you sure delete?')")); ?>
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

    <?php $this->load->view('admin/footer') ?>
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
    <script>
function formToggle(ID){
    var element = document.getElementById(ID);
    if(element.style.display === "none"){
        element.style.display = "block";
    }else{
        element.style.display = "none";
    }
}
</script>

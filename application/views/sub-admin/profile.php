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
        <!-- <li class="active"><?php echo $title ; ?></li> -->
        <!-- <button style="font-size: 10px;" class="btn btn-primary" id="btnExport" type="button" > PDF</button > -->
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header">
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            <div class="row">
                <div class="col-sm-3">
                    <img src="<?=base_url();?>upload/sub-admin/<?=$this->session->userdata('s_image');?>" style="width:100%;margin-bottom:30px;">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-striped">
                <tr>
                  <th>Name</th>
                  <td><?=ucwords($this->session->userdata('s_name'));?></th>
                </tr>
                <tr>
                  <th>Email</th>
                  <td><?=$this->session->userdata('s_email');?></td>
                </tr>
              </table>
                </div>
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
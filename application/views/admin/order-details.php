<body class="hold-transition skin-blue sidebar-mini">
   <div class="wrapper">
      <?php $this->load->view('admin/navbar') ?>
      <!-- Left side column. contains the logo and sidebar -->
      <?php $this->load->view('admin/sidebar') ?>
      <!-- Content Wrapper. Contains page content -->
      <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Order Detail
        <small>#<?= $main_details->order_id; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Order Detail</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="invoice">
      <!-- title row -->
      <div class="row">
        <div class="col-xs-12">
          <h2 class="page-header">
            <i class="fa fa-globe"></i> Rapidine, Inc.
            <small class="pull-right">Date: <?= date("d/m/Y", strtotime($main_details->date)); ?></small>
          </h2>
        </div>
        <!-- /.col -->
      </div>
      <!-- info row -->
      <div class="row invoice-info">
        <div class="col-sm-4 invoice-col">
          From
          <address>
            <strong><?= $main_details->restaurant_name; ?></strong><br>
            <?= $main_details->restaurant_location; ?><br>
            Phone: <?= $main_details->restaurant_mobile; ?><br>
            Email: <?= $main_details->restaurant_email; ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          To
          <address>
            <strong><?= $main_details->user_name; ?></strong><br>
            <?= $main_details->user_address; ?><br>
            Phone: <?= $main_details->user_mobile; ?><br>
            Email: <?= $main_details->user_email; ?>
          </address>
        </div>
        <!-- /.col -->
        <div class="col-sm-4 invoice-col">
          <!-- <b>Invoice #007612</b><br> -->
          <!-- <br> -->
          <b>Order ID:</b> <?= $main_details->order_id; ?>  <br>
          <b>Order Date:</b> <?= date("d/m/Y", strtotime($main_details->date)); ?><br>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Table row -->
      <div class="row">
        <div class="col-xs-12 table-responsive">
          <table class="table table-striped">
            <thead>
            <tr>
              <th>Product</th>
              <th>Qty</th>
              <th>Price</th>
              <th>Item Total</th>
            </tr>
            </thead>
            <tbody>
               <?php 
                   $total = 0.0;
                   $gst = 0.0;
                   $vat = 0.0;

               if(count($orders_item))
               {
                  foreach ($orders_item as $key => $value)
                  {  
                      $total = $total + $value['total_price'];
                      $gst = $gst + $value['gst'];
                      $vat = $vat + $value['vat'];
                   
                     ?>
                     <tr>
                          <td><?= $value['menue_name']; ?></td>
                          <td><?= $value['quantity']; ?></td>
                          <td><?= $value['price']; ?></td>
                          <td><?= $value['total_price']; ?></td>
                     </tr>
                     <?php
                  }


                     $total_gst = $gst + $con_tax;
                     $promo_discount = $orders_item[$key]['promo_discount'];
                     $total_payble = $total + $gst + $con_tax + $convenience_fees + $vat - $orders_item[$key]['promo_discount'] ;
                     $table_id = $orders_item[$key]['table_number'];
               }
               ?>
            </tbody>
          </table>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <div class="row">
        <!-- accepted payments column -->
        <div class="col-xs-6">
        </div>
        <!-- /.col -->
        <div class="col-xs-6">
          <div class="table-responsive">
            <table class="table">
              <tr>
                <th style="width:50%">Subtotal:</th>
                <td><?= number_format((float)$total, 2, '.', '');  ?></td>
              </tr>
              <tr>
                <th>Convenience Fee</th>
                <td><?=  number_format((float)$convenience_fees, 2, '.', '');  ?></td>
              </tr>
              <tr>
                <th>GST:</th>
                <td><?= number_format((float)$total_gst, 2, '.', '');  ?></td>
              </tr> 
              <tr>
                <th>VAT:</th>
                <td><?= number_format((float)$vat, 2, '.', '');   ?></td>
              </tr>
              <tr>
                <th>Discount:</th>
                <td><?= number_format((float)$promo_discount, 2, '.', '');  ?></td>
              </tr>
              <tr>
                <th>Total Payble:</th>
                <td><?= number_format((float)$total_payble, 2, '.', '');  ?>  </td>
              </tr>
            </table>
          </div>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
  </div>
      <!-- /.content-wrapper -->
      <?php $this->load->view('admin/footer') ?>
      <div class="control-sidebar-bg"></div>
   </div>
</body>
</html>

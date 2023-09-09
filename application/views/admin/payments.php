
<?php 
$from_date=$to_date=""; 
extract($_GET); ?> 
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
               Restaurant Payment
            </h1>
            <ol class="breadcrumb">
               <li><a href="<?php echo site_url('admin/dashboard');?>"><i class="fa fa-dashboard"></i> Home</a></li>
               <li>Payment Management</li>
               <li class="active">Restaurant Payment </li>
            </ol>
         </section>
         <!-- Main content -->
         <section class="content">
            <div class="row">
               <div class="col-xs-12">
                  <div class="box box-primary">
                     <!-- <div class="box-header with-border">
                <h3 class="box-title"><?= $restaurant_name; ?> Orders</h3>
              </div> -->
                     <!-- /.box-header -->
                     <div class="box-body">
                          <form action="<?php echo base_url()?>Admin/payments/" method="get" enctype="multipart/form-data">
                        <div class="box-body">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="form-group">
                                  <div class="col-md-6">
                                    <label class="">From Date:<span class="text-danger">*</span></label>
                                  </div>
                                  <div class="col-md-6">
                                    <input type="text" name="from_date" value="<?php echo $from_date; ?>" class="form-control datepicker"/>
                                  </div>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                  <div class="col-md-6">
                                    <label class="">To Date:<span class="text-danger">*</span></label>
                                  </div>
                                  <div class="col-md-6">
                                    <input type="text" name="to_date" value="<?php echo $to_date; ?>" class="form-control datepicker"/>
                                  </div>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                <div class="form-group">
                                    <input type="submit" class="btn btn-primary" value="Search"/>
                                 </div>

                                  
                              </div>
                           </div>
                        </div>
                     </form>
                     </div>
                     <!-- /.box-body -->
                  </div>
                  <!-- /.box -->
               </div>
               <!-- /.col -->
            </div>
            <!-- /.row -->
         </section>
         <section class="content">
            <div class="row">
               <div class="col-xs-12">
                  <?php if( $this->session->flashdata('message') ) : ?> 
                    <div class="alert alert-success" role="alert">
                      <?php echo $this->session->flashdata('message'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="box box-primary">
                     <div class="box-header">
                        <!-- <a href="<?php echo base_url()?>admin/add_restaurant" class="btn btn-info btn-sm pull-right ">Add Restaurant</a> -->
                     </div>
                     <!-- /.box-header -->
                     <div class="box-body">
                        <div class="table-responsive" style="overflow-x:auto;">
                           <table id="example1" class="table table-bordered table-striped">
                              <thead>
                                 <tr>
                                      <th>Sr. no</th>
                                    <th>Restaurant Name</th>
                                    <th>Date</th>
                                    <th>Order Id</th>
                                    <th>Item Total</th>
                                    <th>Con Fee</th>
                                    <th>GST</th>
                                    <th>VAT</th>
                                    <th>Discount</th>
                                    <th>Total</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 
                                  $a=1; 
                                  foreach ($data as $row) 
                                  {
                                   $datas = $controller->get_single_order_item($row['order_id']);
                                   $total = 0.0;
                                   $gst = 0.0;
                                   $vat = 0.0;
                                   foreach ($datas as $key => $value)
                                    {  
                                      $total = $total + $value['total_price'];
                                      $gst = $gst + $value['gst'];
                                      $vat = $vat + $value['vat'];
                                    }

                                    $total_gst = $gst + $con_tax;
                                    $promo_discount = $datas[$key]['promo_discount'];
                                    $total_payble = $total + $gst + $con_tax + $convenience_fees + $vat - $datas[$key]['promo_discount'];

                                  ?>
                                 <tr>
                                    <td><?php echo $a++;?></td>
                                    <td><?php echo $row['restaurant_name']?></td>
                                    <td><?php echo $row['date']?></td>
                                    <td><?php echo $row['order_id']?></td>
                                    <td><?php echo $row['total_price']?></td>
                                    <td><?php echo $convenience_fees; ?></td>
                                    <td><?php echo $total_gst; ?></td>
                                    <td><?php echo $vat; ?></td>
                                    <td><?php echo $promo_discount; ?></td>
                                    <td><?php echo $total_payble; ?></td>
                                 </tr>
                                 <?php 
                               } ?>
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
</body>
</html>
<script src="https://cdn.datatables.net/buttons/1.2.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.flash.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
<script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.html5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.1/css/buttons.dataTables.min.css" />

<script type="text/javascript">
$(document).ready(function(){
  var table =  $('.table').DataTable({
    // 'columnDefs': [{
    //      'targets': 0,
    //      'searchable':false,
    //      'orderable':false,
    //      'className': 'dt-body-center',
    //      'render': function (data, type, full, meta){
    //          return '<input type="checkbox" name="id[]" value="' 
    //             + $('<div/>').text(data).html() + '">';
    //      }
    //   }]
    dom: 'Bfrtip',
        buttons: [
            { extend: 'excel', text: 'Export in Excel' }
        ]
      });

  // // Handle click on "Select all" control
  //  $('#example-select-all').on('click', function(){
  //     // Check/uncheck all checkboxes in the table
  //     var rows = table.rows({ 'search': 'applied' }).nodes();
  //     $('input[type="checkbox"]', rows).prop('checked', this.checked);
  //  });

  //  // Handle click on checkbox to set state of "Select all" control
  //  $('.table tbody').on('change', 'input[type="checkbox"]', function(){
  //     // If checkbox is not checked
  //     if(!this.checked){
  //        var el = $('#example-select-all').get(0);
  //        // If "Select all" control is checked and has 'indeterminate' property
  //        if(el && el.checked && ('indeterminate' in el)){
  //           // Set visual state of "Select all" control 
  //           // as 'indeterminate'
  //           el.indeterminate = true;
  //        }
  //     }
  //  });
    
   // $('#frm-example').on('submit', function(e){
   //    var form = this;

   //    // Iterate over all checkboxes in the table
   //    table.$('input[type="checkbox"]').each(function(){
   //       // If checkbox doesn't exist in DOM
   //       if(!$.contains(document, this)){
   //          // If checkbox is checked
   //          if(this.checked){
   //             // Create a hidden element 
   //             $(form).append(
   //                $('<input>')
   //                   .attr('type', 'hidden')
   //                   .attr('name', this.name)
   //                   .val(this.value)
   //             );
   //          }
   //       } 
   //    });

   //    $('#example-console').text($(form).serialize()); 
   //    console.log("Form submission", $(form).serialize()); 
       
   //    // Prevent actual form submission
   //    e.preventDefault();
   // });
});
</script>
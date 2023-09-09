<script src="<?php echo base_url()?>admindata/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url()?>admindata/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo base_url()?>admindata/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url()?>admindata/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo base_url()?>admindata/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url()?>admindata/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
     //Date picker
    $('#datepicker').datepicker({
      	autoclose: true,
    	format: 'dd-mm-yyyy'
    })

    $('.datepicker').datepicker({
      	autoclose: true,
    	format: 'dd-mm-yyyy'
    })
    //Timepicker
    $('.timepicker').timepicker({
      showInputs: false
    })
});
</script>
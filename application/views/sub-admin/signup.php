<body class="hold-transition login-page">

<div class="login-box">

  <div class="login-logo">

    <h1>QRSP Sub Admin</h1>

   <!-- <img alt="Logo" style="width: 150px;height: 150px;" src="<?php echo base_url() ?>/images/logo.png" border="0" class="logo"> -->

  </div>



  <!-- /.login-logo -->

  <div class="login-box-body">

    <p class="login-box-msg">Sub Admin Signup </p>

    <form action="<?php echo base_url()?>SubAdmin/save" method="post" enctype="multipart/form-data">
        
        <div class="form-group has-feedback">

        <input type="text" name="s_name" class="form-control" placeholder="Name">

        <span class="glyphicon glyphicon-user form-control-feedback"></span>
        <?php echo form_error('s_name', '<div class="error text-danger">', '</div>'); ?>

      </div>

      <div class="form-group has-feedback">

        <input type="text" name="s_email" class="form-control" placeholder="Email">

        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        <?php echo form_error('s_email', '<div class="error text-danger">', '</div>'); ?>

      </div>

      <div class="form-group has-feedback">

        <input type="password" name="s_password" class="form-control" placeholder="Password">

        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        <?php echo form_error('s_password', '<div class="error text-danger">', '</div>'); ?>

      </div>
      
      <div class="form-group has-feedback">

        <input type="file" name="s_image" class="form-control-file">

      </div>

      <div class="row">

        

        <div class="col-xs-12">

          <button type="submit" name="submit" class="btn btn-primary btn-flat">Signup</button>

        </div>
        <div class="col-xs-12">

          <p>Already have an account? <a href="<?php echo base_url()?>SubAdmin">Login</a></p>

        </div>

       

      </div>

    </form>



   

  </div>

</div>

<script>

  $(function () {

    $('input').iCheck({

      checkboxClass: 'icheckbox_square-blue',

      radioClass: 'iradio_square-blue',

      increaseArea: '20%' /* optional */

    });

  });

</script>

</body>

</html>










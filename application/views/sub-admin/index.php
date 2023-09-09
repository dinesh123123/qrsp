<body class="hold-transition login-page">

<div class="login-box">

  <div class="login-logo">

    <h1>QRSP Sub Admin</h1>

   <!-- <img alt="Logo" style="width: 150px;height: 150px;" src="<?php echo base_url() ?>/images/logo.png" border="0" class="logo"> -->

  </div>



  <!-- /.login-logo -->

  <div class="login-box-body">

    <p class="login-box-msg">Sub Admin Login </p>

    <?php echo isset($error) ? $error : ''; ?>



    <form action="<?php echo base_url()?>SubAdmin/login" method="post">

      <div class="form-group has-feedback">

        <input type="text" name="s_email" class="form-control" placeholder="Email">

        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

      </div>

      <div class="form-group has-feedback">

        <input type="password" name="s_password" class="form-control" placeholder="Password">

        <span class="glyphicon glyphicon-lock form-control-feedback"></span>

      </div>

      <div class="row">

        

        <div class="col-xs-12">

          <button type="submit" class="btn btn-primary btn-flat">Login</button>

        </div>
        <div class="col-xs-12">

          <!--<p>Don't have an account? <a href="<?php echo base_url()?>SubAdmin/signup">Signup</a></p>-->

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










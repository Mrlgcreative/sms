<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St JEAN-HENRY | Log in</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    /* Custom Login Page Styles */
body.login-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-box {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    width: 380px; /* Slightly wider for a modern feel */
}

.login-logo a {
    color: #4A5568; /* Darker, more modern text color */
    font-weight: 600;
    font-size: 28px;
}

.login-box-body {
    padding: 30px;
    border-radius: 0 0 10px 10px;
}

.login-box-msg {
    margin-bottom: 25px;
    font-size: 16px;
    color: #718096; /* Softer message color */
}

.form-control {
    border-radius: 5px;
    box-shadow: none;
    border-color: #E2E8F0;
    padding: 10px 15px;
    height: auto; /* Adjust height based on padding */
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn.btn-primary.btn-flat {
    background-color: #667eea;
    border-color: #667eea;
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn.btn-primary.btn-flat:hover, .btn.btn-primary.btn-flat:focus {
    background-color: #5a67d8;
    border-color: #5a67d8;
}

.btn.btn-default.btn-flat {
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 15px;
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

.btn.btn-default.btn-flat:hover, .btn.btn-default.btn-flat:focus {
    background-color: #f0f2f5;
    border-color: #d1d5db;
    color: #4A5568;
}

/* iCheck adjustments if needed - ensure they don't conflict too much */
.icheckbox_square-blue {
    /* You might want to customize iCheck or consider replacing it if it clashes heavily */
}

.alert-danger {
    border-radius: 5px;
}

.social-auth-links p {
    color: #718096;
}
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>SYSTEME SCOLAIRE</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Connectez-vous pour démarrer votre session</p>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=login" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group has-feedback">
        
        <input type="text" class="form-control" id="username" name="username"  placeholder="Nom utilisateur"required>
        </div>
        
       <div class="form-group has-feedback">

        <input type="password" class="form-control" id="password" placeholder="Mot de passe" name="password" required>

  </div>
        
        <button  class="btn btn-primary btn-block btn-flat"type="submit">Se connecter</button>
    </form>

    <!-- Add this registration link -->
    <div class="social-auth-links text-center">
        <p>- OU -</p>
        <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=register" class="btn btn-block btn-default btn-flat">
            <i class="fa fa-user-plus"></i> Créer un nouveau compte
        </a>
    </div>
    <!-- /.social-auth-links -->

  

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="plugins/iCheck/icheck.min.js"></script>
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



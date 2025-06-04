<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">  <title>Établissement Scolaire | Connexion</title>
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
  <style>    /* Custom Login Page Styles */
body.login-page {
    background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 50%, #1e40af 100%);
    background-image: 
        url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'),
        radial-gradient(circle at 20% 50%, rgba(30, 64, 175, 0.8) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(30, 64, 175, 0.6) 0%, transparent 50%);
    background-size: cover, auto, auto;
    background-position: center, center, center;
    background-blend-mode: overlay;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

body.login-page::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
    background-size: cover;
    background-position: center;
    filter: blur(3px);
    z-index: -1;
}

body.login-page::after {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(44, 90, 160, 0.85) 0%, rgba(30, 58, 138, 0.85) 50%, rgba(30, 64, 175, 0.85) 100%);
    z-index: -1;
}

.login-box {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3), 0 5px 15px rgba(0, 0, 0, 0.2);
    width: 400px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    position: relative;
    z-index: 1;
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
    border-color: #1e40af;
    box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
}

.btn.btn-primary.btn-flat {
    background-color: #1e40af;
    border-color: #1e40af;
    border-radius: 5px;
    padding: 10px 15px;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s ease, border-color 0.3s ease;
}

.btn.btn-primary.btn-flat:hover, .btn.btn-primary.btn-flat:focus {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
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
<div class="login-box">  <div class="login-logo">
    <a href="#"><i class="fa fa-graduation-cap"></i> <b>ÉTABLISSEMENT SCOLAIRE</b></a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Accès au système de gestion scolaire</p>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=login" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group has-feedback">
        
        <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required>
        </div>
        
       <div class="form-group has-feedback">

        <input type="password" class="form-control" id="password" placeholder="Mot de passe" name="password" required>

  </div>
        
        <button class="btn btn-primary btn-block btn-flat" type="submit">Se connecter</button>
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



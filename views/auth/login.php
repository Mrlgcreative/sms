<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Connexion</title>
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
    .login-page {
      /* Nouveau dégradé plus subtil et professionnel */
      background: linear-gradient(135deg, #607D8B 0%, #455A64 100%);
      /* Vous pouvez décommenter la ligne suivante pour ajouter une image de fond (assurez-vous que le chemin est correct) */
      /* background-image: url('dist/img/login-bg.jpg'); */
      background-size: cover;
      background-position: center;
    }
    .login-box {
      margin-top: 5%;
      background-color: rgba(255, 255, 255, 0.95); /* Légère transparence pour intégrer l'arrière-plan */
      border-radius: 8px; /* Coins plus arrondis */
      box-shadow: 0 15px 30px rgba(0,0,0,0.2), 0 10px 10px rgba(0,0,0,0.15);
    }
    box-shadow: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
    }
    .login-logo {
      margin-bottom: 0;
      padding: 20px;
      background-color: #fff;
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }
    .login-logo a {
      color: #37474F; /* Couleur de texte plus foncée pour le logo */
      text-shadow: none; /* Suppression de l'ombre du texte pour un look plus épuré */
    }
    .login-box-body {
      border-bottom-left-radius: 5px;
      border-bottom-right-radius: 5px;
      padding: 30px;
    }
    .login-box-msg {
      font-size: 18px;
      margin-bottom: 20px;
      color: #555;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-control {
      height: 42px;
      border-radius: 4px;
    }
    .form-group.has-feedback .form-control {
      padding-left: 42px;
    }
    .form-control-feedback {
      width: 42px;
      height: 42px;
      line-height: 42px;
      color: #777;
    }
    .btn-primary {
      background-color: #3c8dbc;
      border-color: #367fa9;
      padding: 10px 16px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #367fa9;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .btn-default {
      transition: all 0.3s ease;
    }
    .btn-default:hover {
      background-color: #f5f5f5;
      transform: translateY(-2px);
    }
    .social-auth-links {
      margin-top: 25px;
      padding-top: 15px;
      border-top: 1px solid #eee;
    }
    .alert {
      border-radius: 4px;
    }
    .school-logo {
      max-width: 80px;
      margin-right: 10px;
      vertical-align: middle;
    }
    .remember-me {
      margin-top: 10px;
      margin-bottom: 15px;
    }
    .forgot-password {
      margin-top: 15px;
      text-align: center;
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="#"><img src="dist/img/school-logo.png" alt="Logo" class="school-logo"><b>ST SOPHIE</b> | Système Scolaire</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    <p class="login-box-msg">Connectez-vous pour démarrer votre session</p>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=login" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-group has-feedback">
            <span class="fa fa-user form-control-feedback"></span>
            <input type="text" class="form-control" id="username" name="username" placeholder="Nom d'utilisateur" required autofocus>
        </div>
        
        <div class="form-group has-feedback">
            <span class="fa fa-lock form-control-feedback"></span>
            <input type="password" class="form-control" id="password" placeholder="Mot de passe" name="password" required>
        </div>

        <div class="remember-me">
            <div class="checkbox icheck">
                <label>
                    <input type="checkbox" name="remember"> Se souvenir de moi
                </label>
            </div>
        </div>
        
        <button class="btn btn-primary btn-block btn-flat" type="submit">
            <i class="fa fa-sign-in"></i> Se connecter
        </button>
    </form>

    <!-- Add this registration link -->
    <div class="social-auth-links text-center">
        <p>- OU -</p>
        <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=register" class="btn btn-block btn-default btn-flat">
            <i class="fa fa-user-plus"></i> Créer un nouveau compte
        </a>
    </div>
    <!-- /.social-auth-links -->

    <div class="forgot-password">
        <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=forgot_password">Mot de passe oublié?</a>
    </div>

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
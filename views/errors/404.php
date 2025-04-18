<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Page non trouvée</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .error-page {
      margin-top: 50px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .headline {
      font-size: 100px;
      font-weight: 300;
      margin-bottom: 10px;
      color: #f39c12;
      text-shadow: 2px 2px 3px rgba(0,0,0,0.1);
      order: 2;
    }
    .error-content {
      margin-left: 0;
      order: 3;
    }
    .error-content h3 {
      font-weight: 300;
      font-size: 28px;
      margin-bottom: 20px;
      color: #444;
    }
    .error-content p {
      font-size: 16px;
      line-height: 1.6;
      margin-bottom: 30px;
    }
    .back-home {
      display: inline-block;
      padding: 10px 20px;
      background-color: #9c27b0;
      color: #fff;
      border-radius: 4px;
      text-decoration: none;
      font-weight: 600;
      transition: background-color 0.3s;
      margin-top: 20px;
    }
    .back-home:hover {
      background-color: #7b1fa2;
      color: #fff;
      text-decoration: none;
    }
    .error-bg {
      position: relative;
      margin-bottom: 20px;
      order: 1;
      max-width: 250px;
    }
    .error-bg img {
      max-width: 100%;
      height: auto;
    }
    .main-footer {
      margin-top: 50px;
      text-align: center;
      background-color: #f5f5f5;
      padding: 15px;
      color: #666;
    }
    .animated {
      animation-duration: 1s;
      animation-fill-mode: both;
    }
    @keyframes bounce {
      0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
      40% {transform: translateY(-30px);}
      60% {transform: translateY(-15px);}
    }
    .bounce {
      animation-name: bounce;
    }
  </style>
</head>
<body class="hold-transition skin-purple">
<div class="wrapper">
  <div class="content-wrapper" style="margin-left: 0; background-color: #f9f9f9;">
    <section class="content">
      <div class="error-page">
        <div class="error-bg">
          <img src="<?php echo BASE_URL; ?>dist/img/404.png" alt="404" onerror="this.src='https://cdn-icons-png.flaticon.com/512/755/755014.png'; this.onerror='';">
        </div>
        <h2 class="headline text-yellow animated bounce">404</h2>

        <div class="error-content">
          <h3><i class="fa fa-warning text-yellow"></i> Oops! Page non trouvée.</h3>
          <p>
            Nous n'avons pas pu trouver la page que vous cherchiez.<br>
            Il est possible que la page ait été déplacée ou supprimée.
          </p>
          <a href="<?php echo BASE_URL; ?>index.php" class="back-home">
            <i class="fa fa-home"></i> Retourner à l'accueil
          </a>
        </div>
      </div>
    </section>
  </div>
  <footer class="main-footer" style="margin-left: 0;">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
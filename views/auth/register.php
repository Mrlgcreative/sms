
<?php
require_once 'config/config.php';
require_once 'models/UserModel.php';
require_once 'includes/security.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $message = "Erreur de sécurité. Veuillez réessayer.";
        $messageType = 'error';
    } else {
        $username = cleanInput($_POST['username']);
        $name = cleanInput($_POST['name']);
        $email = cleanInput($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = cleanInput($_POST['role']);

        $userModel = new UserModel();
        $result = $userModel->register($username, $name, $email, $password, $role);

        if ($result) {
            $message = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
            $messageType = 'success';
        } else {
            $message = 'Erreur lors de l\'inscription. Veuillez réessayer.';
            $messageType = 'error';
        }
    }
}

// Generate CSRF token
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Inscription</title>
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
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="#"><b>SYSTEME SCOLAIRE</b></a>
  </div>

  <div class="register-box-body">
    <p class="login-box-msg">Créer un nouveau compte</p>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=register" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Nom d'utilisateur" name="username" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Nom complet" name="name" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Mot de passe" name="password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <select class="form-control" name="role" required>
          <option value="">-- Sélectionnez un rôle --</option>
          <option value="admin">Administrateur</option>
          <option value="comptable">Comptable</option>
          <option value="directeur">Directeur</option>
          <option value="directrice">Directrice</option>
          <option value="prefet">Préfet</option>
          <option value="enseignant">Enseignant</option>
        </select>
      </div>
      
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" required> J'accepte les <a href="#">conditions</a>
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">S'inscrire</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=login" class="text-center">J'ai déjà un compte</a>
  </div>
  <!-- /.form-box -->
</div>
<!-- /.register-box -->

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

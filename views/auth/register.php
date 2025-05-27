<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Define BASE_URL if not already defined (ensure this path is correct for your setup)
if (!defined('BASE_URL')) {
    // Adjust the path according to your project structure relative to the web root
    $script_name = $_SERVER['SCRIPT_NAME'];
    // Correctly determine the base path, considering if index.php is in a subdirectory or root
    $base_path = dirname($script_name);
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = ''; // If script is in root, base_path is empty
    }
    define('BASE_URL', rtrim($base_path, '/\\') . '/');
}
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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>plugins/iCheck/square/blue.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    /* Custom Login Page Styles (Copied from login.php) */
    body.login-page {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        height: auto; /* Adjusted for potentially longer registration form */
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 0; /* Add some padding for scrollable content */
    }

    .login-box {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        width: 1120px; /* Slightly wider for registration form */
        margin-top: 20px; /* Ensure space from top */
        margin-bottom: 20px; /* Ensure space from bottom */
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
        margin-bottom: 15px; /* Add margin between form controls */
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-group.has-feedback {
        margin-bottom: 15px; /* Ensure spacing for feedback icons if used */
    }
    
    .form-group.has-feedback .form-control-feedback {
        /* Adjust if icon alignment is off with new padding */
        /* top: 0; Default AdminLTE might be fine */
         height: auto; /* Let it adjust based on input height */
         line-height: normal; /* Let it adjust based on input height */
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

    .btn.btn-primary.btn-flat:hover,
    .btn.btn-primary.btn-flat:focus {
        background-color: #5a67d8;
        border-color: #5a67d8;
    }

    .btn.btn-default.btn-flat {
        border-radius: 5px;
        padding: 10px 15px;
        font-size: 15px;
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    .btn.btn-default.btn-flat:hover,
    .btn.btn-default.btn-flat:focus {
        background-color: #f0f2f5;
        border-color: #d1d5db;
        color: #4A5568;
    }

    .icheckbox_square-blue {
        /* Customizations if needed */
    }

    .alert-danger {
        border-radius: 5px;
        font-size: 0.9em;
        margin-bottom: 15px;
    }
    .alert-danger ul {
        margin-bottom: 0;
        padding-left: 0; /* Remove default padding for ul */
        list-style-type: none;
    }
    .alert-success {
        border-radius: 5px;
        font-size: 0.9em;
        margin-bottom: 15px;
    }

    .social-auth-links p {
        color: #718096;
    }
    .form-group label {
        font-weight: 600;
        color: #4A5568;
        margin-bottom: 5px;
        display: block; /* Ensure label takes full width */
    }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="<?php echo BASE_URL; ?>"><b>SGS</b> - Inscription</a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">Créer un nouveau compte</p>

    <?php if (isset($errors) && !empty($errors)): ?>
      <div class="alert alert-danger">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo $error; ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=register" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      
 <div class="col-md-6">
 <div class="form-group has-feedback">
        <input type="text" class="form-control" placeholder="Nom d'utilisateur" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="email" class="form-control" placeholder="Email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Mot de passe" name="password" required>
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      
      <div class="form-group has-feedback">
        <input type="password" class="form-control" placeholder="Confirmer le mot de passe" name="confirm_password" required>
        <span class="glyphicon glyphicon-log-in form-control-feedback"></span>
      </div>
 </div>
      
<div class="col-md-6">
<div class="form-group">
        <label for="role">Rôle</label>
        <select class="form-control" name="role" id="role">
          <option value="admin">Administrateur</option>
          <option value="comptable">Comptable</option>
          <option value="prefet">Prefet</option>
          <option value="director">Directeur</option>
          <option value="directrice">Directrice</option>
          <option value="percepteur">Pecepteur</option>
          <option value="directeur_Etude">Drecteur Etude</option>

          </option>
        </select>
      </div>
      
      <div class="form-group has-feedback">
        <input type="tel" class="form-control" placeholder="Téléphone" name="telephone" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
        <span class="glyphicon glyphicon-phone form-control-feedback"></span>
      </div>
      
      <div class="form-group">
        <textarea class="form-control" placeholder="Adresse" name="adresse" rows="2"><?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?></textarea>
      </div>
      
      <div class="form-group">
        <label for="image">Photo de profil</label>
        <input type="file" id="image" name="image" accept="image/*">
        <p class="help-block">Formats acceptés: JPG, PNG, GIF. Max: 2MB</p>
      </div>
</div>
      
      <div class="row">
        <div class="col-xs-8">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=login" class="text-center">J'ai déjà un compte</a>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">S'inscrire</button>
        </div>
      </div>
    </form>
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="<?php echo BASE_URL; ?>plugins/iCheck/icheck.min.js"></script>
<script>
  $(function () {
    // We are not using iCheck for text inputs, selects, or textareas in this form.
    // If you had checkboxes/radios and wanted to style them with iCheck:
    /*
    $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
      checkboxClass: 'icheckbox_minimal-blue',
      radioClass: 'iradio_minimal-blue'
    });
    */
  });
</script>
<?php 
  // Clear form data after successful rendering if it was set
  if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']); 
?>
</body>
</html>
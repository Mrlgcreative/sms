
<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Inscription</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="<?php echo BASE_URL; ?>"><b>SGS</b> - Inscription</a>
  </div>

  <div class="register-box-body">
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
      
      <div class="form-group">
        <label for="role">Rôle</label>
        <select class="form-control" name="role" id="role">
          <option value="admin">Administrateur</option>
          <option value="comptable">Comptable</option>
          <option value="prefet">Prefet</option>
          <option value="director">Directeur</option>
          <option value="directrice">Directrice</option>
          <option value="enseignant">Enseignant</option>
          <option value="etudiant">Etudiant</option>

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

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>

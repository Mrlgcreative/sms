<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
    exit;
}

// Get user info
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Changement de mot de passe</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Main Header -->
  <?php include 'views/partials/header.php'; ?>
  
  <!-- Left side column. contains the logo and sidebar -->
  <?php include 'views/partials/sidebar.php'; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Changement de mot de passe
        <small>Sécurité du compte</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Changement de mot de passe</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Modifier votre mot de passe</h3>
            </div>
            
            <?php if (isset($_SESSION['password_change_required'])): ?>
              <div class="alert alert-warning">
                <h4><i class="icon fa fa-warning"></i> Attention!</h4>
                Vous devez changer votre mot de passe pour continuer.
              </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
                <ul>
                  <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
              <div class="alert alert-success">
                <h4><i class="icon fa fa-check"></i> Succès!</h4>
                Votre mot de passe a été modifié avec succès. Vous allez être redirigé...
              </div>
            <?php endif; ?>
            
            <form method="post" action="<?php echo BASE_URL; ?>index.php?controller=Auth&action=changePassword">
              <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
              
              <div class="box-body">
                <div class="form-group">
                  <label for="current_password">Mot de passe actuel</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Entrez votre mot de passe actuel" required>
                </div>
                
                <div class="form-group">
                  <label for="new_password">Nouveau mot de passe</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Entrez votre nouveau mot de passe" required>
                </div>
                
                <div class="form-group">
                  <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirmez votre nouveau mot de passe" required>
                </div>
              </div>
              
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                
                <?php if (!isset($_SESSION['password_change_required'])): ?>
                  <a href="<?php echo BASE_URL; ?>" class="btn btn-default">Annuler</a>
                <?php endif; ?>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <?php include 'views/partials/footer.php'; ?>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
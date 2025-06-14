<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter Frais</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .form-container {
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 20px;
    }
    .btn-submit {
      background-color: #3c8dbc;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    .btn-submit:hover {
      background-color: #367fa9;
    }
    select.form-control {
      height: 34px;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Ajouter des Frais
        <small>Gestion des frais scolaires</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais">Frais</a></li>
        <li class="active">Ajouter Frais</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'ajout de frais</h3>
            </div>
            
            <?php if(isset($_SESSION['message'])): ?>
              <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check' : 'ban'; ?>"></i> Alerte!</h4>
                <?php echo $_SESSION['message']; ?>
              </div>
              <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <form action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutFrais" method="POST" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="montant" class="col-sm-3 control-label">Montant</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                      <input type="number" class="form-control" id="montant" name="montant" placeholder="Entrez le montant" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="description" class="col-sm-3 control-label">Description</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-file-text-o"></i></span>
                      <input type="text" class="form-control" id="description" name="description" placeholder="Description des frais" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="section" class="col-sm-3 control-label">Section</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-building"></i></span>
                      <select class="form-control" id="section" name="section" required>
                        <option value="">Sélectionnez une section</option>
                        <option value="maternelle">Maternelle</option>
                        <option value="primaire">Primaire</option>
                        <option value="secondaire">Secondaire</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais" class="btn btn-default">Annuler</a>
                <button type="submit" class="btn btn-primary pull-right">Ajouter</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo BASE_URL; ?>dist/js/demo.js"></script>
<script>
  $(document).ready(function() {
    // Validation du formulaire
    $('form').submit(function(e) {
      var montant = $('#montant').val();
      var description = $('#description').val();
      var section = $('#section').val();
      
      if (montant <= 0) {
        alert('Le montant doit être supérieur à 0');
        e.preventDefault();
        return false;
      }
      
      if (description.trim() === '') {
        alert('Veuillez entrer une description');
        e.preventDefault();
        return false;
      }
      
      if (section === '') {
        alert('Veuillez sélectionner une section');
        e.preventDefault();
        return false;
      }
      
      return true;
    });
  });
</script>
</body>
</html>
<?php
// Vue pour l'ajout d'un événement scolaire
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter un événement</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Ajouter un événement scolaire
        <small>Créer un nouvel événement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">Événements Scolaires</a></li>
        <li class="active">Ajouter un événement</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations de l'événement</h3>
            </div>
            
            <?php if(isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
              <?php echo isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Une erreur est survenue lors de l\'ajout de l\'événement.'; ?>
            </div>
            <?php endif; ?>
            
            <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajouterEvenement">
              <div class="box-body">
                <div class="form-group">
                  <label for="titre">Titre de l'événement *</label>
                  <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre de l'événement" required>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="date_debut">Date et heure de début *</label>
                      <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="date_fin">Date et heure de fin *</label>
                      <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" required>
                    </div>
                  </div>
                </div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="lieu">Lieu *</label>
                      <input type="text" class="form-control" id="lieu" name="lieu" placeholder="Lieu de l'événement" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="responsable">Responsable *</label>
                      <input type="text" class="form-control" id="responsable" name="responsable" placeholder="Personne responsable" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" id="description" name="description" rows="4" placeholder="Description détaillée de l'événement"></textarea>
                </div>
                
                <div class="form-group">
                  <label>Couleur de l'événement</label>
                  <div class="input-group my-colorpicker2">
                    <input type="text" class="form-control" name="couleur" value="#3c8dbc">
                    <div class="input-group-addon">
                      <i></i>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- bootstrap color picker -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    //Colorpicker
    $('.my-colorpicker2').colorpicker()
    
    // Validation de la date de fin après la date de début
    $('#date_debut, #date_fin').on('change', function() {
      var dateDebut = $('#date_debut').val();
      var dateFin = $('#date_fin').val();
      
      if(dateDebut && dateFin && new Date(dateFin) < new Date(dateDebut)) {
        alert('La date de fin doit être postérieure à la date de début');
        $('#date_fin').val('');
      }
    });
  })
</script>
</body>
</html>
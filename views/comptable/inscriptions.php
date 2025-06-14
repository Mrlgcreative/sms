<?php
// Vérifiez si une session est déjà active avant d'appeler session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les clés si elles ne sont pas déjà définies
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = 'username';
}
if (!isset($_SESSION['email'])) {
  $_SESSION['email'] = 'email';
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = 'role';
}

// Récupérer les valeurs des clés
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Déterminer quelle section afficher
$section = isset($_GET['section']) ? $_GET['section'] : '';

// Récupérer le message d'erreur s'il existe
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
if (isset($_SESSION['error'])) {
  unset($_SESSION['error']);
}

// Récupérer le message de succès s'il existe
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
if (isset($_SESSION['success'])) {
  unset($_SESSION['success']);
}
// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Inscription</title>
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
 
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard-admin.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Inscription des élèves
        <?php if ($section): ?>
          <small>Section <?php echo ucfirst($section); ?></small>
        <?php else: ?>
          <small>Choisissez une section</small>
        <?php endif; ?>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Inscription</li>
        <?php if ($section): ?>
          <li class="active"><?php echo ucfirst($section); ?></li>
        <?php endif; ?>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!$section): ?>
      <!-- Affichage des boutons de section -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sélectionnez la section pour l'inscription</h3>
            </div>
            <div class="box-body text-center">
              <div class="row">
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=maternelle" class="btn btn-lg btn-block section-button maternelle">
                    <i class="fa fa-child"></i> Maternelle
                  </a>
                </div>
                <div class="col-md-4">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=primaire" class="btn btn-lg btn-block section-button primaire">
                    <i class="fa fa-book"></i> Primaire
                  </a>
                </div>
                <div class="col-md-3">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions&section=secondaire" class="btn btn-lg btn-block section-button secondaire">
                    <i class="fa fa-graduation-cap"></i> Secondaire
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php elseif ($section == 'maternelle'): ?>
      <!-- Formulaire d'inscription Maternelle -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Maternelle</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve" enctype="multipart/form-data">
              <input type="hidden" name="section" value="maternelle">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                     <div class="form-group">
                      <label for="sexe">Sexe</label>
                      <select class="form-control" name="sexe" id="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="type_inscription">Type d'inscription</label>
                      <select class="form-control" name="type_inscription" id="type_inscription" required>
                        <option value="Nouvelle inscription" selected>Nouvelle inscription</option>
                       
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo">Photo de l'élève</label>
                      <input type="file" id="photo" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="classe_id">Classe</label>
                      <select class="form-control" id="classe_id" name="classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <?php if ($classe['section'] == 'maternelle') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                   
                     <div class="form-group">
                      <label for="professions">Profession</label>
                      <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession" required>
                    </div>

                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'primaire'): ?>
      <!-- Formulaire d'inscription Primaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Primaire</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve" enctype="multipart/form-data">
              <input type="hidden" name="section" value="primaire">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule-primaire"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                     <div class="form-group">
                      <label for="sexe">Sexe</label>
                      <select class="form-control" name="sexe" id="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="type_inscription">Type d'inscription</label>
                      <select class="form-control" name="type_inscription" id="type_inscription" required>
                        <option value="Nouvelle inscription" selected>Nouvelle inscription</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo-primaire">Photo de l'élève</label>
                      <input type="file" id="photo-primaire" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-primaire" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="classe_id">Classe</label>
                      <select class="form-control" id="classe_id" name="classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <?php if ($classe['section'] == 'primaire') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                   
                     <div class="form-group">
                      <label for="professions">Profession</label>
                      <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php elseif ($section == 'secondaire'): ?>
      <!-- Formulaire d'inscription Secondaire -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'inscription - Section Secondaire</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=enregistrerEleve" enctype="multipart/form-data">
              <input type="hidden" name="section" value="secondaire">
              <input type="hidden" name="session_scolaire_id" value="<?php echo isset($sessions_scolaires[0]['id']) ? $sessions_scolaires[0]['id'] : ''; ?>">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="matricule">Matricule</label>
                      <div class="input-group">
                        <input type="text" class="form-control" id="matricule" name="matricule" value="<?php echo 'SGS-'.date('Y').'-'.str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT); ?>" readonly>
                        <span class="input-group-btn">
                          <button type="button" class="btn btn-default" id="regenerate-matricule-secondaire"><i class="fa fa-refresh"></i></button>
                        </span>
                      </div>
                      <small class="text-muted">Matricule généré automatiquement</small>
                    </div>
                    <div class="form-group">
                      <label for="nom">Nom</label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom" required>
                    </div>
                    <div class="form-group">
                      <label for="post_nom">Post-nom</label>
                      <input type="text" class="form-control" id="post_nom" name="post_nom" placeholder="Post-nom" required>
                    </div>
                    <div class="form-group">
                      <label for="prenom">Prénom</label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom" required>
                    </div>
                    <div class="form-group">
                      <label for="date_naissance">Date de naissance</label>
                      <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="lieu_naissance">Lieu de naissance</label>
                      <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" placeholder="Lieu de naissance" required>
                    </div>
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse" required>
                    </div>
                    <div class="form-group">
                      <label for="type_inscription">Type d'inscription</label>
                      <select class="form-control" name="type_inscription" id="type_inscription" required>
                        <option value="Nouvelle inscription" selected>Nouvelle inscription</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="sexe">Sexe</label>
                      <select class="form-control" name="sexe" id="sexe" required>
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="photo-secondaire">Photo de l'élève</label>
                      <input type="file" id="photo-secondaire" name="photo" accept="image/*">
                      <p class="help-block">Format recommandé: JPG, PNG. Taille max: 2MB</p>
                      <div class="photo-preview" style="margin-top: 10px; max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 3px;">
                        <img id="photo-preview-secondaire" src="dist/img/default-student.png" alt="Aperçu de la photo" style="width: 100%; height: auto;">
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="classe_id">Classe</label>
                      <select class="form-control" id="classe_id" name="classe_id" required>
                        <option value="">-- Sélectionner une classe --</option>
                        <?php foreach ($classes as $classe) : ?>
                          <?php if ($classe['section'] == 'secondaire') : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['niveau']; ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="option_id">Option</label>
                      <select class="form-control" id="option_id" name="option_id" required>
                        <option value="">-- Sélectionner une option --</option>
                        <?php foreach ($options as $option) : ?>
                          <option value="<?php echo $option['id']; ?>"><?php echo $option['nom']; ?></option>
                        <?php endforeach; ?>
                      </select>
                   

                     <div class="form-group">
                      <label for="professions">Profession</label>
                      <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_pere">Nom du père</label>
                      <input type="text" class="form-control" id="nom_pere" name="nom_pere" placeholder="Nom du père" required>
                    </div>
                    <div class="form-group">
                      <label for="nom_mere">Nom de la mère</label>
                      <input type="text" class="form-control" id="nom_mere" name="nom_mere" placeholder="Nom de la mère" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_pere">Contact du père</label>
                      <input type="text" class="form-control" id="contact_pere" name="contact_pere" placeholder="Contact du père" required>
                    </div>
                    <div class="form-group">
                      <label for="contact_mere">Contact de la mère</label>
                      <input type="text" class="form-control" id="contact_mere" name="contact_mere" placeholder="Contact de la mère" required>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-warning">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
      </div>
      <?php endif; ?>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#">St Sofie</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function() {
    // Photo preview pour maternelle
    $("#photo").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Photo preview pour primaire
    $("#photo-primaire").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview-primaire').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Photo preview pour secondaire
    $("#photo-secondaire").change(function() {
      if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#photo-preview-secondaire').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Regenerate matricule pour maternelle
    $("#regenerate-matricule").click(function() {
      var year = new Date().getFullYear();
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val('SGS-' + year + '-' + paddedRandom);
    });
    
    // Regenerate matricule pour primaire
    $("#regenerate-matricule-primaire").click(function() {
      var year = new Date().getFullYear();
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val('SGS-' + year + '-' + paddedRandom);
    });
    
    // Regenerate matricule pour secondaire
    $("#regenerate-matricule-secondaire").click(function() {
      var random = Math.floor(Math.random() * 9999) + 1;
      var paddedRandom = random.toString().padStart(4, '0');
      $("#matricule").val(paddedRandom);
    });
    
    // Afficher les notifications toast
    function showToast(message, type) {
      var toast = $('<div class="toast toast-' + type + '"><span class="toast-close">&times;</span>' + message + '</div>');
      $('#toast-container').append(toast);
      
      // Fermer le toast au clic sur le bouton de fermeture
      toast.find('.toast-close').on('click', function() {
        toast.remove();
      });
      
      // Supprimer automatiquement le toast après 5 secondes
      setTimeout(function() {
        toast.remove();
      }, 5000);
    }
    
    // Afficher les messages d'erreur ou de succès sous forme de toast
    <?php if ($error_message): ?>
      showToast('<?php echo $error_message; ?>', 'error');
    <?php endif; ?>
    
    <?php if ($success_message): ?>
      showToast('<?php echo $success_message; ?>', 'success');
    <?php endif; ?>
  });
</script>
</body>
</html>
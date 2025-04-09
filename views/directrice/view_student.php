<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';

// Vérifier si l'élève existe
if (!isset($eleve) || empty($eleve)) {
    header('Location: ' . BASE_URL . 'index.php?controller=directrice&action=eleves&error=1&message=' . urlencode('Élève non trouvé!'));
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Détails de l'élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">
            <i class="fa fa-users"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes">
            <i class="fa fa-graduation-cap"></i> <span>Classes</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques">
            <i class="fa fa-bar-chart"></i> <span>Statistiques</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil">
            <i class="fa fa-user"></i> <span>Profil</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Détails de l'élève
        <small><?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">Élèves Maternelle</a></li>
        <li class="active">Détails de l'élève</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <!-- Profil de l'élève -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo !empty($eleve['photo']) ? $eleve['photo'] : 'dist/img/avatar5.png'; ?>" alt="Photo de l'élève">

              <h3 class="profile-username text-center"><?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?></h3>

              <p class="text-muted text-center">Élève de <?php echo $eleve['classe_nom']; ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Matricule</b> <a class="pull-right"><?php echo !empty($eleve['matricule']) ? $eleve['matricule'] : 'Non attribué'; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Sexe</b> <a class="pull-right"><?php echo $eleve['sexe'] == 'M' ? 'Garçon' : 'Fille'; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date de naissance</b> <a class="pull-right"><?php echo !empty($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : 'Non renseigné'; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Lieu de naissance</b> <a class="pull-right"><?php echo !empty($eleve['lieu_naissance']) ? $eleve['lieu_naissance'] : 'Non renseigné'; ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves" class="btn btn-primary btn-block"><b>Retour à la liste</b></a>
            </div>
          </div>
        </div>
        
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#details" data-toggle="tab">Informations détaillées</a></li>
              <li><a href="#parents" data-toggle="tab">Parents/Tuteurs</a></li>
              <li><a href="#documents" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="details">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="box box-solid">
                        <div class="box-header with-border">
                          <h3 class="box-title">Informations personnelles</h3>
                        </div>
                        <div class="box-body">
                          <strong><i class="fa fa-book margin-r-5"></i> Nom complet</strong>
                          <p class="text-muted">
                            <?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?>
                          </p>
                          <hr>
                          
                          <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
                          <p class="text-muted"><?php echo !empty($eleve['adresse']) ? $eleve['adresse'] : 'Non renseignée'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-calendar margin-r-5"></i> Date d'inscription</strong>
                          <p class="text-muted"><?php echo !empty($eleve['date_inscription']) ? date('d/m/Y', strtotime($eleve['date_inscription'])) : 'Non renseignée'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-phone margin-r-5"></i> Téléphone d'urgence</strong>
                          <p class="text-muted"><?php echo !empty($eleve['telephone_urgence']) ? $eleve['telephone_urgence'] : 'Non renseigné'; ?></p>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="box box-solid">
                        <div class="box-header with-border">
                          <h3 class="box-title">Informations scolaires</h3>
                        </div>
                        <div class="box-body">
                          <strong><i class="fa fa-graduation-cap margin-r-5"></i> Classe</strong>
                          <p class="text-muted"><?php echo $eleve['classe_nom']; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-building margin-r-5"></i> Section</strong>
                          <p class="text-muted">Maternelle</p>
                          <hr>
                          
                          <strong><i class="fa fa-book margin-r-5"></i> Année scolaire</strong>
                          <p class="text-muted">
                            <?php 
                            // Connexion à la base de données
                            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                            
                            if ($mysqli->connect_error) {
                                die("Erreur de connexion: " . $mysqli->connect_error);
                            }
                            
                            // Vérifier si la table sessions_scolaires existe
                            $tableCheck = $mysqli->query("SHOW TABLES LIKE 'sessions_scolaires'");
                            
                            if ($tableCheck && $tableCheck->num_rows > 0) {
                                // Vérifier si la colonne 'active' existe
                                $columnCheck = $mysqli->query("SHOW COLUMNS FROM sessions_scolaires LIKE 'active'");
                                
                                if ($columnCheck && $columnCheck->num_rows > 0) {
                                    // Récupérer la session scolaire active
                                    $result = $mysqli->query("SELECT * FROM sessions_scolaires WHERE active = 1");
                                    $session = $result->fetch_assoc();
                                    echo $session ? $session['annee_debut'] . '-' . $session['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
                                } else {
                                    // Si la colonne 'active' n'existe pas, prendre la session la plus récente
                                    $result = $mysqli->query("SELECT * FROM sessions_scolaires ORDER BY id DESC LIMIT 1");
                                    $session = $result->fetch_assoc();
                                    echo $session ? $session['annee_debut'] . '-' . $session['annee_fin'] : date('Y') . '-' . (date('Y') + 1);
                                }
                            } else {
                                // Si la table n'existe pas, afficher l'année scolaire par défaut
                                echo date('Y') . '-' . (date('Y') + 1);
                            }
                            
                            $mysqli->close();
                            ?>
                          </p>
                          <hr>
                          
                          <strong><i class="fa fa-comments margin-r-5"></i> Observations</strong>
                          <p class="text-muted"><?php echo !empty($eleve['observations']) ? $eleve['observations'] : 'Aucune observation'; ?></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="tab-pane" id="parents">
                <div class="box-body">
                  <?php
                  // Connexion à la base de données
                  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                  
                  if ($mysqli->connect_error) {
                      die("Erreur de connexion: " . $mysqli->connect_error);
                  }
                  
                  // Récupérer les informations des parents/tuteurs
                  $result_parents = $mysqli->query("SELECT * FROM parents WHERE eleve_id = " . $eleve['id'] . " LIMIT 2");
                  
                  if ($result_parents && $result_parents->num_rows > 0) {
                      while ($parent = $result_parents->fetch_assoc()) {
                  ?>
                  <div class="box box-solid">
                    <div class="box-header with-border">
                      <h3 class="box-title"><?php echo $parent['type_parent']; ?></h3>
                    </div>
                    <div class="box-body">
                      <div class="row">
                        <div class="col-md-6">
                          <strong><i class="fa fa-user margin-r-5"></i> Nom complet</strong>
                          <p class="text-muted"><?php echo $parent['nom'] . ' ' . (!empty($parent['prenom']) ? $parent['prenom'] : ''); ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-phone margin-r-5"></i> Téléphone</strong>
                          <p class="text-muted"><?php echo !empty($parent['telephone']) ? $parent['telephone'] : 'Non renseigné'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-mobile margin-r-5"></i> Téléphone mobile</strong>
                          <p class="text-muted"><?php echo !empty($parent['telephone_mobile']) ? $parent['telephone_mobile'] : 'Non renseigné'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
                          <p class="text-muted"><?php echo !empty($parent['adresse']) ? $parent['adresse'] : 'Non renseignée'; ?></p>
                        </div>
                        <div class="col-md-6">
                          <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
                          <p class="text-muted"><?php echo !empty($parent['email']) ? $parent['email'] : 'Non renseigné'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-briefcase margin-r-5"></i> Profession</strong>
                          <p class="text-muted"><?php echo !empty($parent['profession']) ? $parent['profession'] : 'Non renseignée'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-graduation-cap margin-r-5"></i> Formation</strong>
                          <p class="text-muted"><?php echo !empty($parent['formation']) ? $parent['formation'] : 'Non renseignée'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-building margin-r-5"></i> Lieu de travail</strong>
                          <p class="text-muted"><?php echo !empty($parent['lieu_travail']) ? $parent['lieu_travail'] : 'Non renseigné'; ?></p>
                          <hr>
                          
                          <strong><i class="fa fa-id-card margin-r-5"></i> Relation avec l'élève</strong>
                          <p class="text-muted"><?php echo !empty($parent['relation']) ? $parent['relation'] : $parent['type_parent']; ?></p>
                        </div>
                      </div>
                      <?php if (!empty($parent['observations'])): ?>
                      <hr>
                      <div class="row">
                        <div class="col-md-12">
                          <strong><i class="fa fa-comments margin-r-5"></i> Observations</strong>
                          <p class="text-muted"><?php echo $parent['observations']; ?></p>
                        </div>
                      </div>
                      <?php endif; ?>
                    </div>
                  </div>
                  <?php
                      }
                  } else {
                  ?>
                  <div class="alert alert-info">
                    <h4><i class="icon fa fa-info"></i> Information</h4>
                    Aucune information sur les parents ou tuteurs n'est disponible pour cet élève.
                  </div>
                  <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=addParent&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs">
                      <i class="fa fa-plus"></i> Ajouter un parent
                    </a>
                  </div>
                  <?php
                  }
                  
                  if ($result && $result->num_rows > 0) {
                  ?>
                  <div class="text-center" style="margin-top: 20px;">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=addParent&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-primary">
                      <i class="fa fa-plus"></i> Ajouter un autre parent/tuteur
                    </a>
                  </div>
                  <?php
                  }
                  
                  $mysqli->close();
                  ?>
                </div>
              </div>
              
              <div class="tab-pane" id="documents">
                <div class="box-body">
                  <?php
                  // Connexion à la base de données
                  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                  
                  if ($mysqli->connect_error) {
                      die("Erreur de connexion: " . $mysqli->connect_error);
                  }
                  
                  // Vérifier si la table documents existe
                  $result = $mysqli->query("SHOW TABLES LIKE 'documents'");
                  
                  if ($result && $result->num_rows > 0) {
                      // Récupérer les documents de l'élève
                      $result = $mysqli->query("SELECT * FROM documents WHERE eleve_id = " . $eleve['id']);
                      
                      if ($result && $result->num_rows > 0) {
                  ?>
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Type de document</th>
                        <th>Date d'ajout</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php while ($document = $result->fetch_assoc()) { ?>
                      <tr>
                        <td><?php echo $document['type_document']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($document['date_ajout'])); ?></td>
                        <td>
                          <?php if (!empty($document['chemin_fichier'])) { ?>
                          <a href="<?php echo $document['chemin_fichier']; ?>" class="btn btn-info btn-xs" target="_blank">
                            <i class="fa fa-eye"></i> Voir
                          </a>
                          <?php } else { ?>
                          <span class="label label-warning">Fichier non disponible</span>
                          <?php } ?>
                        </td>
                      </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                  <?php
                      } else {
                  ?>
                  <div class="alert alert-info">
                    <h4><i class="icon fa fa-info"></i> Information</h4>
                    Aucun document n'est disponible pour cet élève.
                  </div>
                  <?php
                      }
                  } else {
                  ?>
                  <div class="alert alert-warning">
                    <h4><i class="icon fa fa-warning"></i> Attention</h4>
                    Le système de gestion des documents n'est pas encore configuré.
                  </div>
                  <?php
                  }
                  
                  $mysqli->close();
                  ?>
                </div>
              </div>
            </div>
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
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
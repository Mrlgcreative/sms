<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Vérifier si les données du professeur sont disponibles
if (!isset($professeur) || empty($professeur)) {
    // Au lieu de sortir immédiatement, affichons un message d'erreur et continuons
    $error_message = "Aucune information disponible pour ce professeur.";
    // Créons un tableau vide pour éviter les erreurs plus loin
    $professeur = [
        'id' => 0,
        'nom' => 'Non disponible',
        'prenom' => '',
        'email' => 'Non disponible',
        'telephone' => 'Non disponible',
        'specialite' => 'Non disponible',
        'date_embauche' => date('Y-m-d'),
        'adresse' => 'Non disponible',
        'formation' => 'Non disponible',
        'notes' => 'Non disponible'
    ];
}

// Récupérer les cours du professeur si disponibles
$cours_professeur = isset($cours) ? $cours : [];
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Détails du Professeur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL . $image; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves Secondaire</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Détails du Professeur
        <small>Informations complètes</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs">Professeurs</a></li>
        <li class="active">Détails du Professeur</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($error_message)): ?>
      <div class="alert alert-danger">
        <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
        <?php echo $error_message; ?>
      </div>
      <?php endif; ?>
      
      <!-- Affichage des données de débogage si nécessaire -->
      <?php if (isset($_GET['debug']) && $_GET['debug'] == 1): ?>
      <div class="box box-danger">
        <div class="box-header with-border">
          <h3 class="box-title">Informations de débogage</h3>
        </div>
        <div class="box-body">
          <pre><?php print_r($professeur); ?></pre>
        </div>
      </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-4">
          <!-- Profil du professeur -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/teacher-avatar.png" alt="Photo du professeur">
              <h3 class="profile-username text-center"><?php echo htmlspecialchars($professeur['nom'] . ' ' . $professeur['prenom']); ?></h3>
              <p class="text-muted text-center">Professeur</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['email']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['telephone']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Spécialité</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['specialite']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date d'embauche</b> <a class="pull-right"><?php echo date('d/m/Y', strtotime($professeur['date_embauche'])); ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=modifierProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-primary btn-block"><b>Modifier</b></a>
            </div>
          </div>

          <!-- Informations supplémentaires -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations supplémentaires</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['adresse']); ?></p>
              <hr>

              <strong><i class="fa fa-graduation-cap margin-r-5"></i> Formation</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['formation']); ?></p>
              <hr>

              <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['notes']); ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <!-- Cours enseignés -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Cours enseignés</h3>
            </div>
            <div class="box-body">
              <?php if (!empty($cours_professeur)): ?>
                <div class="table-responsive">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Nom du cours</th>
                        <th>Classe</th>
                        <th>Horaire</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($cours_professeur as $cours): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($cours['titre']); ?></td>
                          <td><?php echo htmlspecialchars($cours['classe']); ?></td>
                          <td><?php echo htmlspecialchars($cours['horaire']); ?></td>
                          <td>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=voirCours&id=<?php echo $cours['id']; ?>" class="btn btn-xs btn-info">
                              <i class="fa fa-eye"></i> Voir
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              <?php else: ?>
                <div class="alert alert-info">
                  <i class="icon fa fa-info"></i> Ce professeur n'enseigne actuellement aucun cours.
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Emploi du temps -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Emploi du temps</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Heure</th>
                      <th>Lundi</th>
                      <th>Mardi</th>
                      <th>Mercredi</th>
                      <th>Jeudi</th>
                      <th>Vendredi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <!-- Ici, vous pouvez ajouter dynamiquement l'emploi du temps du professeur -->
                    <tr>
                      <td>08:00 - 09:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>09:00 - 10:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>10:00 - 11:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>11:00 - 12:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>12:00 - 13:00</td>
                      <td colspan="5" class="text-center">Pause déjeuner</td>
                    </tr>
                    <tr>
                      <td>13:00 - 14:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>14:00 - 15:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>15:00 - 16:00</td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                    </tr>
                  </tbody>
                </table>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
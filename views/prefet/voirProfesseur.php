<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérification de l'ID du professeur
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: " . BASE_URL . "index.php?controller=Prefet&action=professeurs");
    exit;
}

$professeur_id = $_GET['id'];

// Récupération des informations du professeur
$query = "SELECT p.*, GROUP_CONCAT(c.titre SEPARATOR ', ') as cours_enseignes 
          FROM professeurs p 
          LEFT JOIN cours c ON FIND_IN_SET(c.id, p.cours_id)
          WHERE p.id = ?
          GROUP BY p.id";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $professeur_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: " . BASE_URL . "index.php?controller=Prefet&action=professeurs");
    exit;
}

$professeur = $result->fetch_assoc();

// Récupération des classes où le professeur enseigne
$query_classes = "SELECT c.* 
                 FROM classes c 
                 JOIN cours co ON FIND_IN_SET(co.id, c.cours_ids)
                 WHERE FIND_IN_SET(co.id, ?)
                 GROUP BY c.id
                 ORDER BY c.niveau, c.nom";

$stmt_classes = $mysqli->prepare($query_classes);
$stmt_classes->bind_param("s", $professeur['cours_id']);
$stmt_classes->execute();
$result_classes = $stmt_classes->get_result();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Fermer la connexion
$stmt->close();
$stmt_classes->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Professeur</title>
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
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil Professeur
        <small>Informations détaillées</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs">Professeurs</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/teacher-avatar.png" alt="Photo du professeur">
              <h3 class="profile-username text-center"><?php echo $professeur['prenom'] . ' ' . $professeur['nom']; ?></h3>
              <p class="text-muted text-center">Professeur</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo $professeur['email']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo isset($professeur['telephone']) ? $professeur['telephone'] : 'Non renseigné'; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date d'embauche</b> <a class="pull-right"><?php echo isset($professeur['date_embauche']) ? date('d/m/Y', strtotime($professeur['date_embauche'])) : 'Non renseigné'; ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs" class="btn btn-primary btn-block"><b>Retour à la liste</b></a>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#informations" data-toggle="tab">Informations</a></li>
              <li><a href="#cours" data-toggle="tab">Cours enseignés</a></li>
              <li><a href="#classes" data-toggle="tab">Classes</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="informations">
                <div class="box-body">
                  <strong><i class="fa fa-book margin-r-5"></i> Éducation</strong>
                  <p class="text-muted">
                    <?php echo isset($professeur['education']) ? $professeur['education'] : 'Information non disponible'; ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
                  <p class="text-muted">
                    <?php echo isset($professeur['adresse']) ? $professeur['adresse'] : 'Information non disponible'; ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-pencil margin-r-5"></i> Spécialités</strong>
                  <p class="text-muted">
                    <?php echo isset($professeur['specialites']) ? $professeur['specialites'] : 'Information non disponible'; ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-file-text-o margin-r-5"></i> Notes</strong>
                  <p>
                    <?php echo isset($professeur['notes']) ? $professeur['notes'] : 'Aucune note disponible'; ?>
                  </p>
                </div>
              </div>

              <div class="tab-pane" id="cours">
                <div class="box-body">
                  <h4>Liste des cours enseignés</h4>
                  <?php if (!empty($professeur['cours_enseignes'])): ?>
                    <ul class="list-group">
                      <?php 
                      $cours_array = explode(', ', $professeur['cours_enseignes']);
                      foreach ($cours_array as $cours): 
                      ?>
                        <li class="list-group-item">
                          <i class="fa fa-book margin-r-5"></i> <?php echo $cours; ?>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <p class="text-center">Aucun cours assigné à ce professeur.</p>
                  <?php endif; ?>
                </div>
              </div>

              <div class="tab-pane" id="classes">
                <div class="box-body">
                  <h4>Classes où le professeur enseigne</h4>
                  <?php if ($result_classes && $result_classes->num_rows > 0): ?>
                    <div class="table-responsive">
                      <table class="table table-bordered table-striped">
                        <thead>
                          <tr>
                            <th>Niveau</th>
                            <th>Nom de la classe</th>
                            <th>Année scolaire</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php while ($classe = $result_classes->fetch_assoc()): ?>
                            <tr>
                              <td><?php echo $classe['niveau']; ?></td>
                              <td><?php echo $classe['nom']; ?></td>
                              <td><?php echo isset($classe['annee_scolaire']) ? $classe['annee_scolaire'] : 'Non définie'; ?></td>
                              <td>
                                <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=voirClasse&id=<?php echo $classe['id']; ?>" class="btn btn-info btn-xs">
                                  <i class="fa fa-eye"></i> Voir
                                </a>
                              </td>
                            </tr>
                          <?php endwhile; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <p class="text-center">Ce professeur n'enseigne dans aucune classe pour le moment.</p>
                  <?php endif; ?>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
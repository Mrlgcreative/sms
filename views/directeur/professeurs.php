<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Forcer la section à "primaire" pour n'afficher que les professeurs du primaire
$section = "primaire";

// Récupération des professeurs de la section primaire uniquement
$professeurs = [];
$professeurs_query = "SELECT * FROM professeurs WHERE section = 'primaire' ORDER BY nom, prenom";
$professeurs_result = $mysqli->query($professeurs_query);

if ($professeurs_result) {
    while ($row = $professeurs_result->fetch_assoc()) {
        $professeurs[] = $row;
    }
}

// Fermer la connexion
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Professeurs</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Professeurs
        <small>Liste des professeurs<?php echo !empty($section) ? ' - Section ' . ucfirst($section) : ''; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Professeurs</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des professeurs</h3>
              <div class="box-tools">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Filtrer par section <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">Toutes les sections</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=maternelle">Maternelle</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=primaire">Primaire</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=secondaire">Secondaire</a></li>
                  </ul>
                </div>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterProfesseur" class="btn btn-success">
                  <i class="fa fa-plus"></i> Ajouter un professeur
                </a>
              </div>
            </div>
            <div class="box-body">
              <table id="professeurs-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Spécialité</th>
                    <th>Section</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($professeurs)): ?>
                    <?php foreach ($professeurs as $professeur): ?>
                      <tr>
                        <td><?php echo $professeur['id']; ?></td>
                        <td><?php echo htmlspecialchars($professeur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['email']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['contact']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['specialite']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($professeur['section'])); ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=voirProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-info btn-sm">
                              <i class="fa fa-eye"></i> Voir
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-warning btn-sm">
                              <i class="fa fa-edit"></i> Modifier
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce professeur?');">
                              <i class="fa fa-trash"></i> Supprimer
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">Aucun professeur trouvé</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
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
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  $('#professeurs-table').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
    }
  });
});
</script>
</body>
</html>
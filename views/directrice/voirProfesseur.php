<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Vérifier si l'ID du professeur est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['flash_message'] = "ID du professeur non spécifié.";
    $_SESSION['flash_type'] = "danger";
    header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
    exit;
}

$prof_id = intval($_GET['id']);

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des informations du professeur
$query = "SELECT p.*, GROUP_CONCAT(DISTINCT c.titre SEPARATOR ', ') as cours_nom
          FROM professeurs p
          LEFT JOIN cours c ON p.id = c.professeur_id
          WHERE p.id = ?
          GROUP BY p.id";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['flash_message'] = "Professeur non trouvé.";
    $_SESSION['flash_type'] = "danger";
    header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=professeurs');
    exit;
}

$professeur = $result->fetch_assoc();

// Récupération des cours enseignés par le professeur
$cours_query = "SELECT c.*, cl.nom as classe_nom
                FROM cours c
                LEFT JOIN classes cl ON c.classe_id = cl.id
                WHERE c.professeur_id = ?
                ORDER BY cl.nom";

$cours_stmt = $mysqli->prepare($cours_query);
$cours_stmt->bind_param("i", $prof_id);
$cours_stmt->execute();
$cours_result = $cours_stmt->get_result();

// Fermer la connexion
$stmt->close();
$cours_stmt->close();
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
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil du Professeur
        <small>Détails complets</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=professeurs">Professeurs</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-4">
          <!-- Profil Box -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/teacher.png" alt="Photo du professeur">
              <h3 class="profile-username text-center"><?php echo htmlspecialchars($professeur['prenom'] . ' ' . $professeur['nom']); ?></h3>
              <p class="text-muted text-center">Professeur - Section Maternelle</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['email']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['contact'] ?? 'Non renseigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date d'embauche</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['date_embauche'] ?? 'Non renseigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Cours enseignés</b> <a class="pull-right"><?php echo $professeur['cours_id']; ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=modifierProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-warning btn-block"><b>Modifier</b></a>
            </div>
          </div>

          <!-- Coordonnées Box -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Coordonnées</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['email']); ?></p>
              <hr>

              <strong><i class="fa fa-phone margin-r-5"></i> Téléphone</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['contact'] ?? 'Non renseigné'); ?></p>
              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
              <p class="text-muted"><?php echo htmlspecialchars($professeur['adresse'] ?? 'Non renseignée'); ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#cours" data-toggle="tab">Cours enseignés</a></li>
              <li><a href="#infos" data-toggle="tab">Informations complémentaires</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="cours">
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Titre du cours</th>
                        <th>Classe</th>
                        <th>Jours</th>
                        <th>Horaires</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($cours_result->num_rows > 0) {
                        while ($cours = $cours_result->fetch_assoc()) {
                          echo "<tr>
                                  <td>" . htmlspecialchars($cours['titre']) . "</td>
                                  <td>" . htmlspecialchars($cours['classe_nom']) . "</td>
                                  <td>" . htmlspecialchars($cours['jours'] ?? 'Non défini') . "</td>
                                  <td>" . htmlspecialchars($cours['horaires'] ?? 'Non défini') . "</td>
                                </tr>";
                        }
                      } else {
                        echo "<tr><td colspan='4' class='text-center'>Aucun cours assigné à ce professeur.</td></tr>";
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane" id="infos">
                <div class="box-body">
                  <strong><i class="fa fa-graduation-cap margin-r-5"></i> Formation</strong>
                  <p class="text-muted">
                    <?php echo htmlspecialchars($professeur['formation'] ?? 'Information non disponible'); ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-briefcase margin-r-5"></i> Expérience</strong>
                  <p class="text-muted">
                    <?php echo htmlspecialchars($professeur['experience'] ?? 'Information non disponible'); ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-certificate margin-r-5"></i> Certifications</strong>
                  <p class="text-muted">
                    <?php echo htmlspecialchars($professeur['certifications'] ?? 'Information non disponible'); ?>
                  </p>
                  <hr>

                  <strong><i class="fa fa-info-circle margin-r-5"></i> Notes</strong>
                  <p class="text-muted">
                    <?php echo htmlspecialchars($professeur['notes'] ?? 'Aucune note disponible'); ?>
                  </p>
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
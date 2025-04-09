<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer l'ID de l'élève depuis l'URL
$eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($eleve_id <= 0) {
    // Rediriger vers la liste des élèves si l'ID est invalide
    header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=eleves');
    exit;
}

// Récupérer les informations de l'élève
$query = "SELECT e.*, c.nom as classe_nom 
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          WHERE e.id = ? AND e.section = 'secondaire'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $eleve_id);
$stmt->execute();
$result = $stmt->get_result();
$eleve = $result->fetch_assoc();

// Vérifier si l'élève existe
if (!$eleve) {
    // Rediriger vers la liste des élèves si l'élève n'existe pas
    header('Location: ' . BASE_URL . 'index.php?controller=Prefet&action=eleves');
    exit;
}

// Récupérer les cours de l'élève
$query_cours = "SELECT c.* 
                FROM cours c 
                WHERE c.classe_id = ? 
                ORDER BY c.titre";
$stmt_cours = $mysqli->prepare($query_cours);
$stmt_cours->bind_param("i", $eleve['classe']);
$stmt_cours->execute();
$result_cours = $stmt_cours->get_result();
$cours = [];
while ($row = $result_cours->fetch_assoc()) {
    $cours[] = $row;
}

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Élève</title>
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
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves Secondaire</span>
          </a>
        </li>
        
        <li>
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
        Profil de l'élève
        <small><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves">Élèves</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/<?php echo $eleve['sexe'] == 'M' ? 'avatar5.png' : 'avatar2.png'; ?>" alt="Photo de l'élève">
              <h3 class="profile-username text-center"><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></h3>
              <p class="text-muted text-center">Élève</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Classe</b> <a class="pull-right"><?php echo $eleve['classe_nom']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Matricule</b> <a class="pull-right"><?php echo $eleve['matricule']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right"><?php echo ucfirst($eleve['section']); ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=eleves" class="btn btn-primary btn-block"><b>Retour à la liste</b></a>
            </div>
          </div>

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-calendar margin-r-5"></i> Date de naissance</strong>
              <p class="text-muted"><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></p>
              <hr>

              <strong><i class="fa fa-venus-mars margin-r-5"></i> Sexe</strong>
              <p class="text-muted"><?php echo $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin'; ?></p>
              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
              <p class="text-muted"><?php echo $eleve['adresse']; ?></p>
              

             
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#info" data-toggle="tab">Informations générales</a></li>
              <li><a href="#cours" data-toggle="tab">Cours</a></li>
              <li><a href="#parents" data-toggle="tab">Parents</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="info">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Nom complet:</label>
                        <p><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Date de naissance:</label>
                        <p><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Lieu de naissance:</label>
                        <p><?php echo $eleve['lieu_naissance']; ?></p>
                      </div>
                    </div>
                    
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Adresse:</label>
                        <p><?php echo $eleve['adresse']; ?></p>
                      </div>
                    </div>
                    
                  </div>
                  
                  <div class="row">
                   
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Classe:</label>
                        <p><?php echo $eleve['classe_nom']; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="cours">
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Section</th>
                        <th>Option</th>
                        <th>Coefficient</th>
                        <th>Heures/semaine</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($cours as $c): ?>
                      <tr>
                        <td><?php echo $c['titre']; ?></td>
                        <td><?php echo $c['description']; ?></td>
                        <td><?php echo $c['section']; ?></td>
                        <td><?php echo $c['option_']; ?></td>
                        <td><?php echo isset($c['coefficient']) ? $c['coefficient'] : '1'; ?></td>
                        <td><?php echo isset($c['heures_semaine']) ? $c['heures_semaine'] : '2'; ?></td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if (empty($cours)): ?>
                      <tr>
                        <td colspan="6" class="text-center">Aucun cours disponible pour cette classe</td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane" id="parents">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Père</h3>
                        </div>
                        <div class="box-body">
                          <div class="form-group">
                            <label>Nom:</label>
                            <p><?php echo $eleve['nom_pere']; ?></p>
                          </div>
                         
                          <div class="form-group">
                            <label>Contact:</label>
                            <p><?php echo $eleve['contact_pere']; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Mère</h3>
                        </div>
                        <div class="box-body">
                          <div class="form-group">
                            <label>Nom:</label>
                            <p><?php echo $eleve['nom_mere']; ?></p>
                          </div>
                         
                          <div class="form-group">
                            <label>Contact:</label>
                            <p><?php echo $eleve['contact_mere']; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
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
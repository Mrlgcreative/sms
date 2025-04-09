<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Accueil Directrice</title>
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
      
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=achats">
            <i class="fa fa-shopping-cart"></i> <span>Achats</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=stock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=evenements">
            <i class="fa fa-calendar"></i> <span>Événements</span>
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
        Tableau de bord
        <small>Aperçu général</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Tableau de bord</li>
      </ol>
    </section>

    <section class="content">
      <!-- Contenu de la page d'accueil -->
      <!-- Vous pouvez ajouter ici le contenu spécifique à la page d'accueil -->
      
      <!-- Résumé des statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <?php
              // Connexion à la base de données
              $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
              
              if ($mysqli->connect_error) {
                  die("Erreur de connexion: " . $mysqli->connect_error);
              }
              
              // Compter le nombre total d'élèves
              $result = $mysqli->query("SELECT COUNT(*) as total FROM eleves WHERE section = 'Maternelle'");
              $total_eleves = $result->fetch_assoc()['total'];
              
              // Récupérer les statistiques par classe
              $classes_query = $mysqli->query("SELECT classe_id, 
                                             COUNT(*) as total, 
                                             SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as garcons,
                                             SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as filles
                                      FROM eleves 
                                      WHERE section = 'Maternelle'
                                      GROUP BY classe_id");
              
              $classes_stats = [];
              if ($classes_query) {
                  while ($row = $classes_query->fetch_assoc()) {
                      $classes_stats[] = $row;
                  }
              }
              
              // Statistiques par sexe
              $sexe_query = $mysqli->query("SELECT sexe, COUNT(*) as total FROM eleves WHERE section = 'Maternelle' GROUP BY sexe");
              $sexe_stats = [];
              if ($sexe_query) {
                  while ($row = $sexe_query->fetch_assoc()) {
                      $sexe_stats[$row['sexe']] = $row['total'];
                  }
              }
              ?>
              <h3><?php echo $total_eleves; ?></h3>
              <p>Élèves inscrits</p>
            </div>
            <div class="icon">
              <i class="ion ion-person"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo count($classes_stats); ?></h3>
              <p>Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes" class="small-box-footer">Voir les classes <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-4 col-xs-12">
          <div class="small-box bg-yellow">
            <div class="inner">
              <?php
              $garcons = isset($sexe_stats['M']) ? $sexe_stats['M'] : 0;
              $filles = isset($sexe_stats['F']) ? $sexe_stats['F'] : 0;
              ?>
              <h3><?php echo $garcons; ?> G | <?php echo $filles; ?> F</h3>
              <p>Garçons et Filles</p>
            </div>
            <div class="icon">
              <i class="fa fa-venus-mars"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques" class="small-box-footer">Plus de statistiques <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <?php
              // Compter le nombre de filles
              $result = $mysqli->query("SELECT COUNT(*) as total FROM eleves WHERE sexe = 'F'");
              $total_filles = $result->fetch_assoc()['total'];
              ?>
              <h3><?php echo $total_filles; ?></h3>
              <p>Filles</p>
            </div>
            <div class="icon">
              <i class="ion ion-female"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
      
      <!-- Activités récentes et tâches -->
      <div class="row">
        <div class="col-md-8">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Élèves récemment inscrits</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table no-margin">
                  <thead>
                    <tr>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Classe</th>
                      <th>Date d'inscription</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Récupérer les 5 derniers élèves inscrits de la section maternelle
                    $result = $mysqli->query("SELECT e.*, c.nom as classe_nom 
                                             FROM eleves e
                                             LEFT JOIN classes c ON e.classe_id = c.id
                                             WHERE e.section = 'Maternelle' 
                                             ORDER BY e.created_at DESC LIMIT 5");
                    
                    if ($result && $result->num_rows > 0) {
                        while ($eleve = $result->fetch_assoc()) {
                    ?>
                    <tr>
                      <td><?php echo !empty($eleve['matricule']) ? $eleve['matricule'] : 'Non attribué'; ?></td>
                      <td><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=viewStudent&id=<?php echo $eleve['id']; ?>"><?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?></a></td>
                      <td><?php echo $eleve['classe_nom'] ?? 'Non assigné'; ?></td>
                      <td><?php echo !empty($eleve['date_inscription']) ? date('d/m/Y', strtotime($eleve['date_inscription'])) : 'Non renseignée'; ?></td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo '<tr><td colspan="4" class="text-center">Aucun élève inscrit récemment</td></tr>';
                    }
                    $mysqli->close();
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="box-footer clearfix">
              <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves" class="btn btn-sm btn-info btn-flat pull-right">Voir tous les élèves</a>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Accès rapide</h3>
            </div>
            <div class="box-body">
              <ul class="list-unstyled">
                <li><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=addStudent"><i class="fa fa-plus-circle text-green"></i> Ajouter un nouvel élève</a></li>
                <li style="margin-top: 10px;"><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=classes"><i class="fa fa-list text-blue"></i> Gérer les classes</a></li>
                <li style="margin-top: 10px;"><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=statistiques"><i class="fa fa-bar-chart text-yellow"></i> Voir les statistiques</a></li>
                <li style="margin-top: 10px;"><a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=profil"><i class="fa fa-user text-purple"></i> Modifier mon profil</a></li>
              </ul>
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
<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des statistiques
$total_eleves = $mysqli->query("SELECT COUNT(*) AS total FROM eleves")->fetch_assoc()['total'];
$total_professeurs = $mysqli->query("SELECT COUNT(*) AS total FROM professeurs")->fetch_assoc()['total'];
$total_directeurs = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='director'")->fetch_assoc()['total'];
$total_directrices = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='directrice'")->fetch_assoc()['total'];
$total_prefets = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='prefet'")->fetch_assoc()['total'];
$total_comptables = $mysqli->query("SELECT COUNT(*) AS total FROM users WHERE role='comptable'")->fetch_assoc()['total'];
$total_frais = $mysqli->query("SELECT SUM(amount_paid) AS total FROM paiements_frais")->fetch_assoc()['total'] ?? 0;

// Récupérer le nombre de classes et d'employés avant de fermer la connexion
$total_classes = $mysqli->query("SELECT COUNT(*) AS total FROM classes")->fetch_assoc()['total'];
$total_employes = $mysqli->query("SELECT COUNT(*) AS total FROM employes")->fetch_assoc()['total'];

// Récupération des élèves par classe
$eleves_par_classe = [];
$eleves_par_classe_query = "SELECT e.classe_id, COUNT(e.id) as total, c.niveau as classe_nom
                           FROM eleves e 
                           LEFT JOIN classes c ON e.classe_id=c.id
                           GROUP BY e.classe_id 
                           ORDER BY total DESC 
                           LIMIT 5";
$eleves_par_classe_result = $mysqli->query($eleves_par_classe_query);
if ($eleves_par_classe_result) {
    while ($row = $eleves_par_classe_result->fetch_assoc()) {
        $eleves_par_classe[] = $row;
    }
}

// Récupération des actions par utilisateur
$actions_query = "SELECT l.username, COUNT(*) as total_actions 
                 FROM logs l 
                 GROUP BY l.username 
                 ORDER BY total_actions DESC 
                 LIMIT 10";
$actions_result = $mysqli->query($actions_query);
$actions_data = [];
$actions_labels = [];
$actions_values = [];

if ($actions_result && $actions_result->num_rows > 0) {
    while ($row = $actions_result->fetch_assoc()) {
        $actions_labels[] = $row['username'];
        $actions_values[] = $row['total_actions'];
    }
} else {
    // Données par défaut si aucune action n'est trouvée
    $actions_labels = ['Aucune donnée'];
    $actions_values = [0];
}

// Fermer la connexion après avoir récupéré toutes les données nécessaires


// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

$user_details = [];
if ($user_id > 0) {
    // Modification de la requête pour récupérer également l'image de profil
    $stmt = $mysqli->prepare("SELECT telephone, adresse, image FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user_details = $result->fetch_assoc();
        // Mettre à jour la variable de session avec l'image de la base de données si elle existe
        if (!empty($user_details['image'])) {
            $image = $user_details['image'];
            $_SESSION['image'] = $image;
        }
    }
    $stmt->close();
}

$mysqli->close();

// Récupérer les messages de succès ou d'erreur
$success_message = isset($_GET['success']) && isset($_GET['message']) ? $_GET['message'] : '';
$error_message = isset($_GET['error']) && isset($_GET['message']) ? $_GET['message'] : '';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Tableau de bord</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
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
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <!-- Nouveaux liens ajoutés -->
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        <!-- Fin des nouveaux liens -->
      
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=Frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addPrefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addDirecteur"><i class="fa fa-circle-o"></i> Ajouter Directeur</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=adddirectrice"><i class="fa fa-circle-o"></i> Ajouter Directrice</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addcomptable"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutemployes"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
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
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo $total_eleves; ?></h3>
              <p>Élèves inscrits</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo $total_professeurs; ?></h3>
              <p>Professeurs</p>
            </div>
            <div class="icon">
              <i class="fa fa-graduation-cap"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo $total_directeurs + $total_directrices; ?></h3>
              <p>Direction</p>
            </div>
            <div class="icon">
              <i class="fa fa-user-secret"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo number_format($total_frais, 2); ?></h3>
              <p>Total des frais</p>
            </div>
            <div class="icon">
              <i class="fa fa-dollar"></i>
            </div>
            <a href="#" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Deuxième rangée de statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?php echo $total_prefets; ?></h3>
              <p>Préfets</p>
            </div>
            <div class="icon">
              <i class="fa fa-user"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-maroon">
            <div class="inner">
              <h3><?php echo $total_comptables; ?></h3>
              <p>Comptables</p>
            </div>
            <div class="icon">
              <i class="fa fa-calculator"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-olive">
            <div class="inner">
              <h3><?php echo $total_classes; ?></h3>
              <p>Classes</p>
            </div>
            <div class="icon">
              <i class="fa fa-table"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-teal">
            <div class="inner">
              <h3><?php echo $total_employes; ?></h3>
              <p>Employés</p>
            </div>
            <div class="icon">
              <i class="fa fa-users"></i>
            </div>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes" class="small-box-footer">Plus d'infos <i class="fa fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Statistiques détaillées de la direction -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Détails de la Direction</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-male"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Directeurs</span>
                      <span class="info-box-number"><?php echo $total_directeurs; ?></span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-box bg-fuchsia">
                    <span class="info-box-icon"><i class="fa fa-female"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Directrices</span>
                      <span class="info-box-number"><?php echo $total_directrices; ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques des élèves par classe -->
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Élèves par classe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Classe</th>
                    <th>Nombre d'élèves</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (!empty($eleves_par_classe)) {
                    foreach ($eleves_par_classe as $row) {
                      echo "<tr>
                              <td>{$row['classe_nom']}</td>
                              <td>{$row['total']}</td>
                            </tr>";
                    }
                  } else {
                    echo "<tr><td colspan='2' class='text-center'>Aucune donnée disponible</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      
      <!-- Graphique des statistiques détaillées -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-bar-chart"></i> Statistiques détaillées du système</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="chart">
                    <canvas id="statsDetailChart" style="height:250px"></canvas>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="pad">
                    <div class="description-block">
                      <h5 class="description-header"><?php echo $total_eleves; ?></h5>
                      <span class="description-text">ÉLÈVES TOTAUX</span>
                    </div>
                    <?php
                    // Récupérer le nombre d'élèves à jour avec les frais
                    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                    
                    // S'assurer que la connexion est établie
                    if ($mysqli->connect_error) {
                        die("Erreur de connexion à la base de données: " . $mysqli->connect_error);
                    }
                    
                    // Modifié pour utiliser la structure correcte de la table paiements_frais
                    // Nous supposons qu'un élève est à jour s'il a au moins un paiement
                    $eleves_a_jour_query = "SELECT COUNT(DISTINCT e.id) as total FROM eleves e 
                                           JOIN paiements_frais pf ON e.id = pf.eleve_id 
                                           WHERE pf.amount_paid > 0";
                    $eleves_a_jour_result = $mysqli->query($eleves_a_jour_query);
                    $eleves_a_jour = $eleves_a_jour_result ? $eleves_a_jour_result->fetch_assoc()['total'] : 0;
                    
                    // Récupérer les statistiques d'actions
                    $stats_actions_query = "SELECT 
                                          SUM(CASE WHEN action LIKE '%ajout%' THEN 1 ELSE 0 END) as ajouts,
                                          SUM(CASE WHEN action LIKE '%modif%' THEN 1 ELSE 0 END) as modifications,
                                          SUM(CASE WHEN action LIKE '%ban%' OR action LIKE '%block%' THEN 1 ELSE 0 END) as bannissements,
                                          SUM(CASE WHEN action LIKE '%paie%' THEN 1 ELSE 0 END) as paiements,
                                          COUNT(*) as total_actions
                                          FROM historique";
                    $stats_actions_result = $mysqli->query($stats_actions_query);
                    
                    if ($stats_actions_result) {
                        $stats_actions = $stats_actions_result->fetch_assoc();
                    } else {
                        // Valeurs par défaut si la requête échoue
                        $stats_actions = [
                            'ajouts' => 1,
                            'modifications' => 0,
                            'bannissements' => 0,
                            'paiements' => 0,
                            'total_actions' => 0
                        ];
                    }
                    
                    // Récupérer le nombre total d'utilisateurs
                    $total_users_query = "SELECT COUNT(*) as total FROM users";
                    $total_users_result = $mysqli->query($total_users_query);
                    $total_users = $total_users_result ? $total_users_result->fetch_assoc()['total'] : 0;
                    
                    $mysqli->close();
                    ?>
                    <div class="description-block">
                      <h5 class="description-header"><?php echo $eleves_a_jour; ?></h5>
                      <span class="description-text">ÉLÈVES À JOUR AVEC LES FRAIS</span>
                    </div>
                    <div class="description-block">
                      <h5 class="description-header"><?php echo $total_users; ?></h5>
                      <span class="description-text">UTILISATEURS TOTAUX</span>
                    </div>
                    <div class="description-block">
                      <h5 class="description-header"><?php echo $stats_actions['total_actions'] ?? 0; ?></h5>
                      <span class="description-text">ACTIONS TOTALES</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-plus"></i> <?php echo $stats_actions['ajouts'] ?? 0; ?></span>
                    <h5 class="description-header">Ajouts</h5>
                  </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-edit"></i> <?php echo $stats_actions['modifications'] ?? 0; ?></span>
                    <h5 class="description-header">Modifications</h5>
                  </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-red"><i class="fa fa-ban"></i> <?php echo $stats_actions['bannissements'] ?? 0; ?></span>
                    <h5 class="description-header">Bannissements</h5>
                  </div>
                </div>
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-blue"><i class="fa fa-money"></i> <?php echo $stats_actions['paiements'] ?? 0; ?></span>
                    <h5 class="description-header">Paiements</h5>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions par utilisateur (code existant) -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Actions par utilisateur</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <?php if (empty($actions_labels) || (count($actions_labels) == 1 && $actions_labels[0] == 'Aucune donnée')): ?>
                <div class="alert alert-info">
                  <h4><i class="icon fa fa-info"></i> Information</h4>
                  Aucune donnée d'action utilisateur n'est disponible pour le moment.
                </div>
              <?php else: ?>
                <div class="chart">
                  <canvas id="actionsChart" style="height:250px"></canvas>
                </div>
              <?php endif; ?>
            </div>
            <div class="box-footer">
              <div class="row">
                <div class="col-sm-3 col-xs-6">
                  <div class="description-block border-right">
                    <h5 class="description-header text-info">Actions</h5>
                    <span class="description-text">ACTIVITÉS DES UTILISATEURS</span>
                  </div>
                </div>
                <div class="col-sm-9">
                  <p class="text-muted">Ce graphique montre le nombre total d'actions effectuées par chaque utilisateur dans le système.</p>
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
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script>
$(document).ready(function() {
  // Vérifier si l'élément canvas existe
  var statsCtx = document.getElementById('statsDetailChart');
  if (statsCtx) {
    console.log('Canvas trouvé, initialisation du graphique...');
    
    // Définir les données pour le graphique
    var statsData = {
      labels: ['Ajouts', 'Modifications', 'Bannissements', 'Paiements', 'Élèves Totaux', 'Élèves à jour', 'Utilisateurs'],
      datasets: [{
        label: 'Statistiques du système',
        data: [
          <?php echo isset($stats_actions['ajouts']) ? intval($stats_actions['ajouts']) : 0; ?>,
          <?php echo isset($stats_actions['modifications']) ? intval($stats_actions['modifications']) : 0; ?>,
          <?php echo isset($stats_actions['bannissements']) ? intval($stats_actions['bannissements']) : 0; ?>,
          <?php echo isset($stats_actions['paiements']) ? intval($stats_actions['paiements']) : 0; ?>,
          <?php echo intval($total_eleves); ?>,
          <?php echo intval($eleves_a_jour); ?>,
          <?php echo intval($total_users); ?>
        ],
        backgroundColor: [
          'rgba(0, 166, 90, 0.8)',   // Vert - Ajouts
          'rgba(243, 156, 18, 0.8)',  // Jaune - Modifications
          'rgba(221, 75, 57, 0.8)',   // Rouge - Bannissements
          'rgba(60, 141, 188, 0.8)',  // Bleu - Paiements
          'rgba(0, 192, 239, 0.8)',   // Bleu clair - Élèves totaux
          'rgba(0, 166, 90, 0.8)',    // Vert - Élèves à jour
          'rgba(96, 92, 168, 0.8)'    // Violet - Utilisateurs
        ],
        borderColor: [
          'rgba(0, 166, 90, 1)',
          'rgba(243, 156, 18, 1)',
          'rgba(221, 75, 57, 1)',
          'rgba(60, 141, 188, 1)',
          'rgba(0, 192, 239, 1)',
          'rgba(0, 166, 90, 1)',
          'rgba(96, 92, 168, 1)'
        ],
        borderWidth: 1
      }]
    };
    
    try {
      // Créer le graphique
      var statsDetailChart = new Chart(statsCtx, {
        type: 'bar',
        data: statsData,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
      
      console.log('Graphique des statistiques détaillées initialisé avec succès');
    } catch (error) {
      console.error('Erreur lors de l\'initialisation du graphique:', error);
    }
  } else {
    console.error("Canvas element 'statsDetailChart' not found");
  }
  
  // Nouveau graphique pour les actions des utilisateurs
  <?php if (!empty($actions_labels) && !(count($actions_labels) == 1 && $actions_labels[0] == 'Aucune donnée')): ?>
  var actionsCtx = document.getElementById('actionsChart');
  if (actionsCtx) {
    try {
      var actionsChart = new Chart(actionsCtx, {
        type: 'horizontalBar',
        data: {
          labels: <?php echo json_encode($actions_labels); ?>,
          datasets: [{
            label: 'Nombre d\'actions',
            data: <?php echo json_encode($actions_values); ?>,
            backgroundColor: [
              'rgba(60, 141, 188, 0.8)',
              'rgba(0, 166, 90, 0.8)',
              'rgba(243, 156, 18, 0.8)',
              'rgba(221, 75, 57, 0.8)',
              'rgba(0, 192, 239, 0.8)',
              'rgba(210, 214, 222, 0.8)',
              'rgba(216, 27, 96, 0.8)',
              'rgba(156, 39, 176, 0.8)',
              'rgba(63, 81, 181, 0.8)',
              'rgba(0, 150, 136, 0.8)'
            ],
            borderColor: [
              'rgba(60, 141, 188, 1)',
              'rgba(0, 166, 90, 1)',
              'rgba(243, 156, 18, 1)',
              'rgba(221, 75, 57, 1)',
              'rgba(0, 192, 239, 1)',
              'rgba(210, 214, 222, 1)',
              'rgba(216, 27, 96, 1)',
              'rgba(156, 39, 176, 1)',
              'rgba(63, 81, 181, 1)',
              'rgba(0, 150, 136, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          scales: {
            xAxes: [{
              ticks: {
                beginAtZero: true
              }
            }]
          }
        }
      });
      console.log('Graphique des actions initialisé avec succès');
    } catch (error) {
      console.error('Erreur lors de l\'initialisation du graphique des actions:', error);
    }
  } else {
    console.error("Canvas element 'actionsChart' not found");
  }
  <?php endif; ?>
});
</script>
</body>
</html>

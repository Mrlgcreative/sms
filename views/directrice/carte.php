<?php
// Vérifiez si une session est déjà active avant d'appeler session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les clés si elles ne sont pas déjà définies
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = '';
}
if (!isset($_SESSION['email'])) {
  $_SESSION['email'] = '';
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = '';
}

// Récupérer les valeurs des clés
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer l'ID de l'élève depuis l'URL
$eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Définir l'année scolaire actuelle
$annee_actuelle = date('Y');
$annee_suivante = $annee_actuelle + 1;
$annee = $annee_actuelle . '-' . $annee_suivante;

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
              
if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer les informations de l'élève
$eleve_query = $mysqli->prepare("SELECT e.*, c.nom as classe_nom 
                                FROM eleves e
                                LEFT JOIN classes c ON e.classe_id = c.id
                                WHERE e.id = ?");
$eleve_query->bind_param("i", $eleve_id);
$eleve_query->execute();
$eleve_result = $eleve_query->get_result();
$eleve = $eleve_result->fetch_assoc();

if (!$eleve) {
    // Rediriger si l'élève n'existe pas
    header("Location: " . BASE_URL . "index.php?controller=directrice&action=eleves");
    exit;
}

// Générer un code unique pour la carte
$code_carte = isset($eleve['matricule']) ? $eleve['matricule'] : 'MAT-' . str_pad($eleve_id, 5, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Carte d'élève</title>
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
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
   .carte-eleve {
      width:115.6mm;
      height: 64mm;
      border: 1px solid #000;
      border-radius: 10px;
      padding: 10px;
      margin: 20px auto;
      background-color: #fff;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      position: relative;
      overflow: hidden;
    }
    .carte-header {
      text-align: center;
      border-bottom: 2px solid #3c8dbc;
      padding-bottom: 5px;
      margin-bottom: 10px;
    }
    .carte-header h3 {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
      color: #3c8dbc;
    }
    .carte-header p {
      margin: 0;
      font-size: 12px;
    }
    .carte-body {
      display: flex;
    }
    .carte-photo {
      width: 25mm;
      height: 30mm;
      border: 1px solid #ddd;
      margin-right: 10px;
      background-color: #f5f5f5;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .carte-photo img {
      max-width: 100%;
      max-height: 100%;
    }
    .carte-info {
      flex: 1;
      font-size: 12px;
    }
    .carte-info p {
      margin: 3px 0;
    }
    .carte-footer {
      text-align: center;
      margin-top: 5px;
      font-size: 10px;
      position: absolute;
      bottom: 5px;
      width: calc(100% - 20px);
    }
    .carte-qr {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 10mm;
      height: 10mm;
      background-color: #f5f5f5;
      border: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .carte-signature {
      position: absolute;
      bottom: 20px;
      left: 10px;
      font-size: 10px;
      text-align: center;
    }
    .carte-signature img {
      max-width: 20mm;
      max-height: 10mm;
    }
    .watermark {
      position: absolute;
      top: 10%;
      left: 10%;
      transform: translate(-50%, -50%);
      opacity: 0.1;
      font-size: 40px;
      font-weight: bold;
      color: #3c8dbc;
      z-index: 0;
    }
    @media print {
      .no-print {
        display: none;
      }
      body {
        margin: 0;
        padding: 0;
      }
      .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
      }
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>St</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>St</b> Sofie</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - Directrice
                  <small>Directrice de la section maternelle</small>
                </p>
              </li>
              <!-- Menu Footer-->
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
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- sidebar menu: : style can be found in sidebar.less -->
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
    <section class="content-header no-print">
      <h1>
        Carte d'Élève
        <small>Génération de carte d'identité scolaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">Élèves</a></li>
        <li class="active">Carte d'Élève</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border no-print">
              <h3 class="box-title">Carte d'identité scolaire</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            
            <div class="box-body">
              <div class="row no-print">
                <div class="col-md-12">
                  <button onclick="window.print();" class="btn btn-primary pull-right">
                    <i class="fa fa-print"></i> Imprimer la carte
                  </button>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Retour à la liste
                  </a>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12">
                  <div class="carte-eleve">
                    <div class="watermark">SGS</div>
                    <div class="carte-header">
                      <h3>ECOLE St JEAN-HENRI ET St SOFIE</h3>
                      <p>CARTE D'IDENTITÉ SCOLAIRE <?php echo $annee; ?></p>
                    </div>
                    <div class="carte-body">
                      <div class="carte-photo">
                        <?php if (!empty($eleve['photo']) && file_exists($eleve['photo'])): ?>
                          <img src="<?php echo BASE_URL . $eleve['photo']; ?>" alt="Photo de l'élève">
                        <?php else: ?>
                          <i class="fa fa-user fa-5x text-muted"></i>
                        <?php endif; ?>
                      </div>
                      <div class="carte-info">
                        <p><strong>Nom:</strong> <?php echo $eleve['nom']; ?></p>
                        <p><strong>Post-nom:</strong> <?php echo $eleve['post_nom']; ?></p>
                        <p><strong>Prénom:</strong> <?php echo $eleve['prenom']; ?></p>
                        <p><strong>Classe:</strong> <?php echo isset($eleve['classe_id']) ? $eleve['classe_nom'] : 'Non assigné'; ?></p>
                        <p><strong>Section:</strong> <?php echo $eleve['section']; ?></p>
                        <p><strong>ID:</strong> <?php echo isset($eleve['matricule']) ? $eleve['matricule'] : 'N/A'; ?></p>
                      </div>
                    </div>
                    <div class="carte-signature">
                      <div>Le Directeur</div>
                      <img src="<?php echo BASE_URL; ?>dist/img/signature.png" alt="Signature">
                    </div>
                    <div class="carte-qr">
                      <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode(BASE_URL . 'index.php?controller=directrice&action=carte&id=' . $eleve_id); ?>" alt="QR Code">
                    </div>
                    <div class="carte-footer">
                      Cette carte est strictement personnelle et doit être présentée à toute réquisition.
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
  <!-- /.content-wrapper -->
  <footer class="main-footer no-print">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
</body>
</html>
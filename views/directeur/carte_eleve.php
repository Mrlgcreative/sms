<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'ID de l'élève est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "ID de l'élève non spécifié.";
    header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
    exit;
}

$eleve_id = (int)$_GET['id'];

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des informations de l'élève
$query = "SELECT e.*, c.nom as classe_nom
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          WHERE e.id = ? AND e.section = 'primaire'";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $eleve_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Élève non trouvé.";
    header('Location: ' . BASE_URL . 'index.php?controller=Director&action=eleves');
    exit;
}

$eleve = $result->fetch_assoc();

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

$annee_query = "SELECT * FROM sessions_scolaires WHERE est_active = 1 LIMIT 1";
$annee_result = $mysqli->query($annee_query);
$annee_scolaire = $annee_result->fetch_assoc();

// Vérifier si la colonne s'appelle 'annee' ou 'session'
if (isset($annee_scolaire['annee'])) {
    $annee = $annee_scolaire['annee'];
} elseif (isset($annee_scolaire['session'])) {
    $annee = $annee_scolaire['session'];
} else {
    // Valeur par défaut si aucune colonne appropriée n'est trouvée
    $annee = date('Y') . '-' . (date('Y') + 1);
}

// Générer un code unique pour la carte
$code_carte = 'SGS-P-' . str_pad($eleve_id, 5, '0', STR_PAD_LEFT) . '-' . date('Y');
// Fermer la connexion
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Carte d'Élève Primaire</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
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
      border-bottom: 2px solid #605ca8;
      padding-bottom: 5px;
      margin-bottom: 10px;
    }
    .carte-header h3 {
      margin: 0;
      font-size: 16px;
      font-weight: bold;
      color: #605ca8;
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
      bottom: 10px;
      left: 10px;
      font-size: 10px;
      text-align: center;
    }
    .carte-signature img {
      max-width: 15mm;
      max-height: 9mm;
    }
    .watermark {
      position: absolute;
      top: 10%;
      left: 10%;
      transform: translate(-50%, -50%);
      opacity: 0.1;
      font-size: 40px;
      font-weight: bold;
      color: #605ca8;
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
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header no-print">
      <h1>
        Carte d'Élève Primaire
        <small>Génération de carte d'identité scolaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">Élèves</a></li>
        <li class="active">Carte d'Élève</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border no-print">
              <h3 class="box-title">Carte d'identité scolaire - Section Primaire</h3>
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Retour à la liste
                  </a>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12">
                  <div class="carte-eleve">
                    <div class="watermark">SGS</div>
                    <div class="carte-header">
                      <h3>ECOLE St JEAN-HENRI ET St SOPHIE</h3>
                      <p>CARTE D'IDENTITÉ SCOLAIRE PRIMAIRE <?php echo $annee; ?></p>
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
                        <p><strong>Post-nom:</strong> <?php echo isset($eleve['post_nom']) ? $eleve['post_nom'] : ''; ?></p>
                        <p><strong>Prénom:</strong> <?php echo $eleve['prenom']; ?></p>
                        <p><strong>Classe:</strong> <?php echo $eleve['classe_nom']; ?></p>
                        <p><strong>Date de naissance:</strong> <?php echo isset($eleve['date_naissance']) ? date('d/m/Y', strtotime($eleve['date_naissance'])) : ''; ?></p>
                        <p><strong>Section:</strong> Primaire</p>
                        <p><strong>Matricule:</strong> <?php echo isset($eleve['matricule']) ? $eleve['matricule'] : $code_carte; ?></p>
                      </div>
                    </div>
                    <div class="carte-signature">
                      <div>Le Directeur</div>
                      <img src="<?php echo BASE_URL; ?>dist/img/signature.png" alt="Signature">
                    </div>
                    <div class="carte-qr">
  <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode('SGS-' . str_pad($eleve['id'], 5, '0', STR_PAD_LEFT) . '-' . date('Y')); ?>" alt="QR Code">
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

  <footer class="main-footer no-print">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer l'ID de l'élève
$eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'élève
$query = "SELECT e.*, o.nom as option_nom, c.niveau as classe_nom
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          LEFT JOIN options o ON e.option_id = o.id 
          WHERE e.id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $eleve_id);
$stmt->execute();
$result = $stmt->get_result();
$eleve = $result->fetch_assoc();

// Si l'élève n'existe pas, rediriger vers la liste des élèves
if (!$eleve) {
    header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves&error=1&message=' . urlencode('Élève non trouvé'));
    exit;
}

// Récupérer l'année scolaire actuelle
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
$code_carte = 'SGS-' . str_pad($eleve_id, 5, '0', STR_PAD_LEFT) . '-' . date('Y');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Carte d'Élève</title>
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
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

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
                      <h3>ECOLE St JEAN-HENRI ET St SOPHIE</h3>
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
                        <p><strong>Classe:</strong> <?php echo $eleve['classe_nom']; ?></p>
                        <p><strong>Option:</strong> <?php echo $eleve['option_nom']; ?></p>
                        <p><strong>Section:</strong> <?php echo $eleve['section']; ?></p>
                        <p><strong>ID:</strong> <?php echo $code_carte; ?></p>
                      </div>
                    </div>
                    <div class="carte-signature">
                      <div>Le Directeur</div>
                      <img src="<?php echo BASE_URL; ?>dist/img/signature.png" alt="Signature">
                    </div>
                    <div class="carte-qr">
                      <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode($code_carte); ?>" alt="QR Code">
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

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
<?php
// Vue pour la gestion des achats de fournitures
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Instancier le modèle
require_once 'models/AchatFourniture.php';
$achatModel = new AchatFourniture($mysqli);

// Récupérer tous les achats
$achats = $achatModel->getAllAchats();
$totalDepenses = $achatModel->getTotalDepenses();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Achats Fournitures</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

<?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 
  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Achats de Fournitures
        <small>Suivi des dépenses</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Achats Fournitures</li>
      </ol>
    </section>

    <section class="content">
      <!-- Boîtes d'information -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-shopping-cart"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Achats</span>
              <span class="info-box-number"><?php echo count($achats); ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Dépenses</span>
              <span class="info-box-number"><?php echo number_format($totalDepenses, 2); ?> $</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Formulaire d'ajout -->
    

      <!-- Liste des achats -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des achats de fournitures</h3>
            </div>
            <div class="box-body">
              <table id="achats-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Fournisseur</th>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Montant ($)</th>
                    <th>Référence</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($achats as $achat): ?>
                  <tr>
                    <td><?php echo $achat['id']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($achat['date_achat'])); ?></td>
                    <td><?php echo $achat['fournisseur']; ?></td>
                    <td><?php echo $achat['description']; ?></td>
                    <td><?php echo $achat['quantite']; ?></td>
                    <td><?php echo number_format($achat['montant'], 2); ?> $</td>
                    <td><?php echo $achat['facture_ref']; ?></td>
                    <td>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=modifierAchat&id=<?php echo $achat['id']; ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Modifier</a>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=supprimerAchat&id=<?php echo $achat['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat ?');"><i class="fa fa-trash"></i> Supprimer</a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
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
    <strong>Copyright &copy; 2023 <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    $('#achats-table').DataTable({
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
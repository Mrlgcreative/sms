<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer le nombre d'élèves réinscrits
$result = $mysqli->query("SELECT COUNT(*) AS total_eleves FROM historique_reinscriptions ");
$row = $result->fetch_assoc();
$total_eleves = $row['total_eleves'];

// Fermer la connexion à la base de données
$mysqli->close();

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
$current_session = isset($current_session) ? $current_session : date('Y') . '-' . (date('Y') + 1);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Liste des élèves réinscrits</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      .content-wrapper, .main-footer {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Liste des élèves réinscrits
        <small>Tous les élèves réinscrits pour l'année <?php echo $current_session; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Réinscriptions</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves réinscrits (Total: <?php echo $total_eleves; ?>)</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <input type="text" id="searchInput" class="form-control pull-right" placeholder="Rechercher...">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="box-body">
              <div class="row no-print" style="margin-bottom: 15px;">
                <div class="col-xs-12">
                  <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportReinscrits" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription" class="btn btn-info"><i class="fa fa-plus"></i> Nouvelle réinscription</a>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="eleveTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Photo</th>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Post-nom</th>
                      <th>Prénom</th>
                      <th>Date de naissance</th>
                      <th>Nouvelle classe</th>
                      <th>Date réinscription</th>
                      <th>Statut paiement</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($eleves as $eleve) : ?>
                      <tr>
                        <td><?php echo $eleve['id']; ?></td>
                        <td><img src="<?php echo !empty($eleve['photo']) ? $eleve['photo'] : 'dist/img/default-student.png'; ?>" alt="Photo élève" class="img-circle" style="width: 50px; height: 50px;"></td>
                        <td><?php echo !empty($eleve['matricule']) ? $eleve['matricule'] : 'Non défini'; ?></td>
                        <td><?php echo $eleve['nom_eleve']; ?></td>
                        <td><?php echo $eleve['post_nom']; ?></td>
                        <td><?php echo $eleve['prenom']; ?></td>
                        <td><?php echo $eleve['date_naissance']; ?></td>
                        <td><?php echo $eleve['nouvelle_classe']; ?></td>
                        <td><?php echo $eleve['date_reinscription']; ?></td>
                        <td>
                          <?php if ($eleve['statut_paiement'] == 'Complet') : ?>
                            <span class="label label-success">Complet</span>
                          <?php elseif ($eleve['statut_paiement'] == 'Partiel') : ?>
                            <span class="label label-warning">Partiel</span>
                          <?php else : ?>
                            <span class="label label-danger">Non payé</span>
                          <?php endif; ?>
                        </td>
                        <td class="no-print">
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=viewStudent&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Détails</a>
                          
                       
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
</body>
</html>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // Initialiser DataTables
    $('#eleveTable').DataTable({
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
    
    // Fonction de recherche dynamique
    $("#searchInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#eleveTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    
    // Vérifier les paramètres d'URL au chargement de la page
    var urlParams = new URLSearchParams(window.location.search);
    var success = urlParams.get('success');
    var error = urlParams.get('error');
    var message = urlParams.get('message');
    
    if (success) {
      showAlert(decodeURIComponent(message || 'Opération réussie!'), 'success');
    } else if (error) {
      showAlert(decodeURIComponent(message || 'Une erreur est survenue!'), 'danger');
    }
  });
  
  // Fonction pour afficher une alerte stylisée
  function showAlert(message, type) {
    // Créer l'élément d'alerte
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    
    // Ajouter le contenu de l'alerte
    alertDiv.innerHTML = 
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' + 
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message;
    
    // Ajouter l'alerte au document
    document.body.appendChild(alertDiv);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(function() {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }
</script>
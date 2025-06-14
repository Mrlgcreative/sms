<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer le nombre total de paiements
$result = $mysqli->query("SELECT COUNT(*) AS total_paiements FROM paiements_frais");
$row = $result->fetch_assoc();
$total_paiements = $row['total_paiements'];

// Récupérer le montant total des paiements
$result = $mysqli->query("SELECT SUM(amount_paid) AS montant_total FROM paiements_frais");
$row = $result->fetch_assoc();
$montant_total = $row['montant_total'] ?? 0;

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
  <title>St Sofie | Liste des paiements</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Liste des paiements
        <small>Tous les paiements enregistrés</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Paiements</li>
      </ol>
    </section>

    <section class="content">
      <!-- Messages d'alerte -->
      <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      
      <!-- Résumé des paiements -->
      <div class="row payment-summary">
        <div class="col-md-12">
          <h4><i class="fa fa-bar-chart"></i> Résumé des paiements</h4>
        </div>
        <div class="col-md-6">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total des paiements</span>
              <span class="info-box-number"><?php echo $total_paiements; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Montant total</span>
              <span class="info-box-number"><?php echo number_format($montant_total, 2, ',', ' '); ?> $</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Filtres -->
      <div class="row no-print">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Filtres</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <form id="filterForm" class="form-horizontal">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Classe</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterClasse">
                          <option value="">Toutes les classes</option>
                          <?php foreach ($classes as $classe) : ?>
                            <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Mois</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterMois">
                          <option value="">Tous les mois</option>
                          <?php foreach ($mois as $moi) : ?>
                            <option value="<?php echo $moi['nom']; ?>"><?php echo $moi['nom']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Type de frais</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterFrais">
                          <option value="">Tous les frais</option>
                          <?php foreach ($frais as $frai) : ?>
                            <option value="<?php echo $frai['description']; ?>"><?php echo $frai['description']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-primary" id="applyFilter">
                      <i class="fa fa-filter"></i> Appliquer les filtres
                    </button>
                    <button type="button" class="btn btn-default" id="resetFilter">
                      <i class="fa fa-refresh"></i> Réinitialiser
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste des paiements -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des paiements (Total: <?php echo $total_paiements; ?>)</h3>
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportPaiements" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Nouveau paiement</a>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="paiementsTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Élève</th>
                      <th>Classe</th>
                      <th>Option</th>
                      <th>Type de frais</th>
                      <th>Montant</th>
                      <th>Mois</th>
                      <th>Date de paiement</th>
                      <th>Date d'enregistrement</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($paiements as $paiement) : ?>
                      <tr>
                        <td><?php echo $paiement['id']; ?></td>
                        <td><?php echo $paiement['nom'] . ' ' . $paiement['post_nom'] . ' ' . $paiement['prenom_eleve']; ?></td>
                        <td><?php echo $paiement['classe']; ?></td>
                        <td><?php echo $paiement['option_nom']; ?></td>
                        <td><?php echo $paiement['description_frais']; ?></td>
                        <td><?php echo number_format($paiement['amount_paid'], 2, ',', ' '); ?> $</td>
                        <td><?php echo $paiement['nom_mois']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($paiement['payment_date'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($paiement['created_at'])); ?></td>
                        <td class="no-print">
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=imprimerRecu&id=<?php echo $paiement['id']; ?>" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-print"></i> Reçu</a>
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifierPaiement&id=<?php echo $paiement['id']; ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Modifier</a>
                          <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete(<?php echo $paiement['id']; ?>)"><i class="fa fa-trash"></i> Supprimer</button>
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
  
  <!-- Modal de confirmation de suppression -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Confirmation de suppression</h4>
        </div>
        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer ce paiement ? Cette action est irréversible.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Supprimer</a>
        </div>
      </div>
    </div>
  </div>
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
    var table = $('#paiementsTable').DataTable({
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
      table.search(this.value).draw();
    });
    
    // Appliquer les filtres
    $('#applyFilter').click(function() {
      var classeFilter = $('#filterClasse').val();
      var moisFilter = $('#filterMois').val();
      var fraisFilter = $('#filterFrais').val();
      
      // Réinitialiser la recherche
      table.search('').columns().search('').draw();
      
      // Appliquer les filtres
      if (classeFilter) {
        table.column(2).search(classeFilter).draw();
      }
      if (moisFilter) {
        table.column(6).search(moisFilter).draw();
      }
      if (fraisFilter) {
        table.column(4).search(fraisFilter).draw();
      }
    });
    
    // Réinitialiser les filtres
    $('#resetFilter').click(function() {
      $('#filterClasse').val('');
      $('#filterMois').val('');
      $('#filterFrais').val('');
      table.search('').columns().search('').draw();
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
  
  // Fonction pour afficher la boîte de dialogue de confirmation de suppression
  function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?php echo BASE_URL; ?>index.php?controller=comptable&action=supprimerPaiement&id=' + id);
    $('#deleteModal').modal('show');
  }
  
  // Fonction pour afficher une alerte stylisée
  function showAlert(message, type) {
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    
    alertDiv.innerHTML = 
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' + 
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(function() {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }
</script>
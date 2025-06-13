<?php
// Vue pour la gestion des paiements - Administrateur
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

// Récupérer tous les paiements avec les informations des élèves
$query = "SELECT p.*, e.nom as eleve_nom, e.prenom as eleve_prenom, 
                 c.nom as classe_nom, o.nom as option_nom, e.section,
                 f.description as frais_description,
                 MONTHNAME(p.payment_date) as mois_nom,
                 MONTH(p.payment_date) as mois_numero
          FROM paiements_frais p
          LEFT JOIN eleves e ON p.eleve_id = e.id
          LEFT JOIN classes c ON e.classe_id = c.id
          LEFT JOIN options o ON e.option_id = o.id
          LEFT JOIN frais f ON p.frais_id = f.id
          ORDER BY p.payment_date DESC";
$result = $mysqli->query($query);
$paiements = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Ajouter le mois en français
        $mois_fr = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
        ];
        $row['mois'] = $mois_fr[$row['mois_numero']] ?? 'Inconnu';
        $paiements[] = $row;
    }
}

// Récupérer tous les types de frais
$frais_result = $mysqli->query("SELECT * FROM frais ORDER BY description");
$frais = [];
if ($frais_result) {
    while ($row = $frais_result->fetch_assoc()) {
        $frais[] = $row;
    }
}

// Fermer la connexion à la base de données
$mysqli->close();

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$current_session = isset($current_session) ? $current_session : date('Y') . '-' . (date('Y') + 1);
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Liste des paiements</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- CSS Dependencies -->
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

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Liste des paiements
        <small>Gestion des paiements - Administration</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
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
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total des paiements</span>
              <span class="info-box-number"><?php echo $total_paiements; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-dollar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Montant total</span>
              <span class="info-box-number"><?php echo number_format($montant_total, 2, ',', ' '); ?> $</span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-calendar"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Année scolaire</span>
              <span class="info-box-number"><?php echo $current_session; ?></span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Filtres -->
      <div class="row no-print">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Filtres de recherche</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <form id="filterForm" class="form-horizontal">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Classe</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterClasse">
                          <option value="">Toutes les classes</option>
                          <?php
                          // Get unique classes from the paiements array
                          $classes = [];
                          foreach ($paiements as $paiement) {
                            if (!empty($paiement['classe_nom']) && !in_array($paiement['classe_nom'], $classes)) {
                              $classes[] = $paiement['classe_nom'];
                            }
                          }
                          sort($classes); // Sort classes alphabetically
                          foreach ($classes as $classe) {
                            echo "<option value=\"$classe\">$classe</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Section</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterSection">
                          <option value="">Toutes les sections</option>
                          <?php 
                          $sections = [];
                          foreach ($paiements as $paiement) {
                            if (!empty($paiement['section']) && !in_array($paiement['section'], $sections)) {
                              $sections[] = $paiement['section'];
                            }
                          }
                          sort($sections); // Sort sections alphabetically
                          foreach ($sections as $section) {
                            echo "<option value=\"$section\">$section</option>";
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Type de frais</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterFrais">
                          <option value="">Tous les frais</option>
                          <?php foreach ($frais as $frai) : ?>
                            <option value="<?php echo htmlspecialchars($frai['description']); ?>"><?php echo htmlspecialchars($frai['description']); ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Mois</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterMois">
                          <option value="">Tous les mois</option>
                          <?php 
                          $months = [];
                          foreach ($paiements as $paiement) {
                            if (!empty($paiement['mois']) && !in_array($paiement['mois'], $months)) {
                              $months[] = $paiement['mois'];
                            }
                          }
                          sort($months); // Sort months alphabetically
                          foreach ($months as $month) {
                            echo "<option value=\"$month\">$month</option>";
                          }
                          ?>
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
                  <input type="text" id="searchInput" class="form-control pull-right" placeholder="Rechercher un élève...">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=exportPaiements" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=exportPaiementsPDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Exporter PDF</a>
                 
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="paiementsTable" class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>ID</th>
                      <th>Nom de l'élève</th>
                      <th>Prénom</th>
                      <th>Classe</th>
                      <th>Option</th>
                      <th>Section</th>
                      <th>Type de frais</th>
                      <th>Montant payé</th>
                      <th>Date de paiement</th>
                      <th>Mois</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($paiements as $index => $paiement) : ?>
                      <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($paiement['id']); ?></td>
                        <td><strong><?php echo htmlspecialchars($paiement['eleve_nom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($paiement['eleve_prenom']); ?></td>
                        <td>
                          <span class="label label-primary"><?php echo htmlspecialchars($paiement['classe_nom']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($paiement['option_nom']); ?></td>
                        <td>
                          <span class="label <?php echo $paiement['section'] == 'primaire' ? 'label-success' : ($paiement['section'] == 'secondaire' ? 'label-info' : 'label-warning'); ?>">
                            <?php echo htmlspecialchars($paiement['section']); ?>
                          </span>
                        </td>
                        <td><?php echo htmlspecialchars($paiement['frais_description']); ?></td>
                        <td>
                          <strong class="text-green"><?php echo number_format($paiement['amount_paid'], 2, ',', ' '); ?> $</strong>
                        </td>
                        <td>
                          <small><?php echo date('d/m/Y', strtotime($paiement['payment_date'])); ?></small>
                        </td>
                        <td>
                          <span class="label label-default"><?php echo htmlspecialchars($paiement['mois']); ?></span>
                        </td>
                        <td class="no-print">
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=recu&paiement_id=<?php echo $paiement['id']; ?>" 
                               class="btn btn-info btn-xs" target="_blank" title="Voir le reçu">
                              <i class="fa fa-print"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=modifierPaiement&id=<?php echo $paiement['id']; ?>" 
                               class="btn btn-warning btn-xs" title="Modifier">
                              <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-danger btn-xs" 
                                    onclick="confirmDelete(<?php echo $paiement['id']; ?>)" title="Supprimer">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
  
  <!-- Modal de confirmation de suppression -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">
            <i class="fa fa-warning text-red"></i> Confirmation de suppression
          </h4>
        </div>
        <div class="modal-body">
          <div class="callout callout-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Attention!</h4>
            <p>Êtes-vous sûr de vouloir supprimer ce paiement ?</p>
            <p><strong>Cette action est irréversible et supprimera définitivement l'enregistrement du paiement.</strong></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times"></i> Annuler
          </button>
          <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
            <i class="fa fa-trash"></i> Supprimer définitivement
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript Dependencies -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialiser DataTables avec configuration avancée
  var table = $('#paiementsTable').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'responsive': true,
    'pageLength': 25,
    'lengthMenu': [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
    'order': [[9, 'desc']], // Trier par date de paiement (colonne 9) en ordre décroissant
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
    },
    'columnDefs': [
      { 'orderable': false, 'targets': [11] }, // Désactiver le tri sur la colonne Actions
      { 'searchable': false, 'targets': [0, 11] } // Désactiver la recherche sur # et Actions
    ]
  });
  
  // Fonction de recherche dynamique personnalisée
  $("#searchInput").on("keyup", function() {
    table.search(this.value).draw();
  });
  
  // Appliquer les filtres
  $('#applyFilter').click(function() {
    var classeFilter = $('#filterClasse').val();
    var sectionFilter = $('#filterSection').val();
    var fraisFilter = $('#filterFrais').val();
    var moisFilter = $('#filterMois').val();
    
    // Réinitialiser la recherche
    table.search('').columns().search('').draw();
    
    // Appliquer les filtres sur les colonnes appropriées
    if (classeFilter) {
      table.column(4).search('^' + classeFilter + '$', true, false).draw(); // Recherche exacte pour la classe
    }
    if (sectionFilter) {
      table.column(6).search(sectionFilter).draw(); // Colonne Section
    }
    if (fraisFilter) {
      table.column(7).search(fraisFilter).draw(); // Colonne Type de frais
    }
    if (moisFilter) {
      table.column(10).search('^' + moisFilter + '$', true, false).draw(); // Recherche exacte pour le mois
    }
  });
  
  // Réinitialiser les filtres
  $('#resetFilter').click(function() {
    $('#filterClasse').val('');
    $('#filterSection').val('');
    $('#filterFrais').val('');
    $('#filterMois').val('');
    table.search('').columns().search('').draw();
  });
  
  // Gestion des messages d'URL
  var urlParams = new URLSearchParams(window.location.search);
  var success = urlParams.get('success');
  var error = urlParams.get('error');
  var message = urlParams.get('message');
  
  if (success) {
    showAlert(decodeURIComponent(message || 'Opération réussie!'), 'success');
  } else if (error) {
    showAlert(decodeURIComponent(message || 'Une erreur est survenue!'), 'danger');
  }
  
  // Auto-hide alerts after 5 seconds
  setTimeout(function() {
    $('.alert').fadeOut('slow');
  }, 5000);
});

// Fonction pour afficher la boîte de dialogue de confirmation de suppression
function confirmDelete(id) {
  if (!id) {
    showAlert('ID du paiement manquant. Impossible de supprimer.', 'danger');
    return;
  }
  $('#confirmDeleteBtn').attr('href', '<?php echo BASE_URL; ?>index.php?controller=Admin&action=supprimerPaiement&id=' + id);
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

// Fonction pour imprimer avec style personnalisé
window.addEventListener('beforeprint', function() {
  document.body.classList.add('print-mode');
});

window.addEventListener('afterprint', function() {
  document.body.classList.remove('print-mode');
});
</script>

<style>
/* Styles pour l'impression */
@media print {
  .no-print, .no-print * {
    display: none !important;
  }
  
  .table-responsive {
    overflow: visible !important;
  }
  
  .table {
    font-size: 12px;
  }
  
  .table th, .table td {
    padding: 4px !important;
  }
  
  .box {
    border: none !important;
    box-shadow: none !important;
  }
  
  .content-header {
    border-bottom: 2px solid #000;
    margin-bottom: 20px;
  }
}

/* Styles personnalisés pour une meilleure présentation */
.payment-summary {
  margin-bottom: 20px;
}

.info-box {
  border-radius: 5px;
  transition: all 0.3s ease;
}

.info-box:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}

.btn-group .btn {
  margin-right: 2px;
}

.label {
  font-size: 85%;
  padding: 0.2em 0.6em 0.3em;
}

.callout {
  border-radius: 5px;
  margin: 20px 0;
  padding: 15px 30px 15px 15px;
  border-left: 5px solid #eee;
}

.callout-danger {
  border-left-color: #d73925;
  background-color: #fcf2f2;
}
</style>

</body>
</html>

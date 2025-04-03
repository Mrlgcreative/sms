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
  <style>
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      .content-wrapper, .main-footer {
        margin-left: 0 !important;
      }
    }
    .payment-summary {
      background-color: #f9f9f9;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .payment-summary h4 {
      margin-top: 0;
      color: #3c8dbc;
    }
    .payment-summary .info-box {
      min-height: 80px;
      margin-bottom: 10px;
    }
    .payment-summary .info-box-icon {
      height: 80px;
      width: 80px;
      line-height: 80px;
    }
    .payment-summary .info-box-content {
      padding-top: 10px;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
              <!-- Reste du menu déroulant -->
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
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

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
                          <?php
                    // Get unique classes from the paiements array
                    $classes = [];
                    foreach ($paiements as $paiement) {
                      if (!empty($paiement['classe']) && !in_array($paiement['classe'], $classes)) {
                        $classes[] = $paiement['classe'];
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
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Mois</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterMois">
                          <option value="">Tous les mois</option>
                          <?php $months = [];
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
                  <div class="col-md-4">
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
                  <div class="col-md-8 text-center">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportPaiementsPDF" class="btn btn-danger"><i class="fa fa-file-pdf-o"></i> Exporter PDF</a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement" class="btn btn-info pull-right"><i class="fa fa-plus"></i> Nouveau paiement</a>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="paiementsTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                    
                            <th>#</th>
                            <th>ID</th>
                            <th>Nom de l'élève</th>
                            <th>Classe</th>
                            <th>Option</th>
                            <th>Section</th>
                            <th>Frais</th>
                            <th>Montant Payé</th>
                            <th>Date de Paiement</th>
                            <th>Mois</th>
                            <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($paiements as  $index => $paiement) : ?>
                      <tr>
                      <td><?php echo $index + 1; ?></td>
                                <td><?php echo $paiement['id']; ?></td>
                                <td><?php echo $paiement['eleve_nom']; ?></td>
                                <td><?php echo $paiement['classe']; ?></td>
                                <td><?php echo $paiement['option_nom']?></td>
                                <td><?php echo $paiement['section']; ?></td>
                                <td><?php echo $paiement['frais_description']; ?></td>
                                <td><?php echo $paiement['amount_paid']; ?></td>
                                <td><?php echo $paiement['payment_date']; ?></td>
                                <td><?php echo $paiement['mois']; ?></td>
                        <td class="no-print">
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=recu&paiement_id=<?php echo isset($paiement['id']) ? $paiement['id'] : ''; ?>" class="btn btn-info btn-xs" target="_blank"><i class="fa fa-print"></i> Reçu</a>
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifierPaiement&id=<?php echo isset($paiement['id']) ? $paiement['id'] : ''; ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Modifier</a>
                          <button type="button" class="btn btn-danger btn-xs" onclick="confirmDelete(<?php echo isset($paiement['id']) ? $paiement['id'] : '0'; ?>)"><i class="fa fa-trash"></i> Supprimer</button>
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
      var sectionFilter = $('#filterSection').val();
      
      // Réinitialiser la recherche
      table.search('').columns().search('').draw();
      
      // Appliquer les filtres
      if (classeFilter) {
        table.column(3).search(classeFilter).draw(); // Colonne Classe (index 3)
      }
      if (moisFilter) {
        table.column(9).search(moisFilter).draw(); // Colonne Mois (index 9)
      }
      if (fraisFilter) {
        table.column(6).search(fraisFilter).draw(); // Colonne Frais (index 6)
      }
      if (sectionFilter) {
        table.column(5).search(sectionFilter).draw(); // Colonne Section (index 5)
      }
    });
    
    // Réinitialiser les filtres
    $('#resetFilter').click(function() {
      $('#filterClasse').val('');
      $('#filterMois').val('');
      $('#filterFrais').val('');
      $('#filterSection').val('');
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
    if (!id) {
      showAlert('ID du paiement manquant. Impossible de supprimer.', 'danger');
      return;
    }
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
</body>
</html>
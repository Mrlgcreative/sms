<?php
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
  <title>St Sophie | Rapport d'actions</title>
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
    .action-summary {
      background-color: #f9f9f9;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .action-summary h4 {
      margin-top: 0;
      color: #3c8dbc;
    }
    .action-summary .info-box {
      min-height: 80px;
      margin-bottom: 10px;
    }
    .action-summary .info-box-icon {
      height: 80px;
      width: 80px;
      line-height: 80px;
    }
    .action-summary .info-box-content {
      padding-top: 10px;
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
        Rapport d'actions
        <small>Historique des actions effectuées</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Rapport d'actions</li>
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
      
      <!-- Résumé des actions -->
      <div class="row action-summary">
        <div class="col-md-12">
          <h4><i class="fa fa-bar-chart"></i> Résumé des actions</h4>
        </div>
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-plus"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Ajouts</span>
              <span class="info-box-number"><?php echo isset($stats['ajouts']) ? $stats['ajouts'] : 0; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-edit"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Modifications</span>
              <span class="info-box-number"><?php echo isset($stats['modifications']) ? $stats['modifications'] : 0; ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-trash"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Suppressions</span>
              <span class="info-box-number"><?php echo isset($stats['suppressions']) ? $stats['suppressions'] : 0; ?></span>
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
                      <label class="col-sm-4 control-label">Type d'action</label>
                      <div class="col-sm-8">
                        <select class="form-control" id="filterAction">
                          <option value="">Toutes les actions</option>
                          <option value="add">Ajout</option>
                          <option value="update">Modification</option>
                          <option value="delete">Suppression</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Date début</label>
                      <div class="col-sm-8">
                        <input type="date" class="form-control" id="filterDateDebut">
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label class="col-sm-4 control-label">Date fin</label>
                      <div class="col-sm-8">
                        <input type="date" class="form-control" id="filterDateFin">
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
      
      <!-- Liste des actions -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Historique des actions</h3>
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
              <?php 
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if (isset($_SESSION['backup_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['backup_status'] == 'success' ? 'success' : ($_SESSION['backup_status'] == 'warning' ? 'warning' : 'danger'); ?> alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-<?php echo $_SESSION['backup_status'] == 'success' ? 'check' : ($_SESSION['backup_status'] == 'warning' ? 'exclamation-triangle' : 'ban'); ?>"></i> <?php echo ucfirst($_SESSION['backup_status']); ?>!</h4>
                    <?php echo $_SESSION['backup_message']; ?>
                </div>
                <?php 
                    unset($_SESSION['backup_message']);
                    unset($_SESSION['backup_status']);
                endif; 
              ?>
              <div class="row no-print" style="margin-bottom: 15px;">
                <div class="col-xs-12">
                  <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportLogs" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=backupDatabase" class="btn btn-warning"><i class="fa fa-database"></i> Backup Database</a>
                </div>
              </div>
              
              <table class="table table-bordered" id="actionsTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if(isset($logs) && is_array($logs)): ?>
                    <?php foreach($logs as $index => $log): ?>
                      <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                        <td>
                          <?php 
                            $actionType = '';
                            if(strpos(strtolower($log['action']), 'ajout') !== false || strpos(strtolower($log['action']), 'add') !== false) {
                              $actionType = '<span class="label label-success">Ajout</span>';
                            } elseif(strpos(strtolower($log['action']), 'modif') !== false || strpos(strtolower($log['action']), 'update') !== false) {
                              $actionType = '<span class="label label-warning">Modification</span>';
                            } elseif(strpos(strtolower($log['action']), 'suppr') !== false || strpos(strtolower($log['action']), 'delete') !== false) {
                              $actionType = '<span class="label label-danger">Suppression</span>';
                            } else {
                              $actionType = '<span class="label label-info">Autre</span>';
                            }
                            echo $actionType;
                          ?>
                        </td>
                        <td><?php echo htmlspecialchars($log['action']); ?></td>
                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['date'])); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="text-center">Aucune action enregistrée</td>
                    </tr>
                  <?php endif; ?>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
</body>
</html>
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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Utilisateurs</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- CSS Dependencies -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <style>
    /* Styles personnalisés pour une interface moderne */
    .content-wrapper {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: calc(100vh - 50px);
    }

    .box {
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border: none;
      overflow: hidden;
    }

    .box-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px;
    }

    .box-header h3 {
      margin: 0;
      font-weight: 600;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 5px;
      padding: 10px 20px;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-warning {
      background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
      border: none;
      color: white;
    }

    .btn-danger {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
      border: none;
    }

    .table {
      margin-bottom: 0;
    }

    .table-hover tbody tr:hover {
      background-color: rgba(102, 126, 234, 0.1);
    }

    .badge {
      padding: 5px 10px;
      border-radius: 15px;
    }

    .badge-success {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .badge-warning {
      background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .badge-danger {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
    }

    @media print {
      .no-print {
        display: none;
      }
      .main-footer {
        display: none;
      }
      .breadcrumb {
        display: none;
      }
    }
  </style>
</head>

<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système de Gestion Scolaire</b></span>
    </a>

    <!-- Header Navbar -->
    <?php include 'navbar.php'; ?>
  </header>

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <?php include 'sidebar.php'; ?>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Gestion des Utilisateurs
        <small>Liste et gestion des utilisateurs du système</small>
      </h1>
      <ol class="breadcrumb no-print">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Utilisateurs</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      
      <!-- Messages d'alerte -->
      <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo htmlspecialchars($_GET['message'] ?? 'Opération réussie'); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo htmlspecialchars($_GET['message'] ?? 'Une erreur est survenue'); ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">
                <i class="fa fa-users"></i> Liste des Utilisateurs
              </h3>
              <div class="box-tools pull-right no-print">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutUsers" class="btn btn-primary">
                  <i class="fa fa-plus"></i> Ajouter un utilisateur
                </a>
              </div>
            </div>

            <div class="box-body">
              <div class="table-responsive">
                <table id="usersTable" class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Nom d'utilisateur</th>
                      <th>Email</th>
                      <th>Nom complet</th>
                      <th>Rôle</th>
                      <th>Statut</th>
                      <th>Date de création</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (isset($users) && !empty($users)): ?>
                      <?php foreach ($users as $user): ?>
                        <tr>
                          <td><?php echo htmlspecialchars($user['id']); ?></td>
                          <td>
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                          </td>
                          <td><?php echo htmlspecialchars($user['email']); ?></td>
                          <td>
                            <?php 
                            $fullName = trim(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? ''));
                            echo htmlspecialchars($fullName ?: 'Non renseigné');
                            ?>
                          </td>
                          <td>
                            <?php
                            $roleClass = 'badge-info';
                            switch(strtolower($user['role'])) {
                              case 'admin':
                                $roleClass = 'badge-danger';
                                break;
                              case 'directeur':
                              case 'directrice':
                                $roleClass = 'badge-warning';
                                break;
                              case 'comptable':
                                $roleClass = 'badge-success';
                                break;
                              case 'prefet':
                                $roleClass = 'badge-primary';
                                break;
                            }
                            ?>
                            <span class="badge <?php echo $roleClass; ?>">
                              <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                            </span>
                          </td>
                          <td>
                            <?php
                            $status = $user['status'] ?? 'active';
                            $statusClass = $status === 'active' ? 'badge-success' : 'badge-danger';
                            $statusText = $status === 'active' ? 'Actif' : 'Inactif';
                            ?>
                            <span class="badge <?php echo $statusClass; ?>">
                              <?php echo $statusText; ?>
                            </span>
                          </td>
                          <td>
                            <?php 
                            $dateCreation = $user['created_at'] ?? $user['date_creation'] ?? date('Y-m-d H:i:s');
                            echo date('d/m/Y à H:i', strtotime($dateCreation)); 
                            ?>
                          </td>
                          <td class="no-print">
                            <div class="btn-group">
                              <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editUser&id=<?php echo $user['id']; ?>" 
                                 class="btn btn-sm btn-warning" title="Modifier">
                                <i class="fa fa-edit"></i>
                              </a>
                              <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')"
                                        title="Supprimer">
                                  <i class="fa fa-trash"></i>
                                </button>
                              <?php endif; ?>
                            </div>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">
                          <i class="fa fa-info-circle"></i> Aucun utilisateur trouvé
                        </td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Footer -->
  <footer class="main-footer no-print">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialize DataTable
  $('#usersTable').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
    },
    "responsive": true,
    "paging": true,
    "lengthChange": true,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "pageLength": 25,
    "order": [[ 0, "desc" ]]
  });
});

// Fonction pour confirmer la suppression
function confirmDelete(userId, username) {
  if (confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur "' + username + '" ?\n\nCette action est irréversible.')) {
    window.location.href = '<?php echo BASE_URL; ?>index.php?controller=Admin&action=deleteUser&id=' + userId;
  }
}
</script>

</body>
</html>

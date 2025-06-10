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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Sessions Scolaires</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="bower_components/jvectormap/jquery-jvectormap.css">
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="bower_components/bootstrap-daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

   <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Sessions Scolaires
        <small>Gestion des années académiques</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Sessions Scolaires</li>
      </ol>
      
      <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-check"></i> Succès!</h4>
        <?php echo htmlspecialchars($_GET['message']); ?>
      </div>
      <?php endif; ?>
      
      <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
        <?php echo htmlspecialchars($_GET['message']); ?>
      </div>
      <?php endif; ?>
    </section>

    <section class="content">
      <!-- Contenu principal pour la gestion des sessions scolaires -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Liste des Sessions Scolaires</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSessionModal">
                  <i class="fa fa-plus"></i> Ajouter une session
                </button>
              </div>
            </div>
            <div class="box-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Année Début</th>
                    <th>Année Fin</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($sessions as $session): ?>
                  <tr>
                    <td><?php echo $session['id']; ?></td>
                    <td><?php echo $session['libelle']; ?></td>
                    <td><?php echo $session['annee_debut']; ?></td>
                    <td><?php echo $session['annee_fin']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($session['date_debut'])); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($session['date_fin'])); ?></td>
                    <td>
                      <?php if ($session['est_active']): ?>
                        <span class="label label-success">Active</span>
                      <?php else: ?>
                        <span class="label label-default">Inactive</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#editSessionModal" 
                              data-id="<?php echo $session['id']; ?>" 
                              data-libelle="<?php echo $session['libelle']; ?>"
                              data-debut="<?php echo $session['date_debut']; ?>"
                              data-fin="<?php echo $session['date_fin']; ?>">
                        <i class="fa fa-edit"></i> Modifier
                      </button>
                      <?php if (!$session['est_active']): ?>
                      <button type="button" class="btn btn-success btn-xs activate-session" data-id="<?php echo $session['id']; ?>">
                        <i class="fa fa-check"></i> Activer
                      </button>
                      <?php endif; ?>
                      <?php if (!$session['est_active']): ?>
                      <button type="button" class="btn btn-danger btn-xs delete-session" data-id="<?php echo $session['id']; ?>">
                        <i class="fa fa-trash"></i> Supprimer
                      </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Section pour les archives -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Archives des Sessions</h3>
            </div>
            <div class="box-body">
              <?php
              $archive_dir = 'archives/sessions/';
              if (file_exists($archive_dir) && is_dir($archive_dir)) {
                  $archives = scandir($archive_dir);
                  $archives = array_diff($archives, array('.', '..'));
                  
                  if (count($archives) > 0) {
                      echo '<div class="list-group">';
                      foreach ($archives as $archive) {
                          echo '<a href="' . BASE_URL . 'index.php?controller=comptable&action=viewArchive&session=' . $archive . '" class="list-group-item">';
                          echo '<i class="fa fa-folder-open"></i> Session ' . $archive;
                          echo '</a>';
                      }
                      echo '</div>';
                  } else {
                      echo '<p>Aucune archive disponible.</p>';
                  }
              } else {
                  echo '<p>Le répertoire d\'archives n\'existe pas encore.</p>';
              }
              ?>
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

<!-- Modal pour ajouter une session -->
<div class="modal fade" id="addSessionModal" tabindex="-1" role="dialog" aria-labelledby="addSessionModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addSessionModalLabel">Ajouter une nouvelle session scolaire</h4>
      </div>
      <form action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajouterSession" method="post">
        <div class="modal-body">
          <div class="form-group">
            <label for="libelle">Libellé de la session</label>
            <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Ex: 2023-2024" required>
          </div>
          <div class="form-group">
            <label for="date_debut">Date de début</label>
            <input type="date" class="form-control" id="date_debut" name="date_debut" required>
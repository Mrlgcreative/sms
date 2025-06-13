<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Présence Professeur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php  include 'navbar.php'; ?>
 <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des présences
        <small>Professeur: <?php echo htmlspecialchars($professeur['nom'] . ' ' . $professeur['prenom']); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs">Professeurs</a></li>
        <li class="active">Présence</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php 
            echo $_SESSION['success']; 
            unset($_SESSION['success']);
          ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php 
            echo $_SESSION['error']; 
            unset($_SESSION['error']);
          ?>
        </div>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations du professeur</h3>
            </div>
            <div class="box-body box-profile">
              <h3 class="profile-username text-center"><?php echo htmlspecialchars($professeur['nom'] . ' ' . $professeur['prenom']); ?></h3>
              <p class="text-muted text-center">Professeur - Section <?php echo htmlspecialchars($professeur['section']); ?></p>
              
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['email']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['telephone'] ?? 'Non renseigné'); ?></a>
                </li>
              </ul>
              
              <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=voirProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-primary btn-block"><b>Voir profil complet</b></a>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Enregistrer une présence</h3>
            </div>
            <form class="form-horizontal" method="post">
              <div class="box-body">
                <div class="form-group">
                  <label for="date" class="col-sm-2 control-label">Date</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right datepicker" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="status" class="col-sm-2 control-label">Statut</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="status" name="status">
                      <option value="present">Présent</option>
                      <option value="absent">Absent</option>
                      <option value="retard">En retard</option>
                      <option value="excuse">Absence excusée</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="commentaire" class="col-sm-2 control-label">Commentaire</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Commentaire optionnel..."></textarea>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=professeurs" class="btn btn-default">Annuler</a>
                <button type="submit" class="btn btn-info pull-right">Enregistrer</button>
              </div>
            </form>
          </div>
          
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Historique des présences</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Commentaire</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($presences)): ?>
                    <tr>
                      <td colspan="3" class="text-center">Aucun enregistrement de présence trouvé.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($presences as $presence): ?>
                      <tr>
                        <td><?php echo date('d/m/Y', strtotime($presence['date'])); ?></td>
                        <td>
                          <?php 
                            switch ($presence['status']) {
                              case 'present':
                                echo '<span class="label label-success">Présent</span>';
                                break;
                              case 'absent':
                                echo '<span class="label label-danger">Absent</span>';
                                break;
                              case 'retard':
                                echo '<span class="label label-warning">En retard</span>';
                                break;
                              case 'excuse':
                                echo '<span class="label label-info">Absence excusée</span>';
                                break;
                              default:
                                echo '<span class="label label-default">Inconnu</span>';
                            }
                          ?>
                        </td>
                        <td><?php echo htmlspecialchars($presence['commentaire'] ?? ''); ?></td>
                      </tr>
                    <?php endforeach; ?>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  });
</script>
</body>
</html>
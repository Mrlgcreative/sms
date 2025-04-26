<?php
// Vérifier si l'utilisateur est connecté et a les droits d'accès
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
    exit;
}

// Vérifier si la session existe
if (!isset($session) || !is_array($session)) {
    header('Location: ' . BASE_URL . 'index.php?controller=comptable&action=sessions');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Modifier une Session Scolaire</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- En-tête et barre latérale (inclure les mêmes que dans accueil.php) -->
  <?php include 'header.php'; ?>
  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Modifier une Session Scolaire
        <small>Mettre à jour les informations de la session</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions">Sessions Scolaires</a></li>
        <li class="active">Modifier</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-6">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations de la session</h3>
            </div>
            <form role="form" method="post" action="">
              <div class="box-body">
                <div class="form-group">
                  <label for="annee_debut">Année de début</label>
                  <input type="number" class="form-control" id="annee_debut" name="annee_debut" value="<?php echo htmlspecialchars($session['annee_debut']); ?>" required>
                </div>
                <div class="form-group">
                  <label for="annee_fin">Année de fin</label>
                  <input type="number" class="form-control" id="annee_fin" name="annee_fin" value="<?php echo htmlspecialchars($session['annee_fin']); ?>" required>
                </div>
                <div class="form-group">
                  <label for="libelle">Libellé</label>
                  <input type="text" class="form-control" id="libelle" name="libelle" value="<?php echo htmlspecialchars($session['libelle']); ?>" required>
                </div>
                <div class="form-group">
                  <label>Date de début:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="date" class="form-control pull-right" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($session['date_debut']); ?>" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Date de fin:</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="date" class="form-control pull-right" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($session['date_fin']); ?>" required>
                  </div>
                </div>
                <div class="form-group">
                  <label>Statut:</label>
                  <p>
                    <?php if ($session['est_active']): ?>
                      <span class="label label-success">Session active</span>
                    <?php else: ?>
                      <span class="label label-default">Session inactive</span>
                    <?php endif; ?>
                  </p>
                </div>
              </div>
              <div class="box-footer">
                <button type="submit" name="update_session" class="btn btn-primary">Mettre à jour</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions" class="btn btn-default">Annuler</a>
              </div>
            </form>
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

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Bootstrap datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min.js"></script>

<script>
  $(function () {
    //Date picker
    $('.datepicker').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd',
      language: 'fr'
    });
    
    // Auto-calcul du libellé
    $('#annee_debut, #annee_fin').change(function() {
      var debut = $('#annee_debut').val();
      var fin = $('#annee_fin').val();
      if (debut && fin) {
        $('#libelle').val(debut + '-' + fin);
      }
    });
  });
</script>
</body>
</html>
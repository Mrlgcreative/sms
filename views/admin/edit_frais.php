<?php
// Vérifiez si une session est déjà active avant d'appeler session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les clés si elles ne sont pas déjà définies
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = 'username';
}
if (!isset($_SESSION['email'])) {
  $_SESSION['email'] = 'email';
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = 'role';
}

// Récupérer les valeurs des clés
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];

// Récupérer le message d'erreur s'il existe
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
if (isset($_SESSION['error'])) {
  unset($_SESSION['error']);
}

// Récupérer le message de succès s'il existe
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
if (isset($_SESSION['success'])) {
  unset($_SESSION['success']);
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Modification de frais</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    /* Style pour les notifications toast */
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
    }
    
    .toast {
      min-width: 300px;
      margin-bottom: 10px;
      padding: 15px;
      border-radius: 4px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      animation: fadeIn 0.5s, fadeOut 0.5s 4.5s;
      opacity: 0;
      animation-fill-mode: forwards;
    }
    
    .toast-success {
      background-color: #00a65a;
      color: white;
    }
    
    .toast-error {
      background-color: #dd4b39;
      color: white;
    }
    
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(-20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    
    @keyframes fadeOut {
      from {opacity: 1;}
      to {opacity: 0; display: none;}
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Conteneur pour les notifications toast -->
<div class="toast-container" id="toast-container"></div>
<div class="wrapper">

  <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Modification de frais
        <small>Modifier les informations d'un frais</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais">Frais</a></li>
        <li class="active">Modifier</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- left column -->
        <div class="col-md-12">
          <!-- general form elements -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de modification</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editFrais&id=<?php echo $frais['id']; ?>">
              <div class="box-body">
                <div class="form-group">
                  <label for="montant">Montant</label>
                  <input type="number" step="0.01" class="form-control" id="montant" name="montant" placeholder="Entrez le montant" value="<?php echo $frais['montant']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <input type="text" class="form-control" id="description" name="description" placeholder="Entrez la description" value="<?php echo $frais['description']; ?>" required>
                </div>
                <div class="form-group">
                  <label for="section">Section</label>
                  <select class="form-control" id="section" name="section" required>
                    <option value="">-- Sélectionner une section --</option>
                    <option value="maternelle" <?php echo ($frais['section'] == 'maternelle') ? 'selected' : ''; ?>>Maternelle</option>
                    <option value="primaire" <?php echo ($frais['section'] == 'primaire') ? 'selected' : ''; ?>>Primaire</option>
                    <option value="secondaire" <?php echo ($frais['section'] == 'secondaire') ? 'selected' : ''; ?>>Secondaire</option>
                    <option value="toutes" <?php echo ($frais['section'] == 'toutes') ? 'selected' : ''; ?>>Toutes les sections</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (left) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function() {
    // Afficher les notifications toast
    function showToast(message, type) {
      var toast = $('<div class="toast toast-' + type + '"><span class="toast-close">&times;</span>' + message + '</div>');
      $('#toast-container').append(toast);
      
      // Fermer le toast au clic sur le bouton de fermeture
      toast.find('.toast-close').on('click', function() {
        toast.remove();
      });
      
      // Supprimer automatiquement le toast après 5 secondes
      setTimeout(function() {
        toast.remove();
      }, 5000);
    }
    
    // Afficher les messages d'erreur ou de succès sous forme de toast
    <?php if ($error_message): ?>
      showToast('<?php echo $error_message; ?>', 'error');
    <?php endif; ?>
    
    <?php if ($success_message): ?>
      showToast('<?php echo $success_message; ?>', 'success');
    <?php endif; ?>
  });
</script>
</body>
</html>
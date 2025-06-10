<?php
// Vérifiez si une session est déjà active avant d'appeler session_start()
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les clés si elles ne sont pas déjà définies
if (!isset($_SESSION['username'])) {
  $_SESSION['username'] = 'username';
}
if (!isset($_SESSION['role'])) {
  $_SESSION['role'] = 'role';
}
if (!isset($_SESSION['email'])) {
  $_SESSION['email'] = 'email';
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Admin | Modifier une directrice</title>
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
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
  <style>
    .toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
    }
    
    .toast {
      min-width: 300px;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 4px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      animation: fadeIn 0.5s ease-in-out;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .toast.hide {
      animation: fadeOut 0.5s ease-in-out;
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
        Modification d'une directrice
        <small>Modifier les informations d'une directrice</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices">Directrices</a></li>
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
            <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editDirectrice&id=<?php echo $directrice['id']; ?>">
              <div class="box-body">
                <?php if (isset($_SESSION['error'])): ?>
                  <div class="alert alert-danger">
                    <?php 
                      echo $_SESSION['error']; 
                      unset($_SESSION['error']);
                    ?>
                  </div>
                <?php endif; ?>
                
                <div class="form-group">
                  <label for="nom">Nom</label>
                  <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez le nom" value="<?php echo $directrice['nom']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="prenom">Prénom</label>
                  <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Entrez le prénom" value="<?php echo $directrice['prenom']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="contact">Contact</label>
                  <input type="text" class="form-control" id="contact" name="contact" placeholder="Entrez le numéro de téléphone" value="<?php echo $directrice['contact']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="email">Email</label>
                  <input type="email" class="form-control" id="email" name="email" placeholder="Entrez l'adresse email" value="<?php echo $directrice['email']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="adresse">Adresse</label>
                  <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Entrez l'adresse" value="<?php echo $directrice['adresse']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="section">Section</label>
                  <select class="form-control" id="section" name="section" required>
                    <option value="">Sélectionnez une section</option>
                    <option value="Maternelle" <?php echo ($directrice['section'] == 'Maternelle') ? 'selected' : ''; ?>>Maternelle</option>
                    <option value="Primaire" <?php echo ($directrice['section'] == 'Primaire') ? 'selected' : ''; ?>>Primaire</option>
                    <option value="Secondaire" <?php echo ($directrice['section'] == 'Secondaire') ? 'selected' : ''; ?>>Secondaire</option>
                  </select>
                </div>
              </div>
              <!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices" class="btn btn-default">Annuler</a>
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
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2023 <a href="#">École Sainte Sophie</a>.</strong> Tous droits réservés.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>

<script>
  // Fonction pour afficher une notification toast
  function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      ${message}
      <button type="button" class="close" onclick="this.parentElement.remove()">
        <span>&times;</span>
      </button>
    `;
    toastContainer.appendChild(toast);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
      toast.classList.add('hide');
      setTimeout(() => {
        toast.remove();
      }, 500);
    }, 5000);
  }
  
  // Afficher les messages de succès ou d'erreur s'ils existent
  <?php if (isset($_SESSION['success'])): ?>
    showToast('<?php echo $_SESSION['success']; ?>', 'success');
    <?php unset($_SESSION['success']); ?>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['error'])): ?>
    showToast('<?php echo $_SESSION['error']; ?>', 'error');
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>
</script>
</body>
</html>
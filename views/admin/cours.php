<?php
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Récupérer les informations de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupération des cours avec les noms des professeurs et des classes
$cours = [];
$query = "SELECT c.id, c.titre, c.description, c.section, c.option_, 
          p.nom as professeur_nom, cl.nom as classe_nom 
          FROM cours c 
          LEFT JOIN professeurs p ON c.professeur_id = p.id 
          LEFT JOIN classes cl ON c.classe_id = cl.id 
          ORDER BY c.id";
$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cours[] = $row;
    }
    $result->free();
}

// Gestion de la suppression d'un cours
if (isset($_GET['delete_id']) && !empty($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $delete_query = "DELETE FROM cours WHERE id = ?";
    $stmt = $mysqli->prepare($delete_query);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Cours supprimé avec succès.";
        $_SESSION['message_type'] = "success";
        
        // Rediriger pour éviter la soumission multiple
        header("Location: " . BASE_URL . "index.php?controller=Admin&action=cours");
        exit();
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression du cours: " . $mysqli->error;
        $_SESSION['message_type'] = "danger";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Cours</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print {
        display: none;
      }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Liste des Cours
        <small>Gestion des cours</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Cours</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Message</h4>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
      <?php endif; ?>
      
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Liste des cours</h3>
          <div class="box-tools">
            <button class="btn btn-primary no-print" onclick="printContent('printable-content')"><i class="fa fa-print"></i> Imprimer</button>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours" class="btn btn-success no-print"><i class="fa fa-plus"></i> Ajouter un cours</a>
          </div>
        </div>
        
        <div class="box-body" id="printable-content">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Titre</th>
                <th>Description</th>
                <th>Professeur</th>
                <th>Classe</th>
                <th>Section</th>
                <th>Option</th>
                <th class="no-print">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $index = 0;
              foreach ($cours as $cour): 
              ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td><?php echo $cour['titre']; ?></td>
                  <td><?php echo $cour['description']; ?></td>
                  <td><?php echo $cour['professeur_nom']; ?></td>
                  <td><?php echo $cour['classe_nom']; ?></td>
                  <td><?php echo $cour['section']; ?></td>
                  <td><?php echo $cour['option_']; ?></td>
                  <td class="no-print">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editcours&id=<?php echo $cour['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Modifier</a>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours&delete_id=<?php echo $cour['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours?');"><i class="fa fa-trash"></i> Supprimer</a>
                  </td>
                </tr>
              <?php 
              $index++;
              endforeach; 
              
              if (count($cours) == 0):
              ?>
                <tr>
                  <td colspan="8" class="text-center">Aucun cours trouvé</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<script>
  function printContent(elementId) {
    var content = document.getElementById(elementId).innerHTML;
    var originalContent = document.body.innerHTML;
    document.body.innerHTML = content;
    window.print();
    document.body.innerHTML = originalContent;
    location.reload();
  }
</script>
</body>
</html>
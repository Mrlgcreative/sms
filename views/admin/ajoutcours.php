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

// Récupérer la liste des professeurs
$professeurs = [];
$query_prof = "SELECT id, username FROM users WHERE role = 'professeur' ORDER BY username";
$result_prof = $mysqli->query($query_prof);
if ($result_prof) {
    while ($row = $result_prof->fetch_assoc()) {
        $professeurs[] = $row;
    }
    $result_prof->free();
}

// Récupérer la liste des classes
$classes = [];
$query_classe = "SELECT id, nom FROM classes ORDER BY nom";
$result_classe = $mysqli->query($query_classe);
if ($result_classe) {
    while ($row = $result_classe->fetch_assoc()) {
        $classes[] = $row;
    }
    $result_classe->free();
}

// Traitement du formulaire d'ajout de cours
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $professeur_id = isset($_POST['professeur_id']) ? intval($_POST['professeur_id']) : 0;
    $classe_id = isset($_POST['classe_id']) ? intval($_POST['classe_id']) : 0;
    $section = isset($_POST['section']) ? trim($_POST['section']) : '';
    $option = isset($_POST['option']) ? trim($_POST['option']) : '';
    
    // Validation des données
    $errors = [];
    
    if (empty($titre)) {
        $errors[] = "Le titre du cours est requis.";
    }
    
    if (empty($description)) {
        $errors[] = "La description du cours est requise.";
    }
    
    if ($professeur_id <= 0) {
        $errors[] = "Veuillez sélectionner un professeur valide.";
    }
    
    if ($classe_id <= 0) {
        $errors[] = "Veuillez sélectionner une classe valide.";
    }
    
    if (empty($section)) {
        $errors[] = "La section est requise.";
    }
    
    // Si aucune erreur, procéder à l'insertion
    if (empty($errors)) {
        $insert_query = "INSERT INTO cours (titre, description, professeur_id, classe_id, section, option_) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("ssiiss", $titre, $description, $professeur_id, $classe_id, $section, $option);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Cours ajouté avec succès.";
            $_SESSION['message_type'] = "success";
            
            // Rediriger vers la liste des cours
            header("Location: " . BASE_URL . "index.php?controller=Admin&action=cours");
            exit();
        } else {
            $errors[] = "Erreur lors de l'ajout du cours: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter un cours</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
<?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
          <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=reinscris">
            <i class="fa fa-refresh"></i> <span>Réinscriptions</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=parents">
            <i class="fa fa-users"></i> <span>Parents</span>
          </a>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addprefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="
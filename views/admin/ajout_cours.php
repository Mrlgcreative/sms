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
$query_prof = "SELECT id, nom, prenom FROM professeurs";  // Added id to the query
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
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Ajouter un cours
        <small>Formulaire d'ajout</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours">Cours</a></li>
        <li class="active">Ajouter</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?php echo $error; ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Informations du cours</h3>
        </div>
        
        <form role="form" method="POST" action="">
          <div class="box-body">
            <div class="form-group">
              <label for="titre">Titre du cours *</label>
              <input type="text" class="form-control" id="titre" name="titre" placeholder="Entrez le titre du cours" value="<?php echo isset($titre) ? $titre : ''; ?>" required>
            </div>
            
            <div class="form-group">
              <label for="description">Description *</label>
              <textarea class="form-control" id="description" name="description" rows="3" placeholder="Entrez la description du cours" required><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <div class="form-group">
              <label for="professeur_id">Professeur *</label>
              <select class="form-control" id="professeur_id" name="professeur_id" required>
                <option value="">Sélectionnez un professeur</option>
                <?php foreach ($professeurs as $professeur): ?>
                  <option value="<?php echo $professeur['id']; ?>" <?php echo (isset($professeur_id) && $professeur_id == $professeur['id']) ? 'selected' : ''; ?>>
                    <?php echo $professeur['nom'] . ' ' . $professeur['prenom']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="classe_id">Classe *</label>
              <select class="form-control" id="classe_id" name="classe_id" required>
                <option value="">Sélectionnez une classe</option>
                <?php foreach ($classes as $classe): ?>
                  <option value="<?php echo $classe['id']; ?>" <?php echo (isset($classe_id) && $classe_id == $classe['id']) ? 'selected' : ''; ?>>
                    <?php echo $classe['nom']; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="section">Section *</label>
              <input type="text" class="form-control" id="section" name="section" placeholder="Entrez la section" value="<?php echo isset($section) ? $section : ''; ?>" required>
            </div>
            
            <div class="form-group">
              <label for="option">Option</label>
              <input type="text" class="form-control" id="option" name="option" placeholder="Entrez l'option (facultatif)" value="<?php echo isset($option) ? $option : ''; ?>">
            </div>
            
            <div class="form-group">
              <label for="coefficient">Coefficient *</label>
              <input type="number" class="form-control" id="coefficient" name="coefficient" placeholder="Entrez le coefficient du cours" value="<?php echo isset($coefficient) ? $coefficient : '1'; ?>" min="1" max="10" required>
              <p class="help-block">Valeur entre 1 et 10</p>
            </div>
            
            <div class="form-group">
              <label for="heures_semaine">Heures par semaine *</label>
              <input type="number" class="form-control" id="heures_semaine" name="heures_semaine" placeholder="Entrez le nombre d'heures par semaine" value="<?php echo isset($heures_semaine) ? $heures_semaine : '2'; ?>" min="1" max="20" required>
              <p class="help-block">Valeur entre 1 et 20</p>
            </div>
          </div>
          
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Ajouter</button>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours" class="btn btn-default">Annuler</a>
          </div>
        </form>
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
</body>
</html>
<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Traitement du formulaire d'ajout d'employé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';
    $email_employe = isset($_POST['email']) ? trim($_POST['email']) : '';
    $adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
    $poste = isset($_POST['poste']) ? trim($_POST['poste']) : '';
    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = "Le nom est obligatoire.";
    }
    
    if (empty($prenom)) {
        $errors[] = "Le prénom est obligatoire.";
    }
    
    if (empty($contact)) {
        $errors[] = "Le contact est obligatoire.";
    }
    
    if (empty($email_employe)) {
        $errors[] = "L'email est obligatoire.";
    } elseif (!filter_var($email_employe, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide.";
    }
    
    // Vérifier si l'email existe déjà
    $check_query = "SELECT id FROM employes WHERE email = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $email_employe);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $errors[] = "Cet email existe déjà.";
    }
    
    // Si aucune erreur, insérer l'employé dans la base de données
    if (empty($errors)) {
        // Préparation de la requête d'insertion
        $insert_query = "INSERT INTO employes (nom, prenom, contact, email, adresse, poste, created_at) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $mysqli->prepare($insert_query);
        $stmt->bind_param("ssssss", $nom, $prenom, $contact, $email_employe, $adresse, $poste);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Employé ajouté avec succès.";
            $_SESSION['message_type'] = "success";
            
            // Rediriger vers la liste des employés
            header("Location: " . BASE_URL . "index.php?controller=Admin&action=employes");
            exit();
        } else {
            $errors[] = "Erreur lors de l'ajout de l'employé: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter un employé</title>
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
        Ajouter un employé
        <small>Formulaire d'ajout</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes">Employés</a></li>
        <li class="active">Ajouter</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations de l'employé</h3>
            </div>
            
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
            
            <form role="form" method="post" action="">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="nom">Nom <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez le nom" value="<?php echo isset($nom) ? $nom : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                      <label for="prenom">Prénom <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Entrez le prénom" value="<?php echo isset($prenom) ? $prenom : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                      <label for="contact">Contact <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="contact" name="contact" placeholder="Entrez le numéro de téléphone" value="<?php echo isset($contact) ? $contact : ''; ?>" required>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Email <span class="text-danger">*</span></label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Entrez l'adresse email" value="<?php echo isset($email_employe) ? $email_employe : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                      <label for="adresse">Adresse</label>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Entrez l'adresse" value="<?php echo isset($adresse) ? $adresse : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                      <label for="poste">Poste</label>
                      <input type="text" class="form-control" id="poste" name="poste" placeholder="Entrez le poste" value="<?php echo isset($poste) ? $poste : ''; ?>">
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes" class="btn btn-default">Annuler</a>
              </div>
            </form>
          </div>
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

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo BASE_URL; ?>dist/js/demo.js"></script>
</body>
</html>
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

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer la liste des classes pour le formulaire
$classes = [];
$query = "SELECT * FROM classes ORDER BY nom";
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    $result->free();
}

// Récupérer la liste des cours pour le formulaire
$cours = [];
$query = "SELECT id, titre FROM cours ORDER BY titre";
$result = $mysqli->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cours[] = $row;
    }
    $result->free();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter Professeur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .form-container {
      padding: 20px;
      background-color: #f9f9f9;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 20px;
    }
    .btn-submit {
      background-color: #3c8dbc;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    .btn-submit:hover {
      background-color: #367fa9;
    }
    select.form-control {
      height: 34px;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL . $image; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo $username; ?> - <?php echo $role; ?>
                  <small><?php echo $email; ?></small>
                </p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <aside class="main-sidebar">
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
        
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
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
          <ul class="treeview-menu">
           
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutemployes"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Ajouter un Professeur
        <small>Gestion des professeurs</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs">Professeurs</a></li>
        <li class="active">Ajouter Professeur</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'ajout de professeur</h3>
            </div>
            
            <?php if(isset($_SESSION['message'])): ?>
              <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check' : 'ban'; ?>"></i> Alerte!</h4>
                <?php echo $_SESSION['message']; ?>
              </div>
              <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>
            
            <form action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutProfesseur" method="POST" class="form-horizontal" enctype="multipart/form-data">
              <div class="box-body">
                <div class="form-group">
                  <label for="nom" class="col-sm-3 control-label">Nom</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom du professeur" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="prenom" class="col-sm-3 control-label">Prénom</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-user"></i></span>
                      <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Prénom du professeur" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="contact" class="col-sm-3 control-label">Contact</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                      <input type="text" class="form-control" id="contact" name="contact" placeholder="Numéro de téléphone" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="email" class="col-sm-3 control-label">Email</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Adresse email" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="adresse" class="col-sm-3 control-label">Adresse</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                      <input type="text" class="form-control" id="adresse" name="adresse" placeholder="Adresse du professeur" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="classe_id" class="col-sm-3 control-label">Classe</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-users"></i></span>
                      <select class="form-control" id="classe_id" name="classe_id" required>
                        <option value="">Sélectionnez une classe</option>
                        <?php foreach ($classes as $classe): ?>
                          <option value="<?php echo $classe['id']; ?>"><?php echo $classe['nom']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="cours_id" class="col-sm-3 control-label">Cours</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-book"></i></span>
                      <select class="form-control" id="cours_id" name="cours_id" required>
                        <option value="">Sélectionnez un cours</option>
                        <?php foreach ($cours as $cour): ?>
                          <option value="<?php echo $cour['id']; ?>"><?php echo $cour['titre']; ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="section" class="col-sm-3 control-label">Section</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-building"></i></span>
                      <select class="form-control" id="section" name="section" required>
                        <option value="">Sélectionnez une section</option>
                        <option value="maternelle">Maternelle</option>
                        <option value="primaire">Primaire</option>
                        <option value="secondaire">Secondaire</option>
                      </select>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="photo" class="col-sm-3 control-label">Photo</label>
                  <div class="col-sm-9">
                    <div class="input-group">
                      <span class="input-group-addon"><i class="fa fa-image"></i></span>
                      <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    </div>
                    <p class="help-block">Format recommandé: JPG, PNG (max 2MB)</p>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs" class="btn btn-default">Annuler</a>
                <button type="submit" class="btn btn-primary pull-right">Ajouter</button>
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
<script>
  $(document).ready(function() {
    // Validation du formulaire
    $('form').submit(function(e) {
      var nom = $('#nom').val();
      var prenom = $('#prenom').val();
      var contact = $('#contact').val();
      var email = $('#email').val();
      var adresse = $('#adresse').val();
      var classe_id = $('#classe_id').val();
      var cours_id = $('#cours_id').val();
      var section = $('#section').val();
      
      if (nom.trim() === '') {
        alert('Veuillez entrer un nom');
        e.preventDefault();
        return false;
      }
      
      if (prenom.trim() === '') {
        alert('Veuillez entrer un prénom');
        e.preventDefault();
        return false;
      }
      
      if (contact.trim() === '') {
        alert('Veuillez entrer un numéro de contact');
        e.preventDefault();
        return false;
      }
      
      if (email.trim() === '') {
        alert('Veuillez entrer une adresse email');
        e.preventDefault();
        return false;
      }
      
      if (adresse.trim() === '') {
        alert('Veuillez entrer une adresse');
        e.preventDefault();
        return false;
      }
      
      if (classe_id === '') {
        alert('Veuillez sélectionner une classe');
        e.preventDefault();
        return false;
      }
      
      if (cours_id === '') {
        alert('Veuillez sélectionner un cours');
        e.preventDefault();
        return false;
      }
      
      if (section === '') {
        alert('Veuillez sélectionner une section');
        e.preventDefault();
        return false;
      }
      
      return true;
    });
  });
</script>
</body>
</html>
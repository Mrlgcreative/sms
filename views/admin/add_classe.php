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

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer les professeurs pour le champ titulaire
$professeurs = [];
$query_prof = "SELECT id, nom, prenom FROM professeurs ORDER BY nom, prenom";
$result_prof = $mysqli->query($query_prof);
if ($result_prof) {
    while ($row = $result_prof->fetch_assoc()) {
        $professeurs[] = $row;
    }
    $result_prof->free();
}

// Traitement du formulaire d'ajout de classe
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $mysqli->real_escape_string($_POST['nom']);
    $niveau = $mysqli->real_escape_string($_POST['niveau']);
    $section = $mysqli->real_escape_string($_POST['section']);
    $titulaire = $mysqli->real_escape_string($_POST['titulaire']);

    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) {
        $errors[] = "Le nom de la classe est requis.";
    }
    
    if (empty($niveau)) {
        $errors[] = "Le niveau est requis.";
    }
    
    if (empty($section)) {
        $errors[] = "La section est requise.";
    }
    
    // Si pas d'erreurs, insérer dans la base de données
    if (empty($errors)) {
        $query = "INSERT INTO classes (nom, niveau, section, titulaire, prof_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ssssi", $nom, $niveau, $section, $titulaire, $prof_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Classe ajoutée avec succès.";
            $_SESSION['message_type'] = "success";
            
            // Rediriger vers la liste des classes
            header("Location: " . BASE_URL . "index.php?controller=Admin&action=classes");
            exit();
        } else {
            $errors[] = "Erreur lors de l'ajout de la classe: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter une classe</title>
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
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addPrefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
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

        <li class="treeview active">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
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
        Gestion des Classes
        <small>Ajouter une classe</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes">Classes</a></li>
        <li class="active">Ajouter</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire d'ajout de classe</h3>
            </div>
            
            <?php if(isset($errors) && !empty($errors)): ?>
              <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
                <ul>
                  <?php foreach($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            
            <form role="form" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse" class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="nom" class="col-sm-2 control-label">Nom de la classe</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" id="nom" name="nom" placeholder="Nom de la classe" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                    <p class="help-block">Exemple: 6ème A, CM2, Terminale S, etc.</p>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="niveau" class="col-sm-2 control-label">Niveau</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="niveau" name="niveau" required>
                      <option value="">Sélectionnez un niveau</option>
                      <option value="8eme">8eme</option>
                      <option value="7eme">7eme</option>
                      <option value="6ème">6ème</option>
                      <option value="5ème">5ème</option>
                      <option value="4ème">4ème</option>
                      <option value="3ème">3ème</option>
                      <option value="2eme">2eme</option>
                      <option value="1ère">1ère</option>
                      <option value="Terminale">Terminale</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="titulaire" class="col-sm-2 control-label">Professeur titulaire</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="titulaire" name="titulaire">
                      <option value="">Sélectionnez un professeur</option>
                      <?php foreach ($professeurs as $prof): ?>
                        <option value="<?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?>" data-prof-id="<?php echo $prof['id']; ?>">
                          <?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="prof_id" id="prof_id" value="">
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="section" class="col-sm-2 control-label">Section</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="section" name="section" required>
                      <option value="">Sélectionnez une section</option>
                      <option value="maternelle" <?php echo (isset($_POST['section']) && $_POST['section'] == 'maternelle') ? 'selected' : ''; ?>>Maternelle</option>
                      <option value="primaire" <?php echo (isset($_POST['section']) && $_POST['section'] == 'primaire') ? 'selected' : ''; ?>>Primaire</option>
                      <option value="secondaire" <?php echo (isset($_POST['section']) && $_POST['section'] == 'secondaire') ? 'selected' : ''; ?>>Secondaire</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes" class="btn btn-default">Annuler</a>
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
    // Mise à jour automatique de la section en fonction du niveau sélectionné
    $('#niveau').change(function() {
      var niveau = $(this).val();
      var section = '';
      
      if (niveau.includes('Maternelle')) {
        section = 'maternelle';
      } else if (['CP', 'CE1', 'CE2', 'CM1', 'CM2'].includes(niveau)) {
        section = 'primaire';
      } else {
        section = 'secondaire';
      }
      
      $('#section').val(section);
    });
    
    // Capture professor ID when selecting a professor
    $('#titulaire').on('change', function() {
      var selectedOption = $(this).find('option:selected');
      var profId = selectedOption.data('prof-id');
      $('#prof_id').val(profId);
    });
    
    // Validation du formulaire
    $('form').submit(function(e) {
      var nom = $('#nom').val();
      var niveau = $('#niveau').val();
      var section = $('#section').val();
      
      if (nom.trim() === '') {
        alert('Veuillez entrer un nom pour la classe');
        e.preventDefault();
        return false;
      }
      
      if (niveau === '') {
        alert('Veuillez sélectionner un niveau');
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
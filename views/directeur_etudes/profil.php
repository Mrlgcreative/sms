<?php
// Assurez-vous que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de l'utilisateur depuis la session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur des Études';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Récupération des informations complètes de l'utilisateur
$user_info = null;
if ($user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
    $stmt->close();
}

// Statistiques d'activité du directeur
// $activite_stats = [];
// if ($user_id) {
//     // Communications créées
//     $comm_query = "SELECT COUNT(*) as nb_communications FROM communications WHERE auteur_id = ?";
//     $stmt = $mysqli->prepare($comm_query);
//     $stmt->bind_param("i", $user_id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $activite_stats['communications'] = $result->fetch_assoc()['nb_communications'];
//     $stmt->close();

//     // Connexions récentes (si table de logs existe)
//     $activite_stats['dernieres_connexions'] = [
//         ['date' => date('Y-m-d H:i:s'), 'ip' => $_SERVER['REMOTE_ADDR']],
//         ['date' => date('Y-m-d H:i:s', strtotime('-1 day')), 'ip' => '192.168.1.100'],
//         ['date' => date('Y-m-d H:i:s', strtotime('-2 days')), 'ip' => '192.168.1.100']
//     ];
// }

// Traitement du formulaire de mise à jour
$message = '';
$message_type = '';
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'update_profile') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    
    if ($user_id && $nom && $prenom && $email) {
        $update_query = "UPDATE users SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("ssssi", $nom, $prenom, $email, $telephone, $user_id);
        
        if ($stmt->execute()) {
            $message = "Profil mis à jour avec succès.";
            $message_type = "success";
            // Recharger les informations
            $user_info['nom'] = $nom;
            $user_info['prenom'] = $prenom;
            $user_info['email'] = $email;
            $user_info['telephone'] = $telephone;
        } else {
            $message = "Erreur lors de la mise à jour du profil.";
            $message_type = "danger";
        }
        $stmt->close();
    }
}

// Traitement du changement de mot de passe
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if ($user_id && $current_password && $new_password && $confirm_password) {
        if ($new_password === $confirm_password) {
            // Vérifier l'ancien mot de passe (en supposant qu'il est hashé)
            $check_query = "SELECT mot_de_passe FROM users WHERE id = ?";
            $stmt = $mysqli->prepare($check_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_password = $result->fetch_assoc();
            
            if (password_verify($current_password, $user_password['mot_de_passe'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_query = "UPDATE users SET mot_de_passe = ? WHERE id = ?";
                $stmt = $mysqli->prepare($update_query);
                $stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($stmt->execute()) {
                    $message = "Mot de passe modifié avec succès.";
                    $message_type = "success";
                } else {
                    $message = "Erreur lors du changement de mot de passe.";
                    $message_type = "danger";
                }
            } else {
                $message = "Mot de passe actuel incorrect.";
                $message_type = "danger";
            }
            $stmt->close();
        } else {
            $message = "Les nouveaux mots de passe ne correspondent pas.";
            $message_type = "danger";
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SMS | Mon Profil</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <!-- AdminLTE Skins -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">
    <!-- Logo -->
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>SMS</b></span>
      <span class="logo-lg"><b>School</b>MS</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?>
                  <small><?php echo htmlspecialchars($role); ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>actions/logout.php" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- Left side column. contains the sidebar -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>      <!-- Sidebar Menu -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves">
            <i class="fa fa-graduation-cap"></i> <span>Gestion des Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs">
            <i class="fa fa-users"></i> <span>Gestion des Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=programmesScolaires">
            <i class="fa fa-book"></i> <span>Programmes Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes">
            <i class="fa fa-university"></i> <span>Gestion des Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours">
            <i class="fa fa-calendar"></i> <span>Gestion des Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=examens">
            <i class="fa fa-edit"></i> <span>Gestion des Examens</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=resultatsScolaires">
            <i class="fa fa-bar-chart"></i> <span>Résultats Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps">
            <i class="fa fa-table"></i> <span>Emplois du temps</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires">
            <i class="fa fa-calendar-check-o"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=rapportsGlobaux">
            <i class="fa fa-pie-chart"></i> <span>Rapports Globaux</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=communications">
            <i class="fa fa-envelope"></i> <span>Communications</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
      <h1>
        Mon Profil
        <small>Gestion de votre compte</small>
      </h1>      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <?php if ($message): ?>
      <div class="alert alert-<?php echo $message_type; ?> alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <?php echo htmlspecialchars($message); ?>
      </div>
      <?php endif; ?>

      <div class="row">
        <!-- Profil utilisateur -->
        <div class="col-md-4">
          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/user2-160x160.jpg" alt="User profile picture">

              <h3 class="profile-username text-center">
                <?php echo htmlspecialchars(($user_info['prenom'] ?? '') . ' ' . ($user_info['nom'] ?? '')); ?>
              </h3>

              <p class="text-muted text-center"><?php echo htmlspecialchars($role); ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Communications</b> <a class="pull-right"><?php echo $activite_stats['communications'] ?? 0; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Membre depuis</b> <a class="pull-right">
                    <?php echo $user_info ? date('m/Y', strtotime($user_info['date_creation'] ?? 'now')) : date('m/Y'); ?>
                  </a>
                </li>
                <li class="list-group-item">
                  <b>Dernière connexion</b> <a class="pull-right">Maintenant</a>
                </li>
              </ul>

              <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#changePhotoModal">
                <b>Changer la Photo</b>
              </button>
            </div>
          </div>

          <!-- Activité récente -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Activité Récente</h3>
            </div>
            <div class="box-body">
              <ul class="list-unstyled">
                <?php foreach ($activite_stats['dernieres_connexions'] ?? [] as $connexion): ?>
                <li>
                  <i class="fa fa-sign-in text-aqua"></i> Connexion
                  <span class="text-muted pull-right">
                    <?php echo date('d/m H:i', strtotime($connexion['date'])); ?>
                  </span>
                </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- Informations et formulaires -->
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Informations</a></li>
              <li><a href="#timeline" data-toggle="tab">Paramètres</a></li>
              <li><a href="#settings" data-toggle="tab">Sécurité</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                <!-- Informations personnelles -->
                <form class="form-horizontal" method="POST">
                  <input type="hidden" name="action" value="update_profile">
                  
                  <div class="form-group">
                    <label for="nom" class="col-sm-2 control-label">Nom</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="nom" name="nom" 
                             value="<?php echo htmlspecialchars($user_info['nom'] ?? ''); ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="prenom" class="col-sm-2 control-label">Prénom</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="prenom" name="prenom" 
                             value="<?php echo htmlspecialchars($user_info['prenom'] ?? ''); ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                      <input type="email" class="form-control" id="email" name="email" 
                             value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="telephone" class="col-sm-2 control-label">Téléphone</label>
                    <div class="col-sm-10">
                      <input type="tel" class="form-control" id="telephone" name="telephone" 
                             value="<?php echo htmlspecialchars($user_info['telephone'] ?? ''); ?>">
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="role" class="col-sm-2 control-label">Rôle</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" id="role" 
                             value="<?php echo htmlspecialchars($role); ?>" readonly>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="timeline">
                <!-- Préférences -->
                <form class="form-horizontal">
                  <div class="form-group">
                    <label for="langue" class="col-sm-2 control-label">Langue</label>
                    <div class="col-sm-10">
                      <select class="form-control" id="langue">
                        <option value="fr" selected>Français</option>
                        <option value="en">English</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="timezone" class="col-sm-2 control-label">Fuseau horaire</label>
                    <div class="col-sm-10">
                      <select class="form-control" id="timezone">
                        <option value="Africa/Kinshasa" selected>Afrique/Kinshasa (GMT+1)</option>
                        <option value="Europe/Paris">Europe/Paris (GMT+1)</option>
                      </select>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" checked> Recevoir les notifications par email
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> Activer l'authentification à deux facteurs
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-info">Sauvegarder les préférences</button>
                    </div>
                  </div>
                </form>
              </div>

              <div class="tab-pane" id="settings">
                <!-- Changement de mot de passe -->
                <form class="form-horizontal" method="POST">
                  <input type="hidden" name="action" value="change_password">
                  
                  <div class="form-group">
                    <label for="current_password" class="col-sm-2 control-label">Mot de passe actuel</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="new_password" class="col-sm-2 control-label">Nouveau mot de passe</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="new_password" name="new_password" required>
                      <span class="help-block">Minimum 8 caractères avec lettres et chiffres</span>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <label for="confirm_password" class="col-sm-2 control-label">Confirmer</label>
                    <div class="col-sm-10">
                      <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                  </div>
                  
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-warning">Changer le mot de passe</button>
                    </div>
                  </div>
                </form>

                <hr>

                <!-- Actions de sécurité -->
                <div class="form-group">
                  <div class="col-sm-12">
                    <h4>Actions de sécurité</h4>
                    <button type="button" class="btn btn-danger">Déconnecter tous les appareils</button>
                    <button type="button" class="btn btn-info">Télécharger mes données</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Main Footer -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; 2024 <a href="#">School Management System</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Change Photo Modal -->
<div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Changer la photo de profil</h4>
      </div>
      <div class="modal-body">
        <form enctype="multipart/form-data">
          <div class="form-group">
            <label for="photo">Sélectionner une nouvelle photo</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            <span class="help-block">Formats acceptés: JPG, PNG. Taille max: 2MB</span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary">Télécharger</button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Validation du mot de passe
  $('#new_password, #confirm_password').on('keyup', function() {
    var password = $('#new_password').val();
    var confirm = $('#confirm_password').val();
    
    if (password.length >= 8) {
      $('#new_password').parent().removeClass('has-error').addClass('has-success');
    } else {
      $('#new_password').parent().removeClass('has-success').addClass('has-error');
    }
    
    if (password === confirm && password.length > 0) {
      $('#confirm_password').parent().removeClass('has-error').addClass('has-success');
    } else if (confirm.length > 0) {
      $('#confirm_password').parent().removeClass('has-success').addClass('has-error');
    }
  });
});
</script>

</body>
</html>

<?php
$mysqli->close();
?>

<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer le nombre d'élèves inscrits
$result = $mysqli->query("SELECT COUNT(*) AS total_eleves FROM eleves");
$row = $result->fetch_assoc();
$total_eleves = $row['total_eleves'];

// Fermer la connexion à la base de données
$mysqli->close();

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';
$current_session = isset($current_session) ? $current_session : date('Y') . '-' . (date('Y') + 1);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Liste des élèves</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      .content-wrapper, .main-footer {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <!-- Dans la section de l'en-tête principal -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=profil" class="btn btn-default btn-flat">Profil</a>
                </div>
                <div class="pull-right">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Auth&action=logout" class="btn btn-default btn-flat">Déconnexion</a>
                </div>
              </li>
              <!-- Reste du menu déroulant -->
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
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Liste des élèves
        <small>Tous les élèves inscrits</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves (Total: <?php echo $total_eleves; ?>)</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <input type="text" id="searchInput" class="form-control pull-right" placeholder="Rechercher...">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="box-body">
              <div class="row no-print" style="margin-bottom: 15px;">
                <div class="col-xs-12">
                  <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportEleves" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="eleveTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Photo</th>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Post-nom</th>
                      <th>Prénom</th>
                      <th>Date de naissance</th>
                      <th>Lieu de naissance</th>
                      <th>Section</th>
                      <th>Option</th>
                      <th>Classe</th>
                      <th>Adresse</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($eleves as $eleve) : ?>
                      <tr>
                        <td><?php echo $eleve['id']; ?></td>
                        <td><img src="<?php echo !empty($eleve['photo']) ? $eleve['photo'] : 'dist/img/default-student.png'; ?>" alt="Photo élève" class="img-circle" style="width: 50px; height: 50px;"></td>
                        <td><?php echo !empty($eleve['matricule']) ? $eleve['matricule'] : 'Non défini'; ?></td>
                        <td><?php echo $eleve['nom']; ?></td>
                        <td><?php echo $eleve['post_nom']; ?></td>
                        <td><?php echo $eleve['prenom']; ?></td>
                        <td><?php echo $eleve['date_naissance']; ?></td>
                        <td><?php echo $eleve['lieu_naissance']; ?></td>
                        <td><?php echo $eleve['section']; ?></td>
                        <td><?php echo $eleve['option_nom']; ?></td>
                        <td><?php echo $eleve['classe_nom']; ?></td>
                        <td><?php echo $eleve['adresse']; ?></td>
                        <td class="no-print">
                          <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=viewStudent&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Détails</a>
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-success btn-xs"><i class="fa fa-money"></i> Paiement</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
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
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // Initialiser DataTables
    $('#eleveTable').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'language': {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      }
    });
    
    // Fonction de recherche dynamique
    $("#searchInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#eleveTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    
    // Vérifier les paramètres d'URL au chargement de la page
    var urlParams = new URLSearchParams(window.location.search);
    var success = urlParams.get('success');
    var error = urlParams.get('error');
    var message = urlParams.get('message');
    
    if (success) {
      showAlert(decodeURIComponent(message || 'Opération réussie!'), 'success');
    } else if (error) {
      showAlert(decodeURIComponent(message || 'Une erreur est survenue!'), 'danger');
    }
  });
  
  // Fonction pour afficher une alerte stylisée
  function showAlert(message, type) {
    // Créer l'élément d'alerte
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    
    // Ajouter le contenu de l'alerte
    alertDiv.innerHTML = 
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' + 
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message;
    
    // Ajouter l'alerte au document
    document.body.appendChild(alertDiv);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(function() {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }
</script>
</body>
</html>
<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

// Connexion à la base de données pour récupérer la session active
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer la session scolaire active
$query_session = "SELECT * FROM sessions_scolaires WHERE est_active = 1 LIMIT 1";
$result_session = $mysqli->query($query_session);

if ($result_session && $result_session->num_rows > 0) {
    $session_active = $result_session->fetch_assoc();
    $session_active_nom = $session_active['libelle'] ?? ($session_active['annee_debut'] . '-' . $session_active['annee_fin']);
    $session_active_id = $session_active['id'];
} else {
    // Aucune session active trouvée, utiliser l'année en cours comme fallback
    $session_active_nom = date('Y') . '-' . (date('Y') + 1);
    $session_active_id = null;
}

// Fermer la connexion à la base de données
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sophie | Gestion des Sessions Scolaires</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .session-active {
      background-color: #dff0d8;
      border-color: #d6e9c6;
    }
    .session-info {
      background-color: #d9edf7;
      border-color: #bce8f1;
      color: #31708f;
      padding: 10px 15px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    .btn-action {
      margin-right: 5px;
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
        <li>
           <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFournitures">
             <i class="fa fa-pencil"></i> <span>Achat fourniture</span>
           </a>
         </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscris">
            <i class="fa fa-users"></i> <span>Élèves reinscris</span>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription">
            <i class="fa fa-refresh"></i> <span>Réinscription</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=sessions_scolaires">
            <i class="fa fa-calendar"></i> <span>Sessions Scolaires</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Sessions Scolaires
        <small>Ajouter, modifier et activer des sessions</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Sessions Scolaires</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <!-- Messages d'alerte -->
          <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-check"></i> Succès!</h4>
              <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>
          
          <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
              <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>
          
          <!-- Session active -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Session Scolaire Active</h3>
            </div>
            <div class="box-body">
              <div class="session-info">
                <h4><i class="fa fa-calendar"></i> Session active: <strong><?php echo $session_active_nom; ?></strong></h4>
                <?php if (!$session_active_id): ?>
                  <div class="alert alert-warning">
                    <i class="fa fa-warning"></i> Aucune session n'est actuellement active. Veuillez en activer une ci-dessous.
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          
          <!-- Formulaire d'ajout de session -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Ajouter une nouvelle session scolaire</h3>
            </div>
            <form action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajouterSessionScolaire" method="POST" class="form-horizontal">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="libelle" class="col-sm-4 control-label">Libellé</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="libelle" name="libelle" placeholder="Ex: Année Scolaire 2023-2024" required>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="annee_debut" class="col-sm-4 control-label">Année de début</label>
                      <div class="col-sm-8">
                        <input type="number" class="form-control" id="annee_debut" name="annee_debut" placeholder="Ex: 2023" min="2000" max="2100" required>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="annee_fin" class="col-sm-4 control-label">Année de fin</label>
                      <div class="col-sm-8">
                        <input type="number" class="form-control" id="annee_fin" name="annee_fin" placeholder="Ex: 2024" min="2000" max="2100" required>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="date_debut" class="col-sm-4 control-label">Date de début</label>
                      <div class="col-sm-8">
                        <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="date_fin" class="col-sm-4 control-label">Date de fin</label>
                      <div class="col-sm-8">
                        <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <div class="col-sm-offset-4 col-sm-8">
                        <div class="checkbox">
                          <label>
                            <input type="checkbox" name="est_active"> Définir comme session active
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">
                  <i class="fa fa-plus"></i> Ajouter la session
                </button>
              </div>
            </form>
          </div>
          
          <!-- Liste des sessions scolaires -->
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Liste des sessions scolaires</h3>
            </div>
            <div class="box-body">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($sessions)): ?>
                    <tr>
                      <td colspan="6" class="text-center">Aucune session scolaire enregistrée</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($sessions as $session): ?>
                      <tr class="<?php echo ($session['est_active'] == 1) ? 'session-active' : ''; ?>">
                        <td><?php echo $session['id']; ?></td>
                        <td><?php echo htmlspecialchars($session['libelle']); ?></td>
                        <td><?php echo $session['annee_debut'] . '-' . $session['annee_fin']; ?></td>
                       
                        <td>
                          <?php if ($session['est_active'] == 1): ?>
                            <span class="label label-success">Active</span>
                          <?php else: ?>
                            <span class="label label-default">Inactive</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($session['est_active'] != 1): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=activerSessionScolaire&id=<?php echo $session['id']; ?>" class="btn btn-success btn-xs btn-action">
                              <i class="fa fa-check"></i> Activer
                            </a>
                          <?php endif; ?>
                          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifierSessionScolaire&id=<?php echo $session['id']; ?>" class="btn btn-primary btn-xs btn-action">
                            <i class="fa fa-edit"></i> Modifier
                          </a>
                          <?php if ($session['est_active'] != 1): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=supprimerSessionScolaire&id=<?php echo $session['id']; ?>" class="btn btn-danger btn-xs btn-action" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette session scolaire ?');">
                              <i class="fa fa-trash"></i> Supprimer
                            </a>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Page script -->
<script>
  $(function () {
    // Initialiser Select2
    $('.select2').select2();
    
    // Définir la date du jour comme valeur par défaut
    var today = new Date().toISOString().split('T')[0];
    
    // Calculer l'année scolaire par défaut
    var currentYear = new Date().getFullYear();
    var currentMonth = new Date().getMonth() + 1; // Janvier = 0
    
    // Si on est après août, l'année scolaire commence cette année
    // Sinon, elle a commencé l'année précédente
    var startYear = (currentMonth >= 9) ? currentYear : currentYear - 1;
    var endYear = startYear + 1;
    
    // Définir les valeurs par défaut
    document.getElementById('annee_debut').value = startYear;
    document.getElementById('annee_fin').value = endYear;
    document.getElementById('libelle').value = 'Année Scolaire ' + startYear + '-' + endYear;
    
    // Définir les dates par défaut (1er septembre - 30 juin)
    document.getElementById('date_debut').value = startYear + '-09-01';
    document.getElementById('date_fin').value = endYear + '-06-30';
    
    // Mettre à jour automatiquement l'année de fin lorsque l'année de début change
    $('#annee_debut').on('change', function() {
      var debut = parseInt($(this).val());
      $('#annee_fin').val(debut + 1);
      $('#libelle').val('Année Scolaire ' + debut + '-' + (debut + 1));
      $('#date_debut').val(debut + '-09-01');
      $('#date_fin').val((debut + 1) + '-06-30');
    });
  });
</script>
</body>
</html>
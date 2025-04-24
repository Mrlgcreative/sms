<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les statistiques pour la section primaire
$total_eleves = $mysqli->query("SELECT COUNT(*) AS total FROM eleves WHERE section='primaire'")->fetch_assoc()['total'];
$total_professeurs = $mysqli->query("SELECT COUNT(*) AS total FROM professeurs WHERE section='primaire'")->fetch_assoc()['total'];
$total_classes = $mysqli->query("SELECT COUNT(*) AS total FROM classes WHERE section='primaire'")->fetch_assoc()['total'];

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Génération de Rapports</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/morris.js/morris.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Génération de Rapports
        <small>Statistdashboard"></i> Accueil</a></li>
        <li class="active">Rapports</li>
      </ol>
    </section>

    <section class="content">
      <!-- Statistiques générales -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Statistiques générales - Section Primaire</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="info-box bg-purple">
                    <span class="info-box-icon"><i class="fa fa-child"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Élèves</span>
                      <span class="info-box-number"><?php echo $total_eleves; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-graduation-cap"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Professeurs</span>
                      <span class="info-box-number"><?php echo $total_professeurs; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-table"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total Classes</span>
                      <span class="info-box-number"><?php echo $total_classes; ?></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Types de rapports disponibles -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-default">
            <div class="box-header with-border">
              <h3 class="box-title">Rapports disponibles</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-blue">
                      <h3>Rapports sur les élèves</h3>
                    </div>
                    <div class="box-footer no-padding">
                      <ul class="nav nav-stacked">
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=liste_eleves">Liste complète des élèves <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=eleves_par_classe">Élèves par classe <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=absences_eleves">Rapport d'absences <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=resultats_eleves">Résultats scolaires <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-green">
                      <h3>Rapports sur les professeurs</h3>
                    </div>
                    <div class="box-footer no-padding">
                      <ul class="nav nav-stacked">
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=liste_professeurs">Liste des professeurs <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=charge_horaire">Charge horaire <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=absences_professeurs">Absences des professeurs <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="box box-widget widget-user-2">
                    <div class="widget-user-header bg-red">
                      <h3>Rapports administratifs</h3>
                    </div>
                    <div class="box-footer no-padding">
                      <ul class="nav nav-stacked">
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=emploi_du_temps">Emplois du temps <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=evenements">Calendrier des événements <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=discipline">Incidents disciplinaires <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapport&type=statistiques_generales">Statistiques générales <i class="fa fa-arrow-circle-right pull-right"></i></a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Générateur de rapports personnalisés -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Générateur de rapports personnalisés</h3>
            </div>
            <div class="box-body">
              <form action="<?php echo BASE_URL; ?>index.php?controller=Director&action=genererRapportPersonnalise" method="post" class="form-horizontal">
                <div class="form-group">
                  <label for="rapport_type" class="col-sm-2 control-label">Type de rapport</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="rapport_type" name="rapport_type">
                      <option value="eleves">Élèves</option>
                      <option value="professeurs">Professeurs</option>
                      <option value="classes">Classes</option>
                      <option value="cours">Cours</option>
                      <option value="absences">Absences</option>
                      <option value="discipline">Discipline</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="date_debut" class="col-sm-2 control-label">Période du</label>
                  <div class="col-sm-4">
                    <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo date('Y-m-01'); ?>">
                  </div>
                  <label for="date_fin" class="col-sm-1 control-label">au</label>
                  <div class="col-sm-5">
                    <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo date('Y-m-t'); ?>">
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="classe_id" class="col-sm-2 control-label">Classe</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="classe_id" name="classe_id">
                      <option value="0">Toutes les classes</option>
                      <!-- Les options des classes seront chargées dynamiquement via AJAX -->
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="format" class="col-sm-2 control-label">Format</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="format" name="format">
                      <option value="pdf">PDF</option>
                      <option value="excel">Excel</option>
                      <option value="csv">CSV</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">Générer le rapport</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Historique des rapports générés -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Historique des rapports générés</h3>
            </div>
            <div class="box-body">
              <table id="rapports-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Type de rapport</th>
                    <th>Généré par</th>
                    <th>Format</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Les données seront chargées dynamiquement -->
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialisation de la DataTable pour l'historique des rapports
  $('#rapports-table').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language'    : {
      'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
    }
  });
  
  // Charger les classes disponibles
  $.ajax({
    url: '<?php echo BASE_URL; ?>index.php?controller=Director&action=getClasses',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if(response.success) {
        var classes = response.data;
        var options = '<option value="0">Toutes les classes</option>';
        
        for(var i = 0; i < classes.length; i++) {
          options += '<option value="' + classes[i].id + '">' + classes[i].nom + '</option>';
        }
        
        $('#classe_id').html(options);
      }
    }
  });
  
  // Charger l'historique des rapports
  $.ajax({
    url: '<?php echo BASE_URL; ?>index.php?controller=Director&action=getHistoriqueRapports',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if(response.success) {
        var rapports = response.data;
        var html = '';
        
        for(var i = 0; i < rapports.length; i++) {
          html += '<tr>';
          html += '<td>' + rapports[i].date_generation + '</td>';
          html += '<td>' + rapports[i].type + '</td>';
          html += '<td>' + rapports[i].genere_par + '</td>';
          html += '<td>' + rapports[i].format + '</td>';
          html += '<td>';
          html += '<a href="' + rapports[i].url + '" class="btn btn-xs btn-info" target="_blank"><i class="fa fa-download"></i> Télécharger</a> ';
          html += '<a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerRapport&id=' + rapports[i].id + '" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Supprimer</a>';
          html += '</td>';
          html += '</tr>';
        }
        
        $('#rapports-table tbody').html(html);
      }
    }
  });
  
  // Changer les options disponibles en fonction du type de rapport
  $('#rapport_type').change(function() {
    var type = $(this).val();
    
    if(type === 'eleves' || type === 'absences' || type === 'discipline') {
      $('#classe_id').prop('disabled', false);
    } else {
      $('#classe_id').prop('disabled', true);
    }
  });
});
</script>
</body>
</html>
<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des incidents disciplinaires
$incidents = [];

// Construction de la requête avec filtres
$incidents_query = "SELECT i.id, e.nom, e.prenom, c.nom AS classe_nom, i.date_incident, i.description, i.sanction, i.statut 
                  FROM incidents_disciplinaires i 
                  LEFT JOIN eleves e ON i.eleve_id = e.id 
                  LEFT JOIN classes c ON e.classe_id = c.id 
                  WHERE 1=1 ";

$params = [];
$types = "";

// Filtre par classe
if (isset($_GET['classe']) && !empty($_GET['classe'])) {
    $incidents_query .= " AND e.classe_id = ? ";
    $params[] = $_GET['classe'];
    $types .= "i";
}

// Filtre par date de début
if (isset($_GET['date_debut']) && !empty($_GET['date_debut'])) {
    $date_debut = DateTime::createFromFormat('d/m/Y', $_GET['date_debut']);
    if ($date_debut) {
        $incidents_query .= " AND i.date_incident >= ? ";
        $params[] = $date_debut->format('Y-m-d');
        $types .= "s";
    }
}

// Filtre par date de fin
if (isset($_GET['date_fin']) && !empty($_GET['date_fin'])) {
    $date_fin = DateTime::createFromFormat('d/m/Y', $_GET['date_fin']);
    if ($date_fin) {
        $incidents_query .= " AND i.date_incident <= ? ";
        $params[] = $date_fin->format('Y-m-d');
        $types .= "s";
    }
}

// Filtre par statut
if (isset($_GET['statut']) && !empty($_GET['statut'])) {
    $incidents_query .= " AND i.statut = ? ";
    $params[] = $_GET['statut'];
    $types .= "s";
}

$incidents_query .= " ORDER BY i.date_incident DESC LIMIT 50";

// Exécution de la requête avec les paramètres
if (!empty($params)) {
    $stmt = $mysqli->prepare($incidents_query);
    if ($stmt) {
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $incidents_result = $stmt->get_result();
        while ($row = $incidents_result->fetch_assoc()) {
            $incidents[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Erreur de préparation SQL: " . $mysqli->error);
    }
} else {
    $incidents_result = $mysqli->query($incidents_query);
    if ($incidents_result) {
        while ($row = $incidents_result->fetch_assoc()) {
            $incidents[] = $row;
        }
    } else {
        error_log("Erreur SQL: " . $mysqli->error);
    }
}

// Récupération des classes pour le filtre
$classes = [];
$classes_query = "SELECT id, nom FROM classes WHERE section = 'secondaire' ORDER BY nom";
$classes_result = $mysqli->query($classes_query);
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Récupération des élèves pour le formulaire d'ajout
$eleves = [];
$eleves_query = "SELECT id, nom, prenom, classe_id FROM eleves WHERE section = 'secondaire' ORDER BY nom, prenom";
$eleves_result = $mysqli->query($eleves_query);
if ($eleves_result) {
    while ($row = $eleves_result->fetch_assoc()) {
        $eleves[] = $row;
    }
}

// Statistiques pour les graphiques
// 1. Incidents par classe
$incidents_par_classe_labels = [];
$incidents_par_classe_data = [];
$incidents_par_classe_query = "SELECT c.nom AS classe_nom, COUNT(i.id) AS nombre_incidents 
                             FROM classes c 
                             LEFT JOIN eleves e ON e.classe_id = c.id 
                             LEFT JOIN incidents_disciplinaires i ON i.eleve_id = e.id 
                             WHERE c.section = 'secondaire'
                             GROUP BY c.id, c.nom
                             ORDER BY c.nom";
$incidents_par_classe_result = $mysqli->query($incidents_par_classe_query);
if ($incidents_par_classe_result) {
    while ($row = $incidents_par_classe_result->fetch_assoc()) {
        $incidents_par_classe_labels[] = $row['classe_nom'];
        $incidents_par_classe_data[] = intval($row['nombre_incidents']);
    }
}

// 2. Types de sanctions
$sanctions_query = "SELECT sanction, COUNT(*) AS nombre FROM incidents_disciplinaires GROUP BY sanction";
$sanctions_result = $mysqli->query($sanctions_query);
$sanctions_labels = [];
$sanctions_data = [];
if ($sanctions_result) {
    while ($row = $sanctions_result->fetch_assoc()) {
        $sanctions_labels[] = $row['sanction'] ?: 'Non définie';
        $sanctions_data[] = intval($row['nombre']);
    }
}

// Fermer la connexion
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion de la Discipline</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

   <?php  include 'navbar.php'; ?>
 <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion de la Discipline
        <small>Section Secondaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Gestion de la Discipline</li>
      </ol>
    </section>

    <section class="content">
      <!-- Affichage des messages flash -->
      <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info'; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $_SESSION['flash_message']; ?>
          <?php unset($_SESSION['flash_message']); unset($_SESSION['flash_type']); ?>
        </div>
      <?php endif; ?>

      <!-- Filtres et bouton d'ajout -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Filtres et actions</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-9">
                  <form class="form-inline" method="get" action="<?php echo BASE_URL; ?>index.php">
                    <input type="hidden" name="controller" value="Prefet">
                    <input type="hidden" name="action" value="discipline">
                    
                    <div class="form-group">
                      <label for="classe">Classe:</label>
                      <select class="form-control" id="classe" name="classe">
                        <option value="">Toutes les classes</option>
                        <?php foreach ($classes as $classe): ?>
                          <option value="<?php echo $classe['id']; ?>" <?php echo (isset($_GET['classe']) && $_GET['classe'] == $classe['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($classe['nom']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="date_debut">Du:</label>
                      <input type="text" class="form-control datepicker" id="date_debut" name="date_debut" 
                             value="<?php echo isset($_GET['date_debut']) ? htmlspecialchars($_GET['date_debut']) : ''; ?>" 
                             placeholder="Date début">
                    </div>
                    
                    <div class="form-group">
                      <label for="date_fin">Au:</label>
                      <input type="text" class="form-control datepicker" id="date_fin" name="date_fin" 
                             value="<?php echo isset($_GET['date_fin']) ? htmlspecialchars($_GET['date_fin']) : ''; ?>" 
                             placeholder="Date fin">
                    </div>
                    
                    <div class="form-group">
                      <label for="statut">Statut:</label>
                      <select class="form-control" id="statut" name="statut">
                        <option value="">Tous</option>
                        <option value="En cours" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'En cours') ? 'selected' : ''; ?>>En cours</option>
                        <option value="Résolu" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'Résolu') ? 'selected' : ''; ?>>Résolu</option>
                      </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=discipline" class="btn btn-default">Réinitialiser</a>
                  </form>
                </div>
                <div class="col-md-3 text-right">
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-ajouter-incident">
                    <i class="fa fa-plus"></i> Ajouter un incident
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste des incidents disciplinaires -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Liste des incidents disciplinaires</h3>
            </div>
            <div class="box-body">
              <table id="incidents-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Sanction</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($incidents)): ?>
                    <?php foreach ($incidents as $incident): ?>
                      <tr>
                        <td><?php echo $incident['id']; ?></td>
                        <td><?php echo htmlspecialchars($incident['nom'] . ' ' . $incident['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($incident['classe_nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($incident['date_incident'])); ?></td>
                        <td><?php echo htmlspecialchars($incident['description']); ?></td>
                        <td><?php echo htmlspecialchars($incident['sanction']); ?></td>
                        <td>
                          <?php if ($incident['statut'] == 'Résolu'): ?>
                            <span class="label label-success">Résolu</span>
                          <?php else: ?>
                            <span class="label label-warning">En cours</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modal-editer-incident" 
                                  data-id="<?php echo $incident['id']; ?>"
                                  data-description="<?php echo htmlspecialchars($incident['description']); ?>"
                                  data-sanction="<?php echo htmlspecialchars($incident['sanction']); ?>"
                                  data-statut="<?php echo $incident['statut']; ?>">
                            <i class="fa fa-edit"></i> Éditer
                          </button>
                          <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-supprimer-incident" 
                                  data-id="<?php echo $incident['id']; ?>">
                            <i class="fa fa-trash"></i> Supprimer
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="8" class="text-center">Aucun incident disciplinaire enregistré</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques des incidents -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Incidents par classe</h3>
            </div>
            <div class="box-body">
              <canvas id="incidentsParClasse" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Types de sanctions</h3>
            </div>
            <div class="box-body">
              <canvas id="typesSanctions" style="height:250px"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Informations générales -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
              <i class="fa fa-info-circle"></i>
              <h3 class="box-title">Informations générales sur la discipline</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3><?php echo count($incidents); ?></h3>
                      <p>Total incidents</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-exclamation-triangle"></i>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-yellow">
                    <div class="inner">
                      <h3>
                        <?php 
                          $en_cours = array_filter($incidents, function($incident) {
                            return $incident['statut'] == 'En cours';
                          });
                          echo count($en_cours);
                        ?>
                      </h3>
                      <p>En cours</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-clock-o"></i>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h3>
                        <?php 
                          $resolus = array_filter($incidents, function($incident) {
                            return $incident['statut'] == 'Résolu';
                          });
                          echo count($resolus);
                        ?>
                      </h3>
                      <p>Résolus</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-check"></i>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-red">
                    <div class="inner">
                      <h3>
                        <?php 
                          $debut_mois = date('Y-m-01');
                          $fin_mois = date('Y-m-t');
                          $ce_mois = array_filter($incidents, function($incident) use ($debut_mois, $fin_mois) {
                            // Convertir la date de l'incident en format Y-m-d pour comparaison
                            $date_incident = date('Y-m-d', strtotime($incident['date_incident']));
                            return $date_incident >= $debut_mois && $date_incident <= $fin_mois;
                          });
                          echo count($ce_mois);
                        ?>
                      </h3>
                      <p>Ce mois</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-calendar"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <!-- Modal Ajouter Incident -->
  <div class="modal fade" id="modal-ajouter-incident">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter un incident disciplinaire</h4>
        </div>
        <form id="form-ajouter-incident" action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=ajouterIncident" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="eleve_id">Élève</label>
              <select class="form-control" id="eleve_id" name="eleve_id" required>
                <option value="">Sélectionner un élève</option>
                <?php foreach ($eleves as $eleve): ?>
                  <option value="<?php echo $eleve['id']; ?>"><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="date_incident">Date de l'incident</label>
              <input type="text" class="form-control datepicker" id="date_incident" name="date_incident" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="sanction">Sanction</label>
              <input type="text" class="form-control" id="sanction" name="sanction">
            </div>
            <div class="form-group">
              <label for="statut">Statut</label>
              <select class="form-control" id="statut" name="statut">
                <option value="En cours">En cours</option>
                <option value="Résolu">Résolu</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Modal Éditer Incident -->
  <div class="modal fade" id="modal-editer-incident">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Modifier un incident disciplinaire</h4>
        </div>
        <form id="form-editer-incident" action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=modifierIncident" method="post">
          <input type="hidden" id="edit_incident_id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="edit_description">Description</label>
              <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
            </div>
            <div class="form-group">
              <label for="edit_sanction">Sanction</label>
              <input type="text" class="form-control" id="edit_sanction" name="sanction">
            </div>
            <div class="form-group">
              <label for="edit_statut">Statut</label>
              <select class="form-control" id="edit_statut" name="statut">
                <option value="En cours">En cours</option>
                <option value="Résolu">Résolu</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  
  <!-- Modal Supprimer Incident -->
  <div class="modal fade" id="modal-supprimer-incident">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Confirmer la suppression</h4>
        </div>
        <form id="form-supprimer-incident" action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=supprimerIncident" method="post">
          <input type="hidden" id="delete_incident_id" name="id">
          <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer cet incident disciplinaire ?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </div>
        </form>
      </div>
    </div>
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
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialisation de la DataTable
  $('#incidents-table').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json'
    }
  });
  
  // Initialisation des datepickers
  $('.datepicker').datepicker({
    format: 'dd/mm/yyyy',
    language: 'fr',
    autoclose: true
  });
  
  // Gestion du modal d'édition
  $('#modal-editer-incident').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var description = button.data('description');
    var sanction = button.data('sanction');
    var statut = button.data('statut');
    
    var modal = $(this);
    modal.find('#edit_incident_id').val(id);
    modal.find('#edit_description').val(description);
    modal.find('#edit_sanction').val(sanction);
    modal.find('#edit_statut').val(statut);
  });
  
  // Gestion du modal de suppression
  $('#modal-supprimer-incident').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    
    var modal = $(this);
    modal.find('#delete_incident_id').val(id);
  });
  
  // Graphique des incidents par classe
  var incidentsParClasseCtx = document.getElementById('incidentsParClasse').getContext('2d');
  var incidentsParClasseChart = new Chart(incidentsParClasseCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($incidents_par_classe_labels); ?>,
      datasets: [{
        label: 'Nombre d\'incidents',
        data: <?php echo json_encode($incidents_par_classe_data); ?>,
        backgroundColor: 'rgba(243, 156, 18, 0.8)'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            stepSize: 1
          }
        }]
      }
    }
  });
  
  // Graphique des types de sanctions
  var typesSanctionsCtx = document.getElementById('typesSanctions').getContext('2d');
  var typesSanctionsChart = new Chart(typesSanctionsCtx, {
    type: 'pie',
    data: {
      labels: <?php echo json_encode($sanctions_labels); ?>,
      datasets: [{
        data: <?php echo json_encode($sanctions_data); ?>,
        backgroundColor: ['#dd4b39', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de']
      }]
    }
  });
});
</script>
</body>
</html>
<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des absences des élèves (section maternelle uniquement)
$absences = [];
$absences_query = "SELECT a.id, e.nom, e.prenom, c.nom AS classe_nom, e.section, a.date_absence, a.motif, a.justifiee 
                  FROM absences_m a 
                  LEFT JOIN eleves e ON a.eleve_id = e.id 
                  LEFT JOIN classes c ON e.classe_id = c.id 
                  WHERE e.section = 'maternelle'
                  ORDER BY a.date_absence DESC 
                  LIMIT 100";

// Pour le débogage, afficher la requête
error_log("Requête absences: " . $absences_query);

$absences_result = $mysqli->query($absences_query);
if ($absences_result) {
    while ($row = $absences_result->fetch_assoc()) {
        $absences[] = $row;
    }
    error_log("Nombre d'absences récupérées: " . count($absences));
} else {
    error_log("Erreur SQL: " . $mysqli->error);
}

// Récupération des classes pour le filtre (maternelle uniquement)
$classes = [];
$classes_query = "SELECT id, nom, section FROM classes WHERE section = 'maternelle' ORDER BY nom";
$classes_result = $mysqli->query($classes_query);
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

// Récupération des élèves pour le formulaire d'ajout (maternelle uniquement)
$eleves = [];
$eleves_query = "SELECT id, nom, prenom, classe_id, section FROM eleves WHERE section = 'maternelle' ORDER BY nom, prenom";
$eleves_result = $mysqli->query($eleves_query);
if ($eleves_result) {
    while ($row = $eleves_result->fetch_assoc()) {
        $eleves[] = $row;
    }
}

// Récupération des statistiques pour les graphiques
// 1. Absences par classe (maternelle uniquement)
$absences_par_classe_labels = [];
$absences_par_classe_data = [];
$absences_par_classe_query = "SELECT c.nom AS classe_nom, c.section, COUNT(a.id) AS nombre_absences 
                             FROM classes c 
                             LEFT JOIN eleves e ON e.classe_id = c.id 
                             LEFT JOIN absences_m a ON a.eleve_id = e.id 
                             WHERE a.id IS NOT NULL AND c.section = 'maternelle'
                             GROUP BY c.id, c.nom, c.section
                             ORDER BY c.nom";
$absences_par_classe_result = $mysqli->query($absences_par_classe_query);
if ($absences_par_classe_result) {
    while ($row = $absences_par_classe_result->fetch_assoc()) {
        $absences_par_classe_labels[] = $row['classe_nom'];
        $absences_par_classe_data[] = intval($row['nombre_absences']);
    }
    // Débogage pour voir les données récupérées
    error_log("Labels des classes: " . json_encode($absences_par_classe_labels));
    error_log("Données des absences: " . json_encode($absences_par_classe_data));
} else {
    error_log("Erreur SQL (absences par classe): " . $mysqli->error);
}

// 2. Absences justifiées vs non justifiées (maternelle uniquement)
$absences_justification_query = "SELECT a.justifiee, COUNT(*) AS nombre 
                                FROM absences_m a 
                                JOIN eleves e ON a.eleve_id = e.id 
                                WHERE e.section = 'maternelle'
                                GROUP BY a.justifiee";
$absences_justification_result = $mysqli->query($absences_justification_query);
$absences_justifiees = 0;
$absences_non_justifiees = 0;
if ($absences_justification_result) {
    while ($row = $absences_justification_result->fetch_assoc()) {
        if ($row['justifiee'] == 1) {
            $absences_justifiees = intval($row['nombre']);
        } else {
            $absences_non_justifiees = intval($row['nombre']);
        }
    }
}

// Vérifier si la table absences contient des enregistrements
$check_query = "SELECT COUNT(*) as count FROM absences_m a JOIN eleves e ON a.eleve_id = e.id WHERE e.section = 'maternelle'";
$check_result = $mysqli->query($check_query);
if ($check_result) {
    $count = $check_result->fetch_assoc()['count'];
    error_log("Nombre total d'absences maternelle dans la base de données: " . $count);
}

// Fermer la connexion après avoir récupéré toutes les données nécessaires
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Absences Maternelle</title>
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
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Absences Maternelle
        <small>Vue Maternelle</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Gestion des Absences Maternelle</li>
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
      
      <!-- Résumé des absences -->
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-calendar-check-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Absences Maternelle</span>
              <span class="info-box-number"><?php echo $absences_justifiees + $absences_non_justifiees; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-check"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Absences Justifiées</span>
              <span class="info-box-number"><?php echo $absences_justifiees; ?></span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Non Justifiées</span>
              <span class="info-box-number"><?php echo $absences_non_justifiees; ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-warning"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Taux Non Justifiées</span>
              <span class="info-box-number">
                <?php 
                  $total = $absences_justifiees + $absences_non_justifiees;
                  echo $total > 0 ? round(($absences_non_justifiees / $total) * 100, 1) . '%' : '0%'; 
                ?>
              </span>
            </div>
          </div>
        </div>
      </div>
      
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
                    <input type="hidden" name="controller" value="Directrice">
                    <input type="hidden" name="action" value="absences">
                    <input type="hidden" name="section" value="maternelle">
                    
                    <div class="form-group">
                      <label for="classe">Classe:</label>
                      <select class="form-control" id="classe" name="classe">
                        <option value="">Toutes les classes</option>
                        <?php foreach ($classes as $classe): ?>
                          <option value="<?php echo $classe['id']; ?>">
                            <?php echo htmlspecialchars($classe['nom']); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    
                    <div class="form-group">
                      <label for="date_debut">Du:</label>
                      <input type="text" class="form-control datepicker" id="date_debut" name="date_debut" placeholder="Date début">
                    </div>
                    
                    <div class="form-group">
                      <label for="date_fin">Au:</label>
                      <input type="text" class="form-control datepicker" id="date_fin" name="date_fin" placeholder="Date fin">
                    </div>
                    
                    <div class="form-group">
                      <label for="justifiee">Statut:</label>
                      <select class="form-control" id="justifiee" name="justifiee">
                        <option value="">Tous</option>
                        <option value="1">Justifiée</option>
                        <option value="0">Non justifiée</option>
                      </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                  </form>
                </div>
                <div class="col-md-3 text-right">
                  <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-ajouter-absence">
                    <i class="fa fa-plus"></i> Ajouter une absence
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste des absences -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Liste des absences maternelle</h3>
            </div>
            <div class="box-body">
              <table id="absences-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Date</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($absences)): ?>
                    <?php foreach ($absences as $absence): ?>
                      <tr>
                        <td><?php echo $absence['id']; ?></td>
                        <td><?php echo htmlspecialchars($absence['nom'] . ' ' . $absence['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($absence['classe_nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?></td>
                        <td><?php echo htmlspecialchars($absence['motif']); ?></td>
                        <td>
                          <?php if ($absence['justifiee']): ?>
                            <span class="label label-success">Justifiée</span>
                          <?php else: ?>
                            <span class="label label-danger">Non justifiée</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#modal-editer-absence" 
                                  data-id="<?php echo $absence['id']; ?>"
                                  data-motif="<?php echo htmlspecialchars($absence['motif']); ?>"
                                  data-justifiee="<?php echo $absence['justifiee']; ?>">
                            <i class="fa fa-edit"></i> Éditer
                          </button>
                          <button type="button" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-supprimer-absence" 
                                  data-id="<?php echo $absence['id']; ?>">
                            <i class="fa fa-trash"></i> Supprimer
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">Aucune absence enregistrée</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques des absences -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Absences par classe maternelle</h3>
            </div>
            <div class="box-body">
              <canvas id="absencesParClasse" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="box box-danger">
            <div class="box-header with-border">
              <h3 class="box-title">Absences justifiées vs non justifiées</h3>
            </div>
            <div class="box-body">
              <canvas id="absencesJustification" style="height:250px"></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
  
  <!-- Modal Ajouter Absence -->
  <div class="modal fade" id="modal-ajouter-absence">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter une absence</h4>
        </div>
        <form id="form-ajouter-absence" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=ajouterAbsence" method="post">
          <div class="modal-body">
            <input type="hidden" name="section" value="maternelle">
            <div class="form-group">
              <label for="eleve_id">Élève <span class="text-danger">*</span></label>
              <select class="form-control" id="eleve_id" name="eleve_id" required>
                <option value="">Sélectionner un élève</option>
                <?php foreach ($eleves as $eleve): ?>
                  <option value="<?php echo $eleve['id']; ?>">
                    <?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="date_absence">Date de l'absence <span class="text-danger">*</span></label>
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="date" class="form-control datepicker" id="date_absence" name="date_absence" placeholder="JJ/MM/AAAA" required>
              </div>
              <small class="text-muted">Format: JJ/MM/AAAA</small>
            </div>
            <div class="form-group">
              <label for="motif">Motif</label>
              <textarea class="form-control" id="motif" name="motif" rows="3" placeholder="Entrez le motif de l'absence..."></textarea>
            </div>
            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="justifiee" value="1"> Absence justifiée
                </label>
              </div>
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
  
  <!-- Modal Éditer Absence -->
  <div class="modal fade" id="modal-editer-absence">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Modifier une absence</h4>
        </div>
        <form id="form-editer-absence" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=modifierAbsence" method="post">
          <input type="hidden" id="edit_absence_id" name="id">
          <div class="modal-body">
            <div class="form-group">
              <label for="edit_motif">Motif</label>
              <textarea class="form-control" id="edit_motif" name="motif" rows="3"></textarea>
            </div>
            <div class="form-group">
              <div class="checkbox">
                <label>
                  <input type="checkbox" id="edit_justifiee" name="justifiee" value="1"> Absence justifiée
                </label>
              </div>
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
  
  <!-- Modal Supprimer Absence -->
  <div class="modal fade" id="modal-supprimer-absence">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Confirmer la suppression</h4>
        </div>
        <form id="form-supprimer-absence" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=supprimerAbsence" method="post">
          <input type="hidden" id="delete_absence_id" name="id">
          <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer cette absence ?</p>
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
    <strong>Copyright &copy; 2023 <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Scripts JS -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.fr.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>


<script>
$(function () {
  // Initialisation de la DataTable
  $('#absences-table').DataTable({
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
  
  // Conversion des dates avant soumission du formulaire
  $('form').on('submit', function(e) {
    $(this).find('.datepicker').each(function() {
      var dateInput = $(this);
      var dateValue = dateInput.val();
      
      // Vérifier si la date est au format français (JJ/MM/AAAA)
      if(dateValue && /^\d{2}\/\d{2}\/\d{4}$/.test(dateValue)) {
        // Convertir du format français (JJ/MM/AAAA) au format YYYY-MM-DD
        var parts = dateValue.split('/');
        if(parts.length === 3) {
          var newDate = parts[2] + '-' + parts[1] + '-' + parts[0];
          dateInput.val(newDate);
        }
      }
    });
  });
  
  // Gestion du modal d'édition
  $('#modal-editer-absence').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var motif = button.data('motif');
    var justifiee = button.data('justifiee');
    
    var modal = $(this);
    modal.find('#edit_absence_id').val(id);
    modal.find('#edit_motif').val(motif);
    
    if (justifiee == 1) {
      modal.find('#edit_justifiee').prop('checked', true);
    } else {
      modal.find('#edit_justifiee').prop('checked', false);
    }
  });
  
  // Gestion du modal de suppression
  $('#modal-supprimer-absence').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    
    var modal = $(this);
    modal.find('#delete_absence_id').val(id);
  });
  
  // Graphique des absences par classe
  var absencesParClasseCtx = document.getElementById('absencesParClasse').getContext('2d');
  var absencesParClasseChart = new Chart(absencesParClasseCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode($absences_par_classe_labels); ?>,
      datasets: [{
        label: 'Nombre d\'absences',
        data: <?php echo json_encode($absences_par_classe_data); ?>,
        backgroundColor: 'rgba(255, 193, 7, 0.8)'
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
  
  // Graphique des absences justifiées vs non justifiées
  var absencesJustificationCtx = document.getElementById('absencesJustification').getContext('2d');
  var absencesJustificationChart = new Chart(absencesJustificationCtx, {
    type: 'pie',
    data: {
      labels: ['Justifiées', 'Non justifiées'],
      datasets: [{
        data: [<?php echo $absences_justifiees ?? 0; ?>, <?php echo $absences_non_justifiees ?? 0; ?>],
        backgroundColor: ['#00a65a', '#f56954']
      }]
    }
  });
});
</script>
</body>
</html>
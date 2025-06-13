<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des cours de la section secondaire
$cours = [];
$cours_query = "SELECT c.*, c.titre as nom, p.nom as prof_nom, p.prenom as prof_prenom, cl.nom as classe_nom 
                FROM cours c 
                LEFT JOIN professeurs p ON c.professeur_id = p.id 
                LEFT JOIN classes cl ON c.classe_id = cl.id 
                WHERE cl.section = 'secondaire'
                ORDER BY cl.nom, c.titre";  // Modifié c.nom en c.titre
$cours_result = $mysqli->query($cours_query);
if ($cours_result) {
    while ($row = $cours_result->fetch_assoc()) {
        $cours[] = $row;
    }
}

// Récupération des professeurs pour le formulaire d'ajout/modification
$professeurs = [];
$professeurs_query = "SELECT id, nom, prenom FROM professeurs ORDER BY nom ASC";
$professeurs_result = $mysqli->query($professeurs_query);
if ($professeurs_result) {
    while ($row = $professeurs_result->fetch_assoc()) {
        $professeurs[] = $row;
    }
}

// Récupération des classes pour le formulaire d'ajout/modification
$classes = [];
$classes_query = "SELECT id, nom FROM classes WHERE section = 'secondaire' ORDER BY nom ASC";
$classes_result = $mysqli->query($classes_query);
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Messages de notification
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
// Effacer les messages après les avoir récupérés
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Cours</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
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
        Gestion des Cours
        <small>Section Secondaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Cours</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Cours - Section Secondaire</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ajouter-cours">
                  <i class="fa fa-plus"></i> Ajouter un cours
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="cours-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Nom du cours</th>
                    <th>Classe</th>
                    <th>Professeur</th>
                    <th>Coefficient</th>
                    <th>Heures/Semaine</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cours as $c): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($c['nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['classe_nom']); ?></td>
                    <td><?php echo htmlspecialchars($c['prof_nom'] . ' ' . $c['prof_prenom']); ?></td>
                    <td><?php echo isset($c['coefficient']) ? $c['coefficient'] : 'N/A'; ?></td>
                    <td><?php echo isset($c['heures_semaine']) ? $c['heures_semaine'] : 'N/A'; ?></td>
                    <td>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-voir-cours" 
                                data-id="<?php echo $c['id']; ?>" 
                                data-nom="<?php echo htmlspecialchars($c['nom']); ?>"
                                data-classe="<?php echo htmlspecialchars($c['classe_nom']); ?>"
                                data-classe-id="<?php echo $c['classe_id']; ?>"
                                data-professeur="<?php echo htmlspecialchars($c['prof_nom'] . ' ' . $c['prof_prenom']); ?>"
                                data-professeur-id="<?php echo $c['professeur_id']; ?>"
                                data-coefficient="<?php echo isset($c['coefficient']) ? $c['coefficient'] : ''; ?>"
                                data-heures="<?php echo isset($c['heures_semaine']) ? $c['heures_semaine'] : ''; ?>"
                                data-description="<?php echo htmlspecialchars($c['description']); ?>">
                          <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-modifier-cours"
                                data-id="<?php echo $c['id']; ?>" 
                                data-nom="<?php echo htmlspecialchars($c['nom']); ?>"
                                data-classe-id="<?php echo $c['classe_id']; ?>"
                                data-professeur-id="<?php echo $c['professeur_id']; ?>"
                                data-coefficient="<?php echo isset($c['coefficient']) ? $c['coefficient'] : '1'; ?>"
                                data-heures="<?php echo isset($c['heures_semaine']) ? $c['heures_semaine'] : '2'; ?>"
                                data-description="<?php echo htmlspecialchars($c['description']); ?>">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmerSuppression(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['nom']); ?>')">
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques des cours -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des cours par classe</h3>
            </div>
            <div class="box-body">
              <canvas id="coursParClasseChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des cours par professeur</h3>
            </div>
            <div class="box-body">
              <canvas id="coursParProfesseurChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3">
                  <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-book"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total des cours</span>
                      <span class="info-box-number"><?php echo count($cours); ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total heures/semaine</span>
                      <span class="info-box-number">
                        <?php 
                          $total_heures = array_sum(array_column($cours, 'heures_semaine'));
                          echo $total_heures; 
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="info-box bg-yellow">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Professeurs impliqués</span>
                      <span class="info-box-number">
                        <?php 
                          $prof_ids = array_unique(array_column($cours, 'professeur_id'));
                          echo count($prof_ids); 
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3">
                  <div class="info-box bg-red">
                    <span class="info-box-icon"><i class="fa fa-table"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Classes concernées</span>
                      <span class="info-box-number">
                        <?php 
                          $classe_ids = array_unique(array_column($cours, 'classe_id'));
                          echo count($classe_ids); 
                        ?>
                      </span>
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

  <!-- Modal Ajouter Cours -->
  <div class="modal fade" id="modal-ajouter-cours">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter un nouveau cours</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=ajouterCours" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="nom">Nom du cours</label>
              <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
              <label for="classe_id">Classe</label>
              <select class="form-control" id="classe_id" name="classe_id" required>
                <option value="">Sélectionner une classe</option>
                <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>"><?php echo htmlspecialchars($classe['nom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="professeur_id">Professeur</label>
              <select class="form-control" id="professeur_id" name="professeur_id" required>
                <option value="">Sélectionner un professeur</option>
                <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['id']; ?>"><?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="coefficient">Coefficient</label>
              <input type="number" class="form-control" id="coefficient" name="coefficient" min="1" max="10" value="1" required>
            </div>
            <div class="form-group">
              <label for="heures_semaine">Heures par semaine</label>
              <input type="number" class="form-control" id="heures_semaine" name="heures_semaine" min="1" max="20" value="2" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Modifier Cours -->
  <div class="modal fade" id="modal-modifier-cours">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Modifier le cours</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=modifierCours" method="post">
          <div class="modal-body">
            <input type="hidden" id="edit-id" name="id">
            <div class="form-group">
              <label for="edit-nom">Nom du cours</label>
              <input type="text" class="form-control" id="edit-nom" name="nom" required>
            </div>
            <div class="form-group">
              <label for="edit-classe-id">Classe</label>
              <select class="form-control" id="edit-classe-id" name="classe_id" required>
                <option value="">Sélectionner une classe</option>
                <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>"><?php echo htmlspecialchars($classe['nom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-professeur-id">Professeur</label>
              <select class="form-control" id="edit-professeur-id" name="professeur_id" required>
                <option value="">Sélectionner un professeur</option>
                <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['id']; ?>"><?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-coefficient">Coefficient</label>
              <input type="number" class="form-control" id="edit-coefficient" name="coefficient" min="1" max="10" required>
            </div>
            <div class="form-group">
              <label for="edit-heures-semaine">Heures par semaine</label>
              <input type="number" class="form-control" id="edit-heures-semaine" name="heures_semaine" min="1" max="20" required>
            </div>
            <div class="form-group">
              <label for="edit-description">Description</label>
              <textarea class="form-control" id="edit-description" name="description" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Voir Cours -->
  <div class="modal fade" id="modal-voir-cours">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Détails du cours</h4>
        </div>
        <div class="modal-body">
          <div class="box-body box-profile">
            <h3 class="profile-username text-center" id="view-nom"></h3>
            <p class="text-muted text-center" id="view-classe"></p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Professeur</b> <a class="pull-right" id="view-professeur"></a>
              </li>
              <li class="list-group-item">
                <b>Coefficient</b> <a class="pull-right" id="view-coefficient"></a>
              </li>
              <li class="list-group-item">
                <b>Heures par semaine</b> <a class="pull-right" id="view-heures"></a>
              </li>
            </ul>

            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Description</h3>
              </div>
              <div class="box-body">
                <p id="view-description"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
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
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialiser DataTable
  $('#cours-table').DataTable({
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
  
  // Préparer les données pour les graphiques
  var classeLabels = [];
  var classeData = [];
  var profLabels = [];
  var profData = [];
  var backgroundColors = [
    'rgba(60, 141, 188, 0.8)',
    'rgba(0, 166, 90, 0.8)',
    'rgba(243, 156, 18, 0.8)',
    'rgba(221, 75, 57, 0.8)',
    'rgba(0, 192, 239, 0.8)',
    'rgba(210, 214, 222, 0.8)',
    'rgba(216, 27, 96, 0.8)',
    'rgba(156, 39, 176, 0.8)',
    'rgba(63, 81, 181, 0.8)',
    'rgba(0, 150, 136, 0.8)'
  ];
  
  // Regrouper les données par classe
  var classeStats = {};
  var profStats = {};
  
  <?php
  foreach ($cours as $c) {
    echo "if (!classeStats['" . addslashes($c['classe_nom']) . "']) classeStats['" . addslashes($c['classe_nom']) . "'] = 0;\n";
    echo "classeStats['" . addslashes($c['classe_nom']) . "']++;\n";
    
    echo "var profNom = '" . addslashes($c['prof_nom'] . ' ' . $c['prof_prenom']) . "';\n";
    echo "if (!profStats[profNom]) profStats[profNom] = 0;\n";
    echo "profStats[profNom]++;\n";
  }
  ?>
  
  // Convertir les données regroupées en tableaux pour Chart.js
  for (var classe in classeStats) {
    classeLabels.push(classe);
    classeData.push(classeStats[classe]);
  }
  
  for (var prof in profStats) {
    profLabels.push(prof);
    profData.push(profStats[prof]);
  }
  
  // Créer le graphique des cours par classe
  var classeCtx = document.getElementById('coursParClasseChart').getContext('2d');
  var classeChart = new Chart(classeCtx, {
    type: 'bar',
    data: {
      labels: classeLabels,
      datasets: [{
        label: 'Nombre de cours',
        data: classeData,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      legend: {
        position: 'top',
      },
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
  
  // Créer le graphique des cours par professeur
  var profCtx = document.getElementById('coursParProfesseurChart').getContext('2d');
  var profChart = new Chart(profCtx, {
    type: 'pie',
    data: {
      labels: profLabels,
      datasets: [{
        data: profData,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      legend: {
        position: 'right',
      }
    }
  });
  
  // Gestion des modals
  $('#modal-modifier-cours').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var nom = button.data('nom');
    var classe_id = button.data('classe-id');
    var professeur_id = button.data('professeur-id');
    var coefficient = button.data('coefficient');
    var heures = button.data('heures');
    var description = button.data('description');
    
    var modal = $(this);
    modal.find('#edit-id').val(id);
    modal.find('#edit-nom').val(nom);
    modal.find('#edit-classe-id').val(classe_id);
    modal.find('#edit-professeur-id').val(professeur_id);
    modal.find('#edit-coefficient').val(coefficient);
    modal.find('#edit-heures-semaine').val(heures);
    modal.find('#edit-description').val(description);
  });
  
  $('#modal-voir-cours').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var nom = button.data('nom');
    var classe = button.data('classe');
    var professeur = button.data('professeur');
    var coefficient = button.data('coefficient');
    var heures = button.data('heures');
    var description = button.data('description');
    
    var modal = $(this);
    modal.find('#view-nom').text(nom);
    modal.find('#view-classe').text(classe);
    modal.find('#view-professeur').text(professeur);
    modal.find('#view-coefficient').text(coefficient);
    modal.find('#view-heures').text(heures);
    modal.find('#view-description').text(description || 'Aucune description disponible');
  });
});

// Fonction pour confirmer la suppression d'un cours
function confirmerSuppression(id, nom) {
  if (confirm('Êtes-vous sûr de vouloir supprimer le cours "' + nom + '" ?')) {
    window.location.href = '<?php echo BASE_URL; ?>index.php?controller=Prefet&action=supprimerCours&id=' + id;
  }
}
</script>
</body>
</html>
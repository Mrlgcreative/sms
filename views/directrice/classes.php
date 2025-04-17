<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des classes de la section maternelle
$classes = [];
$classes_query = "SELECT c.*, COUNT(e.id) as nb_eleves 
                 FROM classes c 
                 LEFT JOIN eleves e ON e.classe_id = c.id
                 WHERE c.section = 'maternelle'
                 GROUP BY c.id";
$classes_result = $mysqli->query($classes_query);
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        // Add total_eleves key for compatibility with existing code
        $row['total_eleves'] = $row['nb_eleves'];
        $classes[] = $row;
    }
}

// Récupération des professeurs pour le formulaire d'ajout/modification
$professeurs = [];
$professeurs_query = "SELECT id, nom, prenom FROM professeurs WHERE section = 'maternelle' ORDER BY nom ASC";
$professeurs_result = $mysqli->query($professeurs_query);
if ($professeurs_result) {
    while ($row = $professeurs_result->fetch_assoc()) {
        $professeurs[] = $row;
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
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Classes Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- SweetAlert -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>plugins/sweetalert2/sweetalert2.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Classes
        <small>Section Maternelle</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Classes</li>
      </ol>
    </section>

    <section class="content">
      <!-- Affichage des messages de succès ou d'erreur -->
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] == 'success' ? 'check' : 'ban'; ?>"></i> Information</h4>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
      <?php endif; ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Classes - Section Maternelle</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ajouter-classe">
                  <i class="fa fa-plus"></i> Ajouter une classe
                </button>
                <button type="button" class="btn btn-success" onclick="printContent('classes-table')">
                  <i class="fa fa-print"></i> Imprimer
                </button>
              </div>
            </div>
            <div class="box-body">
              <!-- Filtres de recherche -->
              <div class="row" style="margin-bottom: 15px;">
                <div class="col-md-3">
                  <select id="filter-niveau" class="form-control">
                    <option value="">Tous les niveaux</option>
                    <option value="Petite Section">Petite Section</option>
                    <option value="Moyenne Section">Moyenne Section</option>
                    <option value="Grande Section">Grande Section</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <input type="text" id="search-classe" class="form-control" placeholder="Rechercher une classe...">
                </div>
              </div>

              <table id="classes-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Nom</th>
                    <th>Niveau</th>
                    <th>Titulaire</th>
                    <th>Nombre d'élèves</th>
                    <th class="no-print">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($classes as $classe): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($classe['nom']); ?></td>
                    <td><?php echo htmlspecialchars($classe['niveau']); ?></td>
                    <td><?php echo htmlspecialchars($classe['titulaire']); ?></td>
                    <td><?php echo $classe['total_eleves']; ?></td>
                    <td class="no-print">
                      <div class="btn-group">
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal-voir-classe" 
                                data-id="<?php echo $classe['id']; ?>" 
                                data-nom="<?php echo htmlspecialchars($classe['nom']); ?>"
                                data-niveau="<?php echo htmlspecialchars($classe['niveau']); ?>"
                                data-titulaire="<?php echo htmlspecialchars($classe['titulaire']); ?>"
                                data-eleves="<?php echo $classe['total_eleves']; ?>">
                          <i class="fa fa-eye"></i>
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
      
      <!-- Statistiques des classes -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des élèves par classe</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="classeChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box bg-aqua">
                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total des classes</span>
                      <span class="info-box-number"><?php echo count($classes); ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="info-box bg-green">
                    <span class="info-box-icon"><i class="fa fa-child"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Moyenne d'élèves</span>
                      <span class="info-box-number">
                        <?php 
                          $total_eleves = array_sum(array_column($classes, 'total_eleves'));
                          echo count($classes) > 0 ? round($total_eleves / count($classes), 1) : 0; 
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Niveau</th>
                      <th>Nombre de classes</th>
                      <th>Total d'élèves</th>
                      <th>Moyenne par classe</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $niveaux = [];
                    foreach ($classes as $classe) {
                      if (!isset($niveaux[$classe['niveau']])) {
                        $niveaux[$classe['niveau']] = [
                          'count' => 0,
                          'eleves' => 0
                        ];
                      }
                      $niveaux[$classe['niveau']]['count']++;
                      $niveaux[$classe['niveau']]['eleves'] += $classe['total_eleves'];
                    }
                    
                    foreach ($niveaux as $niveau => $data) {
                      $moyenne = $data['count'] > 0 ? round($data['eleves'] / $data['count'], 1) : 0;
                      echo "<tr>
                              <td>{$niveau}</td>
                              <td>{$data['count']}</td>
                              <td>{$data['eleves']}</td>
                              <td>{$moyenne}</td>
                            </tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique de répartition par niveau -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des élèves par niveau</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <canvas id="niveauChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal Ajouter Classe -->
  <div class="modal fade" id="modal-ajouter-classe">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter une nouvelle classe</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=ajouterClasse" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="nom">Nom de la classe</label>
              <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            
            <div class="form-group">
              <label for="niveau">Niveau</label>
              <select class="form-control" id="niveau" name="niveau" required>
                <option value="">Sélectionner un niveau</option>
                <option value="Petite Section">Petite Section</option>
                <option value="Moyenne Section">Moyenne Section</option>
                <option value="Grande Section">Grande Section</option>
              </select>
            </div>
            
            <div class="form-group">
              <label for="titulaire">Professeur titulaire</label>
              <select class="form-control" id="titulaire" name="titulaire" required>
                <option value="">Sélectionner un professeur</option>
                <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['nom'] . ' ' . $prof['prenom']; ?>"><?php echo $prof['nom'] . ' ' . $prof['prenom']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="salle">Salle</label>
              <input type="text" class="form-control" id="salle" name="salle" required>
            </div>
            
            <input type="hidden" name="section" value="maternelle">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-primary">Ajouter</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Modifier Classe -->
  <div class="modal fade" id="modal-modifier-classe">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Modifier la classe</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=modifierClasse" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="edit-nom">Nom de la classe</label>
              <input type="text" class="form-control" id="edit-nom" name="nom" required>
            </div>
            <div class="form-group">
              <label for="edit-niveau">Niveau</label>
              <select class="form-control" id="edit-niveau" name="niveau" required>
                <option value="">Sélectionner un niveau</option>
                <option value="Petite Section">Petite Section</option>
                <option value="Moyenne Section">Moyenne Section</option>
                <option value="Grande Section">Grande Section</option>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-titulaire">Professeur titulaire</label>
              <select class="form-control" id="edit-titulaire" name="titulaire" required>
                <option value="">Sélectionner un professeur</option>
                <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['nom'] . ' ' . $prof['prenom']; ?>"><?php echo $prof['nom'] . ' ' . $prof['prenom']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-salle">Salle</label>
              <input type="text" class="form-control" id="edit-salle" name="salle" required>
            </div>
            <input type="hidden" name="id" id="edit-id">
            <input type="hidden" name="section" value="maternelle">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Voir Classe -->
  <div class="modal fade" id="modal-voir-classe">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Détails de la classe</h4>
        </div>
        <div class="modal-body">
          <div class="box-body box-profile">
            <h3 class="profile-username text-center" id="view-nom"></h3>
            <p class="text-muted text-center" id="view-niveau"></p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Professeur titulaire</b> <a class="pull-right" id="view-titulaire"></a>
              </li>
              <li class="list-group-item">
                <b>Salle</b> <a class="pull-right" id="view-salle"></a>
              </li>
              <li class="list-group-item">
                <b>Nombre d'élèves</b> <a class="pull-right" id="view-eleves"></a>
              </li>
              <li class="list-group-item">
                <b>Section</b> <a class="pull-right">Maternelle</a>
              </li>
            </ul>

            <a href="#" id="view-eleves-link" class="btn btn-primary btn-block"><b>Voir les élèves</b></a>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Formulaire de suppression caché -->
  <form id="delete-classe-form" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=supprimerClasse" method="post" style="display: none;">
    <input type="hidden" name="id" id="delete-id">
  </form>

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
<script src="<?php echo BASE_URL; ?>plugins/sweetalert2/sweetalert2.min.js"></script>

<script>
$(function () {
  // Initialiser DataTable
  var table = $('#classes-table').DataTable({
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
  
  // Filtrage par niveau
  $('#filter-niveau').on('change', function() {
    table.column(1).search(this.value).draw();
  });
  
  // Recherche globale
  $('#search-classe').on('keyup', function() {
    table.search(this.value).draw();
  });
  
  // Préparer les données pour le graphique des classes
  var classLabels = [];
  var classData = [];
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
  
  <?php
  if (!empty($classes)) {
    echo "// Données des classes\n";
    foreach ($classes as $index => $classe) {
      echo "classLabels.push('" . addslashes($classe['nom']) . "');\n";
      echo "classData.push(" . $classe['total_eleves'] . ");\n";
    }
  }
  ?>
  
  // Créer le graphique des classes
  var classeCtx = document.getElementById('classeChart').getContext('2d');
  var classeChart = new Chart(classeCtx, {
    type: 'bar',
    data: {
      labels: classLabels,
      datasets: [{
        label: 'Nombre d\'élèves',
        data: classData,
        backgroundColor: backgroundColors,
        borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true
          }
        }]
      }
    }
  });
  
  // Préparer les données pour le graphique des niveaux
  var niveauLabels = [];
  var niveauData = [];
  var niveauColors = [
    'rgba(60, 141, 188, 0.8)',
    'rgba(0, 166, 90, 0.8)',
    'rgba(243, 156, 18, 0.8)'
  ];
  
  <?php
  if (!empty($niveaux)) {
    echo "// Données des niveaux\n";
    foreach ($niveaux as $niveau => $data) {
      echo "niveauLabels.push('" . addslashes($niveau) . "');\n";
      echo "niveauData.push(" . $data['eleves'] . ");\n";
    }
  }
  ?>
  
  // Créer le graphique des niveaux
  var niveauCtx = document.getElementById('niveauChart').getContext('2d');
  var niveauChart = new Chart(niveauCtx, {
    type: 'pie',
    data: {
      labels: niveauLabels,
      datasets: [{
        data: niveauData,
        backgroundColor: niveauColors,
        borderColor: niveauColors.map(color => color.replace('0.8', '1')),
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: 'right'
      }
    }
  });
  
  // Gestion des modals
  $('#modal-voir-classe').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var nom = button.data('nom');
    var niveau = button.data('niveau');
    var titulaire = button.data('titulaire');
    var eleves = button.data('eleves');
    var salle = button.data('salle') || 'Non spécifiée';
    
    var modal = $(this);
    modal.find('#view-nom').text(nom);
    modal.find('#view-niveau').text(niveau);
    modal.find('#view-titulaire').text(titulaire);
    modal.find('#view-salle').text(salle);
    modal.find('#view-eleves').text(eleves);
    modal.find('#view-eleves-link').attr('href', '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=voirEleves&classe=' + encodeURIComponent(nom));
  });
  
  $('#modal-modifier-classe').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var nom = button.data('nom');
    var niveau = button.data('niveau');
    var titulaire = button.data('titulaire');
    var salle = button.data('salle') || '';
    
    var modal = $(this);
    modal.find('#edit-id').val(id);
    modal.find('#edit-nom').val(nom);
    modal.find('#edit-niveau').val(niveau);
    modal.find('#edit-titulaire').val(titulaire);
    modal.find('#edit-salle').val(salle);
  });
  
  // Gestion de la suppression
  $('.btn-delete-classe').on('click', function() {
    var id = $(this).data('id');
    var nom = $(this).data('nom');
    var eleves = $(this).data('eleves');
    
    Swal.fire({
      title: 'Êtes-vous sûr?',
      text: "Vous êtes sur le point de supprimer la classe '" + nom + "'. " + 
            (eleves > 0 ? "Cette classe contient " + eleves + " élève(s)!" : ""),
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Oui, supprimer!',
      cancelButtonText: 'Annuler'
    }).then((result) => {
      if (result.isConfirmed) {
        $('#delete-id').val(id);
        $('#delete-classe-form').submit();
      }
    });
  });
  
  // Fonction pour imprimer
  function printContent(elementId) {
    var printContents = document.getElementById(elementId).outerHTML;
    var originalContents = document.body.innerHTML;
    
    document.body.innerHTML = '<div class="container"><h2 class="text-center">Liste des Classes - Section Maternelle</h2>' + printContents + '</div>';
    
    window.print();
    
    document.body.innerHTML = originalContents;
    location.reload();
  }
});
</script>
</body>
</html>

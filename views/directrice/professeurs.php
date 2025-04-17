<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des professeurs qui enseignent dans la section maternelle
$query = "SELECT p.id, p.nom, p.prenom, p.email, cl.nom as classe,
          GROUP_CONCAT(c.titre SEPARATOR ', ') as cours_enseignes,
          COUNT(c.id) as total_cours 
          FROM professeurs p 
          LEFT JOIN classes cl ON p.classe_id = cl.id
          LEFT JOIN cours c ON p.id = c.professeur_id 
          WHERE p.section='maternelle'
          GROUP BY p.id 
          ORDER BY p.nom, p.prenom";
$result = $mysqli->query($query);

// Récupération des présences des professeurs pour aujourd'hui
$today = date('Y-m-d');
$presences_query = "SELECT pp.*, p.nom, p.prenom 
                   FROM presences pp
                   JOIN professeurs p ON pp.professeur_id = p.id
                   WHERE DATE(pp.date_presence) = '$today'
                   ORDER BY pp.heure_arrivee";
$presences_result = $mysqli->query($presences_query);

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Professeurs Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
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
            <i class="fa fa-child"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
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
        Professeurs
        <small>Section Maternelle</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Professeurs</li>
      </ol>
    </section>

    <section class="content">
      <!-- Suivi de présence des professeurs -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Suivi de présence des professeurs</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-ajouter-presence">
                  <i class="fa fa-plus"></i> Enregistrer une présence
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="presencesList" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Professeur</th>
                      <th>Date</th>
                      <th>Heure d'arrivée</th>
                      <th>Heure de départ</th>
                      <th>Durée</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if ($presences_result && $presences_result->num_rows > 0) {
                      while ($presence = $presences_result->fetch_assoc()) {
                        // Calcul de la durée de présence
                        $arrivee = strtotime($presence['heure_arrivee']);
                        $depart = !empty($presence['heure_depart']) ? strtotime($presence['heure_depart']) : time();
                        $duree_minutes = round(($depart - $arrivee) / 60);
                        $duree = floor($duree_minutes / 60) . 'h ' . ($duree_minutes % 60) . 'min';
                        
                        echo "<tr>
                                <td>{$presence['nom']} {$presence['prenom']}</td>
                                <td>" . date('d/m/Y', strtotime($presence['date_presence'])) . "</td>
                                <td>" . date('H:i', strtotime($presence['heure_arrivee'])) . "</td>
                                <td>" . (!empty($presence['heure_depart']) ? date('H:i', strtotime($presence['heure_depart'])) : '<span class="label label-success">En cours</span>') . "</td>
                                <td>{$duree}</td>
                                <td>
                                  <div class='btn-group'>
                                    " . (empty($presence['heure_depart']) ? 
                                    "<button class='btn btn-warning btn-xs enregistrer-depart' data-id='{$presence['id']}'>
                                      <i class='fa fa-sign-out'></i> Enregistrer départ
                                    </button>" : "") . "
                                    <button class='btn btn-danger btn-xs supprimer-presence' data-id='{$presence['id']}'>
                                      <i class='fa fa-trash'></i>
                                    </button>
                                  </div>
                                </td>
                              </tr>";
                      }
                    } else {
                      echo "<tr><td colspan='6' class='text-center'>Aucune présence enregistrée aujourd'hui</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Liste des professeurs -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des professeurs de la section maternelle</h3>
            </div>
            <div class="box-body">
              <table id="professeursList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['id']}</td>
                              <td>{$row['nom']}</td>
                              <td>{$row['prenom']}</td>
                              <td>{$row['classe']}</td>
                              <td>{$row['email']}</td>
                              <td>
                                <div class='btn-group'>
                                  <a href='" . BASE_URL . "index.php?controller=Directrice&action=voirProfesseur&id={$row['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> Voir</a>
                                  <button class='btn btn-success btn-xs enregistrer-presence' data-id='{$row['id']}' data-nom='{$row['nom']}' data-prenom='{$row['prenom']}'>
                                    <i class='fa fa-clock-o'></i> Présence
                                  </button>
                                </div>
                              </td>
                            </tr>";
                    }
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques des professeurs -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des cours par professeur</h3>
            </div>
            <div class="box-body">
              <canvas id="coursParProfesseurChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
  
        <div class="row">
  <div class="col-md-6">
    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">Présence rapide des professeurs</h3>
      </div>
      <div class="box-body">
        <div class="table-responsive">
          <table id="quickPresenceList" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Professeur</th>
                <th>Présent aujourd'hui</th>
                <th>Heure d'arrivée</th>
                
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result && $result->num_rows > 0) {
                $result->data_seek(0);
                while ($row = $result->fetch_assoc()) {
                  // Vérifier si le professeur est déjà marqué présent aujourd'hui
                  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                  $check_query = "SELECT id, heure_arrivee, heure_depart FROM presences 
                                 WHERE professeur_id = {$row['id']} AND date_presence = '$today'";
                  $check_result = $mysqli->query($check_query);
                  $is_present = false;
                  $presence_id = 0;
                  $heure_arrivee = '';
                  
                  
                  if ($check_result && $check_result->num_rows > 0) {
                    $presence_data = $check_result->fetch_assoc();
                    $is_present = true;
                    $presence_id = $presence_data['id'];
                    $heure_arrivee = $presence_data['heure_arrivee'];
                    
                  }
                  $mysqli->close();
                  
                  echo "<tr>
                          <td>{$row['nom']} {$row['prenom']}</td>
                          <td>
                            <div class='checkbox'>
                              <label>
                                <input type='checkbox' class='presence-checkbox' 
                                  data-prof-id='{$row['id']}' 
                                  data-presence-id='{$presence_id}' 
                                  " . ($is_present ? "checked" : "") . ">
                                Présent
                              </label>
                            </div>
                          </td>
                          <td>" . ($is_present ? date('H:i', strtotime($heure_arrivee)) : "-") . "</td>
                          <td>" . (!empty($heure_depart) ? date('H:i', strtotime($heure_depart)) : 
                                  ($is_present ? "<button class='btn btn-warning btn-xs enregistrer-depart-rapide' data-id='{$presence_id}'>
                                    <i class='fa fa-sign-out'></i> Départ
                                  </button>" : "-")) . "</td>
                        </tr>";
                }
              } else {
                echo "<tr><td colspan='4' class='text-center'>Aucun professeur trouvé</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

        
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales</h3>
            </div>
            <div class="box-body">
              <div class="info-box bg-purple">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total des professeurs</span>
                  <span class="info-box-number">
                    <?php echo $result ? $result->num_rows : 0; ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Modal Ajouter Présence -->
  <div class="modal fade" id="modal-ajouter-presence">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Enregistrer une présence</h4>
        </div>
        <form id="form-ajouter-presence" action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=ajouterPresenceProfesseur" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="professeur_id">Professeur</label>
              <select class="form-control" id="professeur_id" name="professeur_id" required>
                <option value="">Sélectionner un professeur</option>
                <?php
                if ($result && $result->num_rows > 0) {
                  $result->data_seek(0);
                  while ($row = $result->fetch_assoc()) {
                    echo "<option value='{$row['id']}'>{$row['nom']} {$row['prenom']}</option>";
                  }
                }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="date_presence">Date</label>
              <input type="date" class="form-control" id="date_presence" name="date_presence" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
              <label for="heure_arrivee">Heure d'arrivée</label>
              <input type="time" class="form-control" id="heure_arrivee" name="heure_arrivee" value="<?php echo date('H:i'); ?>" required>
            </div>
            <div class="form-group">
              <label for="heure_depart">Heure de départ (optionnel)</label>
              <input type="time" class="form-control" id="heure_depart" name="heure_depart">
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


<!-- Enregistrer départ rapide
Add this JavaScript at the end of the file, before the closing </body> tag -->


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
    // Existing JavaScript code...
    
    // Gestion des checkboxes de présence
    $('.presence-checkbox').change(function() {
      var profId = $(this).data('prof-id');
      var presenceId = $(this).data('presence-id');
      var isChecked = $(this).prop('checked');
      
      if (isChecked) {
        // Marquer le professeur comme présent
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=marquerPresenceProfesseur',
          type: 'POST',
          data: {
            professeur_id: profId,
            date_presence: '<?php echo $today; ?>',
            heure_arrivee: new Date().toTimeString().split(' ')[0]
          },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.success) {
              // Mettre à jour l'interface
              location.reload();
            } else {
              alert('Erreur: ' + data.message);
              // Remettre la checkbox à son état précédent
              $(this).prop('checked', false);
            }
          }.bind(this),
          error: function() {
            alert('Une erreur est survenue lors de l\'enregistrement de la présence.');
            // Remettre la checkbox à son état précédent
            $(this).prop('checked', false);
          }.bind(this)
        });
      } else {
        // Supprimer la présence du professeur
        if (presenceId > 0) {
          $.ajax({
            url: '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=supprimerPresenceProfesseur',
            type: 'POST',
            data: {
              presence_id: presenceId
            },
            success: function(response) {
              var data = JSON.parse(response);
              if (data.success) {
                // Mettre à jour l'interface
                location.reload();
              } else {
                alert('Erreur: ' + data.message);
                // Remettre la checkbox à son état précédent
                $(this).prop('checked', true);
              }
            }.bind(this),
            error: function() {
              alert('Une erreur est survenue lors de la suppression de la présence.');
              // Remettre la checkbox à son état précédent
              $(this).prop('checked', true);
            }.bind(this)
          });
        }
      }
    });
    
    // Enregistrer départ rapide
    $('.enregistrer-depart-rapide').click(function() {
      var presenceId = $(this).data('id');
      if (confirm('Voulez-vous enregistrer le départ de ce professeur?')) {
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=enregistrerDepartProfesseur',
          type: 'POST',
          data: {
            presence_id: presenceId,
            heure_depart: new Date().toTimeString().split(' ')[0]
          },
          success: function(response) {
            var data = JSON.parse(response);
            if (data.success) {
              alert('Départ enregistré avec succès!');
              location.reload();
            } else {
              alert('Erreur: ' + data.message);
            }
          },
          error: function() {
            alert('Une erreur est survenue lors de l\'enregistrement du départ.');
          }
        });
      }
    });
  });
</script>

<script>
  $(function () {
    // DataTables initialization
    $('#professeursList').DataTable({
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
    
    $('#presencesList').DataTable({
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
    
    // Enregistrer présence depuis la liste des professeurs
    $('.enregistrer-presence').click(function() {
      var profId = $(this).data('id');
      var nom = $(this).data('nom');
      var prenom = $(this).data('prenom');
      
      $('#professeur_id').val(profId);
      $('#modal-ajouter-presence').modal('show');
    });
    
    // Enregistrer départ
    $('.enregistrer-depart').click(function() {
      var presenceId = $(this).data('id');
      if (confirm('Voulez-vous enregistrer le départ de ce professeur?')) {
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=enregistrerDepartProfesseur',
          type: 'POST',
          data: {
            presence_id: presenceId,
            heure_depart: new Date().toTimeString().split(' ')[0]
          },
          success: function(response) {
            alert('Départ enregistré avec succès!');
            location.reload();
          },
          error: function() {
            alert('Une erreur est survenue lors de l\'enregistrement du départ.');
          }
        });
      }
    });
    
    // Supprimer présence
    $('.supprimer-presence').click(function() {
      var presenceId = $(this).data('id');
      if (confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement de présence?')) {
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Directrice&action=supprimerPresenceProfesseur',
          type: 'POST',
          data: {
            presence_id: presenceId
          },
          success: function(response) {
            alert('Présence supprimée avec succès!');
            location.reload();
          },
          error: function() {
            alert('Une erreur est survenue lors de la suppression de la présence.');
          }
        });
      }
    });
    
    // Données pour le graphique
    var profData = [];
    var profLabels = [];
    var backgroundColors = [
      'rgba(156, 39, 176, 0.8)',
      'rgba(233, 30, 99, 0.8)',
      'rgba(103, 58, 183, 0.8)',
      'rgba(63, 81, 181, 0.8)',
      'rgba(33, 150, 243, 0.8)',
      'rgba(0, 188, 212, 0.8)',
      'rgba(0, 150, 136, 0.8)',
      'rgba(76, 175, 80, 0.8)',
      'rgba(139, 195, 74, 0.8)',
      'rgba(205, 220, 57, 0.8)'
    ];
    
    <?php
    if ($result && $result->num_rows > 0) {
      $result->data_seek(0);
      while ($row = $result->fetch_assoc()) {
        $cours_count = $row['cours_enseignes'] ? count(explode(',', $row['cours_enseignes'])) : 0;
        echo "profLabels.push('" . addslashes($row['nom'] . ' ' . $row['prenom']) . "');\n";
        echo "profData.push(" . $cours_count . ");\n";
      }
    }
    ?>
    
    // Créer le graphique
    var ctx = document.getElementById('coursParProfesseurChart').getContext('2d');
    var myChart = new Chart(ctx, {
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
  });
</script>

</body>
</html>
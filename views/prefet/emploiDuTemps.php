<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Prefet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer l'ID de la classe sélectionnée
$classe_id = isset($_GET['classe_id']) ? intval($_GET['classe_id']) : 0;

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer uniquement les classes de la section secondaire
$classes = [];
$query_classes = "SELECT id, niveau FROM classes WHERE section = 'secondaire' ORDER BY nom";
$result_classes = $mysqli->query($query_classes);
if ($result_classes) {
    while ($row = $result_classes->fetch_assoc()) {
        $classes[] = $row;
    }
    $result_classes->free();
}

// Récupérer les cours disponibles pour la classe sélectionnée (uniquement avec professeurs du secondaire)
$cours_disponibles = [];
if ($classe_id > 0) {
    $query_cours = "SELECT c.id, c.titre, p.nom as prof_nom, p.prenom as prof_prenom 
                    FROM cours c 
                    LEFT JOIN professeurs p ON c.professeur_id = p.id 
                    WHERE p.section = 'secondaire'
                    ORDER BY c.titre";
    $stmt_cours = $mysqli->prepare($query_cours);
    $stmt_cours->execute();
    $result_cours = $stmt_cours->get_result();
    
    if ($result_cours) {
        while ($row = $result_cours->fetch_assoc()) {
            $cours_disponibles[] = $row;
        }
        $result_cours->free();
    }
    $stmt_cours->close();
}

// Récupérer les horaires existants depuis la base de données pour la classe sélectionnée
// (uniquement avec professeurs du secondaire)
$emploi_du_temps = [];
if ($classe_id > 0) {
    $query_horaires = "SELECT h.*, c.titre as cours_titre, p.nom as prof_nom, p.prenom as prof_prenom
                      FROM horaires h 
                      LEFT JOIN cours c ON h.cours_id = c.id 
                      LEFT JOIN professeurs p ON c.professeur_id = p.id 
                      WHERE h.classe_id = ? AND p.section = 'secondaire'
                      ORDER BY h.jour, h.heure_debut";
    $stmt_horaires = $mysqli->prepare($query_horaires);
    $stmt_horaires->bind_param("i", $classe_id);
    $stmt_horaires->execute();
    $result_horaires = $stmt_horaires->get_result();
    
    if ($result_horaires) {
        while ($row = $result_horaires->fetch_assoc()) {
            // Extraire l'heure de début et de fin pour créer la clé du format "08:00-09:00"
            $heure_format = $row['heure_debut'] . '-' . $row['heure_fin'];
            $emploi_du_temps[$row['jour']][$heure_format] = $row;
        }
        $result_horaires->free();
    }
    $stmt_horaires->close();
}

// Fermer la connexion
$mysqli->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Emploi du Temps</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .timetable-cell {
      height: 80px;
      border: 1px solid #ddd;
      padding: 5px;
      vertical-align: top;
    }
    .course-item {
      background-color: #3c8dbc;
      color: white;
      padding: 5px;
      margin-bottom: 5px;
      border-radius: 3px;
      cursor: pointer;
    }
    .empty-cell {
      height: 100%;
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #aaa;
      cursor: pointer;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php  include 'navbar.php'; ?>
 <?php include 'sidebar.php'; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Emploi du Temps
        <small>Gestion des emplois du temps</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Emploi du Temps</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']);
          ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php 
            echo $_SESSION['error_message']; 
            unset($_SESSION['error_message']);
          ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Sélectionner une classe</h3>
            </div>
            <div class="box-body">
              <form method="get" action="<?php echo BASE_URL; ?>index.php">
                <input type="hidden" name="controller" value="Prefet">
                <input type="hidden" name="action" value="emploiDuTemps">
                <div class="form-group">
                  <label for="classe_id">Classe:</label>
                  <select class="form-control" id="classe_id" name="classe_id" onchange="this.form.submit()">
                    <option value="">Sélectionner une classe</option>
                    <?php foreach ($classes as $classe): ?>
                      <option value="<?php echo $classe['id']; ?>" <?php echo (isset($classe_id) && $classe_id == $classe['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($classe['niveau']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($classe_id) && $classe_id > 0): ?>
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Emploi du temps</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class=="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Heure</th>
                      <th>Lundi</th>
                      <th>Mardi</th>
                      <th>Mercredi</th>
                      <th>Jeudi</th>
                      <th>Vendredi</th>
                      <th>Samedi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    $heures = ['08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '12:00-13:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00'];
                    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                    
                    foreach ($heures as $heure): 
                    ?>
                    <tr>
                      <td><?php echo $heure; ?></td>
                      <?php foreach ($jours as $jour): ?>
                      <td class="cell-cours" data-jour="<?php echo $jour; ?>" data-heure="<?php echo $heure; ?>">
                        <?php 
                        // Afficher le cours correspondant à cette heure et ce jour
                        $cours_trouve = false;
                        if (isset($emploi_du_temps[$jour][$heure])) {
                          $cours = $emploi_du_temps[$jour][$heure];
                          echo '<div class="cours-info">';
                          echo '<strong>' . htmlspecialchars($cours['titre']) . '</strong><br>';
                          echo 'Prof: ' . htmlspecialchars($cours['prof_nom'] . ' ' . $cours['prof_prenom']);
                          echo '<div class="cours-actions">';
                          echo '<a href="' . BASE_URL . 'index.php?controller=Prefet&action=modifierHoraire&id=' . $cours['id'] . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a> ';
                          echo '<a href="' . BASE_URL . 'index.php?controller=Prefet&action=supprimerHoraire&id=' . $cours['id'] . '&classe_id=' . $classe_id . '" class="btn btn-xs btn-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce cours ?\');"><i class="fa fa-trash"></i></a>';
                          echo '</div>';
                          echo '</div>';
                          $cours_trouve = true;
                        }
                        
                        if (!$cours_trouve) {
                          echo '<div class="empty-cell">';
                          echo '<span class="no-cours">-</span>';
                          echo '<div class="add-cours-btn">';
                          echo '<a href="#" class="btn btn-xs btn-success add-cours-modal" data-jour="' . $jour . '" data-heure="' . $heure . '" data-toggle="modal" data-target="#addCoursModal"><i class="fa fa-plus"></i></a>';
                          echo '</div>';
                          echo '</div>';
                        }
                        ?>
                      </td>
                      <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Modal pour ajouter un cours rapidement -->
      <div class="modal fade" id="addCoursModal" tabindex="-1" role="dialog" aria-labelledby="addCoursModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="addCoursModalLabel">Ajouter un cours</h4>
            </div>
            <div class="modal-body">
              <form id="addCoursForm" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=ajouterHoraire">
                <input type="hidden" name="classe_id" value="<?php echo $classe_id; ?>">
                <input type="hidden" id="modal_jour" name="jour" value="">
                <input type="hidden" id="modal_heure" name="heure" value="">
                
                <div class="form-group">
                  <label for="modal_cours_id">Cours:</label>
                  <select class="form-control" id="modal_cours_id" name="cours_id" required>
                    <option value="">Sélectionner un cours</option>
                    <?php if (isset($cours_disponibles) && !empty($cours_disponibles)): ?>
                      <?php foreach ($cours_disponibles as $cours): ?>
                        <option value="<?php echo $cours['id']; ?>"><?php echo htmlspecialchars($cours['titre']); ?> (<?php echo htmlspecialchars($cours['prof_nom'] . ' ' . $cours['prof_prenom']); ?>)</option>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <option value="" disabled>Aucun cours disponible</option>
                    <?php endif; ?>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
              <button type="button" class="btn btn-success" onclick="document.getElementById('addCoursForm').submit();">Ajouter</button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Styles CSS pour le tableau dynamique -->
      <style>
        .cell-cours {
          position: relative;
          min-height: 80px;
          transition: all 0.3s ease;
          cursor: pointer;
        }
        
        .empty-cell {
          display: flex;
          flex-direction: column;
          justify-content: center;
          align-items: center;
          height: 100%;
          min-height: 80px;
        }
        
        .empty-cell .no-cours {
          color: #ccc;
          font-size: 18px;
        }
        
        .add-cours-btn {
          display: none;
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          z-index: 10;
        }
        
        .cell-cours:hover {
          background-color: #f9f9f9;
        }
        
        .cell-cours:hover .add-cours-btn {
          display: block;
        }
        
        .cours-info {
          position: relative;
          padding: 8px;
          background-color: #3c8dbc;
          color: white;
          border-radius: 4px;
          height: 100%;
          min-height: 70px;
          transition: all 0.3s ease;
        }
        
        .cours-actions {
          display: none;
          position: absolute;
          bottom: 5px;
          right: 5px;
        }
        
        .cours-info:hover {
          transform: scale(1.02);
          box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .cours-info:hover .cours-actions {
          display: block;
        }
        
        .cours-titre {
          font-weight: bold;
          margin-bottom: 5px;
        }
        
        .cours-prof {
          font-size: 12px;
        }
        
        .cours-salle {
          font-size: 11px;
          font-style: italic;
          position: absolute;
          bottom: 5px;
          left: 8px;
        }
        
        /* Animation pour l'ajout de cours */
        @keyframes coursAdded {
          0% { transform: scale(0.8); opacity: 0; }
          100% { transform: scale(1); opacity: 1; }
        }
        
        .cours-added {
          animation: coursAdded 0.5s ease-out;
        }
      </style>
      
      <!-- Script JavaScript pour le tableau dynamique -->
      <script>
        $(document).ready(function() {
          // Initialiser les boutons d'ajout de cours
          $('.add-cours-modal').click(function() {
            var jour = $(this).data('jour');
            var heure = $(this).data('heure');
            
            // Extraire les heures de début et de fin
            var heures = heure.split('-');
            var heure_debut = heures[0];
            var heure_fin = heures[1];
            
            $('#modal_jour').val(jour);
            $('#modal_heure').val(heure);
            
            // Mettre à jour les champs cachés pour l'heure de début et de fin
            if ($('#addCoursForm input[name="heure_debut"]').length === 0) {
              $('#addCoursForm').append('<input type="hidden" name="heure_debut" value="' + heure_debut + '">');
            } else {
              $('#addCoursForm input[name="heure_debut"]').val(heure_debut);
            }
            
            if ($('#addCoursForm input[name="heure_fin"]').length === 0) {
              $('#addCoursForm').append('<input type="hidden" name="heure_fin" value="' + heure_fin + '">');
            } else {
              $('#addCoursForm input[name="heure_fin"]').val(heure_fin);
            }
            
            $('#addCoursModalLabel').text('Ajouter un cours - ' + jour + ' ' + heure);
          });
          
          // Effet de survol pour les cellules
          $('.cell-cours').hover(
            function() {
              $(this).css('background-color', '#f9f9f9');
            },
            function() {
              $(this).css('background-color', '');
            }
          );
          
          // Fonction pour ajouter un cours via AJAX
          $('#addCoursBtn').click(function(e) {
            e.preventDefault();
            
            // Vérifier que tous les champs sont remplis
            var cours_id = $('#modal_cours_id').val();
            if (!cours_id) {
              alert('Veuillez sélectionner un cours');
              return;
            }
            
            // Récupérer les données du formulaire
            var formData = $('#addCoursForm').serialize();
            
            // Désactiver le bouton pendant la soumission
            $(this).prop('disabled', true);
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Ajout en cours...');
            
            // Envoyer la requête AJAX
            $.ajax({
              url: $('#addCoursForm').attr('action'),
              type: 'POST',
              data: formData,
              dataType: 'json',
              success: function(response) {
                if (response.success) {
                  // Fermer le modal
                  $('#addCoursModal').modal('hide');
                  
                  // Mettre à jour la cellule dans le tableau
                  var jour = $('#modal_jour').val();
                  var heure = $('#modal_heure').val();
                  var cell = $('.cell-cours[data-jour="' + jour + '"][data-heure="' + heure + '"]');
                  
                  // Récupérer les informations du cours
                  var cours_titre = $('#modal_cours_id option:selected').text().split('(')[0].trim();
                  var cours_prof = $('#modal_cours_id option:selected').text().split('(')[1].replace(')', '').trim();
                  var cours_salle = $('#modal_salle').val();
                  
                  // Créer le HTML pour le cours
                  var coursHTML = `
                    <div class="cours-info cours-added">
                      <div class="cours-titre">${cours_titre}</div>
                      <div class="cours-prof">Prof: ${cours_prof}</div>
                      ${cours_salle ? '<div class="cours-salle">Salle: ' + cours_salle + '</div>' : ''}
                      <div class="cours-actions">
                        <a href="${BASE_URL}index.php?controller=Prefet&action=modifierHoraire&id=${response.id}" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i></a>
                        <a href="${BASE_URL}index.php?controller=Prefet&action=supprimerHoraire&id=${response.id}&classe_id=${$('#addCoursForm input[name="classe_id"]').val()}" class="btn btn-xs btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');"><i class="fa fa-trash"></i></a>
                      </div>
                    </div>
                  `;
                  
                  // Remplacer le contenu de la cellule
                  cell.html(coursHTML);
                  
                  // Afficher un message de succès
                  toastr.success('Le cours a été ajouté avec succès');
                } else {
                  // Afficher un message d'erreur
                  toastr.error(response.message || 'Une erreur est survenue lors de l\'ajout du cours');
                }
              },
              error: function() {
                toastr.error('Une erreur est survenue lors de la communication avec le serveur');
              },
              complete: function() {
                // Réactiver le bouton
                $('#addCoursBtn').prop('disabled', false);
                $('#addCoursBtn').html('Ajouter');
              }
            });
          });
          
          // Initialiser toastr pour les notifications
          toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000"
          };
        });
      </script>
<?php endif; ?>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>


</body>
</html>
<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<!-- Toastr JS pour les notifications -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
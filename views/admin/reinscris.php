<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des élèves de l'année précédente
$eleves_precedents_query = "SELECT e.*, c.niveau as classe_actuelle, e.nom_pere as parent_nom, e.nom_mere as parent_prenom 
                           FROM eleves e 
                           LEFT JOIN classes c ON e.classe_id = c.id 
                          
                           WHERE e.session_scolaire_id = (
                               SELECT id FROM sessions_scolaires 
                               WHERE annee_debut = (SELECT MAX(annee_debut) - 1 FROM sessions_scolaires)
                           )
                           ORDER BY e.nom, e.prenom";
$eleves_precedents_result = $mysqli->query($eleves_precedents_query);

// Récupération des classes disponibles
$classes_query = "SELECT * FROM classes ORDER BY niveau, section";
$classes_result = $mysqli->query($classes_query);
$classes = [];
while ($classe = $classes_result->fetch_assoc()) {
    $classes[] = $classe;
}

// Récupération des sessions scolaires
$sessions_query = "SELECT * FROM sessions_scolaires ORDER BY annee_debut DESC";
$sessions_result = $mysqli->query($sessions_query);
$sessions = [];
while ($session = $sessions_result->fetch_assoc()) {
    $sessions[] = $session;
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Réinscriptions</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/reinscris.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
  
<div class="wrapper">
  <?php include 'navbar.php'; ?>



  <!-- Barre latérale gauche -->
  
  <?php include 'sidebar.php'; ?> 

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <i class="fa fa-refresh"></i> Gestion des Réinscriptions
        <small>Réinscrire les élèves pour la nouvelle année scolaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Réinscriptions</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
      <?php endif; ?>

      <!-- Formulaire de réinscription -->
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><i class="fa fa-refresh"></i> Formulaire de Réinscription</h3>
        </div>
        
        <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=reinscris" id="reinscriptionForm">
          <div class="box-body">
            <!-- Sélection de la session scolaire -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="session_scolaire">Session scolaire de destination :</label>
                  <select class="form-control" id="session_scolaire" name="session_scolaire" required>
                    <option value="">Sélectionner une session</option>
                    <?php foreach ($sessions as $session): ?>
                      <option value="<?php echo $session['id']; ?>">
                        <?php echo htmlspecialchars($session['annee_debut'] . ' - ' . $session['libelle']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div class="reinscription-actions">
                    <button type="button" class="btn btn-info" id="selectAll">
                      <i class="fa fa-check-square-o"></i> Sélectionner tout
                    </button>
                    <button type="button" class="btn btn-warning" id="unselectAll">
                      <i class="fa fa-square-o"></i> Désélectionner tout
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="search">Rechercher un élève :</label>
                  <input type="text" class="form-control" id="search" placeholder="Nom, prénom ou classe...">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="filter-classe">Filtrer par classe :</label>
                  <select class="form-control" id="filter-classe">
                    <option value="">Toutes les classes</option>
                    <?php foreach ($classes as $classe): ?>
                      <option value="<?php echo htmlspecialchars($classe['niveau']); ?>">
                        <?php echo htmlspecialchars($classe['niveau']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Liste des élèves -->
            <div class="reinscription-container">
              <?php if ($eleves_precedents_result && $eleves_precedents_result->num_rows > 0): ?>
                <div class="students-grid">
                  <?php while ($eleve = $eleves_precedents_result->fetch_assoc()): ?>
                    <div class="student-card" data-classe="<?php echo htmlspecialchars($eleve['classe_actuelle']); ?>">
                      <div class="student-header">
                        <div class="student-selection">
                          <input type="checkbox" 
                                 name="eleves_reinscription[]" 
                                 value="<?php echo $eleve['id']; ?>" 
                                 id="eleve_<?php echo $eleve['id']; ?>"
                                 class="student-checkbox">
                          <label for="eleve_<?php echo $eleve['id']; ?>" class="checkbox-label"></label>
                        </div>
                        <div class="student-avatar">
                          <i class="fa fa-user-circle"></i>
                        </div>
                      </div>
                      
                      <div class="student-info">
                        <h4 class="student-name">
                          <?php echo htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']); ?>
                        </h4>
                        <div class="student-details">
                          <span class="current-class">
                            <i class="fa fa-graduation-cap"></i>
                            <?php echo htmlspecialchars($eleve['classe_actuelle'] ?? 'Non assigné'); ?>
                          </span>
                          <span class="student-parent">
                            <i class="fa fa-user"></i>
                            <?php echo htmlspecialchars($eleve['parent_nom'] . ' ' . $eleve['parent_prenom']); ?>
                          </span>
                          <span class="student-age">
                            <i class="fa fa-calendar"></i>
                            <?php echo htmlspecialchars($eleve['age'] ?? 'N/A'); ?> ans
                          </span>
                        </div>
                      </div>

                      <div class="new-class-selection">
                        <label for="nouvelle_classe_<?php echo $eleve['id']; ?>">Nouvelle classe :</label>
                        <select class="form-control class-select" 
                                name="nouvelle_classe_<?php echo $eleve['id']; ?>" 
                                id="nouvelle_classe_<?php echo $eleve['id']; ?>">
                          <option value="">Sélectionner une classe</option>
                          <?php foreach ($classes as $classe): ?>
                            <option value="<?php echo $classe['id']; ?>">
                              <?php echo htmlspecialchars($classe['niveau']); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                  <?php endwhile; ?>
                </div>
              <?php else: ?>
                <div class="no-students">
                  <div class="no-students-icon">
                    <i class="fa fa-users"></i>
                  </div>
                  <h3>Aucun élève trouvé</h3>
                  <p>Il n'y a pas d'élèves de l'année précédente à réinscrire.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <div class="box-footer">
            <div class="reinscription-summary">
              <span class="selected-count">
                <i class="fa fa-users"></i>
                <span id="selectedCount">0</span> élève(s) sélectionné(s)
              </span>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn" disabled>
              <i class="fa fa-refresh"></i> Réinscrire les élèves sélectionnés
            </button>
            <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves" class="btn btn-default btn-lg">
              <i class="fa fa-arrow-left"></i> Retour à la liste des élèves
            </a>
          </div>
        </form>
      </div>
    </section>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.0
    </div>
    <strong>Copyright &copy; 2024 <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Gestion de la sélection/désélection de tous les élèves
    $('#selectAll').click(function() {
        $('.student-checkbox').prop('checked', true);
        updateSelectedCount();
        updateSubmitButton();
    });

    $('#unselectAll').click(function() {
        $('.student-checkbox').prop('checked', false);
        updateSelectedCount();
        updateSubmitButton();
    });

    // Mise à jour du compteur quand une checkbox change
    $('.student-checkbox').change(function() {
        updateSelectedCount();
        updateSubmitButton();
    });

    // Recherche d'élèves
    $('#search').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        filterStudents(searchTerm, $('#filter-classe').val());
    });

    // Filtre par classe
    $('#filter-classe').change(function() {
        var selectedClass = $(this).val();
        filterStudents($('#search').val().toLowerCase(), selectedClass);
    });

    function filterStudents(searchTerm, selectedClass) {
        $('.student-card').each(function() {
            var studentCard = $(this);
            var studentName = studentCard.find('.student-name').text().toLowerCase();
            var currentClass = studentCard.data('classe');
            
            var matchesSearch = searchTerm === '' || studentName.includes(searchTerm) || 
                              currentClass.toLowerCase().includes(searchTerm);
            var matchesClass = selectedClass === '' || currentClass === selectedClass;
            
            if (matchesSearch && matchesClass) {
                studentCard.show();
            } else {
                studentCard.hide();
            }
        });
    }

    function updateSelectedCount() {
        var count = $('.student-checkbox:checked').length;
        $('#selectedCount').text(count);
    }

    function updateSubmitButton() {
        var count = $('.student-checkbox:checked').length;
        var sessionSelected = $('#session_scolaire').val() !== '';
        
        if (count > 0 && sessionSelected) {
            $('#submitBtn').prop('disabled', false);
        } else {
            $('#submitBtn').prop('disabled', true);
        }
    }

    // Vérifier si une session est sélectionnée
    $('#session_scolaire').change(function() {
        updateSubmitButton();
    });

    // Validation du formulaire
    $('#reinscriptionForm').submit(function(e) {
        var selectedStudents = $('.student-checkbox:checked').length;
        var sessionSelected = $('#session_scolaire').val() !== '';
        
        if (!sessionSelected) {
            e.preventDefault();
            alert('Veuillez sélectionner une session scolaire.');
            return false;
        }
        
        if (selectedStudents === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un élève à réinscrire.');
            return false;
        }

        // Vérifier que tous les élèves sélectionnés ont une nouvelle classe
        var hasError = false;
        $('.student-checkbox:checked').each(function() {
            var studentId = $(this).val();
            var newClass = $('select[name="nouvelle_classe_' + studentId + '"]').val();
            if (!newClass) {
                hasError = true;
                $('select[name="nouvelle_classe_' + studentId + '"]').addClass('error');
            } else {
                $('select[name="nouvelle_classe_' + studentId + '"]').removeClass('error');
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Veuillez sélectionner une nouvelle classe pour tous les élèves sélectionnés.');
            return false;
        }

        return confirm('Êtes-vous sûr de vouloir réinscrire les ' + selectedStudents + ' élève(s) sélectionné(s) ?');
    });

    // Animation des cartes
    $('.student-card').hover(
        function() { $(this).addClass('hovered'); },
        function() { $(this).removeClass('hovered'); }
    );
});
</script>

</body>
</html>

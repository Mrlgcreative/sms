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

// Récupérer les cours avec les informations des professeurs et classes
$cours_query = "
    SELECT c.*, 
           p.nom as prof_nom, p.prenom as prof_prenom,
           cl.nom as classe_nom
    FROM cours c
    LEFT JOIN professeurs p ON c.professeur_id = p.id
    LEFT JOIN classes cl ON c.classe_id = cl.id
    WHERE c.section = 'primaire'
    ORDER BY cl.nom, c.titre
";

$cours_result = $mysqli->query($cours_query);
$cours = [];
if ($cours_result) {
    while ($row = $cours_result->fetch_assoc()) {
        $cours[] = $row;
    }
}

// Récupérer les professeurs de la section primaire
$professeurs_query = "SELECT id, nom, prenom FROM professeurs WHERE section = 'primaire' ORDER BY nom, prenom";
$professeurs_result = $mysqli->query($professeurs_query);
$professeurs = [];
if ($professeurs_result) {
    while ($row = $professeurs_result->fetch_assoc()) {
        $professeurs[] = $row;
    }
}

// Récupérer les classes de la section primaire
$classes_query = "SELECT id, nom FROM classes WHERE section = 'primaire' ORDER BY nom";
$classes_result = $mysqli->query($classes_query);
$classes = [];
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
}

$mysqli->close();
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
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  
 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Cours
        <small>Section Primaire</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-home"></i> Accueil</a></li>
        <li class="active">Cours</li>
      </ol>
    </section>

    <section class="content">
      <!-- Messages de notification -->
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      <?php endif; ?>

      <!-- Bouton d'ajout -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Cours</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjouter">
                  <i class="fa fa-plus"></i> Ajouter un cours
                </button>
              </div>
            </div>
            
            <div class="box-body">
              <table id="coursTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Professeur</th>
                    <th>Classe</th>
                    <th>Option</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($cours as $c): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($c['id']); ?></td>
                      <td><?php echo htmlspecialchars($c['titre']); ?></td>
                      <td><?php echo htmlspecialchars($c['description']); ?></td>
                      <td>
                        <?php if ($c['prof_nom']): ?>
                          <?php echo htmlspecialchars($c['prof_nom'] . ' ' . $c['prof_prenom']); ?>
                        <?php else: ?>
                          <span class="text-muted">Non assigné</span>
                        <?php endif; ?>
                      </td>
                      <td><?php echo htmlspecialchars($c['classe_nom'] ?? 'Non assignée'); ?></td>
                      <td><?php echo htmlspecialchars($c['option_'] ?? '-'); ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-info" onclick="voirCours(<?php echo $c['id']; ?>)">
                          <i class="fa fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" onclick="modifierCours(<?php echo $c['id']; ?>, '<?php echo addslashes($c['titre']); ?>', '<?php echo addslashes($c['description']); ?>', <?php echo $c['professeur_id'] ?? 'null'; ?>, <?php echo $c['classe_id'] ?? 'null'; ?>, '<?php echo addslashes($c['option_'] ?? ''); ?>')">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="supprimerCours(<?php echo $c['id']; ?>, '<?php echo addslashes($c['titre']); ?>')">
                          <i class="fa fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
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

<!-- Modal Ajouter Cours -->
<div class="modal fade" id="modalAjouter" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Ajouter un Cours</h4>
      </div>
      <form method="post" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterCours">
        <div class="modal-body">
          <div class="form-group">
            <label for="titre">Titre du cours <span class="text-red">*</span></label>
            <input type="text" class="form-control" id="titre" name="titre" required>
          </div>
          
          <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
          </div>
          
          <div class="form-group">
            <label for="professeur_id">Professeur</label>
            <select class="form-control" id="professeur_id" name="professeur_id">
              <option value="">Sélectionner un professeur</option>
              <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['id']; ?>">
                  <?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom'] . ' - ' . $prof['specialite']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="classe_id">Classe</label>
            <select class="form-control" id="classe_id" name="classe_id">
              <option value="">Sélectionner une classe</option>
              <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>">
                  <?php echo htmlspecialchars($classe['nom']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="option_">Option</label>
            <input type="text" class="form-control" id="option_" name="option_" placeholder="Ex: Obligatoire, Optionnel">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Modifier Cours -->
<div class="modal fade" id="modalModifier" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Modifier le Cours</h4>
      </div>
      <form method="post" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierCours">
        <div class="modal-body">
          <input type="hidden" id="edit_id" name="id">
          
          <div class="form-group">
            <label for="edit_titre">Titre du cours <span class="text-red">*</span></label>
            <input type="text" class="form-control" id="edit_titre" name="titre" required>
          </div>
          
          <div class="form-group">
            <label for="edit_description">Description</label>
            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
          </div>
          
          <div class="form-group">
            <label for="edit_professeur_id">Professeur</label>
            <select class="form-control" id="edit_professeur_id" name="professeur_id">
              <option value="">Sélectionner un professeur</option>
              <?php foreach ($professeurs as $prof): ?>
                <option value="<?php echo $prof['id']; ?>">
                  <?php echo htmlspecialchars($prof['nom'] . ' ' . $prof['prenom'] . ' - ' . $prof['specialite']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="edit_classe_id">Classe</label>
            <select class="form-control" id="edit_classe_id" name="classe_id">
              <option value="">Sélectionner une classe</option>
              <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>">
                  <?php echo htmlspecialchars($classe['nom']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="edit_option_">Option</label>
            <input type="text" class="form-control" id="edit_option_" name="option_">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning">Modifier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Voir Cours -->
<div class="modal fade" id="modalVoir" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Détails du Cours</h4>
      </div>
      <div class="modal-body">
        <div id="coursDetails"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Supprimer Cours -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Confirmer la suppression</h4>
      </div>
      <div class="modal-body">
        <p>Êtes-vous sûr de vouloir supprimer le cours <strong id="coursNomSupprimer"></strong> ?</p>
        <p class="text-red">Cette action est irréversible.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <form method="post" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerCours" style="display: inline;">
          <input type="hidden" id="delete_id" name="id">
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Scripts JavaScript -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
  $('#coursTable').DataTable({
    'paging': true,
    'lengthChange': true,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
    }
  });
});

function voirCours(id) {
  // Récupérer les détails du cours via AJAX
  $.ajax({
    url: '<?php echo BASE_URL; ?>index.php?controller=Director&action=getCours&id=' + id,
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        var cours = response.data;
        var html = '<dl class="dl-horizontal">';
        html += '<dt>Titre:</dt><dd>' + cours.titre + '</dd>';
        html += '<dt>Description:</dt><dd>' + (cours.description || 'Aucune description') + '</dd>';
        html += '<dt>Professeur:</dt><dd>' + (cours.professeur || 'Non assigné') + '</dd>';
        html += '<dt>Classe:</dt><dd>' + (cours.classe || 'Non assignée') + '</dd>';
        html += '<dt>Option:</dt><dd>' + (cours.option_ || 'Aucune') + '</dd>';
        html += '<dt>Section:</dt><dd>' + cours.section + '</dd>';
        html += '</dl>';
        
        $('#coursDetails').html(html);
        $('#modalVoir').modal('show');
      } else {
        alert('Erreur lors de la récupération des détails du cours');
      }
    },
    error: function() {
      alert('Erreur de communication avec le serveur');
    }
  });
}

function modifierCours(id, titre, description, professeur_id, classe_id, option_) {
  $('#edit_id').val(id);
  $('#edit_titre').val(titre);
  $('#edit_description').val(description);
  $('#edit_professeur_id').val(professeur_id);
  $('#edit_classe_id').val(classe_id);
  $('#edit_option_').val(option_);
  $('#modalModifier').modal('show');
}

function supprimerCours(id, titre) {
  $('#delete_id').val(id);
  $('#coursNomSupprimer').text(titre);
  $('#modalSupprimer').modal('show');
}
</script>

</body>
</html>
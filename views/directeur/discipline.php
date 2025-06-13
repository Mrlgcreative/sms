<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données pour récupérer les élèves
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer tous les élèves pour le formulaire d'ajout
$eleves_query = "SELECT e.id, e.nom, e.prenom, c.nom as classe_nom 
                FROM eleves e 
                LEFT JOIN classes c ON e.classe_id = c.id 
                ORDER BY e.nom, e.prenom";
$eleves_result = $mysqli->query($eleves_query);

$eleves = [];
if ($eleves_result) {
    while ($row = $eleves_result->fetch_assoc()) {
        $eleves[] = $row;
    }
}

$mysqli->close();
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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  
 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>


  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion de la Discipline
        <small>Incidents disciplinaires</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Discipline</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des incidents disciplinaires</h3>
              <div class="box-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjouter">
                  <i class="fa fa-plus"></i> Ajouter un incident
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="incidents-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Élève</th>
                    <th>Classe</th>
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
                        <td><?php echo date('d/m/Y', strtotime($incident['date_incident'])); ?></td>
                        <td><?php echo htmlspecialchars($incident['eleve_prenom'] . ' ' . $incident['eleve_nom']); ?></td>
                        <td><?php echo htmlspecialchars($incident['classe_nom']); ?></td>
                        <td>
                          <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars(substr($incident['description'], 0, 100)); ?>
                            <?php if (strlen($incident['description']) > 100): ?>...<?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <?php if (!empty($incident['sanction'])): ?>
                            <div style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">
                              <?php echo htmlspecialchars(substr($incident['sanction'], 0, 50)); ?>
                              <?php if (strlen($incident['sanction']) > 50): ?>...<?php endif; ?>
                            </div>
                          <?php else: ?>
                            <span class="text-muted">Aucune</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php 
                          $badge_class = '';
                          switch ($incident['statut']) {
                            case 'En cours':
                              $badge_class = 'label-warning';
                              break;
                            case 'Résolu':
                              $badge_class = 'label-success';
                              break;
                            case 'Suspendu':
                              $badge_class = 'label-danger';
                              break;
                            default:
                              $badge_class = 'label-default';
                          }
                          ?>
                          <span class="label <?php echo $badge_class; ?>"><?php echo htmlspecialchars($incident['statut']); ?></span>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalVoir" 
                                    data-id="<?php echo $incident['id']; ?>"
                                    data-date="<?php echo $incident['date_incident']; ?>"
                                    data-eleve="<?php echo htmlspecialchars($incident['eleve_prenom'] . ' ' . $incident['eleve_nom']); ?>"
                                    data-classe="<?php echo htmlspecialchars($incident['classe_nom']); ?>"
                                    data-description="<?php echo htmlspecialchars($incident['description']); ?>"
                                    data-sanction="<?php echo htmlspecialchars($incident['sanction']); ?>"
                                    data-statut="<?php echo htmlspecialchars($incident['statut']); ?>"
                                    title="Voir détails">
                              <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalModifier" 
                                    data-id="<?php echo $incident['id']; ?>"
                                    data-description="<?php echo htmlspecialchars($incident['description']); ?>"
                                    data-sanction="<?php echo htmlspecialchars($incident['sanction']); ?>"
                                    data-statut="<?php echo htmlspecialchars($incident['statut']); ?>"
                                    title="Modifier">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSupprimer" 
                                    data-id="<?php echo $incident['id']; ?>"
                                    data-eleve="<?php echo htmlspecialchars($incident['eleve_prenom'] . ' ' . $incident['eleve_nom']); ?>"
                                    data-date="<?php echo date('d/m/Y', strtotime($incident['date_incident'])); ?>"
                                    title="Supprimer">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">Aucun incident disciplinaire trouvé</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<!-- Modal Ajouter Incident -->
<div class="modal fade" id="modalAjouter" tabindex="-1" role="dialog" aria-labelledby="modalAjouterLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalAjouterLabel">Ajouter un incident disciplinaire</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterIncident">
        <div class="modal-body">
          <div class="form-group">
            <label for="eleve_id">Élève <span class="text-red">*</span></label>
            <select class="form-control" id="eleve_id" name="eleve_id" required>
              <option value="">Sélectionner un élève</option>
              <?php foreach ($eleves as $eleve): ?>
                <option value="<?php echo $eleve['id']; ?>">
                  <?php echo htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom'] . ' - ' . $eleve['classe_nom']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="date_incident">Date de l'incident <span class="text-red">*</span></label>
            <input type="date" class="form-control" id="date_incident" name="date_incident" required value="<?php echo date('Y-m-d'); ?>">
          </div>
          
          <div class="form-group">
            <label for="description">Description de l'incident <span class="text-red">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Décrivez l'incident en détail..."></textarea>
          </div>
          
          <div class="form-group">
            <label for="sanction">Sanction appliquée</label>
            <textarea class="form-control" id="sanction" name="sanction" rows="3" placeholder="Décrivez la sanction appliquée (optionnel)..."></textarea>
          </div>
          
          <div class="form-group">
            <label for="statut">Statut</label>
            <select class="form-control" id="statut" name="statut">
              <option value="En cours">En cours</option>
              <option value="Résolu">Résolu</option>
              <option value="Suspendu">Suspendu</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Voir Incident -->
<div class="modal fade" id="modalVoir" tabindex="-1" role="dialog" aria-labelledby="modalVoirLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalVoirLabel">Détails de l'incident disciplinaire</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <strong>Date:</strong>
            <p id="voir_date"></p>
          </div>
          <div class="col-md-6">
            <strong>Statut:</strong>
            <p><span id="voir_statut_badge"></span></p>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <strong>Élève:</strong>
            <p id="voir_eleve"></p>
          </div>
          <div class="col-md-6">
            <strong>Classe:</strong>
            <p id="voir_classe"></p>
          </div>
        </div>
        
        <div class="form-group">
          <strong>Description de l'incident:</strong>
          <div class="well" id="voir_description"></div>
        </div>
        
        <div class="form-group">
          <strong>Sanction appliquée:</strong>
          <div class="well" id="voir_sanction"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Modifier Incident -->
<div class="modal fade" id="modalModifier" tabindex="-1" role="dialog" aria-labelledby="modalModifierLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalModifierLabel">Modifier l'incident disciplinaire</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierIncident">
        <div class="modal-body">
          <input type="hidden" id="modifier_id" name="id">
          
          <div class="form-group">
            <label for="modifier_description">Description de l'incident <span class="text-red">*</span></label>
            <textarea class="form-control" id="modifier_description" name="description" rows="4" required></textarea>
          </div>
          
          <div class="form-group">
            <label for="modifier_sanction">Sanction appliquée</label>
            <textarea class="form-control" id="modifier_sanction" name="sanction" rows="3"></textarea>
          </div>
          
          <div class="form-group">
            <label for="modifier_statut">Statut</label>
            <select class="form-control" id="modifier_statut" name="statut">
              <option value="En cours">En cours</option>
              <option value="Résolu">Résolu</option>
              <option value="Suspendu">Suspendu</option>
            </select>
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

<!-- Modal Supprimer Incident -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" role="dialog" aria-labelledby="modalSupprimerLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalSupprimerLabel">Supprimer l'incident disciplinaire</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerIncident">
        <div class="modal-body">
          <input type="hidden" id="supprimer_id" name="id">
          <p>Êtes-vous sûr de vouloir supprimer cet incident disciplinaire ?</p>
          <div class="alert alert-warning">
            <strong>Élève:</strong> <span id="supprimer_eleve"></span><br>
            <strong>Date:</strong> <span id="supprimer_date"></span>
          </div>
          <p><strong>Cette action est irréversible.</strong></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
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

<!-- Scripts JavaScript -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Initialiser DataTable
    $('#incidents-table').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "order": [[0, "desc"]],
        "language": {
            "search": "Rechercher:",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
            "infoFiltered": "(filtré de _MAX_ entrées au total)",
            "emptyTable": "Aucune donnée disponible dans le tableau",
            "zeroRecords": "Aucun enregistrement correspondant trouvé"
        }
    });

    // Initialiser les datepickers
    $('#date_incident').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        language: 'fr'
    });

    // Gestionnaire pour le modal de visualisation
    $('#modalVoir').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var date = button.data('date');
        var eleve = button.data('eleve');
        var classe = button.data('classe');
        var description = button.data('description');
        var sanction = button.data('sanction');
        var statut = button.data('statut');
        
        // Formater la date
        var dateObj = new Date(date);
        var formattedDate = dateObj.toLocaleDateString('fr-FR');
        
        // Déterminer la classe CSS pour le badge de statut
        var badgeClass = '';
        switch (statut) {
            case 'En cours':
                badgeClass = 'label label-warning';
                break;
            case 'Résolu':
                badgeClass = 'label label-success';
                break;
            case 'Suspendu':
                badgeClass = 'label label-danger';
                break;
            default:
                badgeClass = 'label label-default';
        }
        
        // Remplir les données
        $('#voir_date').text(formattedDate);
        $('#voir_eleve').text(eleve);
        $('#voir_classe').text(classe);
        $('#voir_description').text(description || 'Aucune description');
        $('#voir_sanction').text(sanction || 'Aucune sanction');
        $('#voir_statut_badge').html('<span class="' + badgeClass + '">' + statut + '</span>');
    });

    // Gestionnaire pour le modal de modification
    $('#modalModifier').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var description = button.data('description');
        var sanction = button.data('sanction');
        var statut = button.data('statut');
        
        // Remplir le formulaire
        $('#modifier_id').val(id);
        $('#modifier_description').val(description);
        $('#modifier_sanction').val(sanction);
        $('#modifier_statut').val(statut);
    });

    // Gestionnaire pour le modal de suppression
    $('#modalSupprimer').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var eleve = button.data('eleve');
        var date = button.data('date');
        
        // Remplir les informations
        $('#supprimer_id').val(id);
        $('#supprimer_eleve').text(eleve);
        $('#supprimer_date').text(date);
    });

    // Validation du formulaire d'ajout
    $('#modalAjouter form').on('submit', function(e) {
        var eleve_id = $('#eleve_id').val();
        var date_incident = $('#date_incident').val();
        var description = $('#description').val().trim();
        
        if (!eleve_id) {
            e.preventDefault();
            alert('Veuillez sélectionner un élève.');
            return false;
        }
        
        if (!date_incident) {
            e.preventDefault();
            alert('Veuillez sélectionner une date.');
            return false;
        }
        
        if (!description) {
            e.preventDefault();
            alert('Veuillez saisir une description de l\'incident.');
            return false;
        }
    });

    // Validation du formulaire de modification
    $('#modalModifier form').on('submit', function(e) {
        var description = $('#modifier_description').val().trim();
        
        if (!description) {
            e.preventDefault();
            alert('Veuillez saisir une description de l\'incident.');
            return false;
        }
    });
});
</script>

</body>
</html>
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
  <title>SGS | Gestion des Absences</title>
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
        Gestion des Absences
        <small>Suivi des absences des élèves</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Absences</li>
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
      
      <!-- Statistiques -->
      <div class="row">
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-aqua">
            <div class="inner">
              <h3><?php echo count($absences); ?></h3>
              <p>Total Absences</p>
            </div>
            <div class="icon">
              <i class="fa fa-clock-o"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-green">
            <div class="inner">
              <h3><?php echo count(array_filter($absences, function($a) { return $a['justifiee'] == 1; })); ?></h3>
              <p>Absences Justifiées</p>
            </div>
            <div class="icon">
              <i class="fa fa-check-circle"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-yellow">
            <div class="inner">
              <h3><?php echo count(array_filter($absences, function($a) { return $a['justifiee'] == 0; })); ?></h3>
              <p>Absences Non Justifiées</p>
            </div>
            <div class="icon">
              <i class="fa fa-exclamation-triangle"></i>
            </div>
          </div>
        </div>
        
        <div class="col-lg-3 col-xs-6">
          <div class="small-box bg-red">
            <div class="inner">
              <h3><?php echo count(array_filter($absences, function($a) { return $a['date_absence'] == date('Y-m-d'); })); ?></h3>
              <p>Absences Aujourd'hui</p>
            </div>
            <div class="icon">
              <i class="fa fa-calendar"></i>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des absences</h3>
              <div class="box-tools">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Filtrer <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="#" onclick="filtrerAbsences('toutes')">Toutes les absences</a></li>
                    <li><a href="#" onclick="filtrerAbsences('justifiees')">Absences justifiées</a></li>
                    <li><a href="#" onclick="filtrerAbsences('non-justifiees')">Absences non justifiées</a></li>
                    <li><a href="#" onclick="filtrerAbsences('aujourd-hui')">Aujourd'hui</a></li>
                    <li><a href="#" onclick="filtrerAbsences('cette-semaine')">Cette semaine</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjouter">
                  <i class="fa fa-plus"></i> Ajouter une absence
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="absences-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Élève</th>
                    <th>Classe</th>
                    <th>Motif</th>
                    <th>Justifiée</th>
                    <th>Date d'enregistrement</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($absences)): ?>
                    <?php foreach ($absences as $absence): ?>
                      <tr>
                        <td><?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?></td>
                        <td><?php echo htmlspecialchars($absence['eleve_prenom'] . ' ' . $absence['eleve_nom']); ?></td>
                        <td><?php echo htmlspecialchars($absence['classe_nom']); ?></td>
                        <td>
                          <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo !empty($absence['motif']) ? htmlspecialchars($absence['motif']) : '<span class="text-muted">Non spécifié</span>'; ?>
                          </div>
                        </td>
                        <td>
                          <?php if ($absence['justifiee']): ?>
                            <span class="label label-success">Oui</span>
                          <?php else: ?>
                            <span class="label label-warning">Non</span>
                          <?php endif; ?>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($absence['date_creation'])); ?></td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalVoir" 
                                    data-id="<?php echo $absence['id']; ?>"
                                    data-date="<?php echo $absence['date_absence']; ?>"
                                    data-eleve="<?php echo htmlspecialchars($absence['eleve_prenom'] . ' ' . $absence['eleve_nom']); ?>"
                                    data-classe="<?php echo htmlspecialchars($absence['classe_nom']); ?>"
                                    data-motif="<?php echo htmlspecialchars($absence['motif']); ?>"
                                    data-justifiee="<?php echo $absence['justifiee']; ?>"
                                    data-creation="<?php echo $absence['date_creation']; ?>"
                                    title="Voir détails">
                              <i class="fa fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalModifier" 
                                    data-id="<?php echo $absence['id']; ?>"
                                    data-motif="<?php echo htmlspecialchars($absence['motif']); ?>"
                                    data-justifiee="<?php echo $absence['justifiee']; ?>"
                                    title="Modifier">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSupprimer" 
                                    data-id="<?php echo $absence['id']; ?>"
                                    data-eleve="<?php echo htmlspecialchars($absence['eleve_prenom'] . ' ' . $absence['eleve_nom']); ?>"
                                    data-date="<?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?>"
                                    title="Supprimer">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">Aucune absence trouvée</td>
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

<!-- Modal Ajouter Absence -->
<div class="modal fade" id="modalAjouter" tabindex="-1" role="dialog" aria-labelledby="modalAjouterLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalAjouterLabel">Ajouter une absence</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterAbsence">
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
            <label for="date_absence">Date de l'absence <span class="text-red">*</span></label>
            <input type="date" class="form-control" id="date_absence" name="date_absence" required value="<?php echo date('Y-m-d'); ?>">
          </div>
          
          <div class="form-group">
            <label for="motif">Motif de l'absence</label>
            <textarea class="form-control" id="motif" name="motif" rows="3" placeholder="Précisez le motif de l'absence (optionnel)..."></textarea>
          </div>
          
          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" id="justifiee" name="justifiee" value="1">
                Cette absence est justifiée
              </label>
            </div>
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

<!-- Modal Voir Absence -->
<div class="modal fade" id="modalVoir" tabindex="-1" role="dialog" aria-labelledby="modalVoirLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalVoirLabel">Détails de l'absence</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <strong>Date d'absence:</strong>
            <p id="voir_date"></p>
          </div>
          <div class="col-md-6">
            <strong>Justifiée:</strong>
            <p><span id="voir_justifiee_badge"></span></p>
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
          <strong>Motif de l'absence:</strong>
          <div class="well" id="voir_motif"></div>
        </div>
        
        <div class="form-group">
          <strong>Date d'enregistrement:</strong>
          <p id="voir_creation"></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Modifier Absence -->
<div class="modal fade" id="modalModifier" tabindex="-1" role="dialog" aria-labelledby="modalModifierLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalModifierLabel">Modifier l'absence</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierAbsence">
        <div class="modal-body">
          <input type="hidden" id="modifier_id" name="id">
          
          <div class="form-group">
            <label for="modifier_motif">Motif de l'absence</label>
            <textarea class="form-control" id="modifier_motif" name="motif" rows="3"></textarea>
          </div>
          
          <div class="form-group">
            <div class="checkbox">
              <label>
                <input type="checkbox" id="modifier_justifiee" name="justifiee" value="1">
                Cette absence est justifiée
              </label>
            </div>
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

<!-- Modal Supprimer Absence -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" role="dialog" aria-labelledby="modalSupprimerLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalSupprimerLabel">Supprimer l'absence</h4>
      </div>
      <form method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerAbsence">
        <div class="modal-body">
          <input type="hidden" id="supprimer_id" name="id">
          <p>Êtes-vous sûr de vouloir supprimer cette absence ?</p>
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
    var table = $('#absences-table').DataTable({
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
    $('#date_absence').datepicker({
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
        var motif = button.data('motif');
        var justifiee = button.data('justifiee');
        var creation = button.data('creation');
        
        // Formater la date
        var dateObj = new Date(date);
        var formattedDate = dateObj.toLocaleDateString('fr-FR');
        
        // Formater la date de création
        var creationObj = new Date(creation);
        var formattedCreation = creationObj.toLocaleDateString('fr-FR') + ' à ' + creationObj.toLocaleTimeString('fr-FR');
        
        // Déterminer la classe CSS pour le badge justifiée
        var justifieeBadge = justifiee == 1 ? 
            '<span class="label label-success">Oui</span>' : 
            '<span class="label label-warning">Non</span>';
        
        // Remplir les données
        $('#voir_date').text(formattedDate);
        $('#voir_eleve').text(eleve);
        $('#voir_classe').text(classe);
        $('#voir_motif').text(motif || 'Non spécifié');
        $('#voir_justifiee_badge').html(justifieeBadge);
        $('#voir_creation').text(formattedCreation);
    });

    // Gestionnaire pour le modal de modification
    $('#modalModifier').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var motif = button.data('motif');
        var justifiee = button.data('justifiee');
        
        // Remplir le formulaire
        $('#modifier_id').val(id);
        $('#modifier_motif').val(motif);
        $('#modifier_justifiee').prop('checked', justifiee == 1);
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
        var date_absence = $('#date_absence').val();
        
        if (!eleve_id) {
            e.preventDefault();
            alert('Veuillez sélectionner un élève.');
            return false;
        }
        
        if (!date_absence) {
            e.preventDefault();
            alert('Veuillez sélectionner une date.');
            return false;
        }
    });

    // Fonction de filtrage
    window.filtrerAbsences = function(type) {
        var today = new Date();
        var startOfWeek = new Date(today);
        startOfWeek.setDate(today.getDate() - today.getDay());
        
        switch(type) {
            case 'toutes':
                table.search('').columns().search('').draw();
                break;
            case 'justifiees':
                table.column(4).search('Oui').draw();
                break;
            case 'non-justifiees':
                table.column(4).search('Non').draw();
                break;
            case 'aujourd-hui':
                var todayStr = today.toLocaleDateString('fr-FR');
                table.column(0).search(todayStr).draw();
                break;
            case 'cette-semaine':
                // Filtrer par semaine actuelle (plus complexe, nécessiterait une approche différente)
                table.search('').columns().search('').draw();
                break;
        }
    };
});
</script>

</body>
</html>
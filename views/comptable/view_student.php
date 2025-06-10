<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Profil de l'élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- DataTables -->
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

   <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil de l'élève
        <small><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">Élèves</a></li>
        <li class="active">Profil de l'élève</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <!-- Colonne de gauche - Informations de l'élève -->
        <div class="col-md-4">
          <!-- Profil de l'élève -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations personnelles</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" 
                   src="<?php echo !empty($eleve['photo']) ? BASE_URL . $eleve['photo'] : 'dist/img/avatar5.png'; ?>" 
                   alt="Photo de l'élève">

              <h3 class="profile-username text-center"><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']); ?></h3>

              <p class="text-muted text-center"><?php echo htmlspecialchars($eleve['niveau'] ?? 'Non assigné'); ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Matricule</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['matricule'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date de naissance</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['date_naissance']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Lieu de naissance</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['lieu_naissance']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['section'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Option</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['option_nom'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Adresse</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['adresse']); ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifiereleve&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-block">
                <i class="fa fa-edit"></i> Modifier les informations
              </a>
            </div>
          </div>

          <!-- Informations des parents -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations des parents</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-user margin-r-5"></i> Père</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['nom_pere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-phone margin-r-5"></i> Contact du père</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['contact_pere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-user margin-r-5"></i> Mère</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['nom_mere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-phone margin-r-5"></i> Contact de la mère</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['contact_mere'] ?? 'Non renseigné'); ?></p>
            </div>
          </div>
        </div>
        
        <!-- Colonne de droite - Onglets d'informations -->
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#paiements" data-toggle="tab">Paiements</a></li>
              <li><a href="#notes" data-toggle="tab">Notes</a></li>
              <li><a href="#documents" data-toggle="tab">Documents</a></li>
              <li><a href="#historique" data-toggle="tab">Historique</a></li>
            </ul>
            <div class="tab-content">
              <!-- Onglet Paiements -->
              <div class="active tab-pane" id="paiements">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Historique des paiements</h3>
                    <div class="box-tools">
                      <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Nouveau paiement
                      </a>
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <!-- Sélecteur d'année scolaire -->
                    <div class="form-group">
                      <label for="session_filter">Année scolaire :</label>
                      <select id="session_filter" class="form-control" style="width: 200px; margin-bottom: 15px;">
                        <option value="all">Toutes les années</option>
                        <?php 
                        // Récupérer les années scolaires uniques des paiements
                        $sessions = [];
                        if (!empty($paiements)) {
                          foreach ($paiements as $paiement) {
                            if (isset($paiement['libelle']) && !in_array($paiement['libelle'], $sessions)) {
                              $sessions[] = $paiement['libelle'];
                            }
                          }
                        }
                        foreach ($sessions as $session) : 
                        ?>
                          <option value="<?php echo htmlspecialchars($session); ?>"><?php echo htmlspecialchars($session); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    
                    <table class="table table-bordered table-striped" id="paiements_table">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Type frais</th>
                          <th>Montant</th>
                          <th>Référence</th>
                          <th>Mois</th>
                          <th>Année scolaire</th>
                          <th>Statut</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($paiements)): ?>
                          <tr>
                            <td colspan="8" class="text-center">Aucun paiement enregistré</td>
                          </tr>
                        <?php else: ?>
                          <?php foreach ($paiements as $paiement): ?>
                            <tr class="paiement-row" data-session="<?php echo htmlspecialchars($paiement['libelee'] ?? 'inconnu'); ?>">
                              <td><?php echo htmlspecialchars($paiement['date_paiement']); ?></td>
                              <td><?php echo htmlspecialchars($paiement['type_paiement']); ?></td>
                              <td><?php echo number_format($paiement['montant'], 0, ',', ' ') . ' $'; ?></td>
                              <td><?php echo htmlspecialchars($paiement['reference']); ?></td>
                              <td><?php echo htmlspecialchars($paiement['mois']); ?></td>
                              <td><?php echo htmlspecialchars($paiement['libelle'] ?? 'Non spécifié'); ?></td>
                              <td>
                                <?php if ($paiement['statut'] == 'Payé'): ?>
                                  <span class="label label-success">Payé</span>
                                <?php elseif ($paiement['statut'] == 'En attente'): ?>
                                  <span class="label label-warning">En attente</span>
                                <?php else: ?>
                                  <span class="label label-success">Payé</span>
                                <?php endif; ?>
                              </td>
                              <td>
                                <div class="btn-group">
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=recu&paiement_id=<?php echo isset($paiement['id']) ? $paiement['id'] : ''; ?>" class="btn btn-default btn-xs" title="Imprimer reçu">
                                    <i class="fa fa-print"></i>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifierpaiement&id=<?php echo isset($paiement['id']) ? $paiement['id'] : ''; ?>" class="btn btn-primary btn-xs" title="Modifier">
                                    <i class="fa fa-edit"></i>
                                  </a>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                      <tfoot>
                        <tr>
                          <th colspan="2">Total</th>
                          <th id="total_montant">
                            <?php 
                              $total = 0;
                              if (!empty($paiements)) {
                                foreach ($paiements as $paiement) {
                                  $total += $paiement['montant'];
                                }
                              }
                              echo number_format($total, 0, ',', ' ') . ' $';
                            ?>
                          </th>
                          <th colspan="5"></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              
              <!-- Onglet Notes -->
              <div class="tab-pane" id="notes">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Notes et évaluations</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <table class="table table-bordered table-striped" id="notes_table">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Matière</th>
                          <th>Évaluation</th>
                          <th>Note</th>
                          <th>Coefficient</th>
                          <th>Commentaire</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (empty($notes)): ?>
                          <tr>
                            <td colspan="6" class="text-center">Aucune note enregistrée</td>
                          </tr>
                        <?php else: ?>
                          <?php foreach ($notes as $note): ?>
                            <tr>
                              <td><?php echo htmlspecialchars($note['date_evaluation']); ?></td>
                              <td><?php echo htmlspecialchars($note['matiere_nom']); ?></td>
                              <td><?php echo htmlspecialchars($note['evaluation_nom']); ?></td>
                              <td><?php echo htmlspecialchars($note['note']); ?>/<?php echo htmlspecialchars($note['note_max']); ?></td>
                              <td><?php echo htmlspecialchars($note['coefficient']); ?></td>
                              <td><?php echo htmlspecialchars($note['commentaire'] ?? ''); ?></td>
                            </tr>
                          <?php endforeach; ?>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              
              <!-- Onglet Documents -->
              <div class="tab-pane" id="documents">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Documents de l'élève</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="row">
                      <div class="col-md-12">
                        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#uploadDocumentModal">
                          <i class="fa fa-upload"></i> Téléverser un document
                        </a>
                      </div>
                    </div>
                    <div class="row" style="margin-top: 15px;">
                      <div class="col-md-12">
                        <table class="table table-bordered table-striped" id="documents_table">
                          <thead>
                            <tr>
                              <th>Nom du document</th>
                              <th>Type</th>
                              <th>Date d'ajout</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="4" class="text-center">Fonctionnalité en cours de développement</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Onglet Historique -->
              <div class="tab-pane" id="historique">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Historique académique</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Année scolaire</th>
                          <th>Classe</th>
                          <th>Résultat</th>
                          <th>Moyenne</th>
                          <th>Rang</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td colspan="5" class="text-center">Fonctionnalité en cours de développement</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
  
  <!-- Modal pour téléverser un document -->
  <div class="modal fade" id="uploadDocumentModal" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="uploadDocumentModalLabel">Téléverser un document</h4>
        </div>
        <div class="modal-body">
          <form id="documentUploadForm" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=uploaddocument" method="post" enctype="multipart/form-data">
            <input type="hidden" name="eleve_id" value="<?php echo $eleve['id']; ?>">
            <div class="form-group">
              <label for="document_type">Type de document</label>
              <select class="form-control" id="document_type" name="document_type" required>
                <option value="">Sélectionner un type</option>
                <option value="carte_identite">Carte d'identité</option>
                <option value="certificat_naissance">Certificat de naissance</option>
                <option value="bulletin">Bulletin scolaire</option>
                <option value="certificat_medical">Certificat médical</option>
                <option value="autre">Autre</option>
              </select>
            </div>
            <div class="form-group" id="autre_type_container" style="display: none;">
              <label for="autre_type">Préciser le type</label>
              <input type="text" class="form-control" id="autre_type" name="autre_type">
            </div>
            <div class="form-group">
              <label for="document_file">Fichier</label>
              <input type="file" id="document_file" name="document_file" required>
              <p class="help-block">Formats acceptés: PDF, JPG, PNG (max 5MB)</p>
            </div>
            <div class="form-group">
              <label for="document_description">Description (optionnel)</label>
              <textarea class="form-control" id="document_description" name="document_description" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="button" class="btn btn-primary" id="submitDocumentUpload">Téléverser</button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
  // Initialiser les DataTables
  $('#paiements_table').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
    },
    "responsive": true,
    "order": [[0, "desc"]], // Trier par date décroissante
    "pageLength": 10
  });
  
  $('#notes_table').DataTable({
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
    },
    "responsive": true,
    "pageLength": 10
  });
  
  // Fonction pour filtrer les paiements
  $('#session_filter').change(function() {
    var selectedSession = $(this).val();
    var table = $('#paiements_table').DataTable();
    
    // Réinitialiser le filtre
    table.search('').columns().search('').draw();
    
    if (selectedSession !== 'all') {
      // Appliquer le filtre sur la colonne de l'année scolaire (index 5)
      table.column(5).search(selectedSession).draw();
    }
    
    // Recalculer le total
    updateTotal();
  });
  
  // Fonction pour mettre à jour le total des paiements
  function updateTotal() {
    var total = 0;
    $('#paiements_table tbody tr:visible').each(function() {
      var montantText = $(this).find('td:eq(2)').text();
      var montant = parseFloat(montantText.replace(/[^\d]/g, ''));
      if (!isNaN(montant)) {
        total += montant;
      }
    });
    
    $('#total_montant').text(total.toLocaleString('fr-FR') + ' $');
  }
});
</script>

<script>
$(document).ready(function() {
  // Fonction pour filtrer les paiements
  $('#session_filter').change(function() {
    var selectedSession = $(this).val();
    
    if (selectedSession === 'all') {
      // Afficher tous les paiements
      $('.paiement-row').show();
    } else {
      // Cacher tous les paiements
      $('.paiement-row').hide();
      // Afficher seulement les paiements de l'année scolaire sélectionnée
      $('.paiement-row[data-session="' + selectedSession + '"]').show();
    }
    
    // Vérifier s'il y a des lignes visibles
    if ($('#paiements_table tbody tr:visible').length === 0) {
      // Ajouter une ligne indiquant qu'aucun paiement n'est trouvé
      if ($('#no-results-row').length === 0) {
        $('#paiements_table tbody').append('<tr id="no-results-row"><td colspan="8" class="text-center">Aucun paiement pour cette année scolaire</td></tr>');
      } else {
        $('#no-results-row').show();
      }
    } else {
      // Cacher la ligne "aucun résultat" si elle existe
      $('#no-results-row').hide();
    }
  });
  
  // Fonctionnalité pour les boutons de contrôle (réduire et fermer)
  $('.btn-box-tool[data-widget="collapse"]').click(function() {
    var box = $(this).closest('.box-body');
    box.slideToggle();
    
    // Changer l'icône du bouton
    var icon = $(this).find('i');
    if (icon.hasClass('fa-minus')) {
      icon.removeClass('fa-minus').addClass('fa-plus');
    } else {
      icon.removeClass('fa-plus').addClass('fa-minus');
    }
  });
  
  $('.btn-box-tool[data-widget="remove"]').click(function() {
    $(this).closest('.box-body').hide();
  });
  
  // Ajout de fonctionnalités pour les onglets
  $('.nav-tabs a').click(function(){
    $(this).tab('show');
    // Sauvegarder l'onglet actif dans le localStorage
    localStorage.setItem('activeTab', $(this).attr('href'));
  });
  
  // Restaurer l'onglet actif lors du rechargement de la page
  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
    $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }
  
  // Ajouter une animation lors du changement d'onglet
  $('.nav-tabs a').on('shown.bs.tab', function(e){
    var target = $(e.target).attr('href');
    $(target).addClass('animated fadeIn');
    setTimeout(function(){
      $(target).removeClass('animated fadeIn');
    }, 500);
  });
  
  // Ajouter un effet de survol pour les lignes du tableau
  $('.table-striped tbody tr').hover(
    function() {
      $(this).addClass('bg-info');
    },
    function() {
      $(this).removeClass('bg-info');
    }
  );
  
  // Ajouter une confirmation avant d'imprimer un reçu
  $('.btn-default.btn-xs').click(function(e) {
    if (!confirm('Voulez-vous imprimer le reçu de ce paiement?')) {
      e.preventDefault();
    }
  });
  
  // Ajouter un bouton pour exporter les paiements en CSV
  if ($('#paiements_table tbody tr').length > 1) {
    $('.box-header').append(
      '<button id="export-csv" class="btn btn-info btn-sm pull-right" style="margin-right: 10px;">' +
      '<i class="fa fa-file-excel-o"></i> Exporter CSV</button>'
    );
    
    $('#export-csv').click(function() {
      exportTableToCSV('paiements_eleve.csv');
    });
  }
  
  // Fonction pour exporter le tableau en CSV
  function exportTableToCSV(filename) {
    var csv = [];
    var rows = document.querySelectorAll('#paiements_table tr:visible');
    
    for (var i = 0; i < rows.length; i++) {
      var row = [], cols = rows[i].querySelectorAll('td, th');
      
      for (var j = 0; j < cols.length; j++) {
        // Récupérer le texte et nettoyer les espaces
        var text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').trim();
        // Échapper les guillemets doubles
        text = text.replace(/"/g, '""');
        // Entourer de guillemets si contient des virgules
        row.push('"' + text + '"');
      }
      
      csv.push(row.join(','));
    }
    
    // Télécharger le fichier CSV
    downloadCSV(csv.join('\n'), filename);
  }
  
  function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;
    
    // Créer un objet Blob pour le CSV
    csvFile = new Blob([csv], {type: 'text/csv;charset=utf-8;'});
    
    // Créer un lien de téléchargement
    downloadLink = document.createElement('a');
    
    // Attribuer un nom de fichier
    downloadLink.download = filename;
    
    // Créer un lien vers le fichier
    downloadLink.href = window.URL.createObjectURL(csvFile);
    
    // Cacher le lien
    downloadLink.style.display = 'none';
    
    // Ajouter le lien au DOM
    document.body.appendChild(downloadLink);
    
    // Cliquer sur le lien pour déclencher le téléchargement
    downloadLink.click();
    
    // Nettoyer en supprimant le lien
    document.body.removeChild(downloadLink);
  }
});
</script>
</body>
</html>
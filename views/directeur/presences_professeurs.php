<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Présences Professeur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des présences
        <small>Professeur: <?php echo htmlspecialchars($professeur['nom'] . ' ' . $professeur['prenom']); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">Professeurs</a></li>
        <li class="active">Présences</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-<?php echo $_SESSION['message_type'] === 'success' ? 'check' : 'ban'; ?>"></i> <?php echo $_SESSION['message_type'] === 'success' ? 'Succès!' : 'Erreur!'; ?></h4>
          <?php 
            echo $_SESSION['message']; 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
          ?>
        </div>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-md-4">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations du professeur</h3>
            </div>
            <div class="box-body box-profile">
              <h3 class="profile-username text-center"><?php echo htmlspecialchars($professeur['nom'] . ' ' . $professeur['prenom']); ?></h3>
              <p class="text-muted text-center">Professeur - Section <?php echo htmlspecialchars($professeur['section']); ?></p>
              
              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Email</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['email']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Téléphone</b> <a class="pull-right"><?php echo htmlspecialchars($professeur['telephone'] ?? 'Non renseigné'); ?></a>
                </li>
                
              </ul>
              
              <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=voirProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-primary btn-block"><b>Voir profil complet</b></a>
            </div>
          </div>
          
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Statistiques de présence</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-green"><i class="fa fa-caret-up"></i> <?php echo $statistiques['pourcentage_present']; ?>%</span>
                    <h5 class="description-header"><?php echo $statistiques['present']; ?></h5>
                    <span class="description-text">PRÉSENT</span>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-red"><i class="fa fa-caret-down"></i> <?php echo $statistiques['pourcentage_absent']; ?>%</span>
                    <h5 class="description-header"><?php echo $statistiques['absent']; ?></h5>
                    <span class="description-text">ABSENT</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-6">
                  <div class="description-block border-right">
                    <span class="description-percentage text-yellow"><i class="fa fa-caret-up"></i> <?php echo $statistiques['pourcentage_retard']; ?>%</span>
                    <h5 class="description-header"><?php echo $statistiques['retard']; ?></h5>
                    <span class="description-text">RETARD</span>
                  </div>
                </div>
                <div class="col-xs-6">
                  <div class="description-block">
                    <span class="description-percentage text-blue"><i class="fa fa-caret-up"></i> <?php echo $statistiques['pourcentage_excuse']; ?>%</span>
                    <h5 class="description-header"><?php echo $statistiques['excuse']; ?></h5>
                    <span class="description-text">EXCUSÉ</span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-xs-12 text-center">
                  <h4><strong>Total: <?php echo $statistiques['total']; ?> enregistrements</strong></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Ajouter une présence</h3>
            </div>
            <form method="post" class="form-horizontal">
              <input type="hidden" name="action" value="ajouter_presence">
              <div class="box-body">
                <div class="form-group">
                  <label for="date" class="col-sm-2 control-label">Date</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right datepicker" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="status" class="col-sm-2 control-label">Statut</label>
                  <div class="col-sm-10">
                    <select class="form-control" id="status" name="status" required>
                      <option value="">Sélectionnez un statut</option>
                      <option value="present">Présent</option>
                      <option value="absent">Absent</option>
                      <option value="retard">En retard</option>
                      <option value="excuse">Absence excusée</option>
                    </select>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="commentaire" class="col-sm-2 control-label">Commentaire</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3" placeholder="Commentaire optionnel..."></textarea>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs" class="btn btn-default">Retour</a>
                <button type="submit" class="btn btn-info pull-right">Enregistrer</button>
              </div>
            </form>
          </div>
          
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Filtrer les présences</h3>
            </div>
            <div class="box-body">
              <form method="get" class="form-inline">
                <input type="hidden" name="controller" value="Director">
                <input type="hidden" name="action" value="presencesProfesseurs">
                <input type="hidden" name="professeur_id" value="<?php echo $professeur['id']; ?>">
                
                <div class="form-group">
                  <label for="date_debut">Du:</label>
                  <input type="date" class="form-control" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($date_debut ?? ''); ?>">
                </div>
                
                <div class="form-group">
                  <label for="date_fin">Au:</label>
                  <input type="date" class="form-control" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($date_fin ?? ''); ?>">
                </div>
                
                <div class="form-group">
                  <label for="status_filtre">Statut:</label>
                  <select class="form-control" id="status_filtre" name="status">
                    <option value="">Tous</option>
                    <option value="present" <?php echo ($status_filtre === 'present') ? 'selected' : ''; ?>>Présent</option>
                    <option value="absent" <?php echo ($status_filtre === 'absent') ? 'selected' : ''; ?>>Absent</option>
                    <option value="retard" <?php echo ($status_filtre === 'retard') ? 'selected' : ''; ?>>En retard</option>
                    <option value="excuse" <?php echo ($status_filtre === 'excuse') ? 'selected' : ''; ?>>Absence excusée</option>
                  </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=presencesProfesseurs&professeur_id=<?php echo $professeur['id']; ?>" class="btn btn-default">Réinitialiser</a>
              </form>
            </div>
          </div>
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Historique des présences</h3>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped" id="presencesTable">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Statut</th>
                      <th>Commentaire</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($presences)): ?>
                      <tr>
                        <td colspan="4" class="text-center">Aucun enregistrement de présence trouvé.</td>
                      </tr>
                    <?php else: ?>
                      <?php foreach ($presences as $presence): ?>
                        <tr>
                          <td><?php echo date('d/m/Y', strtotime($presence['date'])); ?></td>
                          <td>
                            <?php 
                              switch ($presence['status']) {
                                case 'present':
                                  echo '<span class="label label-success">Présent</span>';
                                  break;
                                case 'absent':
                                  echo '<span class="label label-danger">Absent</span>';
                                  break;
                                case 'retard':
                                  echo '<span class="label label-warning">En retard</span>';
                                  break;
                                case 'excuse':
                                  echo '<span class="label label-info">Absence excusée</span>';
                                  break;
                                default:
                                  echo '<span class="label label-default">Inconnu</span>';
                              }
                            ?>
                          </td>
                          <td><?php echo htmlspecialchars($presence['commentaire'] ?? ''); ?></td>
                          <td>
                            <button type="button" class="btn btn-xs btn-warning" onclick="modifierPresence(<?php echo $presence['id']; ?>, '<?php echo $presence['status']; ?>', '<?php echo htmlspecialchars($presence['commentaire'] ?? '', ENT_QUOTES); ?>')">
                              <i class="fa fa-edit"></i> Modifier
                            </button>
                            <button type="button" class="btn btn-xs btn-danger" onclick="supprimerPresence(<?php echo $presence['id']; ?>)">
                              <i class="fa fa-trash"></i> Supprimer
                            </button>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Modal pour modifier une présence -->
<div class="modal fade" id="modifierPresenceModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title">Modifier la présence</h4>
      </div>
      <form method="post" id="modifierPresenceForm">
        <input type="hidden" name="action" value="modifier_presence">
        <input type="hidden" name="presence_id" id="modifier_presence_id">
        <div class="modal-body">
          <div class="form-group">
            <label for="modifier_status">Statut:</label>
            <select class="form-control" id="modifier_status" name="status" required>
              <option value="present">Présent</option>
              <option value="absent">Absent</option>
              <option value="retard">En retard</option>
              <option value="excuse">Absence excusée</option>
            </select>
          </div>
          <div class="form-group">
            <label for="modifier_commentaire">Commentaire:</label>
            <textarea class="form-control" id="modifier_commentaire" name="commentaire" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Modifier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Formulaire caché pour suppression -->
<form method="post" id="supprimerPresenceForm" style="display: none;">
  <input type="hidden" name="action" value="supprimer_presence">
  <input type="hidden" name="presence_id" id="supprimer_presence_id">
</form>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialiser le datepicker
  $('.datepicker').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  });
  
  // Initialiser DataTable
  $('#presencesTable').DataTable({
    "responsive": true,
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
    },
    "order": [[ 0, "desc" ]]
  });
});

function modifierPresence(id, status, commentaire) {
  $('#modifier_presence_id').val(id);
  $('#modifier_status').val(status);
  $('#modifier_commentaire').val(commentaire);
  $('#modifierPresenceModal').modal('show');
}

function supprimerPresence(id) {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette présence ?')) {
    $('#supprimer_presence_id').val(id);
    $('#supprimerPresenceForm').submit();
  }
}
</script>
</body>
</html>
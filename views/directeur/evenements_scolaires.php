<?php
// Vue pour la gestion des événements scolaires
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directeur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Instancier le modèle
require_once 'models/EvenementScolaire.php';
$evenementModel = new EvenementScolaire($mysqli);

// Récupérer tous les événements
$evenements = $evenementModel->getAllEvenements();
$evenementsAVenir = $evenementModel->getEvenementsAVenir(5);
$evenementsCalendar = json_encode($evenementModel->getEvenementsForCalendar());
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Événements Scolaires</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- CSS Dependencies -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  
  <!-- Custom CSS for Events Page -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/evenements-scolaires.css">
</head>
<body class="hold-transition skin-purple sidebar-mini">
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
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
                <p>
                  <?php echo htmlspecialchars($username); ?> - <?php echo htmlspecialchars($role); ?>
                  <small><?php echo htmlspecialchars($email); ?></small>
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

  <!-- Barre latérale gauche -->
  <aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo BASE_URL . $image; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
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
        
        <li>
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
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        <i class="fa fa-calendar-check-o"></i> Organisation des Événements Scolaires
        <small>Planification et gestion complète</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active"><i class="fa fa-calendar"></i> Événements Scolaires</li>
      </ol>
    </section>

    <section class="content">
      <!-- Messages de notification -->
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-info"></i> Information</h4>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      <?php endif; ?>

      <!-- Section principale avec calendrier -->
      <div class="row">
        <div class="col-md-3">
          <!-- Événements prédéfinis -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-star text-yellow"></i> Événements Suggérés</h3>
            </div>
            <div class="box-body">
              <div id="external-events">
                <div class="external-event bg-green">
                  <i class="fa fa-users"></i> Réunion des parents
                </div>
                <div class="external-event bg-yellow">
                  <i class="fa fa-trophy"></i> Journée sportive
                </div>
                <div class="external-event bg-aqua">
                  <i class="fa fa-graduation-cap"></i> Sortie culturelle
                </div>
                <div class="external-event bg-light-blue">
                  <i class="fa fa-blackboard"></i> Conférence pédagogique
                </div>
                <div class="external-event bg-red">
                  <i class="fa fa-edit"></i> Examens trimestriels
                </div>
                <div class="external-event bg-purple">
                  <i class="fa fa-heart"></i> Fête de fin d'année
                </div>
                <div class="checkbox" style="margin-top: 15px;">
                  <label for="drop-remove">
                    <input type="checkbox" id="drop-remove">
                    Supprimer après placement
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Créateur d'événement rapide -->
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-plus-circle text-green"></i> Créer un Événement</h3>
            </div>
            <div class="box-body">
              <div class="form-group">
                <label>Sélectionner une couleur:</label>
                <div class="btn-group btn-group-justified" style="margin-bottom: 10px;">
                  <div class="btn-group">
                    <button type="button" id="color-chooser-btn" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                      <i class="fa fa-palette"></i> Couleur <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" id="color-chooser">
                      <li><a class="text-aqua" href="#"><i class="fa fa-square"></i> Aqua</a></li>
                      <li><a class="text-blue" href="#"><i class="fa fa-square"></i> Bleu</a></li>
                      <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i> Bleu clair</a></li>
                      <li><a class="text-teal" href="#"><i class="fa fa-square"></i> Turquoise</a></li>
                      <li><a class="text-yellow" href="#"><i class="fa fa-square"></i> Jaune</a></li>
                      <li><a class="text-orange" href="#"><i class="fa fa-square"></i> Orange</a></li>
                      <li><a class="text-green" href="#"><i class="fa fa-square"></i> Vert</a></li>
                      <li><a class="text-lime" href="#"><i class="fa fa-square"></i> Vert clair</a></li>
                      <li><a class="text-red" href="#"><i class="fa fa-square"></i> Rouge</a></li>
                      <li><a class="text-purple" href="#"><i class="fa fa-square"></i> Violet</a></li>
                      <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i> Fuchsia</a></li>
                      <li><a class="text-muted" href="#"><i class="fa fa-square"></i> Gris</a></li>
                      <li><a class="text-navy" href="#"><i class="fa fa-square"></i> Bleu marine</a></li>
                    </ul>
                  </div>
                </div>
              </div>
              
              <div class="form-group">
                <label>Titre de l'événement:</label>
                <div class="input-group">
                  <span class="input-group-addon"><i class="fa fa-tag"></i></span>
                  <input id="new-event" type="text" class="form-control" placeholder="Saisir le titre de l'événement">
                  <div class="input-group-btn">
                    <button id="add-new-event" type="button" class="btn btn-primary">
                      <i class="fa fa-plus"></i> Ajouter
                    </button>
                  </div>
                </div>
              </div>

              <div class="callout callout-info" style="margin-top: 15px; margin-bottom: 0;">
                <h5><i class="fa fa-info-circle"></i> Astuce</h5>
                <p style="margin: 0; font-size: 12px;">Glissez-déposez les événements sur le calendrier pour les programmer.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-calendar"></i> Calendrier des Événements</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="box-body no-padding">
              <!-- LE CALENDRIER -->
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste détaillée des événements -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-list"></i> Liste Complète des Événements</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-success btn-sm" id="btn-add-detailed-event">
                  <i class="fa fa-plus"></i> Nouvel Événement Détaillé
                </button>
              </div>
            </div>
            <div class="box-body">
              <div class="table-responsive">
                <table id="evenements-table" class="table table-bordered table-striped table-hover">
                  <thead>
                    <tr>
                      <th><i class="fa fa-tag"></i> Titre</th>
                      <th><i class="fa fa-calendar-o"></i> Début</th>
                      <th><i class="fa fa-calendar"></i> Fin</th>
                      <th><i class="fa fa-map-marker"></i> Lieu</th>
                      <th><i class="fa fa-user"></i> Responsable</th>
                      <th><i class="fa fa-cogs"></i> Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($evenements)): ?>
                      <?php foreach ($evenements as $evenement): ?>
                      <tr>
                        <td>
                          <strong><?php echo htmlspecialchars($evenement['titre']); ?></strong>
                        </td>
                        <td>
                          <span class="label label-primary">
                            <i class="fa fa-clock-o"></i> 
                            <?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?>
                          </span>
                        </td>
                        <td>
                          <span class="label label-warning">
                            <i class="fa fa-clock-o"></i> 
                            <?php echo date('d/m/Y H:i', strtotime($evenement['date_fin'])); ?>
                          </span>
                        </td>
                        <td>
                          <i class="fa fa-map-marker text-red"></i> 
                          <?php echo htmlspecialchars($evenement['lieu']); ?>
                        </td>
                        <td>
                          <i class="fa fa-user text-blue"></i> 
                          <?php echo htmlspecialchars($evenement['responsable']); ?>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button class="btn btn-xs btn-info view-event" data-id="<?php echo $evenement['id']; ?>" 
                                    title="Voir les détails">
                              <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-xs btn-warning btn-edit-event" data-id="<?php echo $evenement['id']; ?>" 
                                    title="Modifier">
                              <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-xs btn-danger btn-delete-event" 
                                    data-id="<?php echo $evenement['id']; ?>" 
                                    data-title="<?php echo htmlspecialchars($evenement['titre']); ?>" 
                                    title="Supprimer">
                              <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6" class="text-center">
                          <div class="callout callout-info">
                            <h4><i class="fa fa-info-circle"></i> Aucun événement</h4>
                            <p>Aucun événement n'est encore planifié. Créez votre premier événement!</p>
                            <button type="button" class="btn btn-primary" id="btn-add-first-event" onclick="openAddEventModal()">
                              <i class="fa fa-plus"></i> Créer le Premier Événement
                            </button>
                          </div>
                        </td>
                      </tr>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>

  <!-- Modal pour les détails de l'événement -->
  <div class="modal fade" id="modal-event-details">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><i class="fa fa-calendar-check-o"></i> Détails de l'Événement</h4>
        </div>
        <div class="modal-body">
          <form id="event-form" role="form">
            <input type="hidden" id="event_id" name="event_id">
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="event_title"><i class="fa fa-tag"></i> Titre</label>
                  <input type="text" class="form-control" id="event_title" name="event_title" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="event_location"><i class="fa fa-map-marker"></i> Lieu</label>
                  <input type="text" class="form-control" id="event_location" name="event_location" required>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="event_start"><i class="fa fa-calendar-o"></i> Date de début</label>
                  <input type="datetime-local" class="form-control" id="event_start" name="event_start" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="event_end"><i class="fa fa-calendar"></i> Date de fin</label>
                  <input type="datetime-local" class="form-control" id="event_end" name="event_end" required>
                </div>
              </div>
            </div>
            
            <div class="form-group">
              <label for="event_responsible"><i class="fa fa-user"></i> Responsable</label>
              <input type="text" class="form-control" id="event_responsible" name="event_responsible" required>
            </div>
            
            <div class="form-group">
              <label for="event_description"><i class="fa fa-file-text-o"></i> Description</label>
              <textarea class="form-control" id="event_description" name="event_description" rows="4" placeholder="Description détaillée de l'événement..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
            <i class="fa fa-times"></i> Fermer
          </button>
          <button type="button" id="save-event" class="btn btn-primary">
            <i class="fa fa-save"></i> Enregistrer les modifications
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal de confirmation de suppression -->
  <div class="modal fade" id="confirm-delete-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-red">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title"><i class="fa fa-warning"></i> Confirmation de Suppression</h4>
        </div>
        <div class="modal-body">
          <div class="callout callout-danger">
            <h4><i class="fa fa-exclamation-triangle"></i> Attention!</h4>
            <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
            <p><strong>Cette action est irréversible.</strong></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
            <i class="fa fa-times"></i> Annuler
          </button>
          <a href="#" id="confirm-delete" class="btn btn-danger">
            <i class="fa fa-trash"></i> Supprimer Définitivement
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript Dependencies -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/moment/moment.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/locale/fr.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<!-- Custom JavaScript for Events Page -->
<script>
// Variables PHP pour JavaScript
var evenementsCalendar = <?php echo $evenementsCalendar; ?>;
var BASE_URL = '<?php echo BASE_URL; ?>';
var username = '<?php echo htmlspecialchars($username); ?>';

console.log('Variables initialisées:', {
  evenements: evenementsCalendar,
  baseUrl: BASE_URL,
  user: username
});
</script>
<script src="<?php echo BASE_URL; ?>dist/js/evenements-scolaires.js"></script>

</body>
</html>
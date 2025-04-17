<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des événements scolaires
$evenements = [];
$evenements_query = "SELECT e.* 
                    FROM evenements_scolaires e 
                    ORDER BY e.date_debut DESC";
$evenements_result = $mysqli->query($evenements_query);
if ($evenements_result) {
    while ($row = $evenements_result->fetch_assoc()) {
        $evenements[] = $row;
    }
}

// Récupération des classes pour le formulaire d'ajout/modification
$classes = [];
$classes_query = "SELECT id, nom FROM classes ORDER BY nom ASC";
$classes_result = $mysqli->query($classes_query);
if ($classes_result) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
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

// Messages de notification
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
// Effacer les messages après les avoir récupérés
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Événements Scolaires Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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
            <i class="fa fa-child"></i> <span>Élèves Maternelle</span>
          </a>
        </li>
        
        <li>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=cours&section=maternelle">
            <i class="fa fa-book"></i> <span>Cours Maternelle</span>
          </a>
        </li>

        <li class="active">
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
        Événements Scolaires Maternelle
        <small>Gestion des événements</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Événements Scolaires</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-check"></i> Succès!</h4>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h4 class="box-title">Actions</h4>
            </div>
            <div class="box-body">
              <div class="btn-group-vertical" style="width: 100%;">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-ajouter-evenement">
                  <i class="fa fa-plus"></i> Ajouter un événement
                </button>
                <button type="button" class="btn btn-default" id="btn-refresh-calendar">
                  <i class="fa fa-refresh"></i> Actualiser le calendrier
                </button>
              </div>
            </div>
          </div>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Types d'événements</h3>
            </div>
            <div class="box-body">
              <div class="external-event bg-green">Activités pédagogiques</div>
              <div class="external-event bg-yellow">Réunions parents</div>
              <div class="external-event bg-aqua">Sorties éducatives</div>
              <div class="external-event bg-red">Jours fériés</div>
              <div class="external-event bg-purple">Fêtes scolaires</div>
              <div class="external-event bg-navy">Ateliers</div>
              <div class="checkbox">
                <label for="drop-remove">
                  <input type="checkbox" id="drop-remove">
                  Filtrer par type
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des Événements Scolaires</h3>
            </div>
            <div class="box-body">
              <table id="evenements-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Lieu</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($evenements as $evenement): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($evenement['titre']); ?></td>
                    <td>
                      <span class="label 
                        <?php 
                          switch($evenement['description']) {
                            case 'Activité pédagogique': echo 'bg-green'; break;
                            case 'Réunion parents': echo 'bg-yellow'; break;
                            case 'Sortie éducative': echo 'bg-aqua'; break;
                            case 'Jour férié': echo 'bg-red'; break;
                            case 'Fête scolaire': echo 'bg-purple'; break;
                            case 'Atelier': echo 'bg-navy'; break;
                            default: echo 'bg-gray'; break;
                          }
                        ?>">
                        <?php echo htmlspecialchars($evenement['description']); ?>
                      </span>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_fin'])); ?></td>
                    <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                    <td>
                      <button class="btn btn-xs btn-info view-event" data-toggle="modal" data-target="#modal-voir-evenement" 
                        data-id="<?php echo $evenement['id']; ?>"
                        data-titre="<?php echo htmlspecialchars($evenement['titre']); ?>"
                        data-type="<?php echo htmlspecialchars($evenement['description']); ?>"
                        data-debut="<?php echo $evenement['date_debut']; ?>"
                        data-fin="<?php echo $evenement['date_fin']; ?>"
                        data-lieu="<?php echo htmlspecialchars($evenement['lieu']); ?>"
                        data-description="<?php echo htmlspecialchars($evenement['description']); ?>"
                        data-classe-id="<?php echo $evenement['classe_id'] ?? ''; ?>"
                        data-classe-nom="<?php echo $evenement['classe_nom'] ?? 'Toutes les classes'; ?>">
                        <i class="fa fa-eye"></i> Détails
                      </button>
                      <button class="btn btn-xs btn-warning" data-toggle="modal" data-target="#modal-modifier-evenement"
                        data-id="<?php echo $evenement['id']; ?>"
                        data-titre="<?php echo htmlspecialchars($evenement['titre']); ?>"
                        data-type="<?php echo htmlspecialchars($evenement['description']); ?>"
                        data-debut="<?php echo $evenement['date_debut']; ?>"
                        data-fin="<?php echo $evenement['date_fin']; ?>"
                        data-lieu="<?php echo htmlspecialchars($evenement['lieu']); ?>"
                        data-description="<?php echo htmlspecialchars($evenement['description']); ?>"
                        data-classe-id="<?php echo $evenement['classe_id'] ?? ''; ?>">
                        <i class="fa fa-edit"></i> Modifier
                      </button>
                      <button class="btn btn-xs btn-danger delete-event" onclick="confirmerSuppression(<?php echo $evenement['id']; ?>, '<?php echo addslashes($evenement['titre']); ?>')">
                        <i class="fa fa-trash"></i> Supprimer
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

  <!-- Modal Ajouter Événement -->
  <div class="modal fade" id="modal-ajouter-evenement">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter un nouvel événement</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=ajouterEvenement" method="post">
          <div class="modal-body">
            <div class="form-group">
              <label for="titre">Titre de l'événement</label>
              <input type="text" class="form-control" id="titre" name="titre" required>
            </div>
            <div class="form-group">
              <label for="type">Type d'événement</label>
              <select class="form-control" id="type" name="type" required>
                <option value="">Sélectionner un type</option>
                <option value="Activité pédagogique">Activité pédagogique</option>
                <option value="Réunion parents">Réunion parents</option>
                <option value="Sortie éducative">Sortie éducative</option>
                <option value="Jour férié">Jour férié</option>
                <option value="Fête scolaire">Fête scolaire</option>
                <option value="Atelier">Atelier</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="date_debut">Date de début</label>
                  <input type="datetime-local" class="form-control" id="date_debut" name="date_debut" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="date_fin">Date de fin</label>
                  <input type="datetime-local" class="form-control" id="date_fin" name="date_fin" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="classe">Classe concernée</label>
              <select class="form-control" id="classe" name="classe">
                <option value="">Toutes les classes</option>
                <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>"><?php echo htmlspecialchars($classe['nom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="lieu">Lieu</label>
              <input type="text" class="form-control" id="lieu" name="lieu" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <input type="hidden" name="section" value="maternelle">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Modifier Événement -->
  <div class="modal fade" id="modal-modifier-evenement">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Modifier l'événement</h4>
        </div>
        <form action="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=modifierEvenement" method="post">
          <div class="modal-body">
            <input type="hidden" id="edit-id" name="id">
            <div class="form-group">
              <label for="edit-titre">Titre de l'événement</label>
              <input type="text" class="form-control" id="edit-titre" name="titre" required>
            </div>
            <div class="form-group">
              <label for="edit-type">Type d'événement</label>
              <select class="form-control" id="edit-type" name="type" required>
                <option value="">Sélectionner un type</option>
                <option value="Activité pédagogique">Activité pédagogique</option>
                <option value="Réunion parents">Réunion parents</option>
                <option value="Sortie éducative">Sortie éducative</option>
                <option value="Jour férié">Jour férié</option>
                <option value="Fête scolaire">Fête scolaire</option>
                <option value="Atelier">Atelier</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit-date-debut">Date de début</label>
                  <input type="datetime-local" class="form-control" id="edit-date-debut" name="date_debut" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit-date-fin">Date de fin</label>
                  <input type="datetime-local" class="form-control" id="edit-date-fin" name="date_fin" required>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label for="edit-classe-id">Classe concernée</label>
              <select class="form-control" id="edit-classe-id" name="classe_id">
                <option value="">Toutes les classes</option>
                <?php foreach ($classes as $classe): ?>
                <option value="<?php echo $classe['id']; ?>"><?php echo htmlspecialchars($classe['nom']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="edit-lieu">Lieu</label>
              <input type="text" class="form-control" id="edit-lieu" name="lieu" required>
            </div>
            <div class="form-group">
              <label for="edit-description">Description</label>
              <textarea class="form-control" id="edit-description" name="description" rows="3"></textarea>
            </div>
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

  <!-- Modal Voir Événement -->
  <div class="modal fade" id="modal-voir-evenement">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Détails de l'événement</h4>
        </div>
        <div class="modal-body">
          <div class="box-body box-profile">
            <h3 class="profile-username text-center" id="view-titre"></h3>
            <p class="text-muted text-center">
              <span class="label" id="view-type-label"></span>
            </p>

            <ul class="list-group list-group-unbordered">
              <li class="list-group-item">
                <b>Date de début</b> <span class="pull-right" id="view-debut"></span>
              </li>
              <li class="list-group-item">
                <b>Date de fin</b> <span class="pull-right" id="view-fin"></span>
              </li>
              <li class="list-group-item">
                <b>Classe concernée</b> <span class="pull-right" id="view-classe"></span>
              </li>
              <li class="list-group-item">
                <b>Lieu</b> <span class="pull-right" id="view-lieu"></span>
              </li>
            </ul>

            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">Description</h3>
              </div>
              <div class="box-body">
                <p id="view-description"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
        </div>
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

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/moment/moment.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/locale/fr.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialiser DataTable
  $('#evenements-table').DataTable({
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
  
  // Initialiser le calendrier
  $('#calendar').fullCalendar({
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek,agendaDay'
    },
    buttonText: {
      today: 'Aujourd\'hui',
      month: 'Mois',
      week: 'Semaine',
      day: 'Jour'
    },
    locale: 'fr',
    navLinks: true,
    editable: true,
    eventLimit: true,
    events: [
      <?php foreach ($evenements as $evenement): ?>
      {
        id: <?php echo $evenement['id']; ?>,
        title: '<?php echo addslashes(htmlspecialchars($evenement['titre'])); ?>',
        start: '<?php echo $evenement['date_debut']; ?>',
        end: '<?php echo $evenement['date_fin']; ?>',
        backgroundColor: '<?php 
          switch($evenement['description']) {
            case 'Activité pédagogique': echo '#00a65a'; break;
            case 'Réunion parents': echo '#f39c12'; break;
            case 'Sortie éducative': echo '#00c0ef'; break;
            case 'Jour férié': echo '#dd4b39'; break;
            case 'Fête scolaire': echo '#605ca8'; break;
            case 'Atelier': echo '#001f3f'; break;
            default: echo '#7f7f7f'; break;
          }
        ?>',
        borderColor: '<?php 
          switch($evenement['description']) {
            case 'Activité pédagogique': echo '#00a65a'; break;
            case 'Réunion parents': echo '#f39c12'; break;
            case 'Sortie éducative': echo '#00c0ef'; break;
            case 'Jour férié': echo '#dd4b39'; break;
            case 'Fête scolaire': echo '#605ca8'; break;
            case 'Atelier': echo '#001f3f'; break;
            default: echo '#7f7f7f'; break;
          }
        ?>',
        allDay: <?php echo (strtotime($evenement['date_fin']) - strtotime($evenement['date_debut'])) >= 86400 ? 'true' : 'false'; ?>,
        url: '#',
        className: 'event-type-<?php echo strtolower(str_replace(' ', '-', $evenement['description'])); ?>'
      },
      <?php endforeach; ?>
    ],
    eventClick: function(calEvent, jsEvent, view) {
      // Ouvrir la modal de détails au lieu de suivre l'URL
      jsEvent.preventDefault();
      
      // Afficher les détails de l'événement dans la modal
      $('#view-titre').text(calEvent.title);
      
      // Définir la couleur du label
      var eventType = '';
      var typeClass = '';
      
      // Trouver le type d'événement basé sur la classe CSS
      if (calEvent.className) {
        var className = calEvent.className;
        if (typeof className === 'string') {
          className = [className];
        }
        
        for (var i = 0; i < className.length; i++) {
          if (className[i].startsWith('event-type-')) {
            eventType = className[i].replace('event-type-', '').replace(/-/g, ' ');
            eventType = eventType.charAt(0).toUpperCase() + eventType.slice(1);
            break;
          }
        }
      }
      
      // Définir la classe CSS pour le label
      switch(eventType.toLowerCase()) {
        case 'activité pédagogique': typeClass = 'bg-green'; break;
        case 'réunion parents': typeClass = 'bg-yellow'; break;
        case 'sortie éducative': typeClass = 'bg-aqua'; break;
        case 'jour férié': typeClass = 'bg-red'; break;
        case 'fête scolaire': typeClass = 'bg-purple'; break;
        case 'atelier': typeClass = 'bg-navy'; break;
        default: typeClass = 'bg-gray'; break;
      }
      
      $('#view-type-label').text(eventType).removeClass().addClass('label ' + typeClass);
      
      // Formater les dates
      var dateDebut = moment(calEvent.start).format('DD/MM/YYYY HH:mm');
      var dateFin = moment(calEvent.end).format('DD/MM/YYYY HH:mm');
      
      $('#view-debut').text(dateDebut);
      $('#view-fin').text(dateFin);
      
      // Récupérer les autres détails depuis les attributs data
      var eventId = calEvent.id;
      
      // Trouver les boutons qui ont cet ID dans leurs attributs data
      var detailsButton = $('button[data-id="' + eventId + '"][data-target="#modal-voir-evenement"]');
      
      if (detailsButton.length) {
        $('#view-classe').text(detailsButton.data('classe-nom') || 'Toutes les classes');
        $('#view-lieu').text(detailsButton.data('lieu'));
        $('#view-description').text(detailsButton.data('description'));
      }
      
      // Afficher la modal
      $('#modal-voir-evenement').modal('show');
      
      return false;
    }
  });
  
  // Actualiser le calendrier
  $('#btn-refresh-calendar').click(function() {
    $('#calendar').fullCalendar('refetchEvents');
  });
  
  // Filtrer les événements par type
  $('#drop-remove').change(function() {
    if ($(this).is(':checked')) {
      $('.external-event').each(function() {
        var eventType = $(this).text().toLowerCase().replace(/ /g, '-');
        if (!$(this).hasClass('selected-type')) {
          $('.event-type-' + eventType).hide();
        }
      });
    } else {
      $('.fc-event').show();
    }
  });
  
  // Sélectionner un type d'événement pour le filtrage
  $('.external-event').click(function() {
    if ($('#drop-remove').is(':checked')) {
      $('.external-event').removeClass('selected-type');
      $(this).addClass('selected-type');
      
      var selectedType = $(this).text().toLowerCase().replace(/ /g, '-');
      $('.fc-event').hide();
      $('.event-type-' + selectedType).show();
    }
  });
  
  // Modal Voir Événement
  $('#modal-voir-evenement').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var titre = button.data('titre');
    var type = button.data('type');
    var debut = button.data('debut');
    var fin = button.data('fin');
    var classeNom = button.data('classe-nom') || 'Toutes les classes';
    var lieu = button.data('lieu');
    var description = button.data('description');
    
    var modal = $(this);
    modal.find('#view-titre').text(titre);
    
    // Définir la couleur du label en fonction du type
    var typeClass = '';
    switch(type) {
      case 'Activité pédagogique': typeClass = 'bg-green'; break;
      case 'Réunion parents': typeClass = 'bg-yellow'; break;
      case 'Sortie éducative': typeClass = 'bg-aqua'; break;
      case 'Jour férié': typeClass = 'bg-red'; break;
      case 'Fête scolaire': typeClass = 'bg-purple'; break;
      case 'Atelier': typeClass = 'bg-navy'; break;
      default: typeClass = 'bg-gray'; break;
    }
    
    modal.find('#view-type-label').text(type).removeClass().addClass('label ' + typeClass);
    
    // Formater les dates
    var dateDebut = moment(debut).format('DD/MM/YYYY HH:mm');
    var dateFin = moment(fin).format('DD/MM/YYYY HH:mm');
    
    modal.find('#view-debut').text(dateDebut);
    modal.find('#view-fin').text(dateFin);
    modal.find('#view-classe').text(classeNom);
    modal.find('#view-lieu').text(lieu);
    modal.find('#view-description').text(description);
  });
  
  // Modal Modifier Événement
  $('#modal-modifier-evenement').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var id = button.data('id');
    var titre = button.data('titre');
    var type = button.data('type');
    var debut = button.data('debut');
    var fin = button.data('fin');
    var classeId = button.data('classe-id');
    var lieu = button.data('lieu');
    var description = button.data('description');
    
    var modal = $(this);
    modal.find('#edit-id').val(id);
    modal.find('#edit-titre').val(titre);
    modal.find('#edit-type').val(type);
    
    // Formater les dates pour l'input datetime-local
    var dateDebut = moment(debut).format('YYYY-MM-DDTHH:mm');
    var dateFin = moment(fin).format('YYYY-MM-DDTHH:mm');
    
    modal.find('#edit-date-debut').val(dateDebut);
    modal.find('#edit-date-fin').val(dateFin);
    modal.find('#edit-classe-id').val(classeId);
    modal.find('#edit-lieu').val(lieu);
    modal.find('#edit-description').val(description);
  });
});

// Fonction pour confirmer la suppression d'un événement
function confirmerSuppression(id, titre) {
  if (confirm("Êtes-vous sûr de vouloir supprimer l'événement '" + titre + "' ?")) {
    window.location.href = "<?php echo BASE_URL; ?>index.php?controller=Directrice&action=supprimerEvenement&id=" + id;
  }
}
</script>

<!-- Script pour exporter le calendrier en PDF -->
<script>
$(document).ready(function() {
  // Ajouter un bouton d'exportation
  $('.fc-right').append('<button type="button" class="fc-button fc-state-default fc-corner-left fc-corner-right" id="export-calendar">Exporter</button>');
  
  // Fonction d'exportation
  $('#export-calendar').click(function() {
    alert("Fonctionnalité d'exportation en cours de développement");
    // Ici, vous pourriez implémenter l'exportation du calendrier en PDF
    // en utilisant une bibliothèque comme html2pdf.js ou jsPDF
  });
  
  // Statistiques des événements
  var eventTypes = {};
  var eventsByMonth = {};
  
  <?php foreach ($evenements as $evenement): ?>
  // Compter par type
  var type = "<?php echo $evenement['description']; ?>";
  eventTypes[type] = (eventTypes[type] || 0) + 1;
  
  // Compter par mois
  var month = "<?php echo date('m/Y', strtotime($evenement['date_debut'])); ?>";
  eventsByMonth[month] = (eventsByMonth[month] || 0) + 1;
  <?php endforeach; ?>
  
  console.log("Statistiques des événements par type:", eventTypes);
  console.log("Statistiques des événements par mois:", eventsByMonth);
});
</script>
</body>
</html>
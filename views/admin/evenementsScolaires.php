<?php
// Vue pour la gestion des événements scolaires
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
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
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.print.min.css" media="print">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <p><?php echo $username; ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <!-- Nouveaux liens ajoutés -->
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        <!-- Fin des nouveaux liens -->
      
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=Frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addPrefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addDirecteur"><i class="fa fa-circle-o"></i> Ajouter Directeur</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=adddirectrice"><i class="fa fa-circle-o"></i> Ajouter Directrice</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addcomptable"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutCours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Organisation des Événements Scolaires
        <small>Planification et gestion</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Événements Scolaires</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h4 class="box-title">Événements à venir</h4>
            </div>
            <div class="box-body">
              <div id="external-events">
                <div class="external-event bg-green">Réunion des parents</div>
                <div class="external-event bg-yellow">Journée sportive</div>
                <div class="external-event bg-aqua">Sortie culturelle</div>
                <div class="external-event bg-light-blue">Conférence pédagogique</div>
                <div class="external-event bg-red">Examens trimestriels</div>
                <div class="checkbox">
                  <label for="drop-remove">
                    <input type="checkbox" id="drop-remove">
                    Supprimer après placement
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title">Créer un événement</h3>
            </div>
            <div class="box-body">
              <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                <button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Couleur <span class="caret"></span></button>
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
              <div class="input-group">
                <input id="new-event" type="text" class="form-control" placeholder="Titre de l'événement">
                <div class="input-group-btn">
                  <button id="add-new-event" type="button" class="btn btn-primary btn-flat">Ajouter</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="box box-primary">
            <div class="box-body no-padding">
              <!-- LE CALENDRIER -->
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste des événements planifiés -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Liste des événements planifiés</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajouterEvenement" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Ajouter un événement</a>
              </div>
            </div>
            <div class="box-body">
              <table id="evenements-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Titre</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Lieu</th>
                    <th>Responsable</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($evenements as $evenement): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($evenement['titre']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_debut'])); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($evenement['date_fin'])); ?></td>
                    <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                    <td><?php echo htmlspecialchars($evenement['responsable']); ?></td>
                    <td>
                      <button class="btn btn-xs btn-info view-event" data-id="<?php echo $evenement['id']; ?>"><i class="fa fa-eye"></i> Détails</button>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=modifierEvenement&id=<?php echo $evenement['id']; ?>" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Modifier</a>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=supprimerEvenement&id=<?php echo $evenement['id']; ?>" class="btn btn-xs btn-danger delete-event" data-id="<?php echo $evenement['id']; ?>"><i class="fa fa-trash"></i> Supprimer</a>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>

  <!-- Modal pour les détails de l'événement -->
  <div class="modal fade" id="modal-event-details">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Détails de l'événement</h4>
        </div>
        <div class="modal-body">
          <form id="event-form" role="form">
            <input type="hidden" id="event_id" name="event_id">
            <div class="form-group">
              <label for="event_title">Titre</label>
              <input type="text" class="form-control" id="event_title" name="event_title" required>
            </div>
            <div class="form-group">
              <label for="event_start">Date de début</label>
              <input type="datetime-local" class="form-control" id="event_start" name="event_start" required>
            </div>
            <div class="form-group">
              <label for="event_end">Date de fin</label>
              <input type="datetime-local" class="form-control" id="event_end" name="event_end" required>
            </div>
            <div class="form-group">
              <label for="event_location">Lieu</label>
              <input type="text" class="form-control" id="event_location" name="event_location" required>
            </div>
            <div class="form-group">
              <label for="event_responsible">Responsable</label>
              <input type="text" class="form-control" id="event_responsible" name="event_responsible" required>
            </div>
            <div class="form-group">
              <label for="event_description">Description</label>
              <textarea class="form-control" id="event_description" name="event_description" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Fermer</button>
          <button type="button" id="save-event" class="btn btn-primary">Enregistrer les modifications</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal de confirmation de suppression -->
  <div class="modal fade" id="confirm-delete-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Confirmation de suppression</h4>
        </div>
        <div class="modal-body">
          <p>Êtes-vous sûr de vouloir supprimer cet événement ?</p>
          <p>Cette action est irréversible.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
          <a href="#" id="confirm-delete" class="btn btn-danger">Supprimer</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- FullCalendar -->
<script src="<?php echo BASE_URL; ?>bower_components/moment/moment.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/fullcalendar/dist/locale/fr.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // Initialisation de la DataTable
    $('#evenements-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'language'    : {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      }
    });
    
    /* Initialisation des événements externes */
    function init_events(ele) {
      ele.each(function () {
        var eventObject = {
          title: $.trim($(this).text())
        }
        $(this).data('eventObject', eventObject)
        $(this).draggable({
          zIndex        : 1070,
          revert        : true,
          revertDuration: 0
        })
      })
    }

    init_events($('#external-events div.external-event'))

    /* Initialisation du calendrier */
    var date = new Date()
    var d    = date.getDate(),
        m    = date.getMonth(),
        y    = date.getFullYear()
    $('#calendar').fullCalendar({
      header    : {
        left  : 'prev,next today',
        center: 'title',
        right : 'month,agendaWeek,agendaDay'
      },
      buttonText: {
        today: 'aujourd\'hui',
        month: 'mois',
        week : 'semaine',
        day  : 'jour'
      },
      locale: 'fr',
      events    : <?php echo $evenementsCalendar; ?>,
      editable  : true,
      droppable : true,
      drop      : function (date, allDay) {
        var originalEventObject = $(this).data('eventObject')
        var copiedEventObject = $.extend({}, originalEventObject)
        copiedEventObject.start           = date
        copiedEventObject.allDay          = allDay
        copiedEventObject.backgroundColor = $(this).css('background-color')
        copiedEventObject.borderColor     = $(this).css('border-color')

        $('#calendar').fullCalendar('renderEvent', copiedEventObject, true)

        if ($('#drop-remove').is(':checked')) {
          $(this).remove()
        }
      },
      eventClick: function(calEvent, jsEvent, view) {
        // Charger les détails de l'événement dans le modal
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Admin&action=getEvenementDetails',
          type: 'GET',
          data: {id: calEvent.id},
          dataType: 'json',
          success: function(response) {
            if(response.success) {
              var event = response.data;
              $('#event_id').val(event.id);
              $('#event_title').val(event.titre);
              $('#event_start').val(moment(event.date_debut).format('YYYY-MM-DDTHH:mm'));
              $('#event_end').val(moment(event.date_fin).format('YYYY-MM-DDTHH:mm'));
              $('#event_location').val(event.lieu);
              $('#event_responsible').val(event.responsable);
              $('#event_description').val(event.description);
              $('#modal-event-details').modal('show');
            }
          }
        });
      }
    });

    // Gestion des détails d'événement
    $('.view-event').on('click', function() {
      var eventId = $(this).data('id');
      $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?controller=Admin&action=getEvenementDetails',
        type: 'GET',
        data: {id: eventId},
        dataType: 'json',
        success: function(response) {
          if(response.success) {
            var event = response.data;
            $('#event_id').val(event.id);
            $('#event_title').val(event.titre);
            $('#event_start').val(moment(event.date_debut).format('YYYY-MM-DDTHH:mm'));
            $('#event_end').val(moment(event.date_fin).format('YYYY-MM-DDTHH:mm'));
            $('#event_location').val(event.lieu);
            $('#event_responsible').val(event.responsable);
            $('#event_description').val(event.description);
            $('#modal-event-details').modal('show');
          }
        }
      });
    });

    // Enregistrer les modifications d'un événement
    $('#save-event').on('click', function() {
      $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?controller=Admin&action=updateEvenement',
        type: 'POST',
        data: $('#event-form').serialize(),
        dataType: 'json',
        success: function(response) {
          if(response.success) {
            $('#modal-event-details').modal('hide');
            location.reload();
          } else {
            alert('Erreur lors de la mise à jour: ' + response.message);
          }
        }
      });
    });

    // Confirmation de suppression
    $('.delete-event').on('click', function(e) {
      e.preventDefault();
      var eventId = $(this).data('id');
      var deleteUrl = $(this).attr('href');
      $('#confirm-delete').attr('href', deleteUrl);
      $('#confirm-delete-modal').modal('show');
    });

    /* Gestion des couleurs */
    var currColor = '#3c8dbc'
    $('#color-chooser > li > a').click(function (e) {
      e.preventDefault()
      currColor = $(this).css('color')
      $('#add-new-event').css({
        'background-color': currColor,
        'border-color'    : currColor
      })
    })
    $('#add-new-event').click(function (e) {
      e.preventDefault()
      var val = $('#new-event').val()
      if (val.length == 0) {
        return
      }

      var event = $('<div />')
      event.css({
        'background-color': currColor,
        'border-color'    : currColor,
        'color'           : '#fff'
      }).addClass('external-event')
      event.html(val)
      $('#external-events').prepend(event)

      init_events(event)

      $('#new-event').val('')
    })
  })
</script>
</body>
</html>
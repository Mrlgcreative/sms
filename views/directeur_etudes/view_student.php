<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'authentification
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'directeur_Etude') {
    header('Location: ' . BASE_URL . 'index.php?controller=Auth&action=login');
    exit();
}

// Initialiser les variables de session
$username = $_SESSION['username'];
$email = $_SESSION['email'] ?? 'email@exemple.com';
$role = 'Directeur des Études';

// Vérification que les données de l'élève sont disponibles
if (!isset($eleve) || empty($eleve)) {
    header('Location: ' . BASE_URL . 'index.php?controller=DirecteurEtude&action=eleves');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Profil de l'élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b>SMS</b> Directeur</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
          </li>
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo isset($_SESSION['image']) ? BASE_URL . $_SESSION['image'] : BASE_URL . 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo htmlspecialchars($username); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?php echo isset($_SESSION['image']) ? BASE_URL . $_SESSION['image'] : BASE_URL . 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo htmlspecialchars($role); ?><small>Connecté depuis <?php echo date('M. Y'); ?></small></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <img src="<?php echo isset($_SESSION['image']) ? BASE_URL . $_SESSION['image'] : BASE_URL . 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>        <div class="pull-left info">
          <p><?php echo htmlspecialchars($username); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> En ligne</a>
        </div>
      </div>
      
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Rechercher...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
          </span>
        </div>
      </form>
      
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Gestion des Élèves</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="active"><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves"><i class="fa fa-circle-o"></i> Liste des élèves</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=classes"><i class="fa fa-circle-o"></i> Classes</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=absences"><i class="fa fa-circle-o"></i> Absences</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Enseignement</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=professeurs"><i class="fa fa-circle-o"></i> Professeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=cours"><i class="fa fa-circle-o"></i> Cours</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=emploiDuTemps"><i class="fa fa-circle-o"></i> Emploi du temps</a></li>
          </ul>
        </li>
        <li class="treeview">
          <a href="#">
            <i class="fa fa-calendar"></i> <span>Événements</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=evenementsScolaires"><i class="fa fa-circle-o"></i> Événements scolaires</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=discipline">
            <i class="fa fa-exclamation-triangle"></i> <span>Discipline</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=profil">
            <i class="fa fa-user"></i> <span>Mon Profil</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">      <h1>
        Profil de l'élève
        <small><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=eleves">Élèves</a></li>
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
                   src="<?php echo !empty($eleve['photo']) ? BASE_URL . $eleve['photo'] : BASE_URL . 'dist/img/avatar5.png'; ?>" 
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
              </ul>              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=voirEleve&id=<?php echo $eleve['id']; ?>&action_type=modifier" class="btn btn-primary btn-block">
                <i class="fa fa-edit"></i> Modifier les informations
              </a>
              <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=carteEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-block" target="_blank">
                <i class="fa fa-id-card"></i> Imprimer la carte
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
              <li class="active"><a href="#notes_tab" data-toggle="tab"><i class="fa fa-graduation-cap"></i> Notes</a></li>
              <li><a href="#absences_tab" data-toggle="tab"><i class="fa fa-calendar-times-o"></i> Absences</a></li>
              <li><a href="#discipline_tab" data-toggle="tab"><i class="fa fa-exclamation-triangle"></i> Discipline</a></li>
              <li><a href="#paiements_tab" data-toggle="tab"><i class="fa fa-money"></i> Paiements</a></li>
              <li><a href="#historique_tab" data-toggle="tab"><i class="fa fa-history"></i> Historique</a></li>
            </ul>
            <div class="tab-content">
              <!-- Onglet Notes -->
              <div class="active tab-pane" id="notes_tab">
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">Notes et évaluations</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Matière</th>
                            <th>Période</th>
                            <th>Note</th>
                            <th>Coefficient</th>
                            <th>Note pondérée</th>
                            <th>Observations</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($notes)): ?>
                            <?php foreach ($notes as $note): ?>
                              <tr>
                                <td><?php echo htmlspecialchars($note['matiere'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($note['periode'] ?? 'N/A'); ?></td>
                                <td>
                                  <span class="label <?php echo ($note['note'] >= 10) ? 'label-success' : 'label-danger'; ?>">
                                    <?php echo htmlspecialchars($note['note'] ?? 'N/A'); ?>/20
                                  </span>
                                </td>
                                <td><?php echo htmlspecialchars($note['coefficient'] ?? '1'); ?></td>
                                <td><?php echo htmlspecialchars(($note['note'] * $note['coefficient']) ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($note['observations'] ?? ''); ?></td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="6" class="text-center text-muted">Aucune note disponible</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Onglet Absences -->
              <div class="tab-pane" id="absences_tab">
                <div class="box box-warning">
                  <div class="box-header with-border">
                    <h3 class="box-title">Historique des absences</h3>
                    <div class="box-tools pull-right">
                      <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=ajouterAbsence&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-plus"></i> Ajouter une absence
                      </a>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="absencesTable">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Heure début</th>
                            <th>Heure fin</th>
                            <th>Matière</th>
                            <th>Motif</th>
                            <th>Justifiée</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($absences)): ?>
                            <?php foreach ($absences as $absence): ?>
                              <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($absence['date_absence']))); ?></td>
                                <td><?php echo htmlspecialchars($absence['heure_debut'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($absence['heure_fin'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($absence['matiere'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($absence['motif'] ?? 'Non précisé'); ?></td>
                                <td>
                                  <span class="label <?php echo ($absence['justifiee'] == 1) ? 'label-success' : 'label-danger'; ?>">
                                    <?php echo ($absence['justifiee'] == 1) ? 'Oui' : 'Non'; ?>
                                  </span>
                                </td>
                                <td>
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=modifierAbsence&id=<?php echo $absence['id']; ?>" class="btn btn-xs btn-primary">
                                    <i class="fa fa-edit"></i>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=supprimerAbsence&id=<?php echo $absence['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette absence ?')">
                                    <i class="fa fa-trash"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="7" class="text-center text-muted">Aucune absence enregistrée</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Onglet Discipline -->
              <div class="tab-pane" id="discipline_tab">
                <div class="box box-danger">
                  <div class="box-header with-border">
                    <h3 class="box-title">Incidents disciplinaires</h3>
                    <div class="box-tools pull-right">
                      <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=ajouterIncident&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-sm btn-danger">
                        <i class="fa fa-plus"></i> Signaler un incident
                      </a>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="disciplineTable">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Type d'incident</th>
                            <th>Description</th>
                            <th>Sanctions</th>
                            <th>Professeur</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($incidents)): ?>
                            <?php foreach ($incidents as $incident): ?>
                              <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($incident['date_incident']))); ?></td>
                                <td>
                                  <span class="label label-warning">
                                    <?php echo htmlspecialchars($incident['type_incident'] ?? 'Non précisé'); ?>
                                  </span>
                                </td>
                                <td><?php echo htmlspecialchars(substr($incident['description'], 0, 100)) . (strlen($incident['description']) > 100 ? '...' : ''); ?></td>
                                <td><?php echo htmlspecialchars($incident['sanctions'] ?? 'Aucune'); ?></td>
                                <td><?php echo htmlspecialchars($incident['professeur'] ?? 'N/A'); ?></td>
                                <td>
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=modifierIncident&id=<?php echo $incident['id']; ?>" class="btn btn-xs btn-primary">
                                    <i class="fa fa-edit"></i>
                                  </a>
                                  <a href="<?php echo BASE_URL; ?>index.php?controller=DirecteurEtude&action=supprimerIncident&id=<?php echo $incident['id']; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet incident ?')">
                                    <i class="fa fa-trash"></i>
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="6" class="text-center text-muted">Aucun incident disciplinaire enregistré</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Onglet Paiements -->
              <div class="tab-pane" id="paiements_tab">
                <div class="box box-success">
                  <div class="box-header with-border">
                    <h3 class="box-title">Historique des paiements</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped" id="paymentsTable">
                        <thead>
                          <tr>
                            <th>Date</th>
                            <th>Type de frais</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Mode de paiement</th>
                            <th>Reçu</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($paiements)): ?>
                            <?php foreach ($paiements as $paiement): ?>
                              <tr>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($paiement['date_paiement']))); ?></td>
                                <td><?php echo htmlspecialchars($paiement['type_frais'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($paiement['montant'], 0, ',', ' '); ?> FC</td>
                                <td>
                                  <span class="label <?php echo ($paiement['statut'] == 'payé') ? 'label-success' : 'label-warning'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($paiement['statut'] ?? 'En attente')); ?>
                                  </span>
                                </td>
                                <td><?php echo htmlspecialchars($paiement['mode_paiement'] ?? 'N/A'); ?></td>
                                <td>
                                  <?php if (!empty($paiement['recu'])): ?>
                                    <a href="<?php echo BASE_URL . $paiement['recu']; ?>" target="_blank" class="btn btn-xs btn-info">
                                      <i class="fa fa-download"></i> Télécharger
                                    </a>
                                  <?php else: ?>
                                    <span class="text-muted">Non disponible</span>
                                  <?php endif; ?>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="6" class="text-center text-muted">Aucun paiement enregistré</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Onglet Historique -->
              <div class="tab-pane" id="historique_tab">
                <div class="box box-info">
                  <div class="box-header with-border">
                    <h3 class="box-title">Historique académique</h3>
                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th>Année scolaire</th>
                            <th>Classe</th>
                            <th>Moyenne générale</th>
                            <th>Rang</th>
                            <th>Mention</th>
                            <th>Décision</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if (!empty($historique_academique)): ?>
                            <?php foreach ($historique_academique as $annee): ?>
                              <tr>
                                <td><?php echo htmlspecialchars($annee['annee_scolaire'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($annee['classe'] ?? 'N/A'); ?></td>
                                <td>
                                  <span class="label <?php echo ($annee['moyenne'] >= 10) ? 'label-success' : 'label-danger'; ?>">
                                    <?php echo htmlspecialchars($annee['moyenne'] ?? 'N/A'); ?>/20
                                  </span>
                                </td>
                                <td><?php echo htmlspecialchars($annee['rang'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($annee['mention'] ?? 'N/A'); ?></td>
                                <td>
                                  <span class="label <?php echo ($annee['decision'] == 'Admis') ? 'label-success' : (($annee['decision'] == 'Redouble') ? 'label-warning' : 'label-danger'); ?>">
                                    <?php echo htmlspecialchars($annee['decision'] ?? 'En cours'); ?>
                                  </span>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php else: ?>
                            <tr>
                              <td colspan="6" class="text-center text-muted">Aucun historique académique disponible</td>
                            </tr>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistiques rapides -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-graduation-cap"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Moyenne générale</span>
              <span class="info-box-number">
                <?php 
                  $moyenne_generale = isset($eleve['moyenne_generale']) ? $eleve['moyenne_generale'] : 0;
                  echo number_format($moyenne_generale, 2);
                ?>/20
              </span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-calendar-times-o"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total absences</span>
              <span class="info-box-number"><?php echo count($absences ?? []); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Incidents</span>
              <span class="info-box-number"><?php echo count($incidents ?? []); ?></span>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Paiements</span>
              <span class="info-box-number"><?php echo count($paiements ?? []); ?></span>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SMS St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(function () {
  // Initialiser DataTables pour les tableaux
  $('#absencesTable, #disciplineTable, #paymentsTable').DataTable({
    'paging': true,
    'lengthChange': false,
    'searching': true,
    'ordering': true,
    'info': true,
    'autoWidth': false,
    'language': {
      'url': '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
    }
  });
  
  // Activer les tooltips
  $('[data-toggle="tooltip"]').tooltip();
  
  // Confirmation avant suppression
  $('.delete-confirm').on('click', function(e) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      e.preventDefault();
    }
  });
});
</script>
</body>
</html>

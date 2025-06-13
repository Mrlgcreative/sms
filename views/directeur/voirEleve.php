<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer l'ID de l'élève depuis l'URL
$eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupération des informations de l'élève
$eleve = null;
if ($eleve_id > 0) {
    $eleve_query = "SELECT e.*, c.nom as classe_nom 
                    FROM eleves e 
                    LEFT JOIN classes c ON e.classe_id = c.id 
                    WHERE e.id = ? AND e.section = 'primaire'";
    
    $stmt = $mysqli->prepare($eleve_query);
    $stmt->bind_param("i", $eleve_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $eleve = $result->fetch_assoc();
    }
    $stmt->close();
}

// Récupération des absences de l'élève
$absences = [];
if ($eleve_id > 0) {
    $absences_query = "SELECT * FROM absences WHERE eleve_id = ? ORDER BY date_absence DESC";
    $stmt = $mysqli->prepare($absences_query);
    $stmt->bind_param("i", $eleve_id);
    $stmt->execute();
    $absences_result = $stmt->get_result();
    
    while ($row = $absences_result->fetch_assoc()) {
        $absences[] = $row;
    }
    $stmt->close();
}

// Récupération des incidents disciplinaires de l'élève
$incidents = [];
if ($eleve_id > 0) {
    $incidents_query = "SELECT * FROM incidents_disciplinaires WHERE eleve_id = ? ORDER BY date_incident DESC";
    $stmt = $mysqli->prepare($incidents_query);
    $stmt->bind_param("i", $eleve_id);
    $stmt->execute();
    $incidents_result = $stmt->get_result();
    
    while ($row = $incidents_result->fetch_assoc()) {
        $incidents[] = $row;
    }
    $stmt->close();
}

// Fermer la connexion
$mysqli->close();

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
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Détails de l'élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
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
        Détails de l'élève
        <small><?php echo $eleve ? htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']) : 'Élève non trouvé'; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">Élèves</a></li>
        <li class="active">Détails de l'élève</li>
      </ol>
    </section>

    <section class="content">
      <?php if (!$eleve): ?>
        <div class="alert alert-danger">
          <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
          L'élève demandé n'existe pas ou n'appartient pas à la section primaire.
        </div>
        <div class="text-center">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> Retour à la liste des élèves
          </a>
        </div>
      <?php else: ?>
        <div class="row">
          <div class="col-md-4">
            <!-- Profil de l'élève -->
            <div class="box box-primary">
              <div class="box-body box-profile">
                <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/student-avatar.png" alt="Photo de l'élève">
                <h3 class="profile-username text-center"><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['prenom']); ?></h3>
                <p class="text-muted text-center">Élève - Section <?php echo ucfirst(htmlspecialchars($eleve['section'])); ?></p>

                <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
                    <b>ID</b> <a class="pull-right"><?php echo $eleve['id']; ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Date de naissance</b> <a class="pull-right"><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Classe</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['classe_nom']); ?></a>
                  </li>
                  <li class="list-group-item">
                    <b>Genre</b> <a class="pull-right"><?php echo $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin'; ?></a>
                  </li>
                </ul>

                <div class="btn-group btn-group-justified">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Modifier
                  </a>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves" class="btn btn-primary">
                    <i class="fa fa-list"></i> Liste
                  </a>
                </div>
              </div>
            </div>

            <!-- Coordonnées -->
            <div class="box box-info">
              <div class="box-header with-border">
                <h3 class="box-title">Coordonnées</h3>
              </div>
              <div class="box-body">
                <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
                <p class="text-muted"><?php echo htmlspecialchars($eleve['adresse'] ?? 'Non renseignée'); ?></p>

                <hr>

                <strong><i class="fa fa-phone margin-r-5"></i> Téléphone</strong>
                <p class="text-muted"><?php echo htmlspecialchars($eleve['telephone'] ?? 'Non renseigné'); ?></p>

                <hr>

                <strong><i class="fa fa-envelope margin-r-5"></i> Email</strong>
                <p class="text-muted"><?php echo htmlspecialchars($eleve['email'] ?? 'Non renseigné'); ?></p>
              </div>
            </div>
          </div>

          <div class="col-md-8">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs">
                <li class="active"><a href="#absences" data-toggle="tab">Absences</a></li>
                <li><a href="#notes" data-toggle="tab">Notes</a></li>
                <li><a href="#discipline" data-toggle="tab">Discipline</a></li>
                <li><a href="#parents" data-toggle="tab">Parents</a></li>
              </ul>
              <div class="tab-content">
                <!-- Onglet Absences -->
                <div class="active tab-pane" id="absences">
                  <?php if (empty($absences)): ?>
                    <div class="alert alert-info">
                      <h4><i class="icon fa fa-info"></i> Information</h4>
                      Aucune absence enregistrée pour cet élève.
                    </div>
                  <?php else: ?>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Motif</th>
                          <th>Justifiée</th>
                          <th>Commentaire</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($absences as $absence): ?>
                          <tr>
                            <td><?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?></td>
                            <td><?php echo htmlspecialchars($absence['motif']); ?></td>
                            <td>
                              <?php if ($absence['justifiee']): ?>
                                <span class="label label-success">Oui</span>
                              <?php else: ?>
                                <span class="label label-danger">Non</span>
                              <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($absence['commentaire'] ?? ''); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php endif; ?>
                  <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterAbsence&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-primary">
                      <i class="fa fa-plus"></i> Ajouter une absence
                    </a>
                  </div>
                </div>

                <!-- Onglet Notes -->
                <div class="tab-pane" id="notes">
                  <?php if (empty($notes)): ?>
                    <div class="alert alert-info">
                      <h4><i class="icon fa fa-info"></i> Information</h4>
                      Aucune note enregistrée pour cet élève.
                    </div>
                  <?php else: ?>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Cours</th>
                          <th>Note</th>
                          <th>Commentaire</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($notes as $note): ?>
                          <tr>
                            <td><?php echo date('d/m/Y', strtotime($note['date_evaluation'])); ?></td>
                            <td><?php echo htmlspecialchars($note['cours_nom']); ?></td>
                            <td><?php echo $note['valeur']; ?>/20</td>
                            <td><?php echo htmlspecialchars($note['commentaire'] ?? ''); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php endif; ?>
                  <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterNote&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-primary">
                      <i class="fa fa-plus"></i> Ajouter une note
                    </a>
                  </div>
                </div>

                <!-- Onglet Discipline -->
                <div class="tab-pane" id="discipline">
                  <?php if (empty($incidents)): ?>
                    <div class="alert alert-info">
                      <h4><i class="icon fa fa-info"></i> Information</h4>
                      Aucun incident disciplinaire enregistré pour cet élève.
                    </div>
                  <?php else: ?>
                    <table class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th>Date</th>
                          <th>Type</th>
                          <th>Description</th>
                          <th>Sanction</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($incidents as $incident): ?>
                          <tr>
                            <td><?php echo date('d/m/Y', strtotime($incident['date_incident'])); ?></td>
                            <td><?php echo htmlspecialchars($incident['type_incident']); ?></td>
                            <td><?php echo htmlspecialchars($incident['description']); ?></td>
                            <td><?php echo htmlspecialchars($incident['sanction'] ?? 'Aucune'); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  <?php endif; ?>
                  <div class="text-center">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterIncident&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-primary">
                      <i class="fa fa-plus"></i> Ajouter un incident
                    </a>
                  </div>
                </div>

                <!-- Onglet Parents -->
                <div class="tab-pane" id="parents">
                  <div class="box-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="box box-solid">
                          <div class="box-header with-border">
                            <h3 class="box-title">Père</h3>
                          </div>
                          <div class="box-body">
                            <p><strong>Nom:</strong> <?php echo htmlspecialchars($eleve['nom_pere'] ?? 'Non renseigné'); ?></p>
                            <p><strong>Profession:</strong> <?php echo htmlspecialchars($eleve['profession_pere'] ?? 'Non renseignée'); ?></p>
                            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($eleve['telephone_pere'] ?? 'Non renseigné'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($eleve['email_pere'] ?? 'Non renseigné'); ?></p>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="box box-solid">
                          <div class="box-header with-border">
                            <h3 class="box-title">Mère</h3>
                          </div>
                          <div class="box-body">
                            <p><strong>Nom:</strong> <?php echo htmlspecialchars($eleve['nom_mere'] ?? 'Non renseigné'); ?></p>
                            <p><strong>Profession:</strong> <?php echo htmlspecialchars($eleve['profession_mere'] ?? 'Non renseignée'); ?></p>
                            <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($eleve['telephone_mere'] ?? 'Non renseigné'); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($eleve['email_mere'] ?? 'Non renseigné'); ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </section>
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
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
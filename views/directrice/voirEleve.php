<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Directrice';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Récupérer l'ID de l'élève depuis l'URL
$eleve_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($eleve_id <= 0) {
    // Rediriger vers la liste des élèves si l'ID est invalide
    header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
    exit;
}

// Récupérer les informations de l'élève
$query = "SELECT e.*, c.nom as classe_nom 
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          WHERE e.id = ? AND e.section = 'maternelle'";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $eleve_id);
$stmt->execute();
$result = $stmt->get_result();
$eleve = $result->fetch_assoc();

// Vérifier si l'élève existe
if (!$eleve) {
    // Rediriger vers la liste des élèves si l'élève n'existe pas
    header('Location: ' . BASE_URL . 'index.php?controller=Directrice&action=eleves');
    exit;
}

// Récupérer les activités de l'élève
$query_activites = "SELECT c.* 
                FROM cours c 
                WHERE c.classe_id = ? 
                ORDER BY c.titre";
$stmt_activites = $mysqli->prepare($query_activites);
$stmt_activites->bind_param("i", $eleve['classe_id']);
$stmt_activites->execute();
$result_activites = $stmt_activites->get_result();
$activites = [];
while ($row = $result_activites->fetch_assoc()) {
    $activites[] = $row;
}

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Élève Maternelle</title>
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
        Profil de l'élève
        <small><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves">Élèves</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/<?php echo $eleve['sexe'] == 'M' ? 'avatar5.png' : 'avatar2.png'; ?>" alt="Photo de l'élève">
              <h3 class="profile-username text-center"><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></h3>
              <p class="text-muted text-center">Élève Maternelle</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Classe</b> <a class="pull-right"><?php echo $eleve['classe_nom']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Matricule</b> <a class="pull-right"><?php echo $eleve['matricule']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right"><?php echo ucfirst($eleve['section']); ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=carteEleve&id=<?php echo $eleve_id; ?>" class="btn btn-success btn-block"><b>Voir la carte d'élève</b></a>
              <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves" class="btn btn-primary btn-block"><b>Retour à la liste</b></a>
            </div>
          </div>

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-calendar margin-r-5"></i> Date de naissance</strong>
              <p class="text-muted"><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></p>
              <hr>

              <strong><i class="fa fa-venus-mars margin-r-5"></i> Sexe</strong>
              <p class="text-muted"><?php echo $eleve['sexe'] == 'M' ? 'Masculin' : 'Féminin'; ?></p>
              <hr>

              <strong><i class="fa fa-map-marker margin-r-5"></i> Adresse</strong>
              <p class="text-muted"><?php echo $eleve['adresse']; ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#info" data-toggle="tab">Informations générales</a></li>
              <li><a href="#activites" data-toggle="tab">Activités</a></li>
              <li><a href="#parents" data-toggle="tab">Parents</a></li>
              <li><a href="#absences" data-toggle="tab">Absences</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="info">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Nom complet:</label>
                        <p><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Date de naissance:</label>
                        <p><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Lieu de naissance:</label>
                        <p><?php echo $eleve['lieu_naissance']; ?></p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Âge:</label>
                        <p><?php 
                          $dob = new DateTime($eleve['date_naissance']);
                          $now = new DateTime();
                          $diff = $now->diff($dob);
                          echo $diff->y . ' ans, ' . $diff->m . ' mois';
                        ?></p>
                      </div>
                    </div>
                  </div>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Adresse:</label>
                        <p><?php echo $eleve['adresse']; ?></p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>Classe:</label>
                        <p><?php echo $eleve['classe_nom']; ?></p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="activites">
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Jour</th>
                        <th>Durée</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($activites as $a): ?>
                      <tr>
                        <td><?php echo $a['titre']; ?></td>
                        <td><?php echo $a['description']; ?></td>
                        <td><?php echo $a['jour']; ?></td>
                        <td><?php echo $a['duree']; ?> minutes</td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if (empty($activites)): ?>
                      <tr>
                        <td colspan="4" class="text-center">Aucune activité disponible pour cette classe</td>
                      </tr>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane" id="parents">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Père</h3>
                        </div>
                        <div class="box-body">
                          <div class="form-group">
                            <label>Nom:</label>
                            <p><?php echo $eleve['nom_pere']; ?></p>
                          </div>
                          <div class="form-group">
                            <label>Profession:</label>
                            <p><?php echo isset($eleve['profession_pere']) ? $eleve['profession_pere'] : 'Non spécifiée'; ?></p>
                          </div>
                          <div class="form-group">
                            <label>Contact:</label>
                            <p><?php echo $eleve['contact_pere']; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Mère</h3>
                        </div>
                        <div class="box-body">
                          <div class="form-group">
                            <label>Nom:</label>
                            <p><?php echo $eleve['nom_mere']; ?></p>
                          </div>
                          <div class="form-group">
                            <label>Profession:</label>
                            <p><?php echo isset($eleve['profession_mere']) ? $eleve['profession_mere'] : 'Non spécifiée'; ?></p>
                          </div>
                          <div class="form-group">
                            <label>Contact:</label>
                            <p><?php echo $eleve['contact_mere']; ?></p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="tab-pane" id="absences">
                <div class="box-body">
                  <?php
                  // Connexion à la base de données pour récupérer les absences
                  $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                  
                  if ($mysqli->connect_error) {
                      die("Connection failed: " . $mysqli->connect_error);
                  }
                  
                  $query_absences = "SELECT * FROM absences_m WHERE eleve_id = ? ORDER BY date_absence DESC";
                  $stmt_absences = $mysqli->prepare($query_absences);
                  $stmt_absences->bind_param("i", $eleve_id);
                  $stmt_absences->execute();
                  $result_absences = $stmt_absences->get_result();
                  $absences = [];
                  while ($row = $result_absences->fetch_assoc()) {
                      $absences[] = $row;
                  }
                  $stmt_absences->close();
                  $mysqli->close();
                  ?>
                  
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Motif</th>
                        <th>Justifiée</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($absences as $absence): ?>
                      <tr>
                        <td><?php echo date('d/m/Y', strtotime($absence['date_absence'])); ?></td>
                        <td><?php echo $absence['motif']; ?></td>
                        <td>
                          <?php if ($absence['justifiee']): ?>
                            <span class="label label-success">Oui</span>
                          <?php else: ?>
                            <span class="label label-danger">Non</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                      <?php if (empty($absences)): ?>
                      <tr>
                        <td colspan="3" class="text-center">Aucune absence enregistrée pour cet élève</td>
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
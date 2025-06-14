<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des élèves de la section maternelle avec information de classe
$query = "SELECT e.*, c.nom as classe_nom 
          FROM eleves e 
          LEFT JOIN classes c ON e.classe_id = c.id
          WHERE e.section = 'maternelle'
          ORDER BY e.nom, e.prenom";
$result = $mysqli->query($query);

$eleves = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eleves[] = $row;
    }
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

// Mode d'affichage (carte ou liste)
$view_mode = isset($_GET['view']) ? $_GET['view'] : 'list';

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Élèves Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .student-card {
      height: 340px;
      margin-bottom: 20px;
      transition: transform 0.3s;
    }
    .student-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
    .student-card .box-body {
      padding-top: 10px;
    }
    .student-card .profile-img {
      width: 100px;
      height: 100px;
      margin: 0 auto 10px;
      display: block;
      border-radius: 50%;
    }
    .student-card h4 {
      text-align: center;
      margin-bottom: 15px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .student-card .info-row {
      margin-bottom: 5px;
    }
    .student-card .info-label {
      font-weight: bold;
    }
    .view-toggle {
      margin-bottom: 15px;
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Élèves de Maternelle
        <small>Liste complète</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="view-toggle text-right">
            <div class="btn-group">
              <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves&view=list" class="btn btn-default <?php echo $view_mode == 'list' ? 'active' : ''; ?>">
                <i class="fa fa-list"></i> Vue Liste
              </a>
              <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=eleves&view=card" class="btn btn-default <?php echo $view_mode == 'card' ? 'active' : ''; ?>">
                <i class="fa fa-th"></i> Vue Carte
              </a>
            </div>
          </div>
        </div>
      </div>
      
      <?php if ($view_mode == 'list'): ?>
      <!-- Vue Liste (tableau existant) -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves de maternelle</h3>
            </div>
            <div class="box-body">
              <table id="elevesList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Date de naissance</th>
                    <th>Sexe</th>
                    <th>Adresse</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($eleves as $eleve): ?>
                    <tr>
                      <td><?php echo $eleve['id']; ?></td>
                      <td><?php echo $eleve['nom']; ?></td>
                      <td><?php echo $eleve['prenom']; ?></td>
                      <td><?php echo $eleve['classe_nom']; ?></td>
                      <td><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></td>
                      <td><?php echo $eleve['sexe']; ?></td>
                      <td><?php echo $eleve['adresse']; ?></td>
                      <td>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=voirEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Voir</a>
                        <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=carteEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-xs"><i class="fa fa-id-card"></i> Carte</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <!-- Vue Carte -->
      <div class="row">
        <?php foreach ($eleves as $eleve): ?>
        <div class="col-md-3 col-sm-6">
          <div class="box box-primary student-card">
            <div class="box-body box-profile">
              <img class="profile-img" src="<?php echo BASE_URL; ?>dist/img/<?php echo $eleve['sexe'] == 'M' ? 'avatar5.png' : 'avatar2.png'; ?>" alt="Photo de l'élève">
              <h4><?php echo $eleve['nom'] . ' ' . $eleve['prenom']; ?></h4>
              
              <div class="info-row">
                <span class="info-label">Classe:</span> 
                <span class="info-value"><?php echo $eleve['classe_nom']; ?></span>
              </div>
              
              <div class="info-row">
                <span class="info-label">Matricule:</span> 
                <span class="info-value"><?php echo $eleve['matricule']; ?></span>
              </div>
              
              <div class="info-row">
                <span class="info-label">Naissance:</span> 
                <span class="info-value"><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></span>
              </div>
              
              <div class="text-center" style="margin-top: 10px;">
                <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=carteEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-sm">
                  <i class="fa fa-id-card"></i> Carte
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
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
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    $('#elevesList').DataTable({
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
  });
</script>
</body>
</html>
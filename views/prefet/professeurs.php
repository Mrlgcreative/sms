<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des professeurs
$query = "SELECT p.*, GROUP_CONCAT(c.titre SEPARATOR ', ') as cours_enseignes 
          FROM professeurs p 
          LEFT JOIN cours c ON FIND_IN_SET(c.id, p.cours_id)
          WHERE p.section = 'Secondaire' OR p.section = 'Tous'
          GROUP BY p.id
          ORDER BY p.nom, p.prenom";
$result = $mysqli->query($query);

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Préfet';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Professeurs</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
 <?php  include 'navbar.php'; ?>
 <?php include 'sidebar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Professeurs
        <small>Liste complète</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Prefet&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Professeurs</li>
      </ol>
    </section>

    <section class="content">
      <!-- Informations générales -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-solid bg-teal-gradient">
            <div class="box-header">
              <i class="fa fa-info-circle"></i>
              <h3 class="box-title">Informations générales sur les professeurs</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-aqua">
                    <div class="inner">
                      <h3><?php echo $result->num_rows; ?></h3>
                      <p>Total professeurs</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-graduation-cap"></i>
                    </div>
                  </div>
                </div>
                
                <?php
                // Récupération des statistiques
                $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                
                // Nombre de cours enseignés dans la section secondaire
                $cours_query = "SELECT COUNT(DISTINCT id) as total FROM cours WHERE section = 'Secondaire'";
                $cours_result = $mysqli->query($cours_query);
                $total_cours = $cours_result->fetch_assoc()['total'];
                
                // Nombre de professeurs titulaires de la section secondaire
                $titulaires_query = "SELECT COUNT(DISTINCT p.id) as total FROM professeurs p 
                                    JOIN classes c ON c.prof_id = p.id
                                    WHERE c.section = 'Secondaire'";
                $titulaires_result = $mysqli->query($titulaires_query);
                $total_titulaires = $titulaires_result->fetch_assoc()['total'];
                
                // Moyenne de cours par professeur de la section secondaire
                $avg_query = "SELECT AVG(cours_count) as moyenne FROM (
                              SELECT COUNT(c.id) as cours_count 
                              FROM professeurs p 
                              LEFT JOIN cours c ON FIND_IN_SET(c.id, p.cours_id)
                              WHERE (p.section = 'Secondaire' OR p.section = 'Tous')
                              AND (c.section = 'Secondaire' OR c.section IS NULL)
                              GROUP BY p.id) as counts";
                $avg_result = $mysqli->query($avg_query);
                $avg_cours = $avg_result->fetch_assoc()['moyenne'];
                
                $mysqli->close();
                ?>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-green">
                    <div class="inner">
                      <h3><?php echo $total_cours; ?></h3>
                      <p>Cours enseignés</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-book"></i>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-yellow">
                    <div class="inner">
                      <h3><?php echo $total_titulaires; ?></h3>
                      <p>Professeurs titulaires</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-users"></i>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <div class="small-box bg-red">
                    <div class="inner">
                      <h3><?php echo round($avg_cours, 1); ?></h3>
                      <p>Moyenne cours/prof</p>
                    </div>
                    <div class="icon">
                      <i class="fa fa-bar-chart"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des professeurs</h3>
            </div>
            <div class="box-body">
              <table id="professeursList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                
                    <th>Cours enseignés</th>
                   
                    <th>Email</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                      echo "<tr>
                              <td>{$row['id']}</td>
                              <td>{$row['nom']}</td>
                              <td>{$row['prenom']}</td>
                         
                              <td>{$row['cours_enseignes']}</td>
                             
                              <td>{$row['email']}</td>
                              <td>
                                <a href='" . BASE_URL . "index.php?controller=Prefet&action=voirProfesseur&id={$row['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> Voir</a>
                                <a href='" . BASE_URL . "index.php?controller=Prefet&action=presenceProfesseur&id={$row['id']}' class='btn btn-success btn-xs'><i class='fa fa-check-circle'></i> Présence</a>
                              </td>
                            </tr>";
                    }
                  }
                  ?>
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
    $('#professeursList').DataTable({
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
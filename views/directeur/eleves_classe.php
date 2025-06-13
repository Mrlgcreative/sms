<?php
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

// Vérifier si l'ID de classe est défini
if (!isset($classe_id) || empty($classe_id)) {
    header('Location: ' . BASE_URL . 'index.php?controller=Director&action=classes');
    exit;
}

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupérer les informations de la classe
$classe_query = "SELECT c.*, p.nom as prof_nom, p.prenom as prof_prenom 
                FROM classes c 
                LEFT JOIN professeurs p ON c.titulaire = p.id
                WHERE c.id = ?";
                
$stmt = $mysqli->prepare($classe_query);
$stmt->bind_param("i", $classe_id);
$stmt->execute();
$classe_result = $stmt->get_result();
$classe = $classe_result->fetch_assoc();

if (!$classe) {
    header('Location: ' . BASE_URL . 'index.php?controller=Director&action=classes');
    exit;
}

// Récupérer les élèves de cette classe
$eleves_query = "SELECT * FROM eleves WHERE classe_id = ? ORDER BY nom, prenom";
$stmt = $mysqli->prepare($eleves_query);
$stmt->bind_param("i", $classe_id);
$stmt->execute();
$eleves_result = $stmt->get_result();

$eleves = [];
while ($row = $eleves_result->fetch_assoc()) {
    $eleves[] = $row;
}

// Statistiques de la classe
$stats_query = "SELECT 
                COUNT(*) as total_eleves,
                SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as nb_garcons,
                SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as nb_filles,
                AVG(CASE WHEN date_naissance IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_naissance, CURDATE()) ELSE NULL END) as age_moyen
                FROM eleves 
                WHERE classe_id = ?";
                
$stmt = $mysqli->prepare($stats_query);
$stmt->bind_param("i", $classe_id);
$stmt->execute();
$stats_result = $stmt->get_result();
$stats = $stats_result->fetch_assoc();

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Élèves de la classe <?php echo htmlspecialchars($classe['nom']); ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

 <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Classe: <?php echo htmlspecialchars($classe['nom']); ?>
        <small>Liste des élèves</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=classes">Classes</a></li>
        <li class="active"><?php echo htmlspecialchars($classe['nom']); ?></li>
      </ol>
    </section>

    <section class="content">
      <!-- Informations sur la classe -->
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations sur la classe</h3>
            </div>
            <div class="box-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-info-circle"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Niveau</span>
                      <span class="info-box-number"><?php echo htmlspecialchars($classe['niveau']); ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-6">
                  <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-graduation-cap"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Professeur titulaire</span>
                      <span class="info-box-number">
                        <?php 
                          if (!empty($classe['prof_nom']) && !empty($classe['prof_prenom'])) {
                            echo htmlspecialchars($classe['prof_prenom'] . ' ' . $classe['prof_nom']);
                          } else {
                            echo '<span class="text-muted">Non assigné</span>';
                          }
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-4">
                  <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Total des élèves</span>
                      <span class="info-box-number"><?php echo $stats['total_eleves']; ?></span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-male"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Garçons</span>
                      <span class="info-box-number"><?php echo $stats['nb_garcons']; ?> (<?php echo ($stats['total_eleves'] > 0) ? round(($stats['nb_garcons'] / $stats['total_eleves']) * 100) : 0; ?>%)</span>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4">
                  <div class="info-box">
                    <span class="info-box-icon bg-pink"><i class="fa fa-female"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Filles</span>
                      <span class="info-box-number"><?php echo $stats['nb_filles']; ?> (<?php echo ($stats['total_eleves'] > 0) ? round(($stats['nb_filles'] / $stats['total_eleves']) * 100) : 0; ?>%)</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-12">
                  <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-birthday-cake"></i></span>
                    <div class="info-box-content">
                      <span class="info-box-text">Âge moyen des élèves</span>
                      <span class="info-box-number"><?php echo round($stats['age_moyen'], 1); ?> ans</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Liste des élèves -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves de la classe <?php echo htmlspecialchars($classe['nom']); ?></h3>
            </div>
            <div class="box-body">
              <table id="eleves-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Sexe</th>
                    <th>Date de naissance</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($eleves)): ?>
                    <?php foreach ($eleves as $eleve): ?>
                      <tr>
                        <td><?php echo $eleve['id']; ?></td>
                        <td><?php echo htmlspecialchars($eleve['nom']); ?></td>
                        <td><?php echo htmlspecialchars($eleve['prenom']); ?></td>
                        <td><?php echo $eleve['sexe'] === 'M' ? 'Masculin' : 'Féminin'; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($eleve['date_naissance'])); ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=voirEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-sm">
                              <i class="fa fa-eye"></i> Voir
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="6" class="text-center">Aucun élève trouvé dans cette classe</td>
                    </tr>
                  <?php endif; ?>
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
  $('#eleves-table').DataTable({
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
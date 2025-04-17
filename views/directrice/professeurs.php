<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Récupération des professeurs qui enseignent dans la section maternelle
$query = "SELECT p.id, p.nom, p.prenom, p.email, cl.nom as classe,
          GROUP_CONCAT(DISTINCT c.titre SEPARATOR ', ') as cours_enseignes,
          IFNULL(COUNT(DISTINCT c.id), 0) as total_cours 
          FROM professeurs p 
          LEFT JOIN classes cl ON p.classe_id = cl.id
          LEFT JOIN cours c ON p.cours_id = c.id 
          WHERE p.section='maternelle'
          GROUP BY p.id 
          ORDER BY p.nom, p.prenom";
$result = $mysqli->query($query);

// Stocker les données pour le graphique avant de fermer la connexion
$chartData = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chartData[] = [
            'nom' => $row['nom'] . ' ' . $row['prenom'],
            'total_cours' => (int)$row['total_cours']
        ];
    }
    // Réinitialiser le pointeur de résultat pour l'affichage du tableau
    $result->data_seek(0);
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

// Fermer la connexion
$mysqli->close();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Professeurs Maternelle</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
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
        
        <li class="active">
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=cours">
            <i class="fa fa-book"></i> <span>Cours</span>
          </a>
        </li>
        
        <li>
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
        Professeurs
        <small>Section Maternelle</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Directrice&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Professeurs</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des professeurs de la section maternelle</h3>
              <div class="box-tools">
                
              </div>
            </div>
            <div class="box-body">
              <table id="professeursList" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
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
                              <td>{$row['classe']}</td>
                              <td>{$row['email']}</td>
                              <td>
                                <div class='btn-group'>
                                  <a href='" . BASE_URL . "index.php?controller=Directrice&action=voirProfesseur&id={$row['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> Voir</a>
                                </div>
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
      
      <!-- Statistiques des professeurs -->
      <div class="row">
        <div class="col-md-6">
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Répartition des cours par professeur</h3>
            </div>
            <div class="box-body">
              <canvas id="coursParProfesseurChart" style="height:250px"></canvas>
            </div>
          </div>
        </div>
        
        <div class="col-md-6">
          <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title">Informations générales</h3>
            </div>
            <div class="box-body">
              <div class="info-box bg-purple">
                <span class="info-box-icon"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Total des professeurs</span>
                  <span class="info-box-number">
                    <?php echo $result ? $result->num_rows : 0; ?>
                  </span>
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
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/chart.js/Chart.js"></script>
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
    
    // Données pour le graphique
    var profData = [];
    var profLabels = [];
    var backgroundColors = [
      'rgba(156, 39, 176, 0.8)',
      'rgba(233, 30, 99, 0.8)',
      'rgba(103, 58, 183, 0.8)',
      'rgba(63, 81, 181, 0.8)',
      'rgba(33, 150, 243, 0.8)',
      'rgba(0, 188, 212, 0.8)',
      'rgba(0, 150, 136, 0.8)',
      'rgba(76, 175, 80, 0.8)',
      'rgba(139, 195, 74, 0.8)',
      'rgba(205, 220, 57, 0.8)'
    ];
    
    <?php
    // Debug - print chart data to console
    echo "console.log('Chart Data:', " . json_encode($chartData) . ");\n";
    
    foreach ($chartData as $data) {
        echo "profLabels.push('" . addslashes($data['nom']) . "');\n";
        echo "profData.push(" . $data['total_cours'] . ");\n";
    }
    ?>
    
    // Debug - print arrays to console
    console.log('Labels:', profLabels);
    console.log('Data:', profData);
    
    // Only create chart if we have data
    if (profData.length > 0 && profData.some(value => value > 0)) {
        // Créer le graphique
        var ctx = document.getElementById('coursParProfesseurChart').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'pie',
          data: {
            labels: profLabels,
            datasets: [{
              data: profData,
              backgroundColor: backgroundColors,
              borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            legend: {
              position: 'right',
            }
          }
        });
    } else {
        // Display a message if no data
        document.getElementById('coursParProfesseurChart').parentNode.innerHTML = 
            '<div class="alert alert-info">Aucune donnée de cours disponible pour les professeurs de maternelle. ' +
            'Veuillez assigner des cours aux professeurs pour voir la répartition.</div>';
    }
  });
</script>
</body>
</html>
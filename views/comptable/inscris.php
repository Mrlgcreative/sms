<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer les sessions scolaires pour le dropdown
$sessions_scolaires_for_dropdown = [];
$sessions_query_result = $mysqli->query("SELECT id, libelle FROM sessions_scolaires ORDER BY libelle DESC");
if ($sessions_query_result) {
    while ($session_data = $sessions_query_result->fetch_assoc()) {
        $sessions_scolaires_for_dropdown[] = $session_data;
    }
}

// Déterminer la session affichée pour le titre et le total
$current_session_filter_id = isset($_GET['session_id']) && !empty($_GET['session_id']) ? (int)$_GET['session_id'] : null;
$displayed_session_name = "Toutes les sessions";
$eleves_total_for_display = 0; // Sera calculé ci-dessous

// Initialiser la liste des élèves
$eleves = [];

// Préparer la requête de base pour récupérer les élèves avec les informations jointes
$sql_eleves_base = "SELECT e.id, e.matricule, e.nom, e.post_nom, e.prenom, e.date_naissance, e.lieu_naissance, 
                          e.section AS section, o.nom AS option_nom, c.niveau AS classe_nom, 
                          e.adresse, e.photo 
                   FROM eleves e
                 
                   LEFT JOIN options o ON e.option_id = o.id
                   LEFT JOIN classes c ON e.classe_id = c.id";

if ($current_session_filter_id) {
    $found_sess_name = false;
    foreach ($sessions_scolaires_for_dropdown as $sess_item) {
        if ($sess_item['id'] == $current_session_filter_id) {
            $displayed_session_name = htmlspecialchars($sess_item['libelle']);
            $found_sess_name = true;
            break;
        }
    }
    // Calculer le total pour la session sélectionnée
    $stmt_count_filtered = $mysqli->prepare("SELECT COUNT(*) AS total FROM eleves WHERE session_scolaire_id = ?");
    if ($stmt_count_filtered) {
        $stmt_count_filtered->bind_param("i", $current_session_filter_id);
        $stmt_count_filtered->execute();
        $result_filtered_count_get = $stmt_count_filtered->get_result();
        if($result_filtered_count_get) {
            $result_filtered_count = $result_filtered_count_get->fetch_assoc();
            $eleves_total_for_display = $result_filtered_count['total'];
        }
        $stmt_count_filtered->close();
    }

    // Récupérer les élèves pour la session sélectionnée
    $sql_eleves_session = $sql_eleves_base . " WHERE e.session_scolaire_id = ? ORDER BY e.nom, e.post_nom, e.prenom";
    $stmt_eleves = $mysqli->prepare($sql_eleves_session);
    if ($stmt_eleves) {
        $stmt_eleves->bind_param("i", $current_session_filter_id);
        $stmt_eleves->execute();
        $result_eleves = $stmt_eleves->get_result();
        if ($result_eleves) {
            while ($row = $result_eleves->fetch_assoc()) {
                $eleves[] = $row;
            }
        }
        $stmt_eleves->close();
    }

} else {
    // Si aucune session n'est sélectionnée, afficher le grand total
    $result_grand_total_else = $mysqli->query("SELECT COUNT(*) AS total_eleves FROM eleves");
    if($result_grand_total_else){
        $row_grand_total_else = $result_grand_total_else->fetch_assoc();
        $eleves_total_for_display = $row_grand_total_else['total_eleves'];
    }

    // Récupérer tous les élèves
    $sql_tous_les_eleves = $sql_eleves_base . " ORDER BY e.nom, e.post_nom, e.prenom";
    $result_all_eleves = $mysqli->query($sql_tous_les_eleves);
    if ($result_all_eleves) {
        while ($row = $result_all_eleves->fetch_assoc()) {
            $eleves[] = $row;
        }
    }
}

// Fermer la connexion à la base de données
$mysqli->close();

// Vérifier si une session PHP est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session (user info)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// $current_session (variable originale) n'est pas utilisée ici pour le filtrage.
// La variable $eleves est supposée être passée par le contrôleur et déjà filtrée
// en fonction de $_GET['session_id'] si celui-ci est présent.
// La variable $total_eleves (originale) est remplacée par $eleves_total_for_display pour le titre.
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Liste des élèves</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    @media print {
      .no-print, .no-print * {
        display: none !important;
      }
      .content-wrapper, .main-footer {
        margin-left: 0 !important;
      }
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
        Liste des élèves
        <small>Tous les élèves inscrits</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Élèves</li>
      </ol>
    </section>

    <section class="content">
      <div class="row no-print" style="margin-bottom: 15px;">
        <div class="col-md-6">
          <form method="GET" action="<?php echo BASE_URL; ?>index.php" id="sessionFilterForm">
            <input type="hidden" name="controller" value="comptable">
            <input type="hidden" name="action" value="inscris">
            <div class="form-group">
              <label for="session_id_filter">Filtrer par session scolaire:</label>
              <select name="session_id" id="session_id_filter" class="form-control" onchange="document.getElementById('sessionFilterForm').submit();">
                <option value="">Toutes les sessions</option>
                <?php if (!empty($sessions_scolaires_for_dropdown)): ?>
                  <?php foreach ($sessions_scolaires_for_dropdown as $session_opt) : ?>
                    <option value="<?php echo $session_opt['id']; ?>" <?php if ($current_session_filter_id == $session_opt['id']) echo 'selected'; ?>>
                      <?php echo htmlspecialchars($session_opt['libelle']); ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des élèves (Session: <?php echo $displayed_session_name; ?>, Total: <?php echo $eleves_total_for_display; ?>)</h3>
              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                  <input type="text" id="searchInput" class="form-control pull-right" placeholder="Rechercher...">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="box-body">
              <div class="row no-print" style="margin-bottom: 15px;">
                <div class="col-xs-12">
                  <button type="button" class="btn btn-success" onclick="window.print()"><i class="fa fa-print"></i> Imprimer</button>
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=exportEleves" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Exporter Excel</a>
                </div>
              </div>
              
              <div class="table-responsive">
                <table id="eleveTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Photo</th>
                      <th>Matricule</th>
                      <th>Nom</th>
                      <th>Post-nom</th>
                      <th>Prénom</th>
                      <th>Date de naissance</th>
                      <th>Lieu de naissance</th>
                      <th>Section</th>
                      <th>Option</th>
                      <th>Classe</th>
                      <th>Adresse</th>
                      <th class="no-print">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($eleves as $eleve) : ?>
                      <tr>
                        <td><?php echo $eleve['id']; ?></td>
                        <td><img src="<?php echo !empty($eleve['photo']) ? $eleve['photo'] : 'dist/img/default-student.png'; ?>" alt="Photo élève" class="img-circle" style="width: 50px; height: 50px;"></td>
                        <td><?php echo !empty($eleve['matricule']) ? $eleve['matricule'] : 'Non défini'; ?></td>
                        <td><?php echo $eleve['nom']; ?></td>
                        <td><?php echo $eleve['post_nom']; ?></td>
                        <td><?php echo $eleve['prenom']; ?></td>
                        <td><?php echo $eleve['date_naissance']; ?></td>
                        <td><?php echo $eleve['lieu_naissance']; ?></td>
                        <td><?php echo $eleve['section']; ?></td>
                        <td><?php echo $eleve['option_nom']; ?></td>
                        <td><?php echo $eleve['classe_nom']; ?></td>
                        <td><?php echo $eleve['adresse']; ?></td>
                        <td class="no-print">
                          <a href="<?php echo BASE_URL; ?>index.php?controller=Comptable&action=viewStudent&id=<?php echo $eleve['id']; ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Détails</a>
                          
                        </td>
                      </tr>
                    <?php endforeach; ?>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // Initialiser DataTables
    $('#eleveTable').DataTable({
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
    
    // Fonction de recherche dynamique
    $("#searchInput").on("keyup", function() {
      var value = $(this).val().toLowerCase();
      $("#eleveTable tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });
    
    // Vérifier les paramètres d'URL au chargement de la page
    var urlParams = new URLSearchParams(window.location.search);
    var success = urlParams.get('success');
    var error = urlParams.get('error');
    var message = urlParams.get('message');
    
    if (success) {
      showAlert(decodeURIComponent(message || 'Opération réussie!'), 'success');
    } else if (error) {
      showAlert(decodeURIComponent(message || 'Une erreur est survenue!'), 'danger');
    }
  });
  
  // Fonction pour afficher une alerte stylisée
  function showAlert(message, type) {
    // Créer l'élément d'alerte
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    
    // Ajouter le contenu de l'alerte
    alertDiv.innerHTML = 
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' + 
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message;
    
    // Ajouter l'alerte au document
    document.body.appendChild(alertDiv);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(function() {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }
</script>
</body>
</html>
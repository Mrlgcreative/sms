<?php
session_start();
require_once '../../config/database.php';
require_once '../../controllers/DirecteurEtude.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directeur_etudes') {
    header('Location: ../../login.php');
    exit();
}

$directeur = new DirecteurEtude();
$classe_id = isset($_GET['classe_id']) ? (int)$_GET['classe_id'] : 0;

if (!$classe_id) {
    header('Location: classes.php');
    exit();
}

// Récupérer les informations de la classe
$stmt = $pdo->prepare("SELECT c.*, n.nom as niveau_nom, s.nom as section_nom 
                      FROM classes c 
                      JOIN niveaux n ON c.niveau_id = n.id 
                      JOIN sections s ON c.section_id = s.id 
                      WHERE c.id = ?");
$stmt->execute([$classe_id]);
$classe = $stmt->fetch();

if (!$classe) {
    header('Location: classes.php');
    exit();
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter_eleve':
                $eleve_id = (int)$_POST['eleve_id'];
                
                // Vérifier si l'élève n'est pas déjà dans une classe
                $stmt = $pdo->prepare("SELECT classe_id FROM eleves WHERE id = ?");
                $stmt->execute([$eleve_id]);
                $eleve_actuel = $stmt->fetch();
                
                if (!$eleve_actuel['classe_id']) {
                    $stmt = $pdo->prepare("UPDATE eleves SET classe_id = ? WHERE id = ?");
                    if ($stmt->execute([$classe_id, $eleve_id])) {
                        $success = "Élève ajouté à la classe avec succès.";
                    } else {
                        $error = "Erreur lors de l'ajout de l'élève.";
                    }
                } else {
                    $error = "Cet élève est déjà dans une classe.";
                }
                break;
                
            case 'retirer_eleve':
                $eleve_id = (int)$_POST['eleve_id'];
                $stmt = $pdo->prepare("UPDATE eleves SET classe_id = NULL WHERE id = ? AND classe_id = ?");
                if ($stmt->execute([$eleve_id, $classe_id])) {
                    $success = "Élève retiré de la classe avec succès.";
                } else {
                    $error = "Erreur lors du retrait de l'élève.";
                }
                break;
                
            case 'transferer_eleve':
                $eleve_id = (int)$_POST['eleve_id'];
                $nouvelle_classe_id = (int)$_POST['nouvelle_classe_id'];
                
                $stmt = $pdo->prepare("UPDATE eleves SET classe_id = ? WHERE id = ? AND classe_id = ?");
                if ($stmt->execute([$nouvelle_classe_id, $eleve_id, $classe_id])) {
                    $success = "Élève transféré avec succès.";
                } else {
                    $error = "Erreur lors du transfert de l'élève.";
                }
                break;
        }
    }
}

// Statistiques de la classe
$stmt = $pdo->prepare("SELECT COUNT(*) as total_eleves FROM eleves WHERE classe_id = ?");
$stmt->execute([$classe_id]);
$stats_eleves = $stmt->fetch();

// Élèves de la classe
$stmt = $pdo->prepare("SELECT e.*, u.prenom, u.nom, u.email, u.telephone 
                      FROM eleves e 
                      JOIN users u ON e.user_id = u.id 
                      WHERE e.classe_id = ? 
                      ORDER BY u.nom, u.prenom");
$stmt->execute([$classe_id]);
$eleves = $stmt->fetchAll();

// Élèves sans classe (pour l'ajout)
$stmt = $pdo->prepare("SELECT e.*, u.prenom, u.nom 
                      FROM eleves e 
                      JOIN users u ON e.user_id = u.id 
                      WHERE e.classe_id IS NULL AND e.statut = 'active'
                      ORDER BY u.nom, u.prenom");
$stmt->execute();
$eleves_sans_classe = $stmt->fetchAll();

// Autres classes pour le transfert
$stmt = $pdo->prepare("SELECT id, nom FROM classes WHERE id != ? AND statut = 'active' ORDER BY nom");
$stmt->execute([$classe_id]);
$autres_classes = $stmt->fetchAll();

// Moyenne de la classe par matière
$stmt = $pdo->prepare("SELECT m.nom as matiere, AVG(n.note) as moyenne 
                      FROM notes n 
                      JOIN examens ex ON n.examen_id = ex.id 
                      JOIN cours c ON ex.cours_id = c.id 
                      JOIN matieres m ON c.matiere_id = m.id 
                      JOIN cours_classes cc ON c.id = cc.cours_id 
                      WHERE cc.classe_id = ? 
                      GROUP BY m.id, m.nom 
                      ORDER BY m.nom");
$stmt->execute([$classe_id]);
$moyennes_matieres = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion Élèves - <?= htmlspecialchars($classe['nom']) ?> | SMS</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="../../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="../../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="../../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="accueil.php" class="brand-link">
            <i class="fas fa-graduation-cap brand-image img-circle elevation-3"></i>
            <span class="brand-text font-weight-light">SMS - Directeur</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="accueil.php" class="nav-link">
                            <i class="nav-icon fa fa-dashboard"></i>
                            <p>Tableau de bord</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="eleves.php" class="nav-link active">
                            <i class="nav-icon fa fa-graduation-cap"></i>
                            <p>Gestion des Élèves</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="professeurs.php" class="nav-link">
                            <i class="nav-icon fa fa-users"></i>
                            <p>Gestion des Professeurs</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="programmesScolaires.php" class="nav-link">
                            <i class="nav-icon fa fa-book"></i>
                            <p>Programmes Scolaires</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="classes.php" class="nav-link">
                            <i class="nav-icon fa fa-university"></i>
                            <p>Gestion des Classes</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="cours.php" class="nav-link">
                            <i class="nav-icon fa fa-calendar"></i>
                            <p>Gestion des Cours</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="examens.php" class="nav-link">
                            <i class="nav-icon fa fa-edit"></i>
                            <p>Gestion des Examens</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="resultatsScolaires.php" class="nav-link">
                            <i class="nav-icon fa fa-bar-chart"></i>
                            <p>Résultats Scolaires</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="emploiDuTemps.php" class="nav-link">
                            <i class="nav-icon fa fa-table"></i>
                            <p>Emplois du temps</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="evenementsScolaires.php" class="nav-link">
                            <i class="nav-icon fa fa-calendar-check-o"></i>
                            <p>Événements Scolaires</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="rapports.php" class="nav-link">
                            <i class="nav-icon fa fa-pie-chart"></i>
                            <p>Rapports Globaux</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="communications.php" class="nav-link">
                            <i class="nav-icon fa fa-envelope"></i>
                            <p>Communications</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="profil.php" class="nav-link">
                            <i class="nav-icon fa fa-user"></i>
                            <p>Mon Profil</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Élèves - <?= htmlspecialchars($classe['nom']) ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="accueil.php">Accueil</a></li>
                            <li class="breadcrumb-item"><a href="classes.php">Classes</a></li>
                            <li class="breadcrumb-item"><a href="voirClasse.php?id=<?= $classe_id ?>">
                                <?= htmlspecialchars($classe['nom']) ?>
                            </a></li>
                            <li class="breadcrumb-item active">Élèves</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $success ?>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $error ?>
                </div>
                <?php endif; ?>

                <!-- Info boxes -->
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Élèves</span>
                                <span class="info-box-number"><?= $stats_eleves['total_eleves'] ?></span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?= ($stats_eleves['total_eleves'] / $classe['capacite_max']) * 100 ?>%"></div>
                                </div>
                                <span class="progress-description">
                                    Capacité: <?= $classe['capacite_max'] ?> élèves
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-male"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Garçons</span>
                                <?php 
                                $garcons = array_filter($eleves, function($e) { return $e['sexe'] == 'M'; });
                                ?>
                                <span class="info-box-number"><?= count($garcons) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-female"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Filles</span>
                                <?php 
                                $filles = array_filter($eleves, function($e) { return $e['sexe'] == 'F'; });
                                ?>
                                <span class="info-box-number"><?= count($filles) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Places libres</span>
                                <span class="info-box-number"><?= $classe['capacite_max'] - $stats_eleves['total_eleves'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Actions Rapides</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#ajouterEleveModal">
                                    <i class="fas fa-plus"></i> Ajouter un Élève
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-info btn-block" onclick="exporterListe()">
                                    <i class="fas fa-download"></i> Exporter la Liste
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-success btn-block" onclick="genererCartes()">
                                    <i class="fas fa-id-card"></i> Générer Cartes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des élèves -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Liste des Élèves de la Classe</h3>
                    </div>
                    <div class="card-body">
                        <table id="elevesTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>Matricule</th>
                                    <th>Nom complet</th>
                                    <th>Date de naissance</th>
                                    <th>Sexe</th>
                                    <th>Contact</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($eleves as $eleve): ?>
                                <tr>
                                    <td class="text-center">
                                        <img src="../../uploads/photos/<?= $eleve['photo'] ? $eleve['photo'] : 'default.jpg' ?>" 
                                             alt="Photo" class="img-circle" style="width: 40px; height: 40px;">
                                    </td>
                                    <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                                    <td><strong><?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></strong></td>
                                    <td><?= date('d/m/Y', strtotime($eleve['date_naissance'])) ?></td>
                                    <td>
                                        <i class="fas fa-<?= $eleve['sexe'] == 'M' ? 'mars' : 'venus' ?> text-<?= $eleve['sexe'] == 'M' ? 'primary' : 'pink' ?>"></i>
                                        <?= $eleve['sexe'] == 'M' ? 'M' : 'F' ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?= htmlspecialchars($eleve['telephone']) ?><br>
                                            <?= htmlspecialchars($eleve['email']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $eleve['statut'] == 'active' ? 'success' : 'danger' ?>">
                                            <?= $eleve['statut'] == 'active' ? 'Actif' : 'Inactif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="view_student.php?id=<?= $eleve['id'] ?>" class="btn btn-info btn-sm" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="carte_eleve.php?id=<?= $eleve['id'] ?>" class="btn btn-secondary btn-sm" title="Carte élève">
                                                <i class="fas fa-id-card"></i>
                                            </a>
                                            <button class="btn btn-warning btn-sm" onclick="transfererEleve(<?= $eleve['id'] ?>, '<?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>')" title="Transférer">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" onclick="retirerEleve(<?= $eleve['id'] ?>, '<?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?>')" title="Retirer">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Moyennes par matière -->
                <?php if (!empty($moyennes_matieres)): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Moyennes de la Classe par Matière</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($moyennes_matieres as $moyenne): ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-<?= $moyenne['moyenne'] >= 10 ? 'success' : 'danger' ?>">
                                        <i class="fas fa-book"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text"><?= htmlspecialchars($moyenne['matiere']) ?></span>
                                        <span class="info-box-number"><?= round($moyenne['moyenne'], 2) ?>/20</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2024 SMS.</strong> Tous droits réservés.
    </footer>
</div>

<!-- Modal Ajouter Élève -->
<div class="modal fade" id="ajouterEleveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ajouter un Élève à la Classe</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="ajouter_eleve">
                    <div class="form-group">
                        <label for="eleve_id">Sélectionner un élève:</label>
                        <select class="form-control select2" name="eleve_id" required>
                            <option value="">-- Choisir un élève --</option>
                            <?php foreach ($eleves_sans_classe as $eleve): ?>
                            <option value="<?= $eleve['id'] ?>">
                                <?= htmlspecialchars($eleve['matricule'] . ' - ' . $eleve['prenom'] . ' ' . $eleve['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (empty($eleves_sans_classe)): ?>
                    <div class="alert alert-info">
                        Aucun élève sans classe disponible.
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" <?= empty($eleves_sans_classe) ? 'disabled' : '' ?>>
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Transférer Élève -->
<div class="modal fade" id="transfererEleveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Transférer l'Élève</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="transferer_eleve">
                    <input type="hidden" name="eleve_id" id="transferEleve_id">
                    <p>Transférer <strong id="transferEleveNom"></strong> vers:</p>
                    <div class="form-group">
                        <label for="nouvelle_classe_id">Nouvelle classe:</label>
                        <select class="form-control" name="nouvelle_classe_id" required>
                            <option value="">-- Choisir une classe --</option>
                            <?php foreach ($autres_classes as $classe_opt): ?>
                            <option value="<?= $classe_opt['id'] ?>">
                                <?= htmlspecialchars($classe_opt['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Transférer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Select2 -->
<script src="../../plugins/select2/js/select2.full.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

<script>
$(function () {
    $("#elevesTable").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        }
    });
    
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});

function transfererEleve(eleveId, eleveNom) {
    $('#transferEleve_id').val(eleveId);
    $('#transferEleveNom').text(eleveNom);
    $('#transfererEleveModal').modal('show');
}

function retirerEleve(eleveId, eleveNom) {
    if (confirm('Êtes-vous sûr de vouloir retirer ' + eleveNom + ' de cette classe ?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = '<input type="hidden" name="action" value="retirer_eleve">' +
                        '<input type="hidden" name="eleve_id" value="' + eleveId + '">';
        document.body.appendChild(form);
        form.submit();
    }
}

function exporterListe() {
    window.open('../../exports/liste_eleves_classe.php?classe_id=<?= $classe_id ?>', '_blank');
}

function genererCartes() {
    if (confirm('Générer les cartes pour tous les élèves de la classe ?')) {
        window.open('../../exports/cartes_classe.php?classe_id=<?= $classe_id ?>', '_blank');
    }
}
</script>

</body>
</html>

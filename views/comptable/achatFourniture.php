<?php
// Vue pour la gestion des achats de fournitures - Comptable
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin racine du projet
$root_path = dirname(dirname(__DIR__));

// Inclusion des fichiers de configuration
require_once $root_path . '/config/config.php';
require_once $root_path . '/config/database.php';

// Vérification de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comptable') {
    header('Location: ' . BASE_URL . 'login.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Comptable';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'assets/img/default-user.png';

// Connexion à la base de données
try {
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $mysqli->set_charset("utf8");
    
    if ($mysqli->connect_error) {
        throw new Exception("Erreur de connexion : " . $mysqli->connect_error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Erreur de connexion à la base de données.");
}

// Instancier le modèle
require_once $root_path . '/models/AchatFourniture.php';
$achatModel = new AchatFourniture($mysqli);

// Traitement des actions POST
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'ajouter':
                    $date = $_POST['date_achat'];
                    $fournisseur = trim($_POST['fournisseur']);
                    $description = trim($_POST['description']);
                    $quantite = (int)$_POST['quantite'];
                    $montant = (float)$_POST['montant'];
                    $factureRef = trim($_POST['facture_ref']);
                    
                    if ($achatModel->ajouterAchat($date, $fournisseur, $description, $quantite, $montant, $factureRef)) {
                        $message = "Achat ajouté avec succès !";
                        $messageType = "success";
                    } else {
                        $message = "Erreur lors de l'ajout de l'achat.";
                        $messageType = "error";
                    }
                    break;
                    
                case 'modifier':
                    $id = (int)$_POST['id'];
                    $date = $_POST['date_achat'];
                    $fournisseur = trim($_POST['fournisseur']);
                    $description = trim($_POST['description']);
                    $quantite = (int)$_POST['quantite'];
                    $montant = (float)$_POST['montant'];
                    $factureRef = trim($_POST['facture_ref']);
                    
                    if ($achatModel->modifierAchat($id, $date, $fournisseur, $description, $quantite, $montant, $factureRef)) {
                        $message = "Achat modifié avec succès !";
                        $messageType = "success";
                    } else {
                        $message = "Erreur lors de la modification de l'achat.";
                        $messageType = "error";
                    }
                    break;
                    
                case 'supprimer':
                    $id = (int)$_POST['id'];
                    if ($achatModel->supprimerAchat($id)) {
                        $message = "Achat supprimé avec succès !";
                        $messageType = "success";
                    } else {
                        $message = "Erreur lors de la suppression de l'achat.";
                        $messageType = "error";
                    }
                    break;
            }
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $messageType = "error";
        error_log($e->getMessage());
    }
}

// Récupérer les données pour l'affichage
try {
    $achats = $achatModel->getAllAchats();
    $totalDepenses = $achatModel->getTotalDepenses();
    
    // Calculer les statistiques
    $achatsCeMois = array_filter($achats, function($achat) { 
        return date('Y-m', strtotime($achat['date_achat'])) === date('Y-m'); 
    });
    $depensesCeMois = array_sum(array_column($achatsCeMois, 'montant'));
    $nombreFournisseurs = count(array_unique(array_column($achats, 'fournisseur')));
    $moyenneAchat = count($achats) > 0 ? $totalDepenses / count($achats) : 0;
    
    // Top 5 des fournisseurs
    $fournisseurs = array_count_values(array_column($achats, 'fournisseur'));
    arsort($fournisseurs);
    $topFournisseurs = array_slice($fournisseurs, 0, 5, true);
    
} catch (Exception $e) {
    error_log($e->getMessage());
    $achats = [];
    $totalDepenses = 0;
    $depensesCeMois = 0;
    $nombreFournisseurs = 0;
    $moyenneAchat = 0;
    $topFournisseurs = [];
}

// Traitement des paramètres GET pour modification
$achatAModifier = null;
if (isset($_GET['action']) && $_GET['action'] === 'modifier' && isset($_GET['id'])) {
    $achatAModifier = $achatModel->getAchatById((int)$_GET['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SGS | Gestion des Achats de Fournitures</title>
      <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS de base AdminLTE -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
    
    <!-- CSS modulaires personnalisés -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/variables.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/animations.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navigation.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard-admin.css">
    <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
    
    <!-- Stylesheets -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Polices Google -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>


</head>

<body class="hold-transition skin-blue sidebar-mini enable-fixed-layout">


<div class="wrapper">

    <!-- Navigation Header -->
    <?php include 'navbar.php'; ?>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>    <!-- Content Wrapper -->
    <div class="content-wrapper">        <!-- Content Header (Page header) -->
        <section class="content-header animate-slideInFromTop">
            <h1 class="animate-fadeInUp">
                <i class="fas fa-shopping-cart animate-pulse"></i>
                Gestion des Achats de Fournitures
                <small class="animate-slideInRight">Interface comptable - Gérez efficacement vos achats et suivez vos dépenses</small>
            </h1>
            <ol class="breadcrumb animate-slideInRight">
                <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
                <li class="active">Achat Fournitures</li>
            </ol>
        </section>        <!-- Main content -->
        <section class="content">
            <div class="main-container fade-in">
                <!-- Statistiques -->
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow animate-slideInLeft" style="animation-delay: 0.1s;">
                            <div class="inner">
                                <h3><?php echo number_format($totalDepenses, 2); ?>€</h3>
                                <p>Total des dépenses</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-euro animate-pulse"></i>
                            </div>
                            <a href="#" class="small-box-footer">Plus d'informations <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green animate-slideInLeft" style="animation-delay: 0.2s;">
                            <div class="inner">
                                <h3><?php echo number_format($depensesCeMois, 2); ?>€</h3>
                                <p>Dépenses ce mois</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-calendar-check-o animate-bounce"></i>
                            </div>
                            <a href="#" class="small-box-footer">Plus d'informations <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua animate-slideInLeft" style="animation-delay: 0.3s;">
                            <div class="inner">
                                <h3><?php echo $nombreFournisseurs; ?></h3>
                                <p>Fournisseurs actifs</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-truck animate-float"></i>
                            </div>
                            <a href="#" class="small-box-footer">Plus d'informations <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red animate-slideInLeft" style="animation-delay: 0.4s;">
                            <div class="inner">
                                <h3><?php echo number_format($moyenneAchat, 2); ?>€</h3>
                                <p>Achat moyen</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-line-chart animate-glow"></i>
                            </div>
                            <a href="#" class="small-box-footer">Plus d'informations <i class="fa fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

        <!-- Content Section -->
        <div class="content-section">
            <!-- Messages d'alerte -->
            <?php if (!empty($message)): ?>
                <div class="alert-modern alert-<?php echo $messageType === 'success' ? 'success' : 'error'; ?>-modern">
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'ajout/modification -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-<?php echo $achatAModifier ? 'edit' : 'plus-circle'; ?>"></i>
                    <?php echo $achatAModifier ? 'Modifier l\'achat' : 'Nouvel Achat'; ?>
                </h2>
                
                <form method="POST" class="form-modern" id="achatForm">
                    <input type="hidden" name="action" value="<?php echo $achatAModifier ? 'modifier' : 'ajouter'; ?>">
                    <?php if ($achatAModifier): ?>
                        <input type="hidden" name="id" value="<?php echo $achatAModifier['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="date_achat" class="form-label-modern">
                                <i class="fas fa-calendar me-2"></i>Date d'achat *
                            </label>
                            <input type="date" 
                                   class="form-control form-control-modern" 
                                   id="date_achat" 
                                   name="date_achat" 
                                   value="<?php echo $achatAModifier ? $achatAModifier['date_achat'] : date('Y-m-d'); ?>" 
                                   required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="fournisseur" class="form-label-modern">
                                <i class="fas fa-truck me-2"></i>Fournisseur *
                            </label>
                            <input type="text" 
                                   class="form-control form-control-modern" 
                                   id="fournisseur" 
                                   name="fournisseur" 
                                   value="<?php echo $achatAModifier ? htmlspecialchars($achatAModifier['fournisseur']) : ''; ?>"
                                   placeholder="Nom du fournisseur" 
                                   required>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="quantite" class="form-label-modern">
                                <i class="fas fa-sort-numeric-up me-2"></i>Quantité *
                            </label>
                            <input type="number" 
                                   class="form-control form-control-modern" 
                                   id="quantite" 
                                   name="quantite" 
                                   value="<?php echo $achatAModifier ? $achatAModifier['quantite'] : ''; ?>"
                                   min="1" 
                                   required
                                   placeholder="0">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="montant" class="form-label-modern">
                                <i class="fas fa-euro-sign me-2"></i>Montant (€) *
                            </label>
                            <input type="number" 
                                   class="form-control form-control-modern" 
                                   id="montant" 
                                   name="montant" 
                                   value="<?php echo $achatAModifier ? $achatAModifier['montant'] : ''; ?>"
                                   step="0.01" 
                                   min="0" 
                                   required
                                   placeholder="0.00">
                        </div>
                        
                        <div class="col-md-4">
                            <label for="facture_ref" class="form-label-modern">
                                <i class="fas fa-file-invoice me-2"></i>Référence facture
                            </label>
                            <input type="text" 
                                   class="form-control form-control-modern" 
                                   id="facture_ref" 
                                   name="facture_ref" 
                                   value="<?php echo $achatAModifier ? htmlspecialchars($achatAModifier['facture_ref']) : ''; ?>"
                                   placeholder="Référence de la facture">
                        </div>
                        
                        <div class="col-12">
                            <label for="description" class="form-label-modern">
                                <i class="fas fa-comment me-2"></i>Description *
                            </label>
                            <textarea class="form-control form-control-modern" 
                                      id="description" 
                                      name="description" 
                                      rows="3"
                                      placeholder="Description détaillée de l'achat"
                                      required><?php echo $achatAModifier ? htmlspecialchars($achatAModifier['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="col-12 text-end">
                            <?php if ($achatAModifier): ?>
                                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFourniture" 
                                   class="btn btn-secondary me-2">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                                    <i class="fas fa-undo me-2"></i>Réinitialiser
                                </button>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary-modern">
                                <i class="fas fa-save me-2"></i><?php echo $achatAModifier ? 'Modifier' : 'Enregistrer'; ?> l'achat
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Liste des achats -->
            <div class="section-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="section-title mb-0">
                        <i class="fas fa-list"></i>
                        Liste des Achats (<?php echo count($achats); ?>)
                    </h2>
                    <div class="d-flex gap-2">
                        <button class="btn btn-success-modern" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Exporter Excel
                        </button>
                        <button class="btn btn-primary-modern" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimer
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="achatsTable" class="table table-modern table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Fournisseur</th>
                                <th>Description</th>
                                <th>Quantité</th>
                                <th>Montant (€)</th>
                                <th>Référence</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($achats as $achat): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        <?php echo htmlspecialchars($achat['id']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        <?php echo date('d/m/Y', strtotime($achat['date_achat'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($achat['fournisseur']); ?></div>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($achat['description']); ?>">
                                        <?php echo htmlspecialchars($achat['description']); ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info rounded-pill">
                                        <?php echo number_format($achat['quantite']); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success"><?php echo number_format($achat['montant'], 2); ?>€</strong>
                                </td>
                                <td>
                                    <?php echo $achat['facture_ref'] ? htmlspecialchars($achat['facture_ref']) : '<span class="text-muted">-</span>'; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFourniture&action=modifier&id=<?php echo $achat['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteAchat(<?php echo $achat['id']; ?>, '<?php echo htmlspecialchars($achat['description']); ?>')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top fournisseurs -->
            <?php if (!empty($topFournisseurs)): ?>
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    Top 5 des Fournisseurs
                </h2>
                
                <div class="row g-3">
                    <?php 
                    $colors = ['primary', 'success', 'warning', 'info', 'danger'];
                    $i = 0;
                    foreach ($topFournisseurs as $fournisseur => $count): 
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="d-flex align-items-center p-3 border rounded-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-<?php echo $colors[$i % 5]; ?> rounded-pill fs-6">
                                    <?php echo $count; ?>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold"><?php echo htmlspecialchars($fournisseur); ?></div>
                                <small class="text-muted">achats effectués</small>
                            </div>
                        </div>
                    </div>
                    <?php 
                    $i++;
                    endforeach; 
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Initialisation de DataTable
            $('#achatsTable').DataTable({
                responsive: true,
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fas fa-file-excel"></i> Excel',
                        className: 'btn btn-success btn-sm'
                    }
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                columnDefs: [
                    { targets: [7], orderable: false }
                ]
            });

            // Animation des cartes statistiques
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'fadeIn 0.6s ease-out';
                    }
                });
            });

            document.querySelectorAll('.stat-card').forEach(card => {
                observer.observe(card);
            });

            // Validation du formulaire
            $('#achatForm').on('submit', function(e) {
                const quantite = parseInt($('#quantite').val());
                const montant = parseFloat($('#montant').val());
                
                if (quantite <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de validation',
                        text: 'La quantité doit être supérieure à 0.'
                    });
                    return;
                }
                
                if (montant <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de validation',
                        text: 'Le montant doit être supérieur à 0.'
                    });
                    return;
                }
            });
        });

        // Fonction pour réinitialiser le formulaire
        function resetForm() {
            document.getElementById('achatForm').reset();
            document.getElementById('date_achat').value = '<?php echo date('Y-m-d'); ?>';
        }

        // Fonction pour supprimer un achat
        function deleteAchat(id, description) {
            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Êtes-vous sûr de vouloir supprimer l'achat "${description}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Créer un formulaire pour la suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="${id}">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Fonction pour exporter en Excel
        function exportToExcel() {
            $('#achatsTable').DataTable().button('.buttons-excel').trigger();
        }

        // Animation de chargement
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter une classe d'animation après le chargement
            setTimeout(() => {
                document.querySelectorAll('.stat-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 100);
                });
            }, 100);        });

        // Gestion responsive du tableau
        $(window).on('resize', function() {
            $('#achatsTable').DataTable().columns.adjust().responsive.recalc();
        });
    </script>

        </div>
        <!-- /.main-container -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
</div>
<!-- ./wrapper -->

</body>
</html>


<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer la session scolaire active depuis la base de données
$query_session = "SELECT * FROM sessions_scolaires WHERE est_active = 1 LIMIT 1";
$result_session = $mysqli->query($query_session);

if ($result_session && $result_session->num_rows > 0) {
    $session_active = $result_session->fetch_assoc();
    $session_active_nom = $session_active['libelle'] ?? ($session_active['annee_debut'] . '-' . $session_active['annee_fin']);
    $session_active_id = $session_active['id'];
} else {
    // Aucune session active trouvée, utiliser l'année en cours comme fallback
    $session_active_nom = date('Y') . '-' . (date('Y') + 1);
    $session_active_id = null;
}

// Récupérer le nombre d'élèves inscrits
$result = $mysqli->query("SELECT COUNT(*) AS total_eleves FROM eleves");
$row = $result->fetch_assoc();
$total_eleves = $row['total_eleves'];

// Fermer la connexion à la base de données
$mysqli->close();

// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';

// Les données de session scolaire sont maintenant récupérées depuis le contrôleur
$session_active_nom = isset($session_active) ? $session_active['libelle'] : (date('Y') . '-' . (date('Y') + 1));
$session_active_id = isset($session_active) ? $session_active['id'] : null;
$total_eleves = isset($total_eleves) ? $total_eleves : 0;

// Définir la date du jour comme valeur par défaut pour les champs de date
$today = date('Y-m-d');
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Paiement des frais</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .select2-container--default .select2-selection--single {
      height: 34px;
      border-radius: 0;
    }
    .form-group label {
      font-weight: 600;
    }
    .payment-form {
      background-color: #f9f9f9;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .payment-header {
      margin-bottom: 20px;
      border-bottom: 1px solid #eee;
      padding-bottom: 10px;
    }
    .session-info {
      background-color: #d9edf7;
      border-color: #bce8f1;
      color: #31708f;
      padding: 10px 15px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    .loading-spinner {
      display: none;
      text-align: center;
      padding: 20px;
    }
    .form-section {
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 20px;
      margin-bottom: 20px;
    }
    .form-section h4 {
      margin-top: 0;
      color: #3c8dbc;
      border-bottom: 2px solid #3c8dbc;
      padding-bottom: 10px;
    }
    .amount-display {
      font-size: 18px;
      font-weight: bold;
      color: #00a65a;
    }
    .required-field {
      color: #dd4b39;
    }
    .field-help {
      font-size: 12px;
      color: #666;
      margin-top: 5px;
    }
    .payment-summary {
      background: #f0f8ff;
      border: 1px solid #3c8dbc;
      border-radius: 5px;
      padding: 15px;
      margin-top: 20px;
    }
    .btn-submit {
      background: linear-gradient(45deg, #3c8dbc, #5cb85c);
      border: none;
      transition: all 0.3s ease;
    }
    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    .validation-error {
      border-color: #dd4b39 !important;
      box-shadow: 0 0 5px rgba(221, 75, 57, 0.3);
    }
    .validation-success {
      border-color: #00a65a !important;
      box-shadow: 0 0 5px rgba(0, 166, 90, 0.3);
    }
  </style>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil" class="logo">
      <span class="logo-mini"><b>St</b>S</span>
      <span class="logo-lg"><b><?php echo $role; ?></b></span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="user-image" alt="Image utilisateur">
              <span class="hidden-xs"><?php echo $username; ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
              <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
                <p><?php echo $role; ?></p>
              </li>
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=profil" class="btn btn-default btn-flat">Profil</a>
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
        <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo $username; ?></p>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Accueil</span>
          </a>
        </li>
        <li>
           <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFournitures">
             <i class="fa fa-pencil"></i> <span>Achat fourniture</span>
           </a>
         </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscris">
            <i class="fa fa-users"></i> <span>Élèves reinscris</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
            <i class="fa fa-money"></i> <span>Paiement frais</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
            <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription">
            <i class="fa fa-refresh"></i> <span>Réinscription</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Paiement des frais
        <small>Enregistrer un nouveau paiement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Paiement des frais</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <!-- Messages d'alerte -->
          <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-check"></i> Succès!</h4>
              <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>
          
          <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
              <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>
          
          <!-- Affichage de la session scolaire active -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-calendar"></i> Session Scolaire</h3>
            </div>
            <div class="box-body">
              <div class="session-info">
                <h4>Session active: <strong><?php echo $session_active_nom; ?></strong></h4>
                <?php if (!$session_active_id): ?>
                  <div class="alert alert-warning">
                    <i class="fa fa-warning"></i> Aucune session n'est actuellement active. Veuillez en activer une.
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-money"></i> Formulaire de paiement</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
              </div>
            </div>
            
            <form action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutPaiement" method="POST" class="form-horizontal" id="paymentForm">
              <div class="box-body">
                
                <!-- Section Informations Élève -->
                <div class="form-section">
                  <h4><i class="fa fa-user"></i> Informations de l'élève</h4>
                  
                  <!-- Champs cachés -->
                  <input type="hidden" id="reinscription_id" name="reinscription_id" value="<?php echo isset($_GET['reinscription_id']) ? (int)$_GET['reinscription_id'] : ''; ?>">
                  <input type="hidden" name="session_scolaire_id" value="<?php echo $session_active_id; ?>">
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="eleve_id" class="col-sm-4 control-label">
                          Nom de l'élève <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <select class="form-control select2" id="eleve_id" name="eleve_id" onchange="fetchEleveDetails()" required <?php echo isset($_GET['eleve_id']) ? 'disabled' : ''; ?>>
                            <option value="">-- Sélectionner un élève --</option>
                            <?php foreach ($eleves as $eleve) : ?>
                              <option value="<?php echo $eleve['id']; ?>" <?php echo (isset($_GET['eleve_id']) && $_GET['eleve_id'] == $eleve['id']) ? 'selected' : ''; ?>>
                                <?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <?php if(isset($_GET['eleve_id'])): ?>
                            <input type="hidden" name="eleve_id" value="<?php echo (int)$_GET['eleve_id']; ?>">
                          <?php endif; ?>
                          <div class="field-help">Recherchez et sélectionnez l'élève concerné</div>
                        </div>
                      </div>

                      <!-- Alerte réinscription -->
                      <?php if(isset($_GET['reinscription_id']) && !empty($_GET['reinscription_id'])): ?>
                        <div class="form-group">
                          <div class="col-sm-offset-4 col-sm-8">
                            <div class="alert alert-info">
                              <i class="fa fa-info-circle"></i> Ce paiement est lié à une réinscription
                            </div>
                          </div>
                        </div>
                      <?php endif; ?>

                      <div class="form-group">
                        <label for="classe_id" class="col-sm-4 control-label">Classe</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="classe_id" name="classe_id" readonly placeholder="Classe de l'élève">
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="option_id" class="col-sm-4 control-label">Option</label>
                        <div class="col-sm-8">
                          <input type="hidden" id="option_id_value" name="option_id">
                          <input type="text" class="form-control" id="option_display" readonly placeholder="Option de l'élève">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="section" class="col-sm-4 control-label">Section</label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control" id="section" name="section" readonly placeholder="Section de l'élève">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Détails du Paiement -->
                <div class="form-section">
                  <h4><i class="fa fa-credit-card"></i> Détails du paiement</h4>
                  
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="frais_id" class="col-sm-4 control-label">
                          Type de frais <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <select name="frais_id" id="frais_id" class="form-control select2" onchange="fetchFraisMontant()" required>
                            <option value="">-- Sélectionner un frais --</option>
                            <?php foreach ($frais as $frai) : ?>
                              <option value="<?php echo $frai['id']; ?>" data-montant="<?php echo isset($frai['montant']) ? $frai['montant'] : ''; ?>">
                                <?php echo $frai['description']; ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <div class="field-help">Choisissez le type de frais à payer</div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="amount_paid" class="col-sm-4 control-label">
                          Montant à payer <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="number" class="form-control amount-display" id="amount_paid" name="amount_paid" required readonly min="0" step="0.01">
                            <span class="input-group-addon">USD</span>
                          </div>
                          <div class="field-help">Montant calculé automatiquement</div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="mois" class="col-sm-4 control-label">
                          Mois <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <select class="form-control select2" id="mois" name="mois" required>
                            <option value="">-- Sélectionner un mois --</option>
                            <?php foreach ($mois as $moi) : ?>
                              <option value="<?php echo $moi['id']; ?>" <?php echo (date('n') == $moi['id']) ? 'selected' : ''; ?>>
                                <?php echo $moi['nom']; ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <div class="field-help">Mois concerné par le paiement</div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="payment_date" class="col-sm-4 control-label">
                          Date de paiement <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $today; ?>" required max="<?php echo $today; ?>">
                          </div>
                          <div class="field-help">Date à laquelle le paiement a été effectué</div>
                        </div>
                      </div>
                      
                     
                      <div class="form-group">
                        <label for="created_at" class="col-sm-4 control-label">
                          Date d'enregistrement <span class="required-field">*</span>
                        </label>
                        <div class="col-sm-8">
                          <div class="input-group date">
                            <div class="input-group-addon">
                              <i class="fa fa-calendar"></i>
                            </div>
                            <input type="date" class="form-control" id="created_at" name="created_at" value="<?php echo $today; ?>" required>
                          </div>
                          <div class="field-help">Date d'enregistrement du paiement dans le système</div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="mode_paiement" class="col-sm-4 control-label">Mode de paiement</label>
                        <div class="col-sm-8">
                          <select class="form-control select2" id="mode_paiement" name="mode_paiement">
                            <option value="especes">Espèces</option>
                            <option value="cheque">Chèque</option>
                            <option value="virement">Virement bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                          </select>
                          <div class="field-help">Méthode utilisée pour effectuer le paiement</div>
                        </div>
                      </div>

                     
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Section Résumé du Paiement -->
                <div class="payment-summary" id="paymentSummary" style="display: none;">
                  <h4><i class="fa fa-info-circle"></i> Résumé du paiement</h4>
                  <div class="row">
                    <div class="col-md-6">
                      <p><strong>Élève:</strong> <span id="summary-eleve"></span></p>
                      <p><strong>Classe:</strong> <span id="summary-classe"></span></p>
                      <p><strong>Type de frais:</strong> <span id="summary-frais"></span></p>
                    </div>
                    <div class="col-md-6">
                      <p><strong>Montant:</strong> <span id="summary-montant" class="amount-display"></span> USD</p>
                      <p><strong>Mois:</strong> <span id="summary-mois"></span></p>
                      <p><strong>Date:</strong> <span id="summary-date"></span></p>
                    </div>
                  </div>
                </div>

                <!-- Spinner de chargement -->
                <div class="loading-spinner" id="loadingSpinner">
                  <i class="fa fa-spinner fa-spin fa-2x"></i>
                  <p>Chargement en cours...</p>
                </div>
              </div>
              
              <div class="box-footer">
                <div class="row">
                  <div class="col-md-6 col-md-offset-3">
                    <button type="submit" class="btn btn-primary btn-block btn-lg btn-submit" id="submitBtn">
                      <i class="fa fa-save"></i> Enregistrer le paiement
                    </button>
                  </div>
                </div>
                <div class="row" style="margin-top: 15px;">
                  <div class="col-md-6 col-md-offset-3">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements" class="btn btn-default btn-block">
                      <i class="fa fa-list"></i> Voir tous les paiements
                    </a>
                  </div>
                </div>
                <div class="row" style="margin-top: 10px;">
                  <div class="col-md-6 col-md-offset-3">
                    <button type="button" class="btn btn-warning btn-block" onclick="resetForm()">
                      <i class="fa fa-refresh"></i> Réinitialiser le formulaire
                    </button>
                  </div>
                </div>
              </div>
            </form>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Select2 -->
<script src="bower_components/select2/dist/js/select2.full.min.js"></script>
<!-- FastClick -->
<script src="bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 pour de meilleures alertes -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Scripts personnalisés -->
<script>
$(document).ready(function() {
    // Initialiser Select2 avec des options avancées
    $('.select2').select2({
        placeholder: "Sélectionner une option",
        allowClear: true,
        language: {
            noResults: function() {
                return "Aucun résultat trouvé";
            },
            searching: function() {
                return "Recherche en cours...";
            }
        }
    });
    
    // Définir la date du jour comme valeur par défaut
    var today = new Date().toISOString().split('T')[0];
    $('#payment_date').val(today);
    $('#created_at').val(today);
    
    // Vérifier les paramètres d'URL au chargement
    checkUrlParameters();
    
    // Charger les détails de l'élève si déjà sélectionné
    if ($('#eleve_id').val()) {
        fetchEleveDetails();
    }
    
    // Validation en temps réel
    setupRealTimeValidation();
    
    // Gestionnaire de soumission du formulaire
    $('#paymentForm').on('submit', handleFormSubmission);
    
    // Auto-save des données du formulaire
    setupAutoSave();
});


  // Fonction pour récupérer les détails de l'élève
  function fetchEleveDetails() {
    var eleveId = document.getElementById("eleve_id").value;

    if (eleveId) {
      $.ajax({
        url: "index.php?controller=comptable&action=fetchEleveDetails",
        method: "POST",
        data: { eleve_id: eleveId },
        success: function(response) {
          console.log("Réponse du serveur :", response);

          if (response.includes(";")) {
            // Découpe la réponse pour obtenir les détails
            var details = response.split(";");
            $("#classe_id").val(details[1]);   // Nom de la classe
            $("#option_display").val(details[2]); // Afficher le nom de l'option
            
            // Récupérer l'ID de l'option à partir du nom
            $.ajax({
              url: "index.php?controller=comptable&action=getOptionIdByName",
              method: "POST",
              data: { option_name: details[2] },
              success: function(optionId) {
                $("#option_id_value").val(optionId);
              }
            });
            
            $("#section").val(details[3]);   // Nom de la section
            
            // Récupérer les mois non payés pour cet élève
            fetchMoisNonPayes(eleveId);
          } else {
            alert("Erreur: " + response);
          }
        },
        error: function() {
          alert("Erreur lors de la communication avec le serveur.");
        }
      });
    } else {
      // Réinitialiser les champs si aucun élève n'est sélectionné
      $("#classe_id").val("");
      $("#option_display").val("");
      $("#option_id_value").val("");
      $("#section").val("");
      
      // Réinitialiser la liste des mois
      resetMoisList();
    }
  }


  // Fonction pour récupérer le montant du frais sélectionné
  function fetchFraisMontant() {
    var fraisId = document.getElementById("frais_id").value;
    
    if (fraisId) {
      $.ajax({
        url: "index.php?controller=comptable&action=fetchFraisMontant",
        method: "POST",
        data: { frais_id: fraisId },
        success: function(response) {
          // Nettoyer la réponse en supprimant les espaces et retours à la ligne
          var cleanResponse = response.trim();
          console.log("Réponse brute:", response);
          console.log("Réponse nettoyée:", cleanResponse);
          
          // Vérifier si la réponse nettoyée est un nombre
          if (!isNaN(cleanResponse)) {
            $("#amount_paid").val(cleanResponse);
          } else {
            alert("Erreur: La réponse n'est pas un nombre valide");
            $("#amount_paid").val("");
          }
        },
        error: function(xhr, status, error) {
          console.error("Erreur AJAX:", status, error);
          alert("Erreur lors de la communication avec le serveur.");
          $("#amount_paid").val("");
        }
      });
    } else {
      // Réinitialiser le champ si aucun frais n'est sélectionné
      $("#amount_paid").val("");
    }
  }
 
  // Fonction pour récupérer les mois non payés par l'élève
  function fetchMoisNonPayes(eleveId) {
    $.ajax({
        url: "index.php?controller=comptable&action=fetchMoisNonPayes",
        method: "POST",
        data: { eleve_id: eleveId },
        dataType: "json", // Spécifier que la réponse attendue est du JSON
        success: function(response) {
            // Vider la liste des mois
            var moisSelect = $("#mois");
            moisSelect.empty();
            moisSelect.append('<option value="">-- Sélectionner un mois --</option>');
            
            // Ajouter les mois non payés
            if (response && response.length > 0) {
                $.each(response, function(index, mois) {
                    moisSelect.append('<option value="' + mois.id + '">' + mois.nom + '</option>');
                });
            } else {
                moisSelect.append('<option value="" disabled>Tous les mois ont été payés</option>');
            }
            
            // Rafraîchir Select2 si vous l'utilisez
            if ($.fn.select2) {
                moisSelect.trigger('change.select2');
            } else {
                moisSelect.trigger('change');
            }
        },
        error: function(xhr, status, error) {
            console.error("Erreur AJAX:", status, error);
            alert("Erreur lors de la récupération des mois non payés: " + error);
        }
    });
}
// Fonction pour peupler les détails de l'élève
function populateEleveDetails(data) {
    $('#classe_id').val(data.classe_nom || '');
    $('#option_id_value').val(data.option_id || '');
    $('#option_display').val(data.option_nom || '');
    $('#section').val(data.section || '');
    
    // Ajouter des classes de validation
    $('.form-control[readonly]').addClass('validation-success');
}

// Fonction pour effacer les détails de l'élève
function clearEleveDetails() {
    $('#classe_id, #option_display, #section, #amount_paid').val('');
    $('#option_id_value').val('');
    $('.form-control[readonly]').removeClass('validation-success validation-error');
    $('#paymentSummary').hide();
}

// Fonction pour mettre à jour le résumé du paiement
function updatePaymentSummary() {
    var eleveText = $('#eleve_id option:selected').text();
    var classeText = $('#classe_id').val();
    var fraisText = $('#frais_id option:selected').text();
    var montant = $('#amount_paid').val();
    var moisText = $('#mois option:selected').text();
    var date = $('#payment_date').val();
    
    if (eleveText && eleveText !== '-- Sélectionner un élève --' && 
        fraisText && fraisText !== '-- Sélectionner un frais --' && 
        montant && moisText && moisText !== '-- Sélectionner un mois --') {
        
        $('#summary-eleve').text(eleveText);
        $('#summary-classe').text(classeText || 'Non définie');
        $('#summary-frais').text(fraisText);
        $('#summary-montant').text(montant);
        $('#summary-mois').text(moisText);
        $('#summary-date').text(formatDate(date));
        
        $('#paymentSummary').slideDown();
    } else {
        $('#paymentSummary').slideUp();
    }
}

// Fonction pour formater la date
function formatDate(dateString) {
    if (!dateString) return '';
    var date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Fonction pour afficher/masquer le spinner de chargement
function showLoading(show) {
    if (show) {
        $('#loadingSpinner').show();
        $('#submitBtn').prop('disabled', true);
    } else {
        $('#loadingSpinner').hide();
        $('#submitBtn').prop('disabled', false);
    }
}

// Fonction pour afficher des alertes améliorées
function showAlert(message, type = 'info', title = '') {
    var icon = 'info';
    var confirmButtonColor = '#3085d6';
    
    switch(type) {
        case 'success':
            icon = 'success';
            confirmButtonColor = '#28a745';
            title = title || 'Succès!';
            break;
        case 'error':
            icon = 'error';
            confirmButtonColor = '#dc3545';
            title = title || 'Erreur!';
            break;
        case 'warning':
            icon = 'warning';
            confirmButtonColor = '#ffc107';
            title = title || 'Attention!';
            break;
    }
    
    Swal.fire({
        title: title,
        text: message,
        icon: icon,
        confirmButtonColor: confirmButtonColor,
        confirmButtonText: 'OK'
    });
}

// Fonction pour vérifier les paramètres d'URL
function checkUrlParameters() {
    var urlParams = new URLSearchParams(window.location.search);
    var success = urlParams.get('success');
    var error = urlParams.get('error');
    var message = urlParams.get('message');
    
    if (success) {
        showAlert(decodeURIComponent(message || 'Paiement enregistré avec succès!'), 'success');
        // Nettoyer l'URL
        window.history.replaceState({}, document.title, window.location.pathname);
    } else if (error) {
        showAlert(decodeURIComponent(message || 'Une erreur est survenue!'), 'error');
        // Nettoyer l'URL
        window.history.replaceState({}, document.title, window.location.pathname);
    }
}

function resetMoisList() {
    var moisSelect = $("#mois");
    moisSelect.empty();
    moisSelect.append('<option value="">-- Sélectionner un mois --</option>');
    
    // Récupérer tous les mois disponibles
    $.ajax({
      url: "index.php?controller=comptable&action=getAllMois",
      method: "GET",
      success: function(response) {
        try {
          var allMois = JSON.parse(response);
          $.each(allMois, function(index, mois) {
            moisSelect.append('<option value="' + mois.id + '">' + mois.nom + '</option>');
          });
          moisSelect.trigger('change');
        } catch (e) {
          console.error("Erreur lors du parsing JSON:", e);
        }
      }
    });
  }

// Fonction pour la validation en temps réel
function setupRealTimeValidation() {
    // Validation des champs requis
    $('input[required], select[required]').on('blur change', function() {
        var $field = $(this);
        var value = $field.val();
        
        if (value && value.trim() !== '') {
            $field.removeClass('validation-error').addClass('validation-success');
        } else {
            $field.removeClass('validation-success').addClass('validation-error');
        }
    });
    
    // Validation des dates
    $('#payment_date, #created_at').on('change', function() {
        var selectedDate = new Date($(this).val());
        var today = new Date();
        today.setHours(23, 59, 59, 999); // Fin de journée
        
        if (selectedDate > today) {
            $(this).addClass('validation-error');
            showAlert('La date ne peut pas être dans le futur', 'warning');
        } else {
            $(this).removeClass('validation-error').addClass('validation-success');
        }
    });
    
    // Mise à jour du résumé lors des changements
    $('#eleve_id, #frais_id, #mois, #payment_date').on('change', updatePaymentSummary);
}

// Fonction pour gérer la soumission du formulaire
function handleFormSubmission(e) {
    e.preventDefault();
    
    // Validation finale
    if (!validateForm()) {
        return false;
    }
    
    // Confirmation avant soumission
    Swal.fire({
        title: 'Confirmer le paiement',
        text: 'Êtes-vous sûr de vouloir enregistrer ce paiement?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Oui, enregistrer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            submitForm();
        }
    });
}

// Fonction pour valider le formulaire
function validateForm() {
    var isValid = true;
    var errors = [];
    
    // Vérifier les champs requis
    $('input[required], select[required]').each(function() {
        var $field = $(this);
        var value = $field.val();
        
        if (!value || value.trim() === '') {
            isValid = false;
            $field.addClass('validation-error');
            errors.push('Le champ "' + $field.closest('.form-group').find('label').text().replace('*', '').trim() + '" est requis');
        }
    });
    
    // Vérifier les dates
    var paymentDate = new Date($('#payment_date').val());
    var today = new Date();
    
    if (paymentDate > today) {
        isValid = false;
        errors.push('La date de paiement ne peut pas être dans le futur');
    }
    
    // Vérifier le montant
    var amount = parseFloat($('#amount_paid').val());
    if (isNaN(amount) || amount <= 0) {
        isValid = false;
        errors.push('Le montant doit être supérieur à zéro');
    }
    
    if (!isValid) {
        showAlert('Veuillez corriger les erreurs suivantes:\n• ' + errors.join('\n• '), 'error', 'Erreurs de validation');
    }
    
    return isValid;
}

// Fonction pour soumettre le formulaire
function submitForm() {
    showLoading(true);
    
    var formData = new FormData($('#paymentForm')[0]);
    
    $.ajax({
        url: $('#paymentForm').attr('action'),
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (typeof response === 'string') {
                // Redirection vers la page avec message de succès
                window.location.href = response;
            } else if (response.success) {
                showAlert('Paiement enregistré avec succès!', 'success');
                resetForm();
            } else {
                showAlert(response.message || 'Erreur lors de l\'enregistrement', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erreur AJAX:', error);
            showAlert('Erreur de communication avec le serveur', 'error');
        },
        complete: function() {
            showLoading(false);
        }
    });
}

// Fonction pour réinitialiser le formulaire
function resetForm() {
    $('#paymentForm')[0].reset();
    $('.select2').val(null).trigger('change');
    clearEleveDetails();
    $('.validation-error, .validation-success').removeClass('validation-error validation-success');
    
    // Remettre les dates par défaut
    var today = new Date().toISOString().split('T')[0];
    $('#payment_date').val(today);
    $('#created_at').val(today);
    
    showAlert('Formulaire réinitialisé', 'success');
}

// Fonction pour l'auto-sauvegarde (optionnel)
function setupAutoSave() {
    var autoSaveTimer;
    
    $('#paymentForm input, #paymentForm select').on('change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Sauvegarder les données dans le localStorage
            var formData = {};
            $('#paymentForm').serializeArray().forEach(function(item) {
                formData[item.name] = item.value;
            });
            localStorage.setItem('payment_form_data', JSON.stringify(formData));
        }, 2000); // Sauvegarder après 2 secondes d'inactivité
    });
    
    // Restaurer les données au chargement
    var savedData = localStorage.getItem('payment_form_data');
    if (savedData) {
        try {
            var data = JSON.parse(savedData);
            Object.keys(data).forEach(function(key) {
                var $field = $('[name="' + key + '"]');
                if ($field.length && data[key]) {
                    $field.val(data[key]).trigger('change');
                }
            });
        } catch (e) {
            console.error('Erreur lors de la restauration des données:', e);
        }
    }
}

// Fonction pour imprimer le reçu (bonus)
function printReceipt() {
    if (!$('#paymentSummary').is(':visible')) {
        showAlert('Veuillez d\'abord remplir le formulaire', 'warning');
        return;
    }
    
    var printContent = `
        <div style="text-align: center; font-family: Arial, sans-serif;">
            <h2>École St Sofie</h2>
            <h3>Reçu de Paiement</h3>
            <hr>
            <p><strong>Élève:</strong> ${$('#summary-eleve').text()}</p>
            <p><strong>Classe:</strong> ${$('#summary-classe').text()}</p>
            <p><strong>Type de frais:</strong> ${$('#summary-frais').text()}</p>
            <p><strong>Montant:</strong> ${$('#summary-montant').text()} USD</p>
            <p><strong>Mois:</strong> ${$('#summary-mois').text()}</p>
            <p><strong>Date:</strong> ${$('#summary-date').text()}</p>
            <hr>
            <p style="font-size: 12px;">Merci pour votre paiement</p>
        </div>
    `;
    
    var printWindow = window.open('', '_blank');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

// Raccourcis clavier
$(document).keydown(function(e) {
    // Ctrl + S pour sauvegarder
    if (e.ctrlKey && e.which === 83) {
        e.preventDefault();
        $('#paymentForm').submit();
    }
    
    // Ctrl + R pour réinitialiser
    if (e.ctrlKey && e.which === 82) {
        e.preventDefault();
        resetForm();
    }
    
    // Échap pour fermer les alertes
    if (e.which === 27) {
        Swal.close();
    }
});
</script>

</body>
</html>
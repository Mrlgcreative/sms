

<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
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
$current_session = isset($current_session) ? $current_session : date('Y') . '-' . (date('Y') + 1);

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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
            <i class="fa fa-file"></i> <span>Rapports</span>
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
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de paiement</h3>
            </div>
            
            <form action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutPaiement" method="POST" class="form-horizontal">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="eleve_id" class="col-sm-4 control-label">Nom de l'élève</label>
                      <div class="col-sm-8">
                        <select class="form-control select2" id="eleve_id" name="eleve_id" onchange="fetchEleveDetails()" required>
                          <option value="">-- Sélectionner un élève --</option>
                          <?php foreach ($eleves as $eleve) : ?>
                            <option value="<?php echo $eleve['id']; ?>">
                              <?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="classe" class="col-sm-4 control-label">Classe</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="classe" name="classe" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="option_id" class="col-sm-4 control-label">Option</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="option_id" name="option_id" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="section" class="col-sm-4 control-label">Section</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="section" name="section" readonly>
                      </div>
                    </div>
                  </div>
                  
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="frais_id" class="col-sm-4 control-label">Type de frais</label>
                      <div class="col-sm-8">
                        <select name="frais_id" id="frais_id" class="form-control select2" onchange="fetchFraisMontant()" required>
                          <option value="">-- Sélectionner un frais --</option>
                          <?php foreach ($frais as $frai) : ?>
                            <option value="<?php echo $frai['id']; ?>"><?php echo $frai['description']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="amount_paid" class="col-sm-4 control-label">Montant à payer</label>
                      <div class="col-sm-8">
                        <div class="input-group">
                          <span class="input-group-addon">$</span>
                          <input type="number" class="form-control" id="amount_paid" name="amount_paid" required readonly>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="payment_date" class="col-sm-4 control-label">Date de paiement</label>
                      <div class="col-sm-8">
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo $today; ?>" required>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="mois" class="col-sm-4 control-label">Mois</label>
                      <div class="col-sm-8">
                        <select class="form-control select2" id="mois" name="mois" required>
                          <option value="">-- Sélectionner un mois --</option>
                          <?php foreach ($mois as $moi) : ?>
                            <option value="<?php echo $moi['id']; ?>"><?php echo $moi['nom']; ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="created_at" class="col-sm-4 control-label">Date d'enregistrement</label>
                      <div class="col-sm-8">
                        <div class="input-group date">
                          <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                          </div>
                          <input type="date" class="form-control" id="created_at" name="created_at" value="<?php echo $today; ?>" required>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="box-footer">
                <div class="row">
                  <div class="col-md-6 col-md-offset-3">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
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
<!-- Page script -->
<script>
  $(function () {
    // Initialiser Select2
    $('.select2').select2({
      placeholder: "Sélectionner une option",
      allowClear: true
    });
    
    // Définir la date du jour comme valeur par défaut
    var today = new Date().toISOString().split('T')[0];
    $('#payment_date').val(today);
    $('#created_at').val(today);
    
    // Vérifier les paramètres d'URL au chargement de la page
    var urlParams = new URLSearchParams(window.location.search);
    var success = urlParams.get('success');
    var error = urlParams.get('error');
    var message = urlParams.get('message');
    
    if (success) {
      showAlert(decodeURIComponent(message || 'Paiement enregistré avec succès!'), 'success');
    } else if (error) {
      showAlert(decodeURIComponent(message || 'Une erreur est survenue lors de l\'enregistrement du paiement!'), 'danger');
    }
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
            $("#classe").val(details[1]);   // Nom de la classe
            $("#option_id").val(details[2]); // Nom de l'option
            $("#section").val(details[3]);   // Nom de la section
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
      $("#classe").val("");
      $("#option_id").val("");
      $("#section").val("");
    }
  }

  // Fonction pour récupérer le montant des frais
  function fetchFraisMontant() {
    var fraisId = document.getElementById("frais_id").value;

    if (fraisId) {
      $.ajax({
        url: "index.php?controller=comptable&action=fetchFraisMontant",
        method: "POST",
        data: { frais_id: fraisId },
        success: function(response) {
          console.log("Réponse du serveur :", response);

          if (!isNaN(response) && response.trim() !== "") {
            $("#amount_paid").val(response.trim());
          } else {
            alert("Erreur: " + response.trim());
          }
        },
        error: function() {
          alert("Erreur lors de la communication avec le serveur.");
        }
      });
    } else {
      $("#amount_paid").val("");
    }
  }
  
  // Fonction pour afficher une alerte stylisée
  function showAlert(message, type) {
    var alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-' + type + ' alert-dismissible';
    alertDiv.style.position = 'fixed';
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.2)';
    
    alertDiv.innerHTML = 
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' + 
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(function() {
      if (alertDiv.parentNode) {
        alertDiv.parentNode.removeChild(alertDiv);
      }
    }, 5000);
  }
</script>
</body>
</html>

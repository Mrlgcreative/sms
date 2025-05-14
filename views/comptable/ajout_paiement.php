

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
        <li>
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
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de paiement</h3>
            </div>
            
            <form action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutPaiement" method="POST" class="form-horizontal">
              <div class="box-body">
                <div class="row">
                  <div class="col-md-6">
                    <!-- Champ caché pour l'ID de réinscription -->
                    <input type="hidden" id="reinscription_id" name="reinscription_id" value="<?php echo isset($_GET['reinscription_id']) ? (int)$_GET['reinscription_id'] : ''; ?>">
                    
                    <div class="form-group">
                      <label for="eleve_id" class="col-sm-4 control-label">Nom de l'élève</label>
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
                      </div>
                    </div>

                    <!-- Champ pour indiquer si c'est un paiement de réinscription -->
                    <?php if(isset($_GET['reinscription_id']) && !empty($_GET['reinscription_id'])): ?>
                      <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                          <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Ce paiement est lié à une réinscription
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>

                    <!-- Reste du formulaire -->
                    <div class="form-group">
                      <label for="classe_id" class="col-sm-4 control-label">Classe</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" id="classe_id" name="classe_id" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="option_id" class="col-sm-4 control-label">Option</label>
                      <div class="col-sm-8">
                        <input type="hidden" id="option_id_value" name="option_id">
                        <input type="text" class="form-control" id="option_display" readonly>
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
    
    // Définir la date du jour comme valeur par défaut - Correction du format de date
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('payment_date').value = today;
    document.getElementById('created_at').value = today;
    
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
    
    // Ajouter un gestionnaire d'événements pour le formulaire
    $('form').on('submit', function(e) {
      // Vérifier que les dates sont correctement formatées
      var paymentDate = $('#payment_date').val();
      var createdAt = $('#created_at').val();
      
      if (!paymentDate || !createdAt) {
        e.preventDefault();
        showAlert('Veuillez remplir toutes les dates requises', 'danger');
        return false;
      }
      
      // Continuer avec la soumission du formulaire
      return true;
    });
    
    // Vérifier si c'est un paiement de réinscription
    var reinscriptionId = $("input[name='reinscription_id']").val();
    if (reinscriptionId) {
        // Sélectionner automatiquement le premier mois de l'année scolaire (généralement septembre)
        // Adaptez l'ID selon votre configuration
        var premierMoisId = 9; // ID du mois de septembre, à adapter
        
        // Sélectionner le mois dans le dropdown
        $("#mois").val(premierMoisId).trigger('change');
        
        // Sélectionner automatiquement les frais de réinscription
        // Adaptez l'ID selon votre configuration
        var fraisReinscriptionId = 2; // ID des frais de réinscription, à adapter
        $("#frais_id").val(fraisReinscriptionId).trigger('change');
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
  
  // Fonction pour réinitialiser la liste des mois
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

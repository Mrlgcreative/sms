<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Déterminer le contrôleur en fonction du rôle
$controller = 'Auth';
if ($role === 'Director') {
    $controller = 'Director';
} elseif ($role === 'Directrice') {
    $controller = 'Directrice';
} elseif ($role === 'Prefet') {
    $controller = 'Prefet';
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Scanner de Codes QR</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <style>
    #preview {
      width: 100%;
      max-width: 400px;
      height: auto;
      border: 1px solid #ddd;
      margin-bottom: 20px;
    }
    .loading {
      display: none;
      margin: 20px 0;
      text-align: center;
    }
    .result-container {
      margin: 20px 0;
    }
    .eleve-info {
      display: none;
      margin-top: 30px;
      border: 1px solid #ddd;
      border-radius: 5px;
      padding: 15px;
      background-color: #f9f9f9;
    }
    .eleve-header {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    .eleve-photo {
      width: 100px;
      height: 100px;
      overflow: hidden;
      border-radius: 50%;
      margin-right: 20px;
      text-align: center;
      line-height: 100px;
      background-color: #eee;
    }
    .eleve-photo img {
      width: 100%;
      height: auto;
    }
    .eleve-details {
      flex: 1;
    }
    .eleve-nom {
      font-size: 24px;
      margin: 0 0 5px 0;
    }
    .eleve-matricule {
      font-size: 16px;
      color: #666;
      margin: 0 0 5px 0;
    }
    .eleve-classe {
      font-size: 18px;
      font-weight: bold;
      margin: 0;
    }
    .info-section {
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
    }
    .info-section h4 {
      margin-top: 0;
    }
    .frais-info {
      padding: 15px;
      border-radius: 5px;
      margin-top: 15px;
    }
    .frais-en-ordre {
      background-color: #dff0d8;
      border: 1px solid #d6e9c6;
    }
    .frais-en-retard {
      background-color: #f2dede;
      border: 1px solid #ebccd1;
    }
    .frais-partiel {
      background-color: #fcf8e3;
      border: 1px solid #faebcc;
    }
    .error-message {
      color: #a94442;
      background-color: #f2dede;
      border: 1px solid #ebccd1;
      padding: 10px;
      border-radius: 5px;
      display: inline-block;
    }
    .text-success {
      color: #3c763d;
      background-color: #dff0d8;
      border: 1px solid #d6e9c6;
      padding: 10px;
      border-radius: 5px;
      display: inline-block;
    }
    #paiement-details {
      display: none;
      margin-top: 15px;
    }
  </style>
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=<?php echo $controller; ?>&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>SGS</b> - Gestion Scolaire</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=<?php echo $controller; ?>&action=profile" class="btn btn-default btn-flat">Profil</a>
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
        <li class="header">NAVIGATION PRINCIPALE</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=<?php echo $controller; ?>&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Scan&action=index">
            <i class="fa fa-qrcode"></i> <span>Scanner QR Code</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Scan&action=presences">
            <i class="fa fa-calendar-check-o"></i> <span>Présences</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Scanner de Codes QR
        <small>Identification des élèves</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=<?php echo $controller; ?>&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Scanner QR Code</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Scanner un code QR d'élève</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              </div>
            </div>
            
            <div class="box-body">
              <div class="row">
                <div class="col-md-6 col-md-offset-3 text-center">
                  <p>Placez le code QR de la carte d'élève devant la caméra pour le scanner</p>
                  <video id="preview" class="img-responsive center-block"></video>
                  
                  <div class="loading">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Recherche de l'élève...</p>
                  </div>
                  
                  <div class="result-container">
                    <h4>Résultat du scan:</h4>
                    <div id="result">Aucun code QR détecté</div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-8 col-md-offset-2">
                  <div id="eleve-info" class="eleve-info">
                    <div class="eleve-header">
                      <div id="eleve-photo" class="eleve-photo">
                        <i class="fa fa-user fa-5x text-muted"></i>
                      </div>
                      <div class="eleve-details">
                        <h3 id="eleve-nom" class="eleve-nom">Nom de l'élève</h3>
                        <p id="eleve-matricule" class="eleve-matricule">Matricule: XXX-XXX</p>
                        <p id="eleve-classe" class="eleve-classe">Classe</p>
                      </div>
                      <div>
                        <a id="voir-eleve-btn" href="#" class="btn btn-primary">
                          <i class="fa fa-user"></i> Voir détails
                        </a>
                      </div>
                    </div>
                    
                    <div class="row info-section">
                      <div class="col-md-6">
                        <h4>Informations personnelles</h4>
                        <table class="table table-striped">
                          <tr>
                            <th>Section:</th>
                            <td id="eleve-section">-</td>
                          </tr>
                          <tr>
                            <th>Date de naissance:</th>
                            <td id="eleve-date-naissance">-</td>
                          </tr>
                          <tr>
                            <th>Sexe:</th>
                            <td id="eleve-sexe">-</td>
                          </tr>
                        </table>
                      </div>
                      
                      <div class="col-md-6" id="frais-container">
                        <h4>Frais scolaires - <span id="session-scolaire">Session actuelle</span></h4>
                        <div id="frais-info" class="frais-info">
                          <p id="frais-status"><i class="fa fa-info-circle"></i> Statut des frais</p>
                          
                          <div class="progress">
                            <div id="frais-progress" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                              0%
                            </div>
                          </div>
                          
                          <table class="table table-striped">
                            <tr>
                              <th>Total payé:</th>
                              <td><span id="total-paye">0</span> Fc</td>
                            </tr>
                            <tr>
                              <th>Total dû:</th>
                              <td><span id="total-du">0</span> Fc</td>
                            </tr>
                            <tr>
                              <th>Reste à payer:</th>
                              <td><span id="reste-a-payer">0</span> Fc</td>
                            </tr>
                          </table>
                          
                          <button id="toggle-paiements" class="btn btn-default btn-sm">
                            <i class="fa fa-plus-circle"></i> Afficher les détails des paiements
                          </button>
                          
                          <div id="paiement-details">
                            <h5>Historique des paiements</h5>
                            <table class="table table-striped table-bordered">
                              <thead>
                                <tr>
                                  <th>Type</th>
                                  <th>Montant</th>
                                  <th>Date</th>
                                  <th>Description</th>
                                </tr>
                              </thead>
                              <tbody id="paiements-table-body">
                                <!-- Les paiements seront ajoutés ici dynamiquement -->
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
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
      <b>Version</b> 1.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
<script>
  $(function() {
    let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
    
    scanner.addListener('scan', function (content) {
      document.getElementById('result').innerHTML = 'Code détecté: ' + content;
      $('.loading').show();
      $('#eleve-info').hide();
      
      // Envoyer le matricule au serveur
      $.ajax({
        url: '<?php echo BASE_URL; ?>index.php?controller=Scan&action=processQRCode',
        type: 'POST',
        data: { 
          matricule: content,
          enregistrer_presence: true
        },
        dataType: 'json',
        timeout: 15000, // Timeout de 15 secondes
        success: function(data) {
          $('.loading').hide();
          
          if (data.success) {
            // Afficher les informations de l'élève
            $('#eleve-nom').text(data.nom + ' ' + data.prenom + (data.post_nom ? ' ' + data.post_nom : ''));
            $('#eleve-matricule').text(data.matricule);
            $('#eleve-classe').text(data.classe_nom || 'Non assigné');
            $('#eleve-section').text(data.section || 'Non spécifiée');
            $('#eleve-date-naissance').text(data.date_naissance || 'Non spécifiée');
            $('#eleve-sexe').text(data.sexe === 'M' ? 'Masculin' : (data.sexe === 'F' ? 'Féminin' : 'Non spécifié'));
            
            // Mettre à jour le lien pour voir les détails de l'élève
            let controllerName = '<?php echo $controller; ?>';
            $('#voir-eleve-btn').attr('href', '<?php echo BASE_URL; ?>index.php?controller=' + controllerName + '&action=voirEleve&id=' + data.id);
            
            // Mettre à jour la photo de l'élève
            if (data.photo) {
              $('#eleve-photo').html('<img src="<?php echo BASE_URL; ?>' + data.photo + '" alt="Photo de l\'élève">');
            } else {
              $('#eleve-photo').html('<i class="fa fa-user fa-5x text-muted"></i>');
            }
            
            // Mettre à jour les informations sur les frais scolaires
            if (data.frais_info) {
              $('#frais-container').show();
              $('#session-scolaire').text(data.frais_info.session_scolaire || 'Session actuelle');
              
              // Vérifier si les valeurs sont des nombres avant d'appliquer toLocaleString
              var totalPaye = typeof data.frais_info.total_paye === 'number' ? data.frais_info.total_paye.toLocaleString('fr-FR') : '0';
              var totalDu = typeof data.frais_info.total_du === 'number' ? data.frais_info.total_du.toLocaleString('fr-FR') : '0';
              var resteAPayer = typeof data.frais_info.reste_a_payer === 'number' ? data.frais_info.reste_a_payer.toLocaleString('fr-FR') : '0';
              
              $('#total-paye').text(totalPaye);
              $('#total-du').text(totalDu);
              $('#reste-a-payer').text(resteAPayer);
              
              // Mettre à jour la barre de progression
              var pourcentage = data.frais_info.pourcentage_paye || 0;
              $('#frais-progress').css('width', pourcentage + '%').attr('aria-valuenow', pourcentage).text(pourcentage + '%');
              
              // Définir la classe CSS en fonction du statut de paiement
              $('#frais-info').removeClass('frais-en-ordre frais-en-retard frais-partiel');
              
              if (data.frais_info.est_en_ordre) {
                $('#frais-info').addClass('frais-en-ordre');
                $('#frais-status').html('<i class="fa fa-check-circle"></i> <strong>En ordre</strong> - Tous les frais ont été payés.');
                $('#frais-progress').addClass('progress-bar-success').removeClass('progress-bar-warning progress-bar-danger');
              } else if (pourcentage > 0) {
                $('#frais-info').addClass('frais-partiel');
                $('#frais-status').html('<i class="fa fa-exclamation-circle"></i> <strong>Paiement partiel</strong> - Certains frais restent à payer.');
                $('#frais-progress').addClass('progress-bar-warning').removeClass('progress-bar-success progress-bar-danger');
              } else {
                $('#frais-info').addClass('frais-en-retard');
                $('#frais-status').html('<i class="fa fa-times-circle"></i> <strong>En retard</strong> - Aucun paiement effectué.');
                $('#frais-progress').addClass('progress-bar-danger').removeClass('progress-bar-success progress-bar-warning');
              }
              
              // Remplir le tableau des paiements
              var paiementsHtml = '';
              if (data.frais_info.paiements && data.frais_info.paiements.length > 0) {
                $('#toggle-paiements').show();
                
                $.each(data.frais_info.paiements, function(index, paiement) {
                  try {
                    var datePaiement = new Date(paiement.date_paiement);
                    var dateFormatee = datePaiement.toLocaleDateString('fr-FR');
                    
                    var montantFormate = typeof paiement.montant === 'number' ? paiement.montant.toLocaleString('fr-FR') : paiement.montant;
                    
                    paiementsHtml += '<tr>' +
                      '<td>' + (paiement.type_frais || 'Non spécifié') + '</td>' +
                      '<td>' + montantFormate + ' Fc</td>' +
                      '<td>' + dateFormatee + '</td>' +
                      '<td>' + (paiement.description || '') + '</td>' +
                      '</tr>';
                  } catch (e) {
                    console.error('Erreur lors du formatage des données de paiement:', e);
                  }
                });
              } else {
                $('#toggle-paiements').hide();
              }
              
              $('#paiements-table-body').html(paiementsHtml);
            } else {
              $('#frais-container').hide();
            }
            
            // Afficher le bloc d'informations
            $('#eleve-info').fadeIn();
            
            // Enregistrer la présence si nécessaire
            if (data.presence_enregistree) {
              $('#result').html('<span class="text-success"><i class="fa fa-check-circle"></i> Présence enregistrée pour ' + data.nom + ' ' + data.prenom + '</span>');
            }
          } else {
            // Afficher le message d'erreur
            $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> ' + data.message + '</span>');
            console.log('Détails de l\'erreur:', data.debug || 'Aucun détail disponible');
          }
        },
        error: function(xhr, status, error) {
          $('.loading').hide();
          console.error('Erreur AJAX détaillée:', {
            status: status,
            error: error,
            responseText: xhr.responseText,
            statusCode: xhr.status,
            statusText: xhr.statusText
          });
          
          if (status === 'timeout') {
            $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> Délai d\'attente dépassé. Veuillez réessayer.</span>');
          } else {
            let errorMessage = 'Erreur de communication avec le serveur. Veuillez réessayer.';
            
            if (xhr.responseText) {
              try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                  errorMessage = response.message;
                }
              } catch (e) {
                // Si la réponse n'est pas du JSON valide, utiliser le texte brut
                if (xhr.responseText.length < 200) {
                  errorMessage = 'Erreur serveur: ' + xhr.responseText;
                }
              }
            }
            
            $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> ' + errorMessage + '</span>');
          }
        }
      });
    });
    
    // Afficher/masquer les détails des paiements
    $(document).on('click', '#toggle-paiements', function() {
      $('#paiement-details').slideToggle();
      var icon = $(this).find('i');
      if (icon.hasClass('fa-plus-circle')) {
        icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
        $(this).html('<i class="fa fa-minus-circle"></i> Masquer les détails des paiements');
      } else {
        icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
        $(this).html('<i class="fa fa-plus-circle"></i> Afficher les détails des paiements');
      }
    });
    
    Instascan.Camera.getCameras().then(function (cameras) {
      if (cameras.length > 0) {
        // Utiliser la caméra arrière si disponible (pour les mobiles)
        let selectedCamera = cameras[0];
        for (let i = 0; i < cameras.length; i++) {
          if (cameras[i].name && cameras[i].name.indexOf('back') !== -1) {
            selectedCamera = cameras[i];
            break;
          }
        }
        scanner.start(selectedCamera).catch(function(e) {
          console.error('Erreur lors du démarrage de la caméra:', e);
          $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> Erreur lors du démarrage de la caméra. Veuillez vérifier les permissions.</span>');
        });
      } else {
        console.error('Aucune caméra trouvée.');
        $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> Aucune caméra n\'a été trouvée sur cet appareil.</span>');
      }
    }).catch(function (e) {
      console.error(e);
      $('#result').html('<span class="error-message"><i class="fa fa-exclamation-triangle"></i> Erreur lors de l\'accès à la caméra: ' + e + '</span>');
    });
  });
</script>
</body>
</html>
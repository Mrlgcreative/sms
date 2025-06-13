<?php
// Vue pour l'ajout d'utilisateurs - Administrateur
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Ajouter un utilisateur</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  
  <!-- CSS Dependencies -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>plugins/iCheck/square/blue.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

  <style>
    /* Styles personnalisés pour une interface moderne */
    .content-wrapper {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      min-height: calc(100vh - 50px);
    }

    .box {
      border-radius: 10px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border: none;
      overflow: hidden;
    }

    .box-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 10px 10px 0 0;
      padding: 20px;
    }

    .box-header h3 {
      margin: 0;
      font-weight: 600;
      font-size: 24px;
    }

    .box-body {
      background: white;
      padding: 30px;
    }

    .form-group label {
      font-weight: 600;
      color: #4A5568;
      margin-bottom: 8px;
      display: block;
    }

    .form-control {
      border-radius: 8px;
      border: 2px solid #E2E8F0;
      padding: 12px 15px;
      font-size: 14px;
      transition: all 0.3s ease;
      margin-bottom: 5px;
    }

    .form-control:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
      outline: none;
    }

    .input-group {
      margin-bottom: 20px;
    }

    .input-group-addon {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      color: white;
      border-radius: 8px 0 0 8px;
      padding: 12px 15px;
    }

    .input-group .form-control {
      border-left: none;
      border-radius: 0 8px 8px 0;
      margin-bottom: 0;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 8px;
      padding: 12px 30px;
      font-weight: 600;
      font-size: 16px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-default {
      border-radius: 8px;
      padding: 12px 30px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .alert {
      border-radius: 8px;
      border: none;
      padding: 15px 20px;
      margin-bottom: 20px;
    }

    .alert-success {
      background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%);
      color: white;
    }

    .alert-danger {
      background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
      color: white;
    }

    .select2-container--default .select2-selection--single {
      border: 2px solid #E2E8F0;
      border-radius: 8px;
      height: 46px;
      padding: 8px 12px;
    }

    .select2-container--default .select2-selection--single:focus {
      border-color: #667eea;
    }

    .form-section {
      background: #f8fafc;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 20px;
      border-left: 4px solid #667eea;
    }

    .form-section h4 {
      color: #667eea;
      font-weight: 600;
      margin-bottom: 15px;
      font-size: 18px;
    }

    .file-upload-wrapper {
      position: relative;
      overflow: hidden;
      display: inline-block;
      width: 100%;
    }

    .file-upload-input {
      position: absolute;
      left: -9999px;
    }

    .file-upload-label {
      cursor: pointer;
      display: inline-block;
      padding: 12px 20px;
      background: linear-gradient(135deg, #a8e6cf 0%, #88d8a3 100%);
      color: white;
      border-radius: 8px;
      transition: all 0.3s ease;
      width: 100%;
      text-align: center;
      font-weight: 600;
    }

    .file-upload-label:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 15px rgba(136, 216, 163, 0.3);
    }

    .preview-image {
      max-width: 150px;
      max-height: 150px;
      border-radius: 8px;
      margin-top: 10px;
      border: 3px solid #E2E8F0;
    }

    .role-badge {
      display: inline-block;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      margin: 2px;
    }

    .role-admin { background: #ff6b6b; color: white; }
    .role-comptable { background: #4ecdc4; color: white; }
    .role-prefet { background: #45b7d1; color: white; }
    .role-director { background: #96ceb4; color: white; }
    .role-directrice { background: #feca57; color: white; }
    .role-percepteur { background: #ff9ff3; color: white; }
    .role-directeur_etude { background: #54a0ff; color: white; }

    @media (max-width: 768px) {
      .box-body {
        padding: 20px;
      }
      
      .col-md-6 {
        margin-bottom: 20px;
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
        <i class="fa fa-user-plus"></i> Ajouter un utilisateur
        <small>Créer un nouveau compte utilisateur</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Ajouter utilisateur</li>
      </ol>
    </section>

    <section class="content">
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

      <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h4><i class="icon fa fa-ban"></i> Erreurs de validation!</h4>
          <ul style="margin-bottom: 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row">
        <div class="col-md-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">
                <i class="fa fa-user-plus"></i> Formulaire d'ajout d'utilisateur
              </h3>
            </div>

            <div class="box-body">
              <form action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutUsers" method="post" enctype="multipart/form-data" id="userForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="row">
                  <!-- Section Informations de base -->
                  <div class="col-md-6">
                    <div class="form-section">
                      <h4><i class="fa fa-user"></i> Informations de base</h4>
                      
                      <div class="form-group">
                        <label for="username"><i class="fa fa-user"></i> Nom d'utilisateur *</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fa fa-user"></i>
                          </div>
                          <input type="text" class="form-control" id="username" name="username" 
                                 placeholder="Entrez le nom d'utilisateur" 
                                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                 required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="email"><i class="fa fa-envelope"></i> Adresse email *</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fa fa-envelope"></i>
                          </div>
                          <input type="email" class="form-control" id="email" name="email" 
                                 placeholder="Entrez l'adresse email" 
                                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                 required>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="password"><i class="fa fa-lock"></i> Mot de passe *</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fa fa-lock"></i>
                          </div>
                          <input type="password" class="form-control" id="password" name="password" 
                                 placeholder="Entrez le mot de passe" required>
                        </div>
                        <small class="text-muted">Minimum 6 caractères</small>
                      </div>

                      <div class="form-group">
                        <label for="confirm_password"><i class="fa fa-lock"></i> Confirmer le mot de passe *</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fa fa-lock"></i>
                          </div>
                          <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                 placeholder="Confirmez le mot de passe" required>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Section Rôle et permissions -->
                  <div class="col-md-6">
                    <div class="form-section">
                      <h4><i class="fa fa-users"></i> Rôle et permissions</h4>
                      
                      <div class="form-group">
                        <label for="role"><i class="fa fa-users"></i> Rôle de l'utilisateur *</label>
                        <select class="form-control" name="role" id="role" required>
                          <option value="">-- Sélectionner un rôle --</option>
                          <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>
                            Administrateur
                          </option>
                          <option value="comptable" <?php echo (isset($_POST['role']) && $_POST['role'] == 'comptable') ? 'selected' : ''; ?>>
                            Comptable
                          </option>
                          <option value="prefet" <?php echo (isset($_POST['role']) && $_POST['role'] == 'prefet') ? 'selected' : ''; ?>>
                            Préfet
                          </option>
                          <option value="director" <?php echo (isset($_POST['role']) && $_POST['role'] == 'director') ? 'selected' : ''; ?>>
                            Directeur
                          </option>
                          <option value="directrice" <?php echo (isset($_POST['role']) && $_POST['role'] == 'directrice') ? 'selected' : ''; ?>>
                            Directrice
                          </option>
                          <option value="percepteur" <?php echo (isset($_POST['role']) && $_POST['role'] == 'percepteur') ? 'selected' : ''; ?>>
                            Percepteur
                          </option>
                          <option value="directeur_etude" <?php echo (isset($_POST['role']) && $_POST['role'] == 'directeur_etude') ? 'selected' : ''; ?>>
                            Directeur d'Études
                          </option>
                        </select>
                        <small class="text-muted">Définit les permissions de l'utilisateur</small>
                      </div>

                      <div class="form-group">
                        <label>Aperçu des rôles disponibles :</label>
                        <div class="roles-preview">
                          <span class="role-badge role-admin">Administrateur</span>
                          <span class="role-badge role-comptable">Comptable</span>
                          <span class="role-badge role-prefet">Préfet</span>
                          <span class="role-badge role-director">Directeur</span>
                          <span class="role-badge role-directrice">Directrice</span>
                          <span class="role-badge role-percepteur">Percepteur</span>
                          <span class="role-badge role-directeur_etude">Directeur d'Études</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <!-- Section Informations personnelles -->
                  <div class="col-md-6">
                    <div class="form-section">
                      <h4><i class="fa fa-info-circle"></i> Informations personnelles</h4>
                      
                      <div class="form-group">
                        <label for="telephone"><i class="fa fa-phone"></i> Téléphone</label>
                        <div class="input-group">
                          <div class="input-group-addon">
                            <i class="fa fa-phone"></i>
                          </div>
                          <input type="tel" class="form-control" id="telephone" name="telephone" 
                                 placeholder="Ex: +243 123 456 789" 
                                 value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : ''; ?>">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="adresse"><i class="fa fa-map-marker"></i> Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3" 
                                  placeholder="Entrez l'adresse complète"><?php echo isset($_POST['adresse']) ? htmlspecialchars($_POST['adresse']) : ''; ?></textarea>
                      </div>
                    </div>
                  </div>

                  <!-- Section Photo de profil -->
                  <div class="col-md-6">
                    <div class="form-section">
                      <h4><i class="fa fa-camera"></i> Photo de profil</h4>
                      
                      <div class="form-group">
                        <label for="image">Choisir une photo</label>
                        <div class="file-upload-wrapper">
                          <input type="file" id="image" name="image" accept="image/*" class="file-upload-input">
                          <label for="image" class="file-upload-label">
                            <i class="fa fa-cloud-upload"></i> Cliquer pour choisir une image
                          </label>
                        </div>
                        <small class="text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 2MB</small>
                        <div id="imagePreview" style="display: none;">
                          <img id="previewImg" src="" alt="Aperçu" class="preview-image">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group text-center" style="margin-top: 30px;">
                      <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-save"></i> Créer l'utilisateur
                      </button>
                      <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="btn btn-default btn-lg">
                        <i class="fa fa-times"></i> Annuler
                      </a>
                    </div>
                  </div>
                </div>
              </form>
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
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">SGS - Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- JavaScript Dependencies -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>plugins/iCheck/icheck.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
  // Prévisualisation de l'image
  $('#image').change(function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        $('#previewImg').attr('src', e.target.result);
        $('#imagePreview').show();
      }
      reader.readAsDataURL(file);
      
      // Mettre à jour le label
      $('.file-upload-label').html('<i class="fa fa-check"></i> Image sélectionnée: ' + file.name);
    }
  });

  // Validation du formulaire
  $('#userForm').on('submit', function(e) {
    const password = $('#password').val();
    const confirmPassword = $('#confirm_password').val();
    
    // Vérifier que les mots de passe correspondent
    if (password !== confirmPassword) {
      e.preventDefault();
      showAlert('Les mots de passe ne correspondent pas!', 'danger');
      return false;
    }
    
    // Vérifier la longueur du mot de passe
    if (password.length < 6) {
      e.preventDefault();
      showAlert('Le mot de passe doit contenir au moins 6 caractères!', 'danger');
      return false;
    }
    
    // Afficher un message de chargement
    $('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Création en cours...').prop('disabled', true);
  });

  // Fonction pour afficher les alertes
  function showAlert(message, type) {
    const alertDiv = $('<div class="alert alert-' + type + ' alert-dismissible">' +
      '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
      '<h4><i class="icon fa ' + (type === 'success' ? 'fa-check' : 'fa-ban') + '"></i> ' +
      (type === 'success' ? 'Succès!' : 'Erreur!') + '</h4>' +
      message +
      '</div>');
    
    $('.content').prepend(alertDiv);
    
    // Auto-hide après 5 secondes
    setTimeout(function() {
      alertDiv.fadeOut();
    }, 5000);
  }

  // Animation au focus des champs
  $('.form-control').focus(function() {
    $(this).parent().addClass('focused');
  }).blur(function() {
    $(this).parent().removeClass('focused');
  });

  // Validation en temps réel de l'email
  $('#email').on('input', function() {
    const email = $(this).val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
      $(this).addClass('error');
    } else {
      $(this).removeClass('error');
    }
  });

  // Indicateur de force du mot de passe
  $('#password').on('input', function() {
    const password = $(this).val();
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.match(/[a-z]/)) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[^a-zA-Z0-9]/)) strength++;
    
    let strengthText = '';
    let strengthClass = '';
    
    switch(strength) {
      case 0:
      case 1:
        strengthText = 'Très faible';
        strengthClass = 'text-danger';
        break;
      case 2:
        strengthText = 'Faible';
        strengthClass = 'text-warning';
        break;
      case 3:
        strengthText = 'Moyen';
        strengthClass = 'text-info';
        break;
      case 4:
        strengthText = 'Fort';
        strengthClass = 'text-success';
        break;
      case 5:
        strengthText = 'Très fort';
        strengthClass = 'text-success';
        break;
    }
    
    $('#password').next('.strength-indicator').remove();
    if (password) {
      $('#password').after('<small class="strength-indicator ' + strengthClass + '">Force: ' + strengthText + '</small>');
    }
  });
});
</script>

<style>
/* Styles additionnels pour les animations */
.focused {
  transform: scale(1.02);
  transition: all 0.3s ease;
}

.form-control.error {
  border-color: #e74c3c !important;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1) !important;
}

.strength-indicator {
  display: block;
  margin-top: 5px;
  font-weight: 600;
}

.alert {
  animation: slideInDown 0.5s ease-out;
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.box {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>

</body>
</html>

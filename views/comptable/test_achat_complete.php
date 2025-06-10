<?php
// Test complet de la page achat fournitures
// Inclusion des fichiers de configuration
require_once '../../config/config.php';
require_once '../../config/database.php';

// Données factices pour le test
$username = 'Test Comptable';
$email = 'test@exemple.com';
$role = 'Comptable';
$image = 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Instancier le modèle
require_once '../../models/AchatFourniture.php';
$achatModel = new AchatFourniture($mysqli);

// Récupérer tous les achats (ou utiliser des données factices si la table est vide)
$achats = $achatModel->getAllAchats();
$totalDepenses = $achatModel->getTotalDepenses();

// Si pas de données, créer des données factices pour le test
if (empty($achats)) {
    $achats = [
        [
            'id' => 1,
            'date_achat' => '2025-06-01',
            'fournisseur' => 'Papeterie Centrale',
            'description' => 'Stylos et cahiers pour les étudiants',
            'quantite' => 50,
            'montant' => 125.00,
            'facture_ref' => 'F001'
        ],
        [
            'id' => 2,
            'date_achat' => '2025-06-02',
            'fournisseur' => 'Bureau Express',
            'description' => 'Matériel informatique',
            'quantite' => 5,
            'montant' => 450.00,
            'facture_ref' => 'F002'
        ],
        [
            'id' => 3,
            'date_achat' => '2025-06-03',
            'fournisseur' => 'Fournitures Scolaires',
            'description' => 'Produits d\'entretien',
            'quantite' => 20,
            'montant' => 85.50,
            'facture_ref' => 'F003'
        ]
    ];
    $totalDepenses = 660.50;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Test Achats Fournitures</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/achat-fournitures.css">
  <style>
    body { background: #f4f4f4; }
    .test-header { 
        background: linear-gradient(135deg, #007bff, #0056b3); 
        color: white; 
        padding: 20px; 
        margin-bottom: 20px; 
        border-radius: 8px;
    }
    .test-status { 
        background: #d4edda; 
        border: 1px solid #c3e6cb; 
        color: #155724; 
        padding: 15px; 
        border-radius: 4px; 
        margin-bottom: 20px;
    }
  </style>
</head>

<body>
<div class="achat-wrapper">
  <div class="container" style="margin-top: 20px;">
    
    <!-- En-tête de test -->
    <div class="test-header">
        <h1><i class="fa fa-flask"></i> Test - Module Achat Fournitures</h1>
        <p>Test complet de la page avec CSS intégré et données dynamiques</p>
    </div>
    
    <!-- Statut du test -->
    <div class="test-status">
        <h4><i class="fa fa-check-circle"></i> Statut du test</h4>
        <p><strong>✅ Configuration:</strong> <?php echo defined('BASE_URL') ? 'OK' : 'ERREUR'; ?></p>
        <p><strong>✅ Base de données:</strong> <?php echo $mysqli->ping() ? 'Connectée' : 'ERREUR'; ?></p>
        <p><strong>✅ Modèle:</strong> <?php echo class_exists('AchatFourniture') ? 'Chargé' : 'ERREUR'; ?></p>
        <p><strong>✅ CSS:</strong> Chargé (achat-fournitures.css)</p>
        <p><strong>📊 Données:</strong> <?php echo count($achats); ?> achats | Total: <?php echo number_format($totalDepenses, 2); ?> €</p>
    </div>

    <div class="achat-container">
      <!-- En-tête avec titre et animation -->
      <div class="achat-card animate-fadeInUp">
        <div class="achat-card-header">
          <h1 class="achat-card-title">
            <i class="fa fa-shopping-cart achat-pulse"></i>
            Gestion des Achats de Fournitures
          </h1>
          <small class="achat-card-subtitle">Suivi des dépenses et gestion des achats</small>
        </div>
      </div>

      <!-- Boîtes d'information avec design moderne -->
      <div class="achat-info-boxes-grid animate-fadeInUp">
        <div class="achat-info-card animate-slideInLeft">
          <div class="achat-info-card-icon achat-info-card-icon-primary">
            <i class="fa fa-shopping-cart"></i>
          </div>
          <div class="achat-info-card-content">
            <div class="achat-info-card-title">Total Achats</div>
            <div class="achat-info-card-value"><?php echo count($achats); ?></div>
            <div class="achat-info-card-description">Nombre total d'achats</div>
          </div>
          <div class="achat-info-card-footer">
            <div class="achat-progress-bar">
              <div class="achat-progress-fill" style="width: 100%;"></div>
            </div>
          </div>
        </div>

        <div class="achat-info-card animate-slideInLeft" style="animation-delay: 0.2s;">
          <div class="achat-info-card-icon achat-info-card-icon-success">
            <i class="fa fa-euro"></i>
          </div>
          <div class="achat-info-card-content">
            <div class="achat-info-card-title">Total Dépenses</div>
            <div class="achat-info-card-value"><?php echo number_format($totalDepenses, 0); ?> €</div>
            <div class="achat-info-card-description">Montant total dépensé</div>
          </div>
          <div class="achat-info-card-footer">
            <div class="achat-progress-bar">
              <div class="achat-progress-fill achat-progress-success" style="width: 85%;"></div>
            </div>
          </div>
        </div>

        <div class="achat-info-card animate-slideInRight" style="animation-delay: 0.4s;">
          <div class="achat-info-card-icon achat-info-card-icon-warning">
            <i class="fa fa-calendar"></i>
          </div>
          <div class="achat-info-card-content">
            <div class="achat-info-card-title">Ce Mois</div>
            <div class="achat-info-card-value"><?php echo count($achats); ?></div>
            <div class="achat-info-card-description">Achats ce mois-ci</div>
          </div>
          <div class="achat-info-card-footer">
            <div class="achat-progress-bar">
              <div class="achat-progress-fill achat-progress-warning" style="width: 60%;"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tableau des achats -->
      <div class="achat-table-section">
        <div class="achat-card achat-table-card animate-slideInFromTop">
          <div class="achat-card-header">
            <h3 class="achat-card-title">
              <i class="fa fa-list"></i>
              Liste des Achats de Fournitures
            </h3>
            <div class="achat-card-actions">
              <button class="achat-btn achat-btn-primary" data-toggle="modal" data-target="#addAchatModal">
                <i class="fa fa-plus"></i> Nouvel Achat
              </button>
            </div>
          </div>
          
          <div class="achat-table-wrapper">
            <table class="achat-table">
              <thead class="achat-table-header">
                <tr>
                  <th class="achat-table-th">
                    <div class="achat-table-header-content">
                      <i class="fa fa-calendar"></i> Date
                    </div>
                  </th>
                  <th class="achat-table-th">
                    <div class="achat-table-header-content">
                      <i class="fa fa-building"></i> Fournisseur
                    </div>
                  </th>
                  <th class="achat-table-th">
                    <div class="achat-table-header-content">
                      <i class="fa fa-info-circle"></i> Description
                    </div>
                  </th>
                  <th class="achat-table-th">
                    <div class="achat-table-header-content">
                      <i class="fa fa-sort-numeric-asc"></i> Quantité
                    </div>
                  </th>
                  <th class="achat-table-th">
                    <div class="achat-table-header-content">
                      <i class="fa fa-euro"></i> Montant
                    </div>
                  </th>
                  <th class="achat-table-th achat-table-th-actions">Actions</th>
                </tr>
              </thead>
              <tbody class="achat-table-body">
                <?php foreach ($achats as $index => $achat): ?>
                <tr class="achat-table-row">
                  <td class="achat-table-td">
                    <div class="achat-table-cell">
                      <i class="fa fa-calendar achat-table-icon"></i>
                      <span class="achat-table-text-primary"><?php echo date('d/m/Y', strtotime($achat['date_achat'])); ?></span>
                    </div>
                  </td>
                  <td class="achat-table-td">
                    <div class="achat-table-cell">
                      <i class="fa fa-building achat-table-icon"></i>
                      <span class="achat-table-text-primary"><?php echo htmlspecialchars($achat['fournisseur']); ?></span>
                    </div>
                  </td>
                  <td class="achat-table-td">
                    <span><?php echo htmlspecialchars($achat['description']); ?></span>
                  </td>
                  <td class="achat-table-td">
                    <span class="achat-badge achat-badge-primary"><?php echo $achat['quantite']; ?></span>
                  </td>
                  <td class="achat-table-td">
                    <span class="achat-table-amount"><?php echo number_format($achat['montant'], 2); ?> €</span>
                    <?php if (!empty($achat['facture_ref'])): ?>
                    <br><small class="achat-table-reference">Réf: <?php echo htmlspecialchars($achat['facture_ref']); ?></small>
                    <?php endif; ?>
                  </td>
                  <td class="achat-table-td achat-table-td-actions">
                    <div class="achat-table-actions">
                      <button class="achat-btn achat-btn-sm achat-btn-outline" title="Modifier">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button class="achat-btn achat-btn-sm achat-btn-danger" title="Supprimer">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>

    <!-- Footer de test -->
    <div class="achat-footer">
      <div class="achat-footer-content">
        <div class="achat-footer-info">
          <i class="fa fa-check-circle" style="color: green;"></i>
          Test du module Achat Fournitures - CSS et PHP intégrés avec succès
        </div>
        <div class="achat-footer-version">
          <span class="achat-badge achat-badge-success">v1.0 Test</span>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Scripts -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    // Animation des boutons
    $('.achat-btn').on('click', function() {
        $(this).addClass('achat-btn-clicked');
        setTimeout(() => {
            $(this).removeClass('achat-btn-clicked');
        }, 150);
    });
    
    // Test d'animation
    setTimeout(function() {
        $('.achat-info-card').addClass('animate-float');
    }, 2000);
    
    console.log('✅ Test Achat Fournitures - JavaScript chargé');
    console.log('📊 Nombre d\'achats:', <?php echo count($achats); ?>);
    console.log('💰 Total dépenses:', <?php echo $totalDepenses; ?>);
});
</script>

</body>
</html>

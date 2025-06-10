<?php

// Déterminer la page actuelle pour l'état actif du menu
$current_action = isset($_GET['action']) ? $_GET['action'] : 'accueil';
?>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard-admin.css">
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
      <li <?php echo ($current_action == 'accueil') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
          <i class="fa fa-dashboard"></i> <span>Accueil</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'achatFournitures') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=achatFournitures">
          <i class="fa fa-shopping-cart"></i> <span>Achat fourniture</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'inscris') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
          <i class="fa fa-users"></i> <span>Élèves</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'reinscris') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscris">
          <i class="fa fa-users"></i> <span>Élèves réinscrits</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'inscriptions') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
          <i class="fa fa-pencil"></i> <span>Inscription</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'ajoutpaiement') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
          <i class="fa fa-money"></i> <span>Paiement frais</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'paiements') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
          <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'reinscription') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=reinscription">
          <i class="fa fa-refresh"></i> <span>Réinscription</span>
        </a>
      </li>
      <li <?php echo ($current_action == 'rapportactions') ? 'class="active"' : ''; ?>>
        <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
          <i class="fa fa-file-text"></i> <span>Rapports</span>
        </a>
      </li>
    </ul>
  </section>
</aside>

<aside class="main-sidebar">
    <section class="sidebar">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg'; ?>" class="img-circle" alt="Image utilisateur">
        </div>
        <div class="pull-left info">
          <p><?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur'; ?></p>
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
        <?php
        // Déterminer le contrôleur actuel
        $controller = isset($_GET['controller']) ? strtolower($_GET['controller']) : '';
        $action = isset($_GET['action']) ? strtolower($_GET['action']) : '';
        $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
        
        // Menu pour le rôle de comptable
        if ($role == 'comptable') {
        ?>
          <li <?php echo ($action == 'accueil') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil">
              <i class="fa fa-dashboard"></i> <span>Accueil</span>
            </a>
          </li>
          <li <?php echo ($action == 'inscris') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
              <i class="fa fa-users"></i> <span>Élèves</span>
            </a>
          </li>
          <li <?php echo ($action == 'inscriptions') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
              <i class="fa fa-pencil"></i> <span>Inscription</span>
            </a>
          </li>
          <li <?php echo ($action == 'ajoutpaiement') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement">
              <i class="fa fa-money"></i> <span>Paiement frais</span>
            </a>
          </li>
          <li <?php echo ($action == 'paiements') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">
              <i class="fa fa-check-circle"></i> <span>Élèves en ordre</span>
            </a>
          </li>
          <li <?php echo ($action == 'rapportactions') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=rapportactions">
              <i class="fa fa-file"></i> <span>Rapports</span>
            </a>
          </li>
        <?php
        // Menu pour le rôle de directrice
        } elseif ($role == 'directrice') {
        ?>
          <li <?php echo ($action == 'accueil') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=accueil">
              <i class="fa fa-dashboard"></i> <span>Accueil</span>
            </a>
          </li>
          <li <?php echo ($action == 'eleves') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=directrice&action=eleves">
              <i class="fa fa-users"></i> <span>Élèves Maternelle</span>
            </a>
          </li>
        <?php
        // Menu pour le rôle d'admin
        } elseif ($role == 'admin') {
        ?>
          <li <?php echo ($action == 'accueil') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=accueil">
              <i class="fa fa-dashboard"></i> <span>Accueil</span>
            </a>
          </li>
          <li <?php echo ($action == 'users') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=users">
              <i class="fa fa-users"></i> <span>Utilisateurs</span>
            </a>
          </li>
          <li <?php echo ($action == 'settings') ? 'class="active"' : ''; ?>>
            <a href="<?php echo BASE_URL; ?>index.php?controller=admin&action=settings">
              <i class="fa fa-cogs"></i> <span>Paramètres</span>
            </a>
          </li>
        <?php
        }
        ?>
      </ul>
    </section>
  </aside>
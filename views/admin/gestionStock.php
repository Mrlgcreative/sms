<?php
// Vue pour la gestion de stock (livres, fournitures, matériel informatique)
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Instancier le modèle
require_once 'models/Stock.php';
$stockModel = new Stock($mysqli);

// Récupérer tous les articles
$items = $stockModel->getAllItems();
$itemsEnAlerte = $stockModel->getItemsEnAlerte();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion de Stock</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <!-- En-tête principal -->
  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Basculer la navigation</span>
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=profil" class="btn btn-default btn-flat">Profil</a>
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
  
  <!-- Barre latérale gauche -->
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
        <li class="header">MENU PRINCIPAL</li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <!-- Nouveaux liens ajoutés -->
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=achatFournitures">
            <i class="fa fa-shopping-cart"></i> <span>Achats Fournitures</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=gestionStock">
            <i class="fa fa-cubes"></i> <span>Gestion de Stock</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        <!-- Fin des nouveaux liens -->
      
        <li class="treeview">
          <a href="#">
            <i class="fa fa-dollar"></i> <span>Frais</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=Frais"><i class="fa fa-circle-o"></i> Voir Frais</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutfrais"><i class="fa fa-circle-o"></i> Ajouter frais</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutprofesseur"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=professeurs"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Préfets</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addPrefet"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=prefets"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-user-secret"></i> <span>Direction</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addDirecteur"><i class="fa fa-circle-o"></i> Ajouter Directeur</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directeurs"><i class="fa fa-circle-o"></i> Voir Directeurs</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=adddirectrice"><i class="fa fa-circle-o"></i> Ajouter Directrice</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=directrices"><i class="fa fa-circle-o"></i> Voir Directrices</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-calculator"></i> <span>Comptables</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addcomptable"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=comptable"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-table"></i> <span>Classes</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=addclasse"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=classes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i> <span>Cours</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutCours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
      </ul>
    </section>
  </aside>

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion de Stock
        <small>Livres, fournitures, matériel informatique</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Gestion de Stock</li>
      </ol>
    </section>

    <section class="content">
      <!-- Boîtes d'information -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-cubes"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Articles</span>
              <span class="info-box-number"><?php echo count($items); ?></span>
            </div>
          </div>
        </div>
        
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Articles en Alerte</span>
              <span class="info-box-number"><?php echo count($itemsEnAlerte); ?></span>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <!-- Onglets pour différentes catégories -->
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tous" data-toggle="tab">Tous les articles</a></li>
              <li><a href="#livres" data-toggle="tab">Livres</a></li>
              <li><a href="#fournitures" data-toggle="tab">Fournitures</a></li>
              <li><a href="#informatique" data-toggle="tab">Matériel Informatique</a></li>
              <li><a href="#mouvements" data-toggle="tab">Mouvements de stock</a></li>
            </ul>
            <div class="tab-content">
              <!-- Onglet Tous les articles -->
              <div class="tab-pane active" id="tous">
                <div class="box-header with-border">
                  <h3 class="box-title">Inventaire complet</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-ajouter-article">
                      <i class="fa fa-plus"></i> Ajouter un article
                    </button>
                  </div>
                </div>
                <div class="box-body">
                  <table id="stock-table" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Seuil d'alerte</th>
                        <th>Emplacement</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                      <tr class="<?php echo ($item['quantite'] <= $item['seuil_alerte']) ? 'bg-danger' : ''; ?>">
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['nom']; ?></td>
                        <td><?php echo $item['categorie']; ?></td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td><?php echo $item['seuil_alerte']; ?></td>
                        <td><?php echo $item['emplacement']; ?></td>
                        <td>
                          <button class="btn btn-xs btn-info" onclick="viewItem(<?php echo $item['id']; ?>)"><i class="fa fa-eye"></i></button>
                          <button class="btn btn-xs btn-warning" onclick="editItem(<?php echo $item['id']; ?>)"><i class="fa fa-edit"></i></button>
                          <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-mouvement" data-id="<?php echo $item['id']; ?>" data-nom="<?php echo $item['nom']; ?>"><i class="fa fa-exchange"></i></button>
                          <button class="btn btn-xs btn-danger" onclick="deleteItem(<?php echo $item['id']; ?>)"><i class="fa fa-trash"></i></button>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- Onglet Livres -->
              <div class="tab-pane" id="livres">
                <div class="box-header with-border">
                  <h3 class="box-title">Inventaire des livres</h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Seuil d'alerte</th>
                        <th>Emplacement</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                        <?php if ($item['categorie'] == 'Livre'): ?>
                        <tr class="<?php echo ($item['quantite'] <= $item['seuil_alerte']) ? 'bg-danger' : ''; ?>">
                          <td><?php echo $item['id']; ?></td>
                          <td><?php echo $item['nom']; ?></td>
                          <td><?php echo $item['quantite']; ?></td>
                          <td><?php echo $item['seuil_alerte']; ?></td>
                          <td><?php echo $item['emplacement']; ?></td>
                          <td>
                            <button class="btn btn-xs btn-info" onclick="viewItem(<?php echo $item['id']; ?>)"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-xs btn-warning" onclick="editItem(<?php echo $item['id']; ?>)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-mouvement" data-id="<?php echo $item['id']; ?>" data-nom="<?php echo $item['nom']; ?>"><i class="fa fa-exchange"></i></button>
                          </td>
                        </tr>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- Onglet Fournitures -->
              <div class="tab-pane" id="fournitures">
                <div class="box-header with-border">
                  <h3 class="box-title">Inventaire des fournitures</h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Seuil d'alerte</th>
                        <th>Emplacement</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                        <?php if ($item['categorie'] == 'Fourniture'): ?>
                        <tr class="<?php echo ($item['quantite'] <= $item['seuil_alerte']) ? 'bg-danger' : ''; ?>">
                          <td><?php echo $item['id']; ?></td>
                          <td><?php echo $item['nom']; ?></td>
                          <td><?php echo $item['quantite']; ?></td>
                          <td><?php echo $item['seuil_alerte']; ?></td>
                          <td><?php echo $item['emplacement']; ?></td>
                          <td>
                            <button class="btn btn-xs btn-info" onclick="viewItem(<?php echo $item['id']; ?>)"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-xs btn-warning" onclick="editItem(<?php echo $item['id']; ?>)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-mouvement" data-id="<?php echo $item['id']; ?>" data-nom="<?php echo $item['nom']; ?>"><i class="fa fa-exchange"></i></button>
                          </td>
                        </tr>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- Onglet Matériel Informatique -->
              <div class="tab-pane" id="informatique">
                <div class="box-header with-border">
                  <h3 class="box-title">Inventaire du matériel informatique</h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Seuil d'alerte</th>
                        <th>Emplacement</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item): ?>
                        <?php if ($item['categorie'] == 'Informatique'): ?>
                        <tr class="<?php echo ($item['quantite'] <= $item['seuil_alerte']) ? 'bg-danger' : ''; ?>">
                          <td><?php echo $item['id']; ?></td>
                          <td><?php echo $item['nom']; ?></td>
                          <td><?php echo $item['quantite']; ?></td>
                          <td><?php echo $item['seuil_alerte']; ?></td>
                          <td><?php echo $item['emplacement']; ?></td>
                          <td>
                            <button class="btn btn-xs btn-info" onclick="viewItem(<?php echo $item['id']; ?>)"><i class="fa fa-eye"></i></button>
                            <button class="btn btn-xs btn-warning" onclick="editItem(<?php echo $item['id']; ?>)"><i class="fa fa-edit"></i></button>
                            <button class="btn btn-xs btn-success" data-toggle="modal" data-target="#modal-mouvement" data-id="<?php echo $item['id']; ?>" data-nom="<?php echo $item['nom']; ?>"><i class="fa fa-exchange"></i></button>
                          </td>
                        </tr>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <!-- Onglet Mouvements de stock -->
              <div class="tab-pane" id="mouvements">
                <div class="box-header with-border">
                  <h3 class="box-title">Historique des mouvements</h3>
                </div>
                <div class="box-body">
                  <div class="form-group">
                    <label>Sélectionner un article</label>
                    <select class="form-control" id="select-item-mouvement">
                      <option value="">Tous les articles</option>
                      <?php foreach ($items as $item): ?>
                      <option value="<?php echo $item['id']; ?>"><?php echo $item['nom']; ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div id="mouvements-container">
                    <!-- Les mouvements seront chargés ici via AJAX -->
                    <p class="text-center">Sélectionnez un article pour voir ses mouvements</p>
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
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">St Sofie</a>.</strong> Tous droits réservés.
  </footer>

  <!-- Modal pour ajouter un article -->
  <div class="modal fade" id="modal-ajouter-article">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Ajouter un nouvel article</h4>
        </div>
        <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajouterArticle">
          <div class="modal-body">
            <div class="form-group">
              <label for="nom">Nom</label>
              <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
              <label for="categorie">Catégorie</label>
              <select class="form-control" id="categorie" name="categorie" required>
                <option value="">Sélectionner une catégorie</option>
                <option value="Livre">Livre</option>
                <option value="Fourniture">Fourniture</option>
                <option value="Informatique">Matériel Informatique</option>
              </select>
            </div>
            <div class="form-group">
              <label for="quantite">Quantité</label>
              <input type="number" class="form-control" id="quantite" name="quantite" min="0" required>
            </div>
            <div class="form-group">
              <label for="seuil_alerte">Seuil d'alerte</label>
              <input type="number" class="form-control" id="seuil_alerte" name="seuil_alerte" min="1" required>
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label for="emplacement">Emplacement</label>
              <input type="text" class="form-control" id="emplacement" name="emplacement">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal pour les mouvements de stock -->
  <div class="modal fade" id="modal-mouvement">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Enregistrer un mouvement</h4>
        </div>
        <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=Admin&action=enregistrerMouvement">
          <div class="modal-body">
            <input type="hidden" id="item_id" name="item_id">
            <div class="form-group">
              <label>Article</label>
              <p id="item_nom" class="form-control-static"></p>
            </div>
            <div class="form-group">
              <label for="type_mouvement">Type de mouvement</label>
              <select class="form-control" id="type_mouvement" name="type_mouvement" required>
                <option value="entree">Entrée en stock</option>
                <option value="sortie">Sortie de stock</option>
              </select>
            </div>
            <div class="form-group">
              <label for="quantite_mouvement">Quantité</label>
              <input type="number" class="form-control" id="quantite_mouvement" name="quantite" min="1" required>
            </div>
            <div class="form-group">
              <label for="motif">Motif</label>
              <textarea class="form-control" id="motif" name="motif" rows="3" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- DataTables -->
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<script>
  $(function () {
    // Initialiser DataTables
    $('#stock-table').DataTable({
      'paging'      : true,
      'lengthChange': true,
      'searching'   : true,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'language': {
        'url': '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
      }
    });
    
    // Gérer le modal de mouvement
    $('#modal-mouvement').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var nom = button.data('nom');
      
      var modal = $(this);
      modal.find('#item_id').val(id);
      modal.find('#item_nom').text(nom);
    });
    
    // Charger les mouvements d'un article
    $('#select-item-mouvement').change(function() {
      var itemId = $(this).val();
      if (itemId) {
        $.ajax({
          url: '<?php echo BASE_URL; ?>index.php?controller=Admin&action=getMouvementsParItem',
          type: 'GET',
          data: { item_id: itemId },
          success: function(response) {
            $('#mouvements-container').html(response);
          },
          error: function() {
            $('#mouvements-container').html('<p class="text-danger">Erreur lors du chargement des mouvements</p>');
          }
        });
      } else {
        $('#mouvements-container').html('<p class="text-center">Sélectionnez un article pour voir ses mouvements</p>');
      }
    });
  });
  
  // Fonctions pour les actions sur les articles
  function viewItem(id) {
    window.location.href = '<?php echo BASE_URL; ?>index.php?controller=Admin&action=voirArticle&id=' + id;
  }
  
  function editItem(id) {
    window.location.href = '<?php echo BASE_URL; ?>index.php?controller=Admin&action=modifierArticle&id=' + id;
  }
  
  function deleteItem(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
      window.location.href = '<?php echo BASE_URL; ?>index.php?controller=Admin&action=supprimerArticle&id=' + id;
    }
  }
</script>
</body>
</html>
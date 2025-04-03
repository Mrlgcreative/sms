<?php
// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Administrateur';
$image = isset($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

// Vérifier si l'ID de l'élève est défini
if (!isset($eleve) || empty($eleve)) {
    header('Location: ' . BASE_URL . 'index.php?controller=Admin&action=eleves');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Profil Élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil" class="logo">
      <span class="logo-mini"><b>SGS</b></span>
      <span class="logo-lg"><b>Système</b> Gestion</span>
    </a>
    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Navigation</span>
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
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=parents">
            <i class="fa fa-users"></i> <span>Parents</span>
          </a>
        </li>
        
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
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutcours"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=cours"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>
        
        <li class="treeview">
          <a href="#">
            <i class="fa fa-users"></i> <span>Employés</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=ajoutemployes"><i class="fa fa-circle-o"></i> Ajouter</a></li>
            <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=employes"><i class="fa fa-circle-o"></i> Voir</a></li>
          </ul>
        </li>

        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=rapportactions">
            <i class="fa fa-file-text"></i> <span>Rapports</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Profil de l'élève
        <small>Informations détaillées</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=eleves">Élèves</a></li>
        <li class="active">Profil</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" src="<?php echo BASE_URL; ?>dist/img/student-avatar.png" alt="Photo de l'élève">
              <h3 class="profile-username text-center"><?php echo $eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']; ?></h3>
              <p class="text-muted text-center">Élève</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Classe</b> <a class="pull-right"><?php echo $eleve['classe']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right"><?php echo $eleve['section']; ?></a>
                </li>
                <li class="list-group-item">
                  <b>Option</b> <a class="pull-right"><?php echo $eleve['option_nom']; ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=Admin&action=editeleve&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-block"><b>Modifier</b></a>
            </div>
          </div>

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations des parents</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-user margin-r-5"></i> Père</strong>
              <p class="text-muted"><?php echo $eleve['nom_pere']; ?></p>
              <hr>
              <strong><i class="fa fa-phone margin-r-5"></i> Contact du père</strong>
              <p class="text-muted"><?php echo $eleve['contact_pere']; ?></p>
              <hr>
              <strong><i class="fa fa-user margin-r-5"></i> Mère</strong>
              <p class="text-muted"><?php echo $eleve['nom_mere']; ?></p>
              <hr>
              <strong><i class="fa fa-phone margin-r-5"></i> Contact de la mère</strong>
              <p class="text-muted"><?php echo $eleve['contact_mere']; ?></p>
            </div>
          </div>
        </div>

        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#details" data-toggle="tab">Détails</a></li>
              <li><a href="#frais" data-toggle="tab">Frais scolaires</a></li>
              <li><a href="#resultats" data-toggle="tab">Résultats</a></li>
              <li><a href="#documents" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="details">
                <div class="box-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="box box-info">
                        <div class="box-header with-border">
                          <h3 class="box-title">Informations personnelles</h3>
                        </div>
                        <div class="box-body">
                          <table class="table table-bordered">
                            <tr>
                              <th style="width: 150px">Nom</th>
                              <td><?php echo $eleve['nom']; ?></td>
                            </tr>
                            <tr>
                              <th>Post-nom</th>
                              <td><?php echo $eleve['post_nom']; ?></td>
                            </tr>
                            <tr>
                              <th>Prénom</th>
                              <td><?php echo $eleve['prenom']; ?></td>
                            </tr>
                            <tr>
                              <th>Date de naissance</th>
                              <td><?php echo $eleve['date_naissance']; ?></td>
                            </tr>
                            <tr>
                              <th>Lieu de naissance</th>
                              <td><?php echo $eleve['lieu_naissance']; ?></td>
                            </tr>
                            <tr>
                              <th>Adresse</th>
                              <td><?php echo $eleve['adresse']; ?></td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="box box-success">
                        <div class="box-header with-border">
                          <h3 class="box-title">Informations scolaires</h3>
                        </div>
                        <div class="box-body">
                          <table class="table table-bordered">
                            <tr>
                              <th style="width: 150px">Matricule</th>
                              <td><?php echo $eleve['id']; ?></td>
                            </tr>
                            <tr>
                              <th>Classe</th>
                              <td><?php echo $eleve['classe']; ?></td>
                            </tr>
                            <tr>
                              <th>Section</th>
                              <td><?php echo $eleve['section']; ?></td>
                            </tr>
                            <tr>
                              <th>Option</th>
                              <td><?php echo $eleve['option_nom']; ?></td>
                            </tr>
                            <tr>
                              <th>Année scolaire</th>
                              <td><?php echo isset($eleve['annee_scolaire']) ? $eleve['annee_scolaire'] : date('Y') . '-' . (date('Y') + 1); ?></td>
                            </tr>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="tab-pane" id="frais">
                <div class="box-body">
                  <div class="callout callout-info">
                    <h4>Information!</h4>
                    <p>Cette section affichera les informations sur les frais scolaires de l'élève.</p>
                  </div>
                  <!-- Ici, vous pouvez ajouter un tableau des paiements de frais scolaires -->
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Type de frais</th>
                        <th>Montant</th>
                        <th>Statut</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Vous pouvez remplir ce tableau avec des données réelles de la base de données -->
                      <tr>
                        <td colspan="4" class="text-center">Aucune donnée disponible</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane" id="resultats">
                <div class="box-body">
                  <div class="callout callout-info">
                    <h4>Information!</h4>
                    <p>Cette section affichera les résultats scolaires de l'élève.</p>
                  </div>
                  <!-- Ici, vous pouvez ajouter un tableau des résultats scolaires -->
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Période</th>
                        <th>Cours</th>
                        <th>Note</th>
                        <th>Maximum</th>
                        <th>Pourcentage</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Vous pouvez remplir ce tableau avec des données réelles de la base de données -->
                      <tr>
                        <td colspan="5" class="text-center">Aucune donnée disponible</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="tab-pane" id="documents">
                <div class="box-body">
                  <div class="callout callout-info">
                    <h4>Information!</h4>
                    <p>Cette section affichera les documents relatifs à l'élève.</p>
                  </div>
                  <!-- Liste des documents -->
                  <ul class="list-group">
                    <li class="list-group-item">
                      <i class="fa fa-file-pdf-o"></i> Bulletin scolaire
                      <span class="pull-right">
                        <button class="btn btn-xs btn-primary"><i class="fa fa-download"></i> Télécharger</button>
                      </span>
                    </li>
                    <li class="list-group-item">
                      <i class="fa fa-file-pdf-o"></i> Certificat de scolarité
                      <span class="pull-right">
                        <button class="btn btn-xs btn-primary"><i class="fa fa-download"></i> Télécharger</button>
                      </span>
                    </li>
                  </ul>
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

<!-- jQuery 3 -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo BASE_URL; ?>bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo BASE_URL; ?>dist/js/demo.js"></script>
</script>
</body>
</html>
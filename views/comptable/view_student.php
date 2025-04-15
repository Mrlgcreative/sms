<?php
// Vérifier si une session est déjà active
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Utilisateur';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Profil de l'élève</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
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
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">
            <i class="fa fa-users"></i> <span>Élèves</span>
          </a>
        </li>
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscriptions">
            <i class="fa fa-pencil"></i> <span>Inscription</span>
          </a>
        </li>
        <li>
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
        Profil de l'élève
        <small><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']); ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=inscris">Élèves</a></li>
        <li class="active">Profil de l'élève</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-4">
          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img class="profile-user-img img-responsive img-circle" 
                   src="<?php echo !empty($eleve['photo']) ? BASE_URL . $eleve['photo'] : 'dist/img/avatar5.png'; ?>" 
                   alt="Photo de l'élève">

              <h3 class="profile-username text-center"><?php echo htmlspecialchars($eleve['nom'] . ' ' . $eleve['post_nom'] . ' ' . $eleve['prenom']); ?></h3>

              <p class="text-muted text-center"><?php echo htmlspecialchars($eleve['classe_nom'] ?? 'Non assigné'); ?></p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Matricule</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['matricule'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Date de naissance</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['date_naissance']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Lieu de naissance</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['lieu_naissance']); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Section</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['section'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Option</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['option_nom'] ?? 'Non assigné'); ?></a>
                </li>
                <li class="list-group-item">
                  <b>Adresse</b> <a class="pull-right"><?php echo htmlspecialchars($eleve['adresse']); ?></a>
                </li>
              </ul>

              <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=modifierEleve&id=<?php echo $eleve['id']; ?>" class="btn btn-primary btn-block"><b>Modifier</b></a>
            </div>
          </div>

          <!-- Parents Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Informations des parents</h3>
            </div>
            <div class="box-body">
              <strong><i class="fa fa-user margin-r-5"></i> Père</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['nom_pere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-phone margin-r-5"></i> Contact du père</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['contact_pere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-user margin-r-5"></i> Mère</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['nom_mere'] ?? 'Non renseigné'); ?></p>

              <hr>

              <strong><i class="fa fa-phone margin-r-5"></i> Contact de la mère</strong>
              <p class="text-muted"><?php echo htmlspecialchars($eleve['contact_mere'] ?? 'Non renseigné'); ?></p>
            </div>
          </div>
        </div>
        
        <div class="col-md-8">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#paiements" data-toggle="tab">Paiements</a></li>
              <li><a href="#notes" data-toggle="tab">Notes</a></li>
              <li><a href="#documents" data-toggle="tab">Documents</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="paiements">
                <div class="box-header">
                  <h3 class="box-title">Historique des paiements</h3>
                  <div class="box-tools">
                    <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=ajoutpaiement&eleve_id=<?php echo $eleve['id']; ?>" class="btn btn-success btn-sm">
                      <i class="fa fa-plus"></i> Nouveau paiement
                    </a>
                  </div>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Référence</th>
                        <th>Statut</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($paiements)): ?>
                        <tr>
                          <td colspan="6" class="text-center">Aucun paiement enregistré</td>
                        </tr>
                      <?php else: ?>
                        <?php foreach ($paiements as $paiement): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($paiement['date_paiement']); ?></td>
                            <td><?php echo htmlspecialchars($paiement['type_paiement']); ?></td>
                            <td><?php echo number_format($paiement['montant'], 0, ',', ' ') . ' $'; ?></td>
                            <td><?php echo htmlspecialchars($paiement['reference']); ?></td>
                            <td>
                              <?php if ($paiement['statut'] == 'Payé'): ?>
                                <span class="label label-success">Payé</span>
                              <?php elseif ($paiement['statut'] == 'En attente'): ?>
                                <span class="label label-warning">En attente</span>
                              <?php else: ?>
                                <span class="label label-danger">Annulé</span>
                              <?php endif; ?>
                            </td>
                            <td>
                              <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=detailPaiement&id=<?php echo $paiement['id']; ?>" class="btn btn-info btn-xs">
                                <i class="fa fa-eye"></i>
                              </a>
                              <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=recu&paiement_id=<?php echo isset($paiement['id']) ? $paiement['id'] : ''; ?>" class="btn btn-default btn-xs">
                                <i class="fa fa-print"></i>
                              </a>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="tab-pane" id="notes">
                <div class="box-header">
                  <h3 class="box-title">Notes et évaluations</h3>
                </div>
                <div class="box-body">
                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Matière</th>
                        <th>Évaluation</th>
                        <th>Note</th>
                        <th>Coefficient</th>
                        <th>Commentaire</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (empty($notes)): ?>
                        <tr>
                          <td colspan="6" class="text-center">Aucune note enregistrée</td>
                        </tr>
                      <?php else: ?>
                        <?php foreach ($notes as $note): ?>
                          <tr>
                            <td><?php echo htmlspecialchars($note['date_evaluation']); ?></td>
                            <td><?php echo htmlspecialchars($note['matiere_nom']); ?></td>
                            <td><?php echo htmlspecialchars($note['evaluation_nom']); ?></td>
                            <td><?php echo htmlspecialchars($note['note']); ?>/<?php echo htmlspecialchars($note['note_max']); ?></td>
                            <td><?php echo htmlspecialchars($note['coefficient']); ?></td>
                            <td><?php echo htmlspecialchars($note['commentaire'] ?? ''); ?></td>
                          </tr>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </tbody>
                  </table>
                </div>
              </div>
              
              <div class="tab-pane" id="documents">
                <div class="box-header">
                  <h3 class="box-title">Documents de l'élève</h3>
                </div>
                <div class="box-body">
                  <p class="text-center">Fonctionnalité en cours de développement</p>
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
</div>

<!-- jQuery 3 -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
</body>
</html>
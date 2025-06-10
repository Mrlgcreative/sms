<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Erreur de connexion: " . $mysqli->connect_error);
}

// Récupérer le nombre total de paiements
$result = $mysqli->query("SELECT COUNT(*) AS total_paiements FROM paiements_frais");
$row = $result->fetch_assoc();
$total_paiements = $row['total_paiements'];

// Récupérer le montant total des paiements
$result = $mysqli->query("SELECT SUM(amount_paid) AS montant_total FROM paiements_frais");
$row = $result->fetch_assoc();
$montant_total = $row['montant_total'] ?? 0;

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
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>St Sofie | Modifier Paiement</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'navbar.php'; ?>

  <?php include 'sidebar.php'; ?>

  <!-- Contenu principal -->
  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Modifier un paiement
        <small>Mettre à jour les informations de paiement</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements">Paiements</a></li>
        <li class="active">Modifier paiement</li>
      </ol>
    </section>

    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <!-- Afficher les messages d'erreur ou de succès -->
          <?php if (isset($_GET['error']) && isset($_GET['message'])): ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
              <h4><i class="icon fa fa-ban"></i> Erreur!</h4>
              <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
            </div>
          <?php endif; ?>
          
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Formulaire de modification de paiement</h3>
            </div>
            <form role="form" method="post" action="<?php echo BASE_URL; ?>index.php?controller=comptable&action=updatePaiement">
              <div class="box-body">
                <input type="hidden" name="paiement_id" value="<?php echo $paiement['id']; ?>">
                
                <div class="form-group">
                  <label for="eleve_id">Élève</label>
                  <select class="form-control" id="eleve_id" name="eleve_id" required>
                    <option value="">Sélectionner un élève</option>
                    <?php foreach ($eleves as $eleve): ?>
                      <option value="<?php echo $eleve['id']; ?>" <?php echo ($eleve['id'] == $paiement['eleve_nom']) ? 'selected' : ''; ?>>
                        <?php echo $eleve['nom'] . ' ' . $eleve['prenom'] . ' - ' . $eleve['classe'] . ' ' . $eleve['option_nom'] . ' (' . $eleve['section'] . ')'; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="frais_id">Type de frais</label>
                  <select class="form-control" id="frais_id" name="frais_id" required>
                    <option value="">Sélectionner un type de frais</option>
                    <?php foreach ($frais as $f): ?>
                      <option value="<?php echo $f['id']; ?>" <?php echo ($f['id'] == $paiement['description']) ? 'selected' : ''; ?>>
                        <?php echo $f['description'] . ' - ' . $f['montant'] . ' ' . $f['devise']; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="amount_paid">Montant payé</label>
                  <input type="number" class="form-control" id="amount_paid" name="amount_paid" value="<?php echo $paiement['amount_paid']; ?>" required>
                </div>
                
                <div class="form-group">
                  <label for="payment_date">Date de paiement</label>
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="date" class="form-control pull-right" id="payment_date" name="payment_date" value="<?php echo $paiement['payment_date']; ?>" required>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="mois">Mois</label>
                  <select class="form-control" id="mois" name="mois" required>
                    <option value="">Sélectionner un mois</option>
                    <?php foreach ($mois as $m): ?>
                      <option value="<?php echo $m; ?>" <?php echo ($m == $paiement['mois']) ? 'selected' : ''; ?>>
                        <?php echo $m; ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="notes">Notes</label>
                  <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo isset($paiement['notes']) ? $paiement['notes'] : ''; ?></textarea>
                </div>
              </div>
              
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements" class="btn btn-default">Annuler</a>
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
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- bootstrap datepicker -->
<script src="bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>

<script>
  $(function () {
    // Initialisation du datepicker
    $('#payment_date').datepicker({
      autoclose: true,
      format: 'yyyy-mm-dd'
    });
  });
</script>
</body>
</html>
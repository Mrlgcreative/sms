<?php
// Connexion à la base de données
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Forcer la section à "primaire" pour n'afficher que les professeurs du primaire
$section = "primaire";

// Récupération des professeurs de la section primaire uniquement
$professeurs = [];
$professeurs_query = "SELECT * FROM professeurs WHERE section = 'primaire' ORDER BY nom, prenom";
$professeurs_result = $mysqli->query($professeurs_query);

if ($professeurs_result) {
    while ($row = $professeurs_result->fetch_assoc()) {
        $professeurs[] = $row;
    }
}

// Fermer la connexion
$mysqli->close();

// Vérification de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Initialiser les variables de session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Utilisateur';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : 'email@exemple.com';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Director';

// Utiliser l'image de la session ou l'image par défaut
$image = isset($_SESSION['image']) && !empty($_SESSION['image']) ? $_SESSION['image'] : 'dist/img/user2-160x160.jpg';
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>SGS | Gestion des Professeurs</title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/AdminLTE.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-purple sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil" class="logo">
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
                  <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=profil" class="btn btn-default btn-flat">Profil</a>
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
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil">
            <i class="fa fa-dashboard"></i> <span>Tableau de bord</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=eleves">
            <i class="fa fa-child"></i> <span>Élèves</span>
          </a>
        </li>
        
        <li class="active">
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">
            <i class="fa fa-graduation-cap"></i> <span>Professeurs</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=classes">
            <i class="fa fa-table"></i> <span>Classes</span>
          </a>
        </li>
 
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=evenementsScolaires">
            <i class="fa fa-calendar"></i> <span>Événements Scolaires</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=absences">
            <i class="fa fa-clock-o"></i> <span>Gestion des Absences</span>
          </a>
        </li>
        
        <li>
          <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=discipline">
            <i class="fa fa-gavel"></i> <span>Discipline</span>
          </a>
        </li>
      </ul>
    </section>
  </aside>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Gestion des Professeurs
        <small>Liste des professeurs<?php echo !empty($section) ? ' - Section ' . ucfirst($section) : ''; ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=accueil"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li class="active">Professeurs</li>
      </ol>
    </section>

    <section class="content">
      <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <?php echo $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
      <?php endif; ?>
      
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Liste des professeurs</h3>
              <div class="box-tools">
                <div class="btn-group">
                  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    Filtrer par section <span class="caret"></span>
                  </button>
                  <ul class="dropdown-menu">
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs">Toutes les sections</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=maternelle">Maternelle</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=primaire">Primaire</a></li>
                    <li><a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=professeurs&section=secondaire">Secondaire</a></li>
                  </ul>
                </div>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalAjouter">
                  <i class="fa fa-plus"></i> Ajouter un professeur
                </button>
              </div>
            </div>
            <div class="box-body">
              <table id="professeurs-table" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Section</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($professeurs)): ?>
                    <?php foreach ($professeurs as $professeur): ?>
                      <tr>
                        <td><?php echo $professeur['id']; ?></td>
                        <td><?php echo htmlspecialchars($professeur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['email']); ?></td>
                        <td><?php echo htmlspecialchars($professeur['contact']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($professeur['section'])); ?></td>
                        <td>
                          <div class="btn-group">
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=voirProfesseur&id=<?php echo $professeur['id']; ?>" class="btn btn-info btn-sm" title="Voir détails">
                              <i class="fa fa-eye"></i>
                            </a>
                            <a href="<?php echo BASE_URL; ?>index.php?controller=Director&action=presencesProfesseurs&professeur_id=<?php echo $professeur['id']; ?>" class="btn btn-primary btn-sm" title="Voir présences">
                              <i class="fa fa-clock-o"></i>
                            </a>
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modalModifier" data-id="<?php echo $professeur['id']; ?>" title="Modifier">
                                <i class="fa fa-edit"></i> 
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalSupprimer" data-id="<?php echo $professeur['id']; ?>" title="Supprimer">
                                <i class="fa fa-trash"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="7" class="text-center">Aucun professeur trouvé</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

<!-- Modal Ajouter Professeur -->
<div class="modal fade" id="modalAjouter" tabindex="-1" role="dialog" aria-labelledby="modalAjouterLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalAjouterLabel">Ajouter un nouveau professeur</h4>
      </div>
      <form id="formAjouter" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=ajouterProfesseur" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row">
            <!-- Colonne gauche -->
            <div class="col-md-6">
              <h5><strong>Informations personnelles</strong></h5>
              
              <div class="form-group">
                <label for="ajouter_nom">Nom <span class="text-red">*</span></label>
                <input type="text" class="form-control" id="ajouter_nom" name="nom" required>
              </div>
              
              <div class="form-group">
                <label for="ajouter_prenom">Prénom <span class="text-red">*</span></label>
                <input type="text" class="form-control" id="ajouter_prenom" name="prenom" required>
              </div>
              
              <div class="form-group">
                <label for="ajouter_date_naissance">Date de naissance <span class="text-red">*</span></label>
                <input type="date" class="form-control" id="ajouter_date_naissance" name="date_naissance" required>
              </div>
              
              <div class="form-group">
                <label for="ajouter_lieu_naissance">Lieu de naissance</label>
                <input type="text" class="form-control" id="ajouter_lieu_naissance" name="lieu_naissance">
              </div>
              
              <div class="form-group">
                <label for="ajouter_sexe">Sexe <span class="text-red">*</span></label>
                <select class="form-control" id="ajouter_sexe" name="sexe" required>
                  <option value="">Sélectionner</option>
                  <option value="M">Masculin</option>
                  <option value="F">Féminin</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="ajouter_situation_matrimoniale">Situation matrimoniale</label>
                <select class="form-control" id="ajouter_situation_matrimoniale" name="situation_matrimoniale">
                  <option value="">Sélectionner</option>
                  <option value="Célibataire">Célibataire</option>
                  <option value="Marié(e)">Marié(e)</option>
                  <option value="Divorcé(e)">Divorcé(e)</option>
                  <option value="Veuf/Veuve">Veuf/Veuve</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="ajouter_nationalite">Nationalité <span class="text-red">*</span></label>
                <input type="text" class="form-control" id="ajouter_nationalite" name="nationalite" required>
              </div>
            </div>
            
            <!-- Colonne droite -->
            <div class="col-md-6">
              <h5><strong>Contact et informations professionnelles</strong></h5>
              
              <div class="form-group">
                <label for="ajouter_email">Email <span class="text-red">*</span></label>
                <input type="email" class="form-control" id="ajouter_email" name="email" required>
              </div>
              
              <div class="form-group">
                <label for="ajouter_contact">Téléphone <span class="text-red">*</span></label>
                <input type="tel" class="form-control" id="ajouter_contact" name="contact" required>
              </div>
              
              <div class="form-group">
                <label for="ajouter_adresse">Adresse</label>
                <textarea class="form-control" id="ajouter_adresse" name="adresse" rows="3"></textarea>
              </div>
              
              <div class="form-group">
                <label for="ajouter_section">Section <span class="text-red">*</span></label>
                <select class="form-control" id="ajouter_section" name="section" required>
                  <option value="">Sélectionner une section</option>
                  <option value="maternelle">Maternelle</option>
                  <option value="primaire">Primaire</option>
                  <option value="secondaire">Secondaire</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="ajouter_diplome">Diplôme</label>
                <input type="text" class="form-control" id="ajouter_diplome" name="diplome">
              </div>
              
              <div class="form-group">
                <label for="ajouter_specialite">Spécialité</label>
                <input type="text" class="form-control" id="ajouter_specialite" name="specialite">
              </div>
              
              <div class="form-group">
                <label for="ajouter_date_embauche">Date d'embauche</label>
                <input type="date" class="form-control" id="ajouter_date_embauche" name="date_embauche" value="<?php echo date('Y-m-d'); ?>">
              </div>
              
              <div class="form-group">
                <label for="ajouter_salaire">Salaire (FCFA)</label>
                <input type="number" class="form-control" id="ajouter_salaire" name="salaire" min="0" step="1000">
              </div>
              
              <div class="form-group">
                <label for="ajouter_statut">Statut</label>
                <select class="form-control" id="ajouter_statut" name="statut">
                  <option value="Actif">Actif</option>
                  <option value="Inactif">Inactif</option>
                  <option value="Suspendu">Suspendu</option>
                  <option value="Congé">En congé</option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="ajouter_image">Photo du professeur</label>
                <input type="file" class="form-control" id="ajouter_image" name="image" accept="image/*">
                <small class="help-block">Formats acceptés: JPG, JPEG, PNG, GIF. Taille max: 2MB</small>
                <div id="image-preview-ajouter" style="margin-top: 10px;"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">
            <i class="fa fa-save"></i> Enregistrer
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Modifier Professeur -->
<div class="modal fade" id="modalModifier" tabindex="-1" role="dialog" aria-labelledby="modalModifierLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalModifierLabel">Modifier le professeur</h4>
      </div>
      <form id="formModifier" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=modifierProfesseur">
        <div class="modal-body">
          <input type="hidden" id="modifier_id" name="id">
          
          <div class="form-group">
            <label for="modifier_nom">Nom:</label>
            <input type="text" class="form-control" id="modifier_nom" name="nom" required>
          </div>
          
          <div class="form-group">
            <label for="modifier_prenom">Prénom:</label>
            <input type="text" class="form-control" id="modifier_prenom" name="prenom" required>
          </div>
          
          <div class="form-group">
            <label for="modifier_email">Email:</label>
            <input type="email" class="form-control" id="modifier_email" name="email" required>
          </div>
          
          <div class="form-group">
            <label for="modifier_contact">Téléphone:</label>
            <input type="text" class="form-control" id="modifier_contact" name="contact" required>
          </div>
          
          <div class="form-group">
            <label for="modifier_section">Section:</label>
            <select class="form-control" id="modifier_section" name="section" required>
              <option value="maternelle">Maternelle</option>
              <option value="primaire">Primaire</option>
              <option value="secondaire">Secondaire</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="modifier_adresse">Adresse:</label>
            <textarea class="form-control" id="modifier_adresse" name="adresse" rows="3"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning">Modifier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Supprimer Professeur -->
<div class="modal fade" id="modalSupprimer" tabindex="-1" role="dialog" aria-labelledby="modalSupprimerLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="modalSupprimerLabel">Supprimer le professeur</h4>
      </div>
      <form id="formSupprimer" method="POST" action="<?php echo BASE_URL; ?>index.php?controller=Director&action=supprimerProfesseur">
        <div class="modal-body">
          <input type="hidden" id="supprimer_id" name="id">
          <p>Êtes-vous sûr de vouloir supprimer ce professeur ?</p>
          <p><strong>Cette action est irréversible.</strong></p>
          <div id="professeur-info"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-danger">Supprimer</button>
        </div>
      </form>
    </div>
  </div>
</div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 1.0.0
    </div>
    <strong>Copyright &copy; <?php echo date('Y'); ?> <a href="#">Système de Gestion Scolaire</a>.</strong> Tous droits réservés.
  </footer>
</div>

<!-- Scripts JavaScript -->
<script src="<?php echo BASE_URL; ?>bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="<?php echo BASE_URL; ?>bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo BASE_URL; ?>dist/js/adminlte.min.js"></script>

<script>
$(document).ready(function() {
    // Initialiser DataTable
    $('#professeurs-table').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "language": {
            "search": "Rechercher:",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            },
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
            "infoFiltered": "(filtré de _MAX_ entrées au total)",
            "emptyTable": "Aucune donnée disponible dans le tableau",
            "zeroRecords": "Aucun enregistrement correspondant trouvé"
        }
    });

    // Initialiser les datepickers
    $('#ajouter_date_naissance, #ajouter_date_embauche').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
        language: 'fr'
    });

    // Gestionnaire pour le modal d'ajout
    $('#modalAjouter').on('show.bs.modal', function (event) {
        // Réinitialiser le formulaire
        $('#formAjouter')[0].reset();
        $('#image-preview-ajouter').empty();
        // Définir la date d'embauche par défaut
        $('#ajouter_date_embauche').val('<?php echo date('Y-m-d'); ?>');
    });

    // Gestionnaire pour le modal de modification
    $('#modalModifier').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var professeurId = button.data('id');
        
        // Récupérer les données du professeur depuis la ligne du tableau
        var row = button.closest('tr');
        var cells = row.find('td');
        
        var nom = cells.eq(1).text();
        var prenom = cells.eq(2).text();
        var email = cells.eq(3).text();
        var contact = cells.eq(4).text();
        var section = cells.eq(5).text().toLowerCase();
        
        // Remplir le formulaire
        $('#modifier_id').val(professeurId);
        $('#modifier_nom').val(nom);
        $('#modifier_prenom').val(prenom);
        $('#modifier_email').val(email);
        $('#modifier_contact').val(contact);
        $('#modifier_section').val(section);
    });

    // Gestionnaire pour le modal de suppression
    $('#modalSupprimer').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var professeurId = button.data('id');
        
        // Récupérer les données du professeur depuis la ligne du tableau
        var row = button.closest('tr');
        var cells = row.find('td');
        
        var nom = cells.eq(1).text();
        var prenom = cells.eq(2).text();
        var email = cells.eq(3).text();
        var section = cells.eq(5).text();
        
        // Remplir les informations
        $('#supprimer_id').val(professeurId);
        $('#professeur-info').html(
            '<div class="alert alert-warning">' +
            '<strong>Nom:</strong> ' + nom + '<br>' +
            '<strong>Prénom:</strong> ' + prenom + '<br>' +
            '<strong>Email:</strong> ' + email + '<br>' +
            '<strong>Section:</strong> ' + section +
            '</div>'
        );
    });

    // Prévisualisation de l'image pour l'ajout
    $('#ajouter_image').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview-ajouter').html(
                    '<img src="' + e.target.result + '" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">'
                );
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview-ajouter').empty();
        }
    });

    // Gestionnaire de soumission du formulaire d'ajout
    $('#formAjouter').on('submit', function(e) {
        e.preventDefault();
        
        // Validation côté client
        var nom = $('#ajouter_nom').val().trim();
        var prenom = $('#ajouter_prenom').val().trim();
        var email = $('#ajouter_email').val().trim();
        var contact = $('#ajouter_contact').val().trim();
        var section = $('#ajouter_section').val();
        var sexe = $('#ajouter_sexe').val();
        var date_naissance = $('#ajouter_date_naissance').val();
        var nationalite = $('#ajouter_nationalite').val().trim();
        
        if (!nom || !prenom || !email || !contact || !section || !sexe || !date_naissance || !nationalite) {
            alert('Veuillez remplir tous les champs obligatoires marqués d\'un astérisque (*).');
            return false;
        }
        
        // Validation de l'email
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Veuillez saisir une adresse email valide.');
            return false;
        }
        
        // Validation de la date de naissance (pas dans le futur)
        var today = new Date();
        var birthDate = new Date(date_naissance);
        if (birthDate >= today) {
            alert('La date de naissance ne peut pas être dans le futur.');
            return false;
        }
        
        // Soumettre le formulaire avec FormData pour gérer les fichiers
        var formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    if (jsonResponse.success) {
                        $('#modalAjouter').modal('hide');
                        alert('Professeur ajouté avec succès');
                        location.reload();
                    } else {
                        alert('Erreur: ' + (jsonResponse.message || 'Une erreur est survenue'));
                    }
                } catch (e) {
                    // Si la réponse n'est pas du JSON, c'est probablement une redirection HTML
                    $('#modalAjouter').modal('hide');
                    location.reload();
                }
            },
            error: function() {
                alert('Erreur de communication avec le serveur');
            }
        });
    });

    // Gestionnaire de soumission du formulaire de modification
    $('#formModifier').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalModifier').modal('hide');
                    location.reload();
                } else {
                    alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
                }
            },
            error: function() {
                alert('Erreur de communication avec le serveur');
            }
        });
    });

    // Gestionnaire de soumission du formulaire de suppression
    $('#formSupprimer').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#modalSupprimer').modal('hide');
                    location.reload();
                } else {
                    alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
                }
            },
            error: function() {
                alert('Erreur de communication avec le serveur');
            }
        });
    });
});
</script>

</body>
</html>
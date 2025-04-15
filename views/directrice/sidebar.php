<div class="list-group">
    <a href="<?= BASE_URL ?>index.php?controller=Directrice&action=accueil" class="list-group-item list-group-item-action <?= $action === 'accueil' ? 'active' : '' ?>">
        <i class="fas fa-tachometer-alt"></i> Tableau de bord
    </a>
    <a href="<?= BASE_URL ?>index.php?controller=Directrice&action=profil" class="list-group-item list-group-item-action <?= $action === 'profil' ? 'active' : '' ?>">
        <i class="fas fa-user-circle"></i> Mon Profil
    </a>
    <!-- Autres liens du menu -->
</div>
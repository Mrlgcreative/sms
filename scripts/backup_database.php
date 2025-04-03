<?php
/**
 * Script de sauvegarde automatique de la base de données
 * À exécuter via une tâche CRON ou une tâche planifiée Windows
 */

// Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'college1';
$backup_dir = __DIR__ . '/../backups';
$date = date('Y-m-d_H-i-s');
$backup_file = $backup_dir . '/backup_' . $date . '.sql';

// Créer le répertoire de sauvegarde s'il n'existe pas
if (!file_exists($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

// Nettoyer les anciennes sauvegardes (garder seulement les 10 dernières)
$files = glob($backup_dir . '/backup_*.sql');
if (count($files) > 10) {
    // Trier par date (le plus ancien en premier)
    usort($files, function($a, $b) {
        return filemtime($a) - filemtime($b);
    });
    
    // Supprimer les plus anciennes
    $filesToDelete = array_slice($files, 0, count($files) - 10);
    foreach ($filesToDelete as $file) {
        unlink($file);
    }
}

// Exécuter la commande de sauvegarde
$command = "mysqldump -h {$db_host} -u {$db_user}";
if ($db_pass) {
    $command .= " -p'{$db_pass}'";
}
$command .= " {$db_name} > {$backup_file}";

exec($command, $output, $return_var);

// Vérifier si la sauvegarde a réussi
if ($return_var === 0) {
    echo "Sauvegarde réussie: {$backup_file}\n";
    
    // Journaliser la sauvegarde
    require_once __DIR__ . '/../includes/logger.php';
    $logger = new Logger();
    $logger->info("Sauvegarde de la base de données réussie", ['file' => $backup_file]);
    
    // Compresser le fichier de sauvegarde
    $zip = new ZipArchive();
    $zipFile = $backup_file . '.zip';
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        $zip->addFile($backup_file, basename($backup_file));
        $zip->close();
        
        // Supprimer le fichier SQL non compressé
        unlink($backup_file);
        echo "Compression réussie: {$zipFile}\n";
    } else {
        echo "Échec de la compression\n";
    }
} else {
    echo "Échec de la sauvegarde\n";
    
    // Journaliser l'échec
    require_once __DIR__ . '/../includes/logger.php';
    $logger = new Logger();
    $logger->error("Échec de la sauvegarde de la base de données", ['error' => implode("\n", $output)]);
}
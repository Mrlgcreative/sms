
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rapport d'activités</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php include 'views/include/sidebar.php'; ?>
    <div class="container">
        <h2>Rapport d'activités</h2>
        <div class="report-section">
            <h3>Inscriptions</h3>
            <p>Total des utilisateurs inscrits : <?php echo $totalUsers; ?></p>
        </div>
        <div class="report-section">
            <h3>Frais</h3>
            <p>Total des frais définis : <?php echo $totalFees; ?></p>
        </div>
        <div class="report-section">
            <h3>Paiements</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>ID Élève</th>
                        <th>ID Frais</th>
                        <th>Montant payé</th>
                        <th>Date de paiement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($totalPayments as $payment): ?>
                        <tr>
                            <td><?php echo $payment['id']; ?></td>
                            <td><?php echo $payment['eleve_id']; ?></td>
                            <td><?php echo $payment['frais_id']; ?></td>
                            <td><?php echo $payment['amount_paid']; ?></td>
                            <td><?php echo $payment['payment_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="report-section">
            <h3>Présences</h3>
            <p>Total des présences enregistrées : <?php echo $totalAttendances; ?></p>
        </div>
        <div class="report-section">
            <h3>Rapport Financier</h3>
            <p>Revenu total : <?php echo $financialReport; ?> $</p>
        </div>
    </div>
  
</body>
</html>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .receipt-header h1 {
            margin: 0;
            color: #333;
        }
        .receipt-body {
            margin-bottom: 20px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .receipt-info div {
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            flex: 1;
            text-align: center;
        }
        .print-button {
            text-align: center;
            margin-top: 20px;
        }
        .print-button button {
            padding: 10px 20px;
            background-color: #3c8dbc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        @media print {
            .print-button {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <h1>REÇU DE PAIEMENT</h1>
            <p>École St Sophie</p>
            <p>N° <?php echo $receipt_number; ?></p>
        </div>
        
        <div class="receipt-body">
            <div class="receipt-info">
                <div>
                    <p><strong>Date:</strong> <?php echo isset($paiement['payment_date']) ? date('d/m/Y', strtotime($paiement['payment_date'])) : date('d/m/Y'); ?></p>
                    <p><strong>Élève:</strong> <?php echo isset($paiement['eleve_nom']) ? htmlspecialchars($paiement['eleve_nom']) : 'Non spécifié'; ?></p>
                    <p><strong>Classe:</strong> <?php echo isset($paiement['classe_nom']) ? htmlspecialchars($paiement['classe_nom']) : 'Non spécifié'; ?></p>
                </div>
                <div>
                    <p><strong>Section:</strong> <?php echo isset($paiement['section']) ? htmlspecialchars($paiement['section']) : 'Non spécifié'; ?></p>
                    <p><strong>Option:</strong> <?php echo isset($paiement['option_nom']) ? htmlspecialchars($paiement['option_nom']) : 'Non spécifié'; ?></p>
                    <p><strong>Mois:</strong> <?php echo isset($paiement['mois']) ? htmlspecialchars($paiement['mois']) : 'Non spécifié'; ?></p>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo isset($paiement['frais_description']) ? htmlspecialchars($paiement['frais_description']) : 'Non spécifié'; ?></td>
                        <td><?php echo isset($paiement['amount_paid']) ? number_format($paiement['amount_paid'], 2) . ' $' : '0.00 $'; ?></td>
                    </tr>
                    <tr>
                        <th>Total</th>
                        <td><strong><?php echo isset($paiement['amount_paid']) ? number_format($paiement['amount_paid'], 2) . ' $' : '0.00 $'; ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="signature">
            <div>
                <p>_______________________</p>
                <p>Signature du Comptable</p>
            </div>
            <div>
                <p>_______________________</p>
                <p>Cachet de l'École</p>
            </div>
        </div>
        
        <div class="receipt-footer">
            <p>Ce reçu est une preuve officielle de paiement. Veuillez le conserver soigneusement.</p>
            <p>Pour toute question, veuillez contacter le service comptable de l'école.</p>
        </div>
    </div>
    
    <div class="print-button">
        <button onclick="window.print()">Imprimer le Reçu</button>
        <button onclick="window.location.href='<?php echo BASE_URL; ?>index.php?controller=comptable&action=paiements'">Retour aux Paiements</button>
    </div>
</body>
</html>

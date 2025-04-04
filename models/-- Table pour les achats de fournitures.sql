-- Table pour les achats de fournitures
CREATE TABLE IF NOT EXISTS achats_fournitures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_achat DATE NOT NULL,
    fournisseur VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    quantite INT NOT NULL,
    montant DECIMAL(10, 2) NOT NULL,
    facture_ref VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table pour la gestion de stock
CREATE TABLE IF NOT EXISTS stock_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    categorie VARCHAR(100) NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    seuil_alerte INT DEFAULT 10,
    description TEXT,
    emplacement VARCHAR(100),
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table pour les mouvements de stock
CREATE TABLE IF NOT EXISTS stock_mouvements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    quantite INT NOT NULL,
    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motif TEXT,
    user_id INT,
    FOREIGN KEY (item_id) REFERENCES stock_items(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table pour les événements scolaires
CREATE TABLE IF NOT EXISTS evenements_scolaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    lieu VARCHAR(255),
    responsable VARCHAR(255),
    couleur VARCHAR(50) DEFAULT '#3c8dbc',
    statut ENUM('planifie', 'en_cours', 'termine', 'annule') DEFAULT 'planifie',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
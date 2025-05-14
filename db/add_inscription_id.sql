-- Ajout de la colonne inscription_id à la table eleves
ALTER TABLE eleves ADD COLUMN inscription_id INT NULL;

-- Ajout de la contrainte de clé étrangère
ALTER TABLE eleves ADD CONSTRAINT fk_eleve_inscription 
FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) 
ON DELETE SET NULL ON UPDATE CASCADE;
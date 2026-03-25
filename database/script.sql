SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS locations;
DROP TABLE IF EXISTS vehicules;
DROP TABLE IF EXISTS utilisateurs;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE utilisateurs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicules (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    marque VARCHAR(50) NOT NULL,
    modele VARCHAR(50) NOT NULL,
    prix_journalier DECIMAL(10,2) NOT NULL,
    statut ENUM('disponible', 'loue') NOT NULL DEFAULT 'disponible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE locations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vehicule_id INT UNSIGNED NOT NULL,
    nom_client VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    CONSTRAINT fk_locations_vehicule
        FOREIGN KEY (vehicule_id) REFERENCES vehicules(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO utilisateurs (identifiant, mot_de_passe) VALUES
('david', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO vehicules (immatriculation, marque, modele, prix_journalier, statut) VALUES
('AB-123-CD', 'Toyota', 'Corolla', 25000.00, 'disponible'),
('CE-456-EF', 'Hyundai', 'Tucson', 45000.00, 'loue'),
('GH-789-IJ', 'Peugeot', '208', 30000.00, 'disponible'),
('KL-321-MN', 'Renault', 'Clio', 28000.00, 'disponible');

INSERT INTO locations (vehicule_id, nom_client, date_debut, date_fin) VALUES
(2, 'Fatou Ndiaye', '2026-03-20', '2026-03-28');
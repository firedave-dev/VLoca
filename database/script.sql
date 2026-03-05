CREATE DATABASE IF NOT EXISTS vloca;
USE vloca;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifiant VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(20) NOT NULL,
    marque VARCHAR(50),
    modele VARCHAR(50),
    prix_journalier DECIMAL(10,2),
    statut ENUM('disponible', 'loue') DEFAULT 'disponible'
);

CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicule_id INT,
    nom_client VARCHAR(100),
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
);


INSERT INTO utilisateurs (identifiant, mot_de_passe) 
VALUES ('david', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
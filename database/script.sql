CREATE DATABASE IF NOT EXISTS vloca;
USE vloca;

-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    identifiant VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL
);

-- Table des véhicules
CREATE TABLE IF NOT EXISTS vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    immatriculation VARCHAR(20) NOT NULL,
    marque VARCHAR(50),
    modele VARCHAR(50),
    prix_journalier DECIMAL(10,2),
    statut ENUM('disponible', 'loue') DEFAULT 'disponible'
);


INSERT INTO utilisateurs (identifiant, mot_de_passe) VALUES ('david', '1234');

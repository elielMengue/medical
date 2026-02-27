-- Création de la base de données
CREATE DATABASE IF NOT EXISTS gestion_medicale CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_medicale;

-- Table des utilisateurs (Admin, Médecins, Infirmiers, Major)
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- SHA1 pour compatibilité
    role ENUM('admin', 'medecin', 'major', 'infirmier') NOT NULL,
    matricule VARCHAR(50) UNIQUE,
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des patients
CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    adresse TEXT,
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des soins
CREATE TABLE IF NOT EXISTS soins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    infirmier_id INT NOT NULL,
    type_soin VARCHAR(100) NOT NULL,
    description TEXT,
    date_soin DATE NOT NULL,
    heure_soin TIME NOT NULL,
    numero_lit VARCHAR(20),
    statut ENUM('planifie', 'en_cours', 'effectue', 'annule') DEFAULT 'planifie',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE RESTRICT,
    FOREIGN KEY (infirmier_id) REFERENCES utilisateurs(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Table des antécédents médicaux
CREATE TABLE IF NOT EXISTS antecedents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    description TEXT,
    date_diagnostic DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Données de test (Mots de passe: admin123)
-- SHA1('admin123') = 

INSERT INTO utilisateurs (nom, prenom, email, password, role, matricule, telephone) VALUES
('Admin', 'System', 'admin@medical.com', '', 'admin', 'ADM001', '0102030405'),
('Dupont', 'Jean', 'medecin@medical.com', '', 'medecin', 'MED001', '0601020304'),
('Martin', 'Sophie', 'major@medical.com', '', 'major', 'MAJ001', '0611223344'),
('Dubois', 'Thomas', 'infirmier@medical.com', '', 'infirmier', 'INF001', '0655667788'),
('Leroy', 'Julie', 'infirmier2@medical.com', '', 'infirmier', 'INF002', '0699887766');

INSERT INTO patients (nom, prenom, date_naissance, adresse, telephone) VALUES
('DURAND', 'Paul', '1980-05-15', '12 Rue des Lilas, Paris', '0612345678'),
('ROBERT', 'Marie', '1992-11-20', '8 Avenue de la République, Lyon', '0698765432'),
('PETIT', 'Lucas', '2010-03-08', '5 Place de la Gare, Marseille', '0654321098'),
('MOREAU', 'Emma', '1955-07-30', '25 Boulevard Haussmann, Bordeaux', '0678901234');

INSERT INTO antecedents (patient_id, type, description, date_diagnostic) VALUES
(1, 'Allergie', 'Pénicilline', '2010-05-15'),
(1, 'Chirurgie', 'Appendicectomie', '2005-02-10'),
(2, 'Asthme', 'Asthme léger', '2000-01-01'),
(4, 'Diabète', 'Type 2', '2015-09-20');

INSERT INTO soins (patient_id, infirmier_id, type_soin, description, date_soin, heure_soin, numero_lit, statut, created_by) VALUES
(1, 4, 'Injection', 'Antibiotiques', CURRENT_DATE, '08:00:00', '101', 'planifie', 3),
(2, 5, 'Pansement', 'Changement pansement jambe droite', CURRENT_DATE, '09:30:00', '102', 'planifie', 3),
(4, 4, 'Prise de sang', 'NFS + Ionogramme', CURRENT_DATE, '10:00:00', '104', 'planifie', 2);

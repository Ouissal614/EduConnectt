CREATE DATABASE IF NOT EXISTS projet_synthese;
USE projet_synthese;
 CREATE TABLE if not exists administrateur (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    nom_admin VARCHAR(50),
    prenom_admin VARCHAR(50),
    email_admin VARCHAR(100),
    mot_de_passe_admin VARCHAR(255)
);
CREATE TABLE if not exists  parent (
    id_parent INT AUTO_INCREMENT PRIMARY KEY,
    nom_parent VARCHAR(50),
    prenom_parent VARCHAR(50),
    email_parent VARCHAR(100),
    mot_de_passe_parent VARCHAR(255),
    telephone_parent VARCHAR(20),
    login_compte_parent VARCHAR(50),
    statut_compte_parent VARCHAR(20)
);
CREATE TABLE IF NOT EXISTS eleve (
    id_eleve INT AUTO_INCREMENT PRIMARY KEY,
    nom_eleve VARCHAR(50),
    prenom_eleve VARCHAR(50),
    classe_eleve VARCHAR(20),
    id_parent INT,
    FOREIGN KEY (id_parent) REFERENCES parent(id_parent)
);

CREATE TABLE if not exists  professeur (
    id_prof INT AUTO_INCREMENT PRIMARY KEY,
    nom_prof VARCHAR(50),
    prenom_prof VARCHAR(50),
    email_prof VARCHAR(100)
);

CREATE TABLE if not exists matiere (
    id_matiere INT AUTO_INCREMENT PRIMARY KEY,
    nom_matiere VARCHAR(50),
    coefficient INT,
    id_prof INT,
    FOREIGN KEY (id_prof) REFERENCES professeur(id_prof)
);

CREATE TABLE if not exists  notes (
    id_note INT AUTO_INCREMENT PRIMARY KEY,
    valeur FLOAT,
    date_evaluation DATE,
    id_eleve INT,
    id_matiere INT,
    FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve),
    FOREIGN KEY (id_matiere) REFERENCES matiere(id_matiere)
);
CREATE TABLE if not exists  notification (
    id_notification INT AUTO_INCREMENT PRIMARY KEY,
    date_envoi DATETIME,
    message TEXT,
    type VARCHAR(50),
    lu BOOLEAN DEFAULT 0,
    id_admin INT,
    id_parent INT,
    FOREIGN KEY (id_admin) REFERENCES administrateur(id_admin),
    FOREIGN KEY (id_parent) REFERENCES parent(id_parent)
);
CREATE TABLE  if not exists absences (
    id_absence INT AUTO_INCREMENT PRIMARY KEY,
    date_absence DATE,
    motif VARCHAR(255),
    id_eleve INT,
    FOREIGN KEY (id_eleve) REFERENCES eleve(id_eleve)
);

INSERT INTO administrateur (nom_admin, prenom_admin, email_admin, mot_de_passe_admin)
VALUES ('Admin', 'System', 'admin@edu.com', '1234');

INSERT INTO parent (nom_parent, prenom_parent, email_parent, mot_de_passe_parent, telephone_parent, login_compte_parent, statut_compte_parent)
VALUES 
('El Amrani', 'Youssef', 'youssef@gmail.com', '1234', '0611111111', 'youssef', 'actif'),
('Bennani', 'Fatima', 'fatima@gmail.com', '1234', '0622222222', 'fatima', 'actif'),
('Farahi', 'Omar', 'omar@gmail.com', '1234', '0633333333', 'omar', 'actif');
INSERT INTO parent 
(nom_parent, prenom_parent, email_parent, mot_de_passe_parent, telephone_parent, login_compte_parent, statut_compte_parent)
VALUES 
('Zahra', 'El Fassi', 'zahra@gmail.com', '1234', '0644444444', 'zahra', 'actif');

INSERT INTO eleve (nom_eleve, prenom_eleve, classe_eleve, id_parent)
VALUES 
('Karim', 'El Amrani', '2AP', 1),
('Sara', 'Bennani', '1AP', 2),
('Sami', 'Farahi', 'A', 3);
INSERT INTO eleve (nom_eleve, prenom_eleve, classe_eleve, id_parent)
VALUES 
('Yassir', 'El Fassi', '2AP', 4);

INSERT INTO professeur (nom_prof, prenom_prof, email_prof)
VALUES 
('Hassan', 'El Idrissi', 'hassan@edu.com'),
('Mohamed', 'Alaoui', 'mohamed@edu.com'),
('Salma', 'Benali', 'salma@edu.com');

INSERT INTO matiere (nom_matiere, coefficient, id_prof)
VALUES 
('Maths', 4, 1),
('Physique', 3, 2),
('Informatique', 2, 3);

INSERT INTO notes (valeur, date_evaluation, id_eleve, id_matiere)
VALUES 
(15, '2026-01-10', 1, 1),
(12, '2026-01-11', 1, 2),
(8,  '2026-01-12', 1, 3),

(17, '2026-01-10', 2, 1),
(9,  '2026-01-11', 2, 2),
(14, '2026-01-12', 2, 3),

(10, '2026-01-10', 3, 1),
(6,  '2026-01-11', 3, 2),
(11, '2026-01-12', 3, 3);
INSERT INTO notes (valeur, date_evaluation, id_eleve, id_matiere)
VALUES 
(5, '2026-01-10', 4, 1),
(6, '2026-01-11', 4, 2),
(7, '2026-01-12', 4, 3);
INSERT INTO absences (date_absence, motif, id_eleve)
VALUES 
('2026-01-15', 'Maladie', 1),
('2026-01-16', 'Retard répété', 1),

('2026-01-15', 'Voyage familial', 2),
('2026-01-18', 'Maladie', 2),

('2026-01-20', 'Absence non justifiée', 3);

INSERT INTO notification (date_envoi, message, type, lu, id_admin, id_parent)
VALUES 
(NOW(), 'Votre enfant a une note faible en informatique', 'alerte', 0, 1, 1),
(NOW(), 'Bon travail pour votre enfant', 'info', 0, 1, 2),
(NOW(), 'Attention aux résultats', 'alerte', 0, 1, 3);


-- On affiche chaque élève avec les informations de son parent
-- Objectif : montrer la relation 1 parent → plusieurs élèves
SELECT 
    e.nom_eleve,
    e.prenom_eleve,
    e.classe_eleve,
    p.nom_parent,
    p.prenom_parent
FROM eleve e
JOIN parent p ON e.id_parent = p.id_parent;

-- On récupère toutes les notes avec :
-- le nom de l'élève + la matière + la note + la date

SELECT 
    e.nom_eleve,
    m.nom_matiere,
    n.valeur,
    n.date_evaluation
FROM notes n
JOIN eleve e ON n.id_eleve = e.id_eleve
JOIN matiere m ON n.id_matiere = m.id_matiere;


-- Calcul de la moyenne des notes par élève
-- On utilise AVG pour obtenir la moyenne générale

SELECT 
    e.nom_eleve,
    AVG(n.valeur) AS moyenne
FROM notes n
JOIN eleve e ON n.id_eleve = e.id_eleve
GROUP BY e.id_eleve;

-- Calcul de la moyenne en tenant compte des coefficients
-- Les matières importantes ont plus de poids

SELECT 
    e.nom_eleve,
    SUM(n.valeur * m.coefficient) / SUM(m.coefficient) AS moyenne_ponderee
FROM notes n
JOIN eleve e ON n.id_eleve = e.id_eleve
JOIN matiere m ON n.id_matiere = m.id_matiere
GROUP BY e.id_eleve;

-- Affichage du bulletin : élève + matières + notes + coefficients
-- Sert à générer un aperçu complet des résultats

SELECT 
    e.nom_eleve,
    m.nom_matiere,
    n.valeur,
    m.coefficient
FROM notes n
JOIN eleve e ON n.id_eleve = e.id_eleve
JOIN matiere m ON n.id_matiere = m.id_matiere
ORDER BY e.nom_eleve;

UPDATE parent
SET mot_de_passe_parent = '$2y$10$FrzqwRIDGAa79rqDHVw.lebzb9MePGDve3gWDKHqqcFG11dFwQUeK';
UPDATE parent 
SET mot_de_passe_parent = '$2y$10$8ihQCYLUoY0HYCAJm8c/m.Vc2nSrOLuJ9EY5AonHhQD1KwVvLEVZG'
WHERE login_compte_parent = 'zahra';

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempts INT DEFAULT 0,
    last_attempt DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_type ENUM('parent','admin') NOT NULL,
    user_id INT NULL,
    login VARCHAR(100),
    status ENUM('success','failed') NOT NULL,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
UPDATE administrateur
SET mot_de_passe_admin = '$2y$10$baLAs1yT/5YndUk.r30Wc.BjJQ5ssDdXKE7/w5GA7AfSMuVCiSGo.';

SELECT mot_de_passe_parent FROM parent;
SELECT * FROM login_logs;
SELECT * FROM login_attempts;
SELECT * FROM login_logs ORDER BY created_at DESC;
SELECT mot_de_passe_admin FROM administrateur;





SELECT 
    e.nom_eleve,
    SUM(n.valeur * m.coefficient) / SUM(m.coefficient) AS moyenne
FROM notes n
JOIN eleve e ON e.id_eleve = n.id_eleve
JOIN matiere m ON m.id_matiere = n.id_matiere
GROUP BY e.id_eleve;


SELECT e.id_eleve, COUNT(n.id_note)
FROM eleve e
LEFT JOIN notes n ON e.id_eleve = n.id_eleve
GROUP BY e.id_eleve;




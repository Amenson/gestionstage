CREATE DATABASE formatec_stages;
USE formatec_stages;

-- PAYS
CREATE TABLE Pays (
    codePays INT AUTO_INCREMENT PRIMARY KEY,
    libPays VARCHAR(50)
);

-- ETUDIANT
CREATE TABLE Etudiant (
    codeEtud INT AUTO_INCREMENT PRIMARY KEY,
    nomEtud VARCHAR(50),
    prenomEtud VARCHAR(50),
    sexeEtud VARCHAR(10),
    dateNaissEtud DATE,
    photoEtud VARCHAR(255),
    voieEtud VARCHAR(50),
    cpEtud VARCHAR(10),
    villeEtud VARCHAR(50),
    telEtud VARCHAR(20),
    mailEtud VARCHAR(100),
    password VARCHAR(255),
    statutEtud VARCHAR(20),
    fkPays INT,
    FOREIGN KEY (fkPays) REFERENCES Pays(codePays)
);

-- TYPE ENTREPRISE
CREATE TABLE TypeEntreprise (
    codeTypeEntr INT AUTO_INCREMENT PRIMARY KEY,
    libEntr VARCHAR(50)
);

-- DOMAINE ACTIVITE
CREATE TABLE DomaineActivite (
    codeDomaineAct INT AUTO_INCREMENT PRIMARY KEY,
    libDomaineAct VARCHAR(50)
);

-- ENTREPRISE
CREATE TABLE Entreprise (
    numSiret INT AUTO_INCREMENT PRIMARY KEY,
    nomEntreprise VARCHAR(100),
    numVoieEntreprise INT,
    voieEntreprise VARCHAR(100),
    cpEntreprise VARCHAR(10),
    villeEntreprise VARCHAR(50),
    telEntreprise VARCHAR(20),
    mailEntreprise VARCHAR(100),
    fkTypeEntreprise INT,
    fkDomaineAct INT,
    FOREIGN KEY (fkTypeEntreprise) REFERENCES TypeEntreprise(codeTypeEntr),
    FOREIGN KEY (fkDomaineAct) REFERENCES DomaineActivite(codeDomaineAct)
);

-- TYPE STAGE
CREATE TABLE TypeStage (
    codeTypeStage INT AUTO_INCREMENT PRIMARY KEY,
    libTypeStage VARCHAR(50),
    dureeStage VARCHAR(50)
);

-- STAGE
CREATE TABLE Stage (
    numOffre INT AUTO_INCREMENT PRIMARY KEY,
    libStage VARCHAR(100),
    dateParution DATE,
    periodeStage VARCHAR(50),
    moisStage VARCHAR(20),
    descStage TEXT,
    fonctionsStage TEXT,
    remunerationStage VARCHAR(50),
    mailContact VARCHAR(100),
    fkTypeStage INT,
    fkEntreprise INT,
    fkEtudiant INT NULL,
    FOREIGN KEY (fkTypeStage) REFERENCES TypeStage(codeTypeStage),
    FOREIGN KEY (fkEntreprise) REFERENCES Entreprise(numSiret),
    FOREIGN KEY (fkEtudiant) REFERENCES Etudiant(codeEtud)
);

-- COMPETENCE
CREATE TABLE Competence (
    codeCompet INT AUTO_INCREMENT PRIMARY KEY,
    typeCompet VARCHAR(50),
    libCompet VARCHAR(100)
);

-- EXIGER
CREATE TABLE Exiger (
    numOffre INT,
    codeCompet INT,
    degreMaitrise VARCHAR(20),
    PRIMARY KEY (numOffre, codeCompet),
    FOREIGN KEY (numOffre) REFERENCES Stage(numOffre),
    FOREIGN KEY (codeCompet) REFERENCES Competence(codeCompet)
);

-- NOTE STAGE
CREATE TABLE NoteStage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numOffre INT,
    numCritere VARCHAR(50),
    noteStage INT,
    FOREIGN KEY (numOffre) REFERENCES Stage(numOffre)
);

-- PROFESSEUR (ADMIN)
CREATE TABLE Professeur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50),
    password VARCHAR(255),
    grade VARCHAR(50)
);

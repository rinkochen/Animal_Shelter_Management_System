

CREATE USER IF NOT EXISTS gatechUser@localhost IDENTIFIED BY 'gatech123';

DROP DATABASE IF EXISTS `shelter_management_system`; 
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS shelter_management_system
    DEFAULT CHARACTER SET utf8mb4 
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE shelter_management_system;

GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `gatechuser`.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `shelter_management_system`.* TO 'gatechUser'@'localhost';
FLUSH PRIVILEGES;

-- Tables  

CREATE TABLE `USER` (
    Username VARCHAR(20) NOT NULL,
    Password VARCHAR(20) NOT NULL,
    StartDate DATETIME NOT NULL,
    FirstName VARCHAR(45) NOT NULL,
    LastName VARCHAR(45) NOT NULL,
    Email VARCHAR(45) NULL,
    PRIMARY KEY (Username));

CREATE TABLE EMPLOYEE (
    Username VARCHAR(20) NOT NULL,
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES `USER` (Username));

CREATE TABLE ADMIN (
    Username VARCHAR(20) NOT NULL,
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES EMPLOYEE (Username));

CREATE TABLE VOLUNTEER (
    Username VARCHAR(20) NOT NULL,
    PhoneNumber VARCHAR(20) NOT NULL,
    PRIMARY KEY (Username),
    FOREIGN KEY (Username) REFERENCES `USER` (Username));

CREATE TABLE HOURSWORKED (
    Username VARCHAR(20) NOT NULL,
    Date DATETIME NOT NULL,
    WorkHour FLOAT NULL,
    PRIMARY KEY (Username, Date),
    FOREIGN KEY (Username) REFERENCES `USER` (Username));

CREATE TABLE SPECIES (
    Species VARCHAR(20) NOT NULL,
    MaxCapacity INT NOT NULL,
    PRIMARY KEY (Species));

CREATE TABLE ANIMAL (
    PetID INT NOT NULL AUTO_INCREMENT,
    AlterationStatus BOOLEAN NOT NULL,
    Name VARCHAR(45) NOT NULL,
    Sex VARCHAR(10) NOT NULL,
    Age FLOAT NOT NULL,
    Description VARCHAR(200) NOT NULL,
    Species VARCHAR(20) NOT NULL,
    SurrenderReason VARCHAR(200) NOT NULL,
    SurrenderByAnimalControl BOOLEAN NOT NULL,
    SurrenderDate DATETIME NOT NULL,
    MicrochipID VARCHAR(50) NULL,
    Username VARCHAR(20) NOT NULL,
    PRIMARY KEY (PetID),
    FOREIGN KEY (Species) REFERENCES SPECIES (Species),
    FOREIGN KEY (Username) REFERENCES `USER` (Username));

CREATE TABLE BREEDTYPE (
    BreedType VARCHAR(50) NOT NULL,
    Species VARCHAR(20) NOT NULL,
    PRIMARY KEY (BreedType, Species),
    FOREIGN KEY (Species) REFERENCES SPECIES (Species));
    
CREATE TABLE BREED (
    BreedType VARCHAR(50) NOT NULL,
    PetID INT NOT NULL,
    Species VARCHAR(20) NOT NULL,
    PRIMARY KEY (BreedType, PetID),
    FOREIGN KEY (PetID) REFERENCES ANIMAL (PetID),
    FOREIGN KEY (BreedType) REFERENCES BREEDTYPE (BreedType),
    FOREIGN KEY (Species) REFERENCES SPECIES (Species));



CREATE TABLE VACCINE (
    VaccineType VARCHAR(50) NOT NULL,
    Species VARCHAR(20) NOT NULL,
    Required BOOLEAN NOT NULL,
    PRIMARY KEY (VaccineType, Species));


CREATE TABLE VACCINATION (
    VaccineType VARCHAR(50) NOT NULL,
    AdminDate DATETIME NOT NULL,
    Username VARCHAR(45) NOT NULL,
    VaccinationNumber VARCHAR(100) NULL,
    ExpDate DATETIME NOT NULL,
    PetID INT NOT NULL,
    PRIMARY KEY (VaccineType, AdminDate, PetID),
    FOREIGN KEY (Username) REFERENCES USER (Username),
    FOREIGN KEY (PetID) REFERENCES ANIMAL (PetID));


CREATE TABLE ADOPTER (
    Email VARCHAR(50) NOT NULL,
    FirstName VARCHAR(45) NOT NULL,
    LastName VARCHAR(45) NOT NULL,
    PhoneNum VARCHAR(20) NOT NULL,
    City VARCHAR(30) NOT NULL,
    Street VARCHAR(100) NOT NULL,
    State VARCHAR(20) NOT NULL,
    ZipCode VARCHAR(6) NOT NULL,
    CoApplicantFirstName VARCHAR(45) NULL,
    CoApplicantLastName VARCHAR(45) NULL,
    PRIMARY KEY (Email));

CREATE TABLE APPLICATION (
    ApplicationNum INT NOT NULL AUTO_INCREMENT,
    ApplicationStatus VARCHAR(20) NOT NULL,
    Date VARCHAR(12) NOT NULL,
    Email VARCHAR(50) NOT NULL,
    PRIMARY KEY (ApplicationNum),
    FOREIGN KEY (Email) REFERENCES ADOPTER (Email));


CREATE TABLE ADOPTION (
    ApplicationNum INT NOT NULL AUTO_INCREMENT,
    PetID INT NOT NULL,
    AdoptionDate DATETIME NOT NULL,
    AdoptionFee FLOAT NOT NULL,
    PRIMARY KEY (ApplicationNum, PetID),
    FOREIGN KEY (ApplicationNum) REFERENCES APPLICATION (ApplicationNum),
    FOREIGN KEY (PetID) REFERENCES ANIMAL (PetID));





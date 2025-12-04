
-- Create database 
CREATE DATABASE IF NOT EXISTS child_welfare
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE child_welfare;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- MAIN TABLES

CREATE TABLE district (
  district_id          INT UNSIGNED AUTO_INCREMENT,
  district_name        VARCHAR(100) NOT NULL,
  population           INT UNSIGNED,
  children_population  INT UNSIGNED,
  poverty_rate         DECIMAL(5,2),
  literacy_rate        DECIMAL(5,2),
  PRIMARY KEY (district_id),
  UNIQUE KEY uq_district_name (district_name)
) ENGINE=InnoDB;

CREATE TABLE local_crisis (
  crisis_id      INT UNSIGNED AUTO_INCREMENT,
  crisis_name    VARCHAR(100) NOT NULL,
  start_date     DATE,
  end_date       DATE,
  crisis_type    VARCHAR(50),
  severity_level VARCHAR(20),
  PRIMARY KEY (crisis_id)
) ENGINE=InnoDB;

CREATE TABLE school (
  school_id     INT UNSIGNED AUTO_INCREMENT,
  school_name   VARCHAR(200) NOT NULL,
  school_type   VARCHAR(50),
  dropout_rate  DECIMAL(5,2),
  mid_day_meal  CHAR(1),
  district_id   INT UNSIGNED,
  PRIMARY KEY (school_id),
  KEY idx_school_district (district_id),
  CONSTRAINT chk_mid_day_meal
    CHECK (mid_day_meal IN ('Y','N') OR mid_day_meal IS NULL),
  CONSTRAINT fk_school_district
    FOREIGN KEY (district_id)
    REFERENCES district(district_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE ngo (
  ngo_id         INT UNSIGNED AUTO_INCREMENT,
  ngo_name       VARCHAR(100) NOT NULL,
  type_service   VARCHAR(100),
  ngo_type       VARCHAR(50),
  ngo_service    VARCHAR(100),
  capacity       INT,
  funding_source VARCHAR(100),
  PRIMARY KEY (ngo_id)
) ENGINE=InnoDB;

CREATE TABLE labour (
  labour_id             INT UNSIGNED AUTO_INCREMENT,
  labor_type            VARCHAR(100) NOT NULL,
  site_type             VARCHAR(100),
  typical_hours_per_week INT,
  typical_wage_amount   DECIMAL(10,2),
  wage_period           VARCHAR(50),
  PRIMARY KEY (labour_id)
) ENGINE=InnoDB;

CREATE TABLE child (
  child_id        INT UNSIGNED AUTO_INCREMENT,
  child_name      VARCHAR(100) NOT NULL,
  age             INT,
  gender          VARCHAR(10),
  parental_status VARCHAR(50),
  grade_level     VARCHAR(20),
  school_id       INT UNSIGNED,
  PRIMARY KEY (child_id),
  KEY idx_child_school (school_id),
  CONSTRAINT chk_gender
    CHECK (gender IN ('Male','Female','Other') OR gender IS NULL),
  CONSTRAINT fk_child_school
    FOREIGN KEY (school_id)
    REFERENCES school(school_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- LINK TABLES

CREATE TABLE crisis_effects (
  crisis_id   INT UNSIGNED NOT NULL,
  district_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (crisis_id, district_id),
  KEY idx_ce_district (district_id),
  CONSTRAINT fk_ce_crisis
    FOREIGN KEY (crisis_id)
    REFERENCES local_crisis(crisis_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_ce_district
    FOREIGN KEY (district_id)
    REFERENCES district(district_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE ngo_operates_in (
  ngo_id      INT UNSIGNED NOT NULL,
  district_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (ngo_id, district_id),
  KEY idx_noi_district (district_id),
  CONSTRAINT fk_noi_ngo
    FOREIGN KEY (ngo_id)
    REFERENCES ngo(ngo_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_noi_district
    FOREIGN KEY (district_id)
    REFERENCES district(district_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE child_works_in (
  child_id  INT UNSIGNED NOT NULL,
  labour_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (child_id, labour_id),
  KEY idx_cwi_labour (labour_id),
  CONSTRAINT fk_cwi_child
    FOREIGN KEY (child_id)
    REFERENCES child(child_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_cwi_labour
    FOREIGN KEY (labour_id)
    REFERENCES labour(labour_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE support (
  ngo_id      INT UNSIGNED NOT NULL,
  school_id   INT UNSIGNED NOT NULL,
  description VARCHAR(255),
  start_date  DATE,
  end_date    DATE,
  PRIMARY KEY (ngo_id, school_id, start_date),
  KEY idx_support_school (school_id),
  CONSTRAINT fk_support_ngo
    FOREIGN KEY (ngo_id)
    REFERENCES ngo(ngo_id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT fk_support_school
    FOREIGN KEY (school_id)
    REFERENCES school(school_id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

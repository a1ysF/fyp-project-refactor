-- CryptoLearn / Sistem Pembelajaran Kriptografi Interaktif
-- MySQL schema for Railway or local XAMPP (database name: fyp)

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS badge_users;
DROP TABLE IF EXISTS badges;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS records;
DROP TABLE IF EXISTS materials;
DROP TABLE IF EXISTS class_users;
DROP TABLE IF EXISTS `class`;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    user_id VARCHAR(10) NOT NULL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    age INT NULL,
    user_type ENUM('teacher', 'student') NOT NULL,
    verify TINYINT(1) NOT NULL DEFAULT 0,
    token VARCHAR(64) NULL,
    UNIQUE KEY uk_users_username (username),
    UNIQUE KEY uk_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `class` (
    class_id VARCHAR(20) NOT NULL PRIMARY KEY,
    class_name VARCHAR(255) NOT NULL,
    class_code VARCHAR(20) NOT NULL,
    teacher_id VARCHAR(10) NOT NULL,
    UNIQUE KEY uk_class_code (class_code),
    KEY idx_class_teacher (teacher_id),
    CONSTRAINT fk_class_teacher FOREIGN KEY (teacher_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE class_users (
    class_id VARCHAR(20) NOT NULL,
    user_id VARCHAR(10) NOT NULL,
    PRIMARY KEY (class_id, user_id),
    CONSTRAINT fk_cu_class FOREIGN KEY (class_id) REFERENCES `class` (class_id),
    CONSTRAINT fk_cu_user FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE materials (
    material_id VARCHAR(50) NOT NULL PRIMARY KEY,
    uploader_id VARCHAR(10) NOT NULL,
    parent_id VARCHAR(50) NOT NULL DEFAULT '',
    date_submitted DATETIME NULL,
    date_edited DATETIME NULL,
    type VARCHAR(50) NULL,
    unit VARCHAR(50) NULL,
    title VARCHAR(255) NULL,
    main_img LONGBLOB NULL,
    description TEXT NULL,
    file_path TEXT NULL,
    url TEXT NULL,
    KEY idx_materials_uploader (uploader_id),
    CONSTRAINT fk_materials_uploader FOREIGN KEY (uploader_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE records (
    record_id VARCHAR(50) NOT NULL PRIMARY KEY,
    user_id VARCHAR(10) NOT NULL,
    material_id VARCHAR(50) NOT NULL,
    score_percentage DECIMAL(5, 2) NULL,
    dstart DATETIME NULL,
    created_at DATETIME NULL,
    KEY idx_records_user (user_id),
    KEY idx_records_material (material_id),
    CONSTRAINT fk_records_user FOREIGN KEY (user_id) REFERENCES users (user_id),
    CONSTRAINT fk_records_material FOREIGN KEY (material_id) REFERENCES materials (material_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE rewards (
    reward_id VARCHAR(50) NOT NULL PRIMARY KEY,
    user_id VARCHAR(10) NOT NULL,
    points INT NOT NULL DEFAULT 0,
    learning INT NOT NULL DEFAULT 0,
    assignment INT NOT NULL DEFAULT 0,
    quiz INT NOT NULL DEFAULT 0,
    UNIQUE KEY uk_rewards_user (user_id),
    CONSTRAINT fk_rewards_user FOREIGN KEY (user_id) REFERENCES users (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE badges (
    badge_id VARCHAR(50) NOT NULL PRIMARY KEY,
    badge_file LONGBLOB NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE badge_users (
    user_id VARCHAR(10) NOT NULL,
    badge_id VARCHAR(50) NOT NULL,
    PRIMARY KEY (user_id, badge_id),
    CONSTRAINT fk_bu_user FOREIGN KEY (user_id) REFERENCES users (user_id),
    CONSTRAINT fk_bu_badge FOREIGN KEY (badge_id) REFERENCES badges (badge_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

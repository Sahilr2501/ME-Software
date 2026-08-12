CREATE DATABASE IF NOT EXISTS repairing_can
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE repairing_can;

CREATE TABLE IF NOT EXISTS repair_received (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    repair_date DATE NOT NULL,
    challa_num VARCHAR(100) NOT NULL,
    with_ring INT UNSIGNED NOT NULL DEFAULT 0,
    without_ring INT UNSIGNED NOT NULL DEFAULT 0,
    without_handle INT UNSIGNED NOT NULL DEFAULT 0,
    total_can INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repair_date (repair_date),
    INDEX idx_challa (challa_num)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS repair_completed (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    repair_date DATE NOT NULL,
    new_handle INT UNSIGNED NOT NULL DEFAULT 0,
    new_bottom_ring INT UNSIGNED NOT NULL DEFAULT 0,
    new_bottom_dish INT UNSIGNED NOT NULL DEFAULT 0,
    repairing INT UNSIGNED NOT NULL DEFAULT 0,
    buffing_can INT UNSIGNED NOT NULL DEFAULT 0,
    cleaning_can INT UNSIGNED NOT NULL DEFAULT 0,
    total_can INT UNSIGNED NOT NULL DEFAULT 0,
    total_reject INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repair_date (repair_date)
) ENGINE=InnoDB;

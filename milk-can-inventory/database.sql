CREATE DATABASE IF NOT EXISTS milk_can_inventory
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE milk_can_inventory;

CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    capacity DECIMAL(10,2) NOT NULL,
    material VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_quantity INT NOT NULL DEFAULT 0,
    minimum_stock INT NOT NULL DEFAULT 5,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE purchases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(150) NOT NULL,
    invoice_no VARCHAR(100) DEFAULT NULL,
    purchase_date DATE NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE purchase_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT UNSIGNED NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit VARCHAR(30) NOT NULL DEFAULT 'pcs',
    rate DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_purchase_items_purchase
        FOREIGN KEY (purchase_id)
        REFERENCES purchases(id)
        ON DELETE CASCADE
);

CREATE TABLE production (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    production_date DATE NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_production_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT
);

CREATE TABLE stock_movements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    movement_type ENUM('IN', 'OUT', 'ADJUSTMENT') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id INT UNSIGNED DEFAULT NULL,
    movement_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT DEFAULT NULL,

    CONSTRAINT fk_stock_movements_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE RESTRICT
);

CREATE INDEX idx_products_name
ON products(product_name);

CREATE INDEX idx_products_capacity
ON products(capacity);

CREATE INDEX idx_purchases_supplier
ON purchases(supplier_name);

CREATE INDEX idx_purchases_date
ON purchases(purchase_date);

CREATE INDEX idx_production_date
ON production(production_date);

CREATE INDEX idx_production_product
ON production(product_id);

CREATE INDEX idx_stock_movements_product
ON stock_movements(product_id);

CREATE INDEX idx_stock_movements_date
ON stock_movements(movement_date);


INSERT INTO products
(product_name, capacity, material, price, stock_quantity, minimum_stock)
VALUES
('Milk Can', 20, 'Stainless Steel', 900, 50, 10),
('Milk Can', 30, 'Stainless Steel', 1200, 35, 10),
('Milk Can', 40, 'Stainless Steel', 1500, 20, 5);
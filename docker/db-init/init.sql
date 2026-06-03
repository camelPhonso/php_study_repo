CREATE DATABASE IF NOT EXISTS php_database
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE php_database;

-- Drop child table first in case the script is re-run
DROP TABLE IF EXISTS invoice_items;
DROP TABLE IF EXISTS invoices;

CREATE TABLE invoices (
    id INT NOT NULL AUTO_INCREMENT,
    amount BIGINT NOT NULL,
    invoice_number VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL,
    created_at DATETIME NOT NULL,
    due_date DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_invoices_invoice_number (invoice_number)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoice_items (
    id INT NOT NULL AUTO_INCREMENT,
    invoice_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_invoice_items_invoice_id (invoice_id),
    CONSTRAINT fk_invoice_items_invoice
        FOREIGN KEY (invoice_id)
        REFERENCES invoices (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Seed one invoice
INSERT INTO invoices (
    id,
    amount,
    invoice_number,
    status,
    created_at,
    due_date
) VALUES (
    1,
    12500,
    'INV-1001',
    'PENDING',
    '2026-06-03 09:00:00',
    '2026-06-17 23:59:59'
);

-- Seed one invoice item linked to that invoice
INSERT INTO invoice_items (
    id,
    invoice_id,
    description,
    quantity,
    unit_price
) VALUES (
    1,
    1,
    'Initial seeded invoice item',
    1,
    125.00
);
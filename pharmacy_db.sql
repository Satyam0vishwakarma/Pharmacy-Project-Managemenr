-- Pharmacy Management System Database
-- Minimal and Essential Structure

CREATE DATABASE IF NOT EXISTS pharmacy;
USE pharmacy;

-- Admin Login Table
CREATE TABLE IF NOT EXISTS admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Pharmacist Login Table
CREATE TABLE IF NOT EXISTS pharmacist (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Medicines Table
CREATE TABLE IF NOT EXISTS medicines (
    med_id INT PRIMARY KEY AUTO_INCREMENT,
    med_name VARCHAR(100) NOT NULL,
    med_type VARCHAR(50),
    med_price DECIMAL(10,2) NOT NULL,
    med_quantity INT NOT NULL DEFAULT 0,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers Table
CREATE TABLE IF NOT EXISTS customers (
    cust_id INT PRIMARY KEY AUTO_INCREMENT,
    cust_name VARCHAR(100) NOT NULL,
    cust_phone VARCHAR(15),
    cust_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sales Table
CREATE TABLE IF NOT EXISTS sales (
    sale_id INT PRIMARY KEY AUTO_INCREMENT,
    cust_id INT,
    emp_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cust_id) REFERENCES customers(cust_id),
    FOREIGN KEY (emp_id) REFERENCES pharmacist(id)
);

-- Sales Items Table
CREATE TABLE IF NOT EXISTS sales_items (
    item_id INT PRIMARY KEY AUTO_INCREMENT,
    sale_id INT,
    med_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(sale_id) ON DELETE CASCADE,
    FOREIGN KEY (med_id) REFERENCES medicines(med_id)
);

-- Insert Default Admin
INSERT INTO admin (username, password) VALUES 
('admin', 'admin123');

-- Insert Sample Pharmacists
INSERT INTO pharmacist (name, username, password, phone) VALUES 
('John Doe', 'john', 'john123', '9876543210'),
('Sarah Smith', 'sarah', 'sarah123', '9876543211');

-- Insert Sample Medicines
INSERT INTO medicines (med_name, med_type, med_price, med_quantity, expiry_date) VALUES 
('Paracetamol', 'Tablet', 50.00, 100, '2026-12-31'),
('Amoxicillin', 'Capsule', 120.00, 50, '2026-06-30'),
('Cough Syrup', 'Syrup', 85.00, 30, '2025-12-31'),
('Aspirin', 'Tablet', 40.00, 150, '2026-11-30'),
('Vitamin C', 'Tablet', 60.00, 80, '2027-01-31');

-- Insert Sample Customers
INSERT INTO customers (cust_name, cust_phone, cust_email) VALUES 
('Rajesh Kumar', '9876543212', 'rajesh@email.com'),
('Priya Sharma', '9876543213', 'priya@email.com'),
('Amit Patel', '9876543214', 'amit@email.com');

-- Trigger to update medicine quantity after sale
DELIMITER $$
CREATE TRIGGER update_medicine_stock AFTER INSERT ON sales_items
FOR EACH ROW
BEGIN
    UPDATE medicines 
    SET med_quantity = med_quantity - NEW.quantity 
    WHERE med_id = NEW.med_id;
END$$
DELIMITER ;
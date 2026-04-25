-- Database: homocertiRL
CREATE DATABASE IF NOT EXISTS homocertiRL;
USE homocertiRL;

-- Table: Solutions
CREATE TABLE IF NOT EXISTS solutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    booked INT DEFAULT 0,
    resources TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: Products
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    booked INT DEFAULT 0,
    resources TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: Booking
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    service VARCHAR(255),
    date DATE,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: Contact
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    reason VARCHAR(255),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Initial Data for Solutions
INSERT INTO solutions (name, description) VALUES 
('Sales Enablement', 'Proprietary data and market analysis to figure out customer needs.'),
('Conversion', 'Conversion systems built for specific regional markets.'),
('Expert copy', 'Subject Matter Experts producing human-first expert content.'),
('Email Marketing', 'Premium email copies for VIP client engagement.'),
('Content Repurposing', 'Multi-channel distribution strategy (LinkedIn, YouTube, etc.).'),
('Long form', 'Authority-building content for Search and Generative Engines.');

-- Initial Data for Products
INSERT INTO products (name, description) VALUES 
('Startup positioning', 'Strategic market entry and dominance for new ventures.'),
('Branding', 'Luxury brand identity and cultural bank development.'),
('GEO Boost', 'Optimization for Generative Engines like ChatGPT and Gemini.'),
('Keywords positioning', 'Modern SEO targeting for 2026 search trends.');

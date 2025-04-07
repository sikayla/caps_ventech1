-- ===========================
-- DATABASE CREATION
-- ===========================
CREATE DATABASE IF NOT EXISTS venue_db;
USE venue_db;

-- ===========================

-- VENUES TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS venues (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venue_id INT UNSIGNED NOT NULL,  
    name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL CHECK (price >= 0),
    lat DECIMAL(10,6) NOT NULL,
    lng DECIMAL(10,6) NOT NULL,
    capacity INT NOT NULL CHECK (capacity > 0),
    tags JSON NOT NULL DEFAULT ('[]'),  
    category VARCHAR(255) NOT NULL,
    category2 ENUM('low price', 'mid price', 'high price') NOT NULL,
    category3 TINYINT UNSIGNED NOT NULL CHECK (category3 BETWEEN 5 AND 25),
    image VARCHAR(255) DEFAULT 'uploads/default_court.jpg',
    status ENUM('open', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_category (category),
    INDEX idx_price_range (category2),
    INDEX idx_capacity_range (category3),
    INDEX idx_location (lat, lng),
    INDEX idx_status (status)
);

-- ===========================
-- VENUE DETAILS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS venue_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venue_id INT UNSIGNED NOT NULL UNIQUE, 
    venue_name VARCHAR(255) NOT NULL,
    owner_name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    map_url TEXT,
    description TEXT,
    header_image VARCHAR(255) DEFAULT 'uploads/default_header.jpg', 
    main_image VARCHAR(255) DEFAULT 'uploads/default_main.jpg',
    gallery_images JSON, 
    sidebar_gallery JSON,
    video_tour VARCHAR(255),
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    linkedin VARCHAR(255),
    instagram VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE,

    -- Index for faster joins
    INDEX idx_venue_id (venue_id)
);

-- ===========================
-- USERS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  
    profile_image VARCHAR(255) NULL DEFAULT '/venue_locator/images/default_profile.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index for fast lookups
CREATE INDEX idx_users_username ON users (username);

-- ===========================
-- ADMINS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Insert a sample admin user
INSERT INTO admins (username, password) 
VALUES 
    ('admin', '$2y$10$abcdefghijABCDEFGHIJabcdefghijABCDEFGHIJabcdefghijABCDEFGHIJ')
ON DUPLICATE KEY UPDATE username=username;

-- ===========================
-- BOOKINGS TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venue_id INT UNSIGNED NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    num_attendees INT NOT NULL CHECK (num_attendees > 0),
    total_cost DECIMAL(10,2) NOT NULL CHECK (total_cost >= 0),
    payment_method ENUM('Cash', 'Credit/Debit', 'Online') NOT NULL,
    shared_booking BOOLEAN NOT NULL DEFAULT FALSE,
    id_photo VARCHAR(255) NULL,
    status ENUM('Pending', 'Canceled', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (venue_id) REFERENCES venues(id) ON DELETE CASCADE
);
-- ===========================
-- USER ADMIN TABLE
-- ===========================
CREATE TABLE IF NOT EXISTS user_admin (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255) NULL DEFAULT '/venue_locator/images/default_profile.png',
    school_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE client (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT UNSIGNED NOT NULL,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    client_address VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ===========================
-- PERFORMANCE INDEXING
-- ===========================
CREATE INDEX idx_venue_name ON venues (name);

-- ===========================
-- TEST QUERIES TO VERIFY DATA
-- ===========================
SHOW TABLES;

-- Check structure of tables
DESCRIBE users;
DESCRIBE venues;
DESCRIBE bookings;

-- Check total records in tables
SELECT COUNT(*) FROM users;
SELECT COUNT(*) FROM venues;
SELECT COUNT(*) FROM bookings;
ALTER TABLE venues ADD COLUMN location VARCHAR(255) NOT NULL;
ALTER TABLE venues ADD COLUMN admin_status ENUM('pending', 'confirmed', 'rejected') NOT NULL DEFAULT 'pending';
ALTER TABLE client ADD INDEX idx_username_email (username, email);






-- Fetch venues sorted by distance (Example: from a specific location)
SELECT *, (111.045 * DEGREES(ACOS(COS(RADIANS(37.7749)) * COS(RADIANS(lat)) 
* COS(RADIANS(lng) - RADIANS(-122.4194)) + SIN(RADIANS(37.7749)) 
* SIN(RADIANS(lat))))) AS distance
FROM venues
ORDER BY distance ASC
LIMIT 10;

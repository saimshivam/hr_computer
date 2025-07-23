-- Create database
-- CREATE DATABASE IF NOT EXISTS hr_computer;
-- USE hr_computer;

-- Create admin_users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create admission_forms table
DROP TABLE IF EXISTS admission_forms;
CREATE TABLE IF NOT EXISTS admission_forms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name VARCHAR(100) NOT NULL,
    father_name VARCHAR(100) NOT NULL,
    mother_name VARCHAR(100) NOT NULL,
    photo VARCHAR(255),
    dob DATE NOT NULL,
    gender VARCHAR(10) NOT NULL,
    course VARCHAR(100) NOT NULL,
    duration VARCHAR(50),
    address TEXT NOT NULL,
    district VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(20),
    contact_no VARCHAR(20) NOT NULL,
    alt_mobile VARCHAR(20),
    matric_board VARCHAR(100),
    matric_year VARCHAR(10),
    matric_total VARCHAR(20),
    matric_obtained VARCHAR(20),
    matric_percent VARCHAR(20),
    degree_board VARCHAR(100),
    degree_year VARCHAR(10),
    degree_total VARCHAR(20),
    degree_obtained VARCHAR(20),
    degree_percent VARCHAR(20),
    other_board VARCHAR(100),
    other_year VARCHAR(10),
    other_total VARCHAR(20),
    other_obtained VARCHAR(20),
    other_percent VARCHAR(20),
    place VARCHAR(100),
    form_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create gallery_images table
CREATE TABLE IF NOT EXISTS gallery_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50),
    fees DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(5,2) DEFAULT 0,
    duration VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create user_activities table
CREATE TABLE IF NOT EXISTS user_activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    activity VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create notices table
CREATE TABLE IF NOT EXISTS notices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO admin_users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: password 

-- Alter courses table to adjust discount to percentage and update fees format if needed
ALTER TABLE courses 
    MODIFY COLUMN discount DECIMAL(5,2) DEFAULT 0; 
-- Drop tables if they exist
-- DROP TABLE IF EXISTS vault_permissions;
-- DROP TABLE IF EXISTS vault_passwords;
-- DROP TABLE IF EXISTS vaults;
-- DROP TABLE IF EXISTS users;
-- DROP TABLE IF EXISTS roles;

-- Create the database
CREATE DATABASE IF NOT EXISTS password_manager;

-- Use the database
USE password_manager;

-- Create the roles table
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('Admin', 'Owner', 'Viewer') NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    default_role_id INT,    
    FOREIGN KEY (default_role_id) REFERENCES roles(role_id) ON DELETE CASCADE 
);

-- Create the vaults table
CREATE TABLE IF NOT EXISTS vaults (
    vault_id INT AUTO_INCREMENT PRIMARY KEY,    
    vault_name VARCHAR(255) NOT NULL    
);

-- Create the passwords table
CREATE TABLE IF NOT EXISTS vault_passwords (
    password_id INT AUTO_INCREMENT PRIMARY KEY,
    vault_id INT,
    username VARCHAR(255) NOT NULL,
    website VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    notes TEXT,
    FOREIGN KEY (vault_id) REFERENCES vaults(vault_id) ON DELETE CASCADE
);


-- Create the permissions table
CREATE TABLE IF NOT EXISTS vault_permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vault_id INT,
    role_id INT,    
    FOREIGN KEY (vault_id) REFERENCES vaults(vault_id) ON DELETE CASCADE,   
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE        
);

-- Create an index on username for faster retrieval
CREATE UNIQUE INDEX idx_username ON users(username);

-- Insert sample roles
INSERT INTO roles (role_id, role)
VALUES
    (1, 'Admin'),
    (2, 'Owner'),
    (3, 'Viewer');

-- Insert sample users
-- Hashed Password Values ARE john_doe:'thisismysecret'
-- and jane_smith:'ihatemyjob'
INSERT INTO users (user_id, username, first_name, last_name, email, password)
VALUES
    (1, 'username', 'User', 'Name', 'user@info310.net', 'password!');


-- Insert sample vaults
INSERT INTO vaults (vault_id, vault_name)
VALUES
    (1, 'Developers Vault'),
    (2, 'Executives Vault'),
    (3, 'HR Vault');

-- Insert sample passwords
INSERT INTO vault_passwords (password_id, vault_id, username, website, password, notes)
VALUES
    (1, 1, 'john_doe', 'example.com', 'secure_password1', 'Personal notes for this password'),
    (2, 2, 'jane_smith', 'workplace.com', 'strong_password2', 'Work-related notes');

-- Assign roles and permissions
INSERT INTO vault_permissions (permission_id, user_id, vault_id, role_id)
VALUES
    (1, 1, 1, 1), -- John Doe has Admin rights on Personal Vault
    (2, 2, 2, 2); -- Jane Smith has Owner rights on Work Vault

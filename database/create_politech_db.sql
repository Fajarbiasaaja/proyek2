-- Create Database Script for Politech Application
-- Run this script in MySQL after starting the MySQL service

CREATE DATABASE IF NOT EXISTS politech 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE politech;

-- Verify database created
SHOW DATABASES LIKE 'politech';
SHOW TABLES;

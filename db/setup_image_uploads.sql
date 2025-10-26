-- Complete Database Setup Script for Image Upload Functionality
-- Run this script to add all necessary columns for image uploads

-- Add brand_image column to brands table
ALTER TABLE `brands` 
ADD COLUMN `brand_image` VARCHAR(255) DEFAULT NULL AFTER `brand_name`;

-- Add index for better performance on brands
ALTER TABLE `brands` 
ADD INDEX `idx_brand_image` (`brand_image`);

-- Add category_image column to categories table  
ALTER TABLE `categories` 
ADD COLUMN `cat_image` VARCHAR(255) DEFAULT NULL AFTER `cat_name`;

-- Add index for better performance on categories
ALTER TABLE `categories` 
ADD INDEX `idx_cat_image` (`cat_image`);

-- Verify the changes
SELECT 'Brands table structure:' as info;
DESCRIBE brands;

SELECT 'Categories table structure:' as info;
DESCRIBE categories;

SELECT 'Products table structure:' as info;
DESCRIBE products;

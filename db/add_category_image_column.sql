-- Add category_image column to categories table
-- This script adds the missing category_image column to support image uploads

ALTER TABLE `categories` 
ADD COLUMN `cat_image` VARCHAR(255) DEFAULT NULL AFTER `cat_name`;

-- Add index for better performance
ALTER TABLE `categories` 
ADD INDEX `idx_cat_image` (`cat_image`);

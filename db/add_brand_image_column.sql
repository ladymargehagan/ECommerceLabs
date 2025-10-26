-- Add brand_image column to brands table
-- This script adds the missing brand_image column to support image uploads

ALTER TABLE `brands` 
ADD COLUMN `brand_image` VARCHAR(255) DEFAULT NULL AFTER `brand_name`;

-- Add index for better performance
ALTER TABLE `brands` 
ADD INDEX `idx_brand_image` (`brand_image`);

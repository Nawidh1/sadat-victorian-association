-- Migration: Add image column to news table
-- Run this SQL script if your news table doesn't have an image column yet

ALTER TABLE news 
ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT '' AFTER content_fa;

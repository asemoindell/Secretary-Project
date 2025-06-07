-- Add document_file column to users table for client document upload
ALTER TABLE users ADD COLUMN document_file VARCHAR(255) NULL AFTER profile_picture;

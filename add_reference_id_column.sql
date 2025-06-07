-- Add reference_id column to reports table for unique alphanumeric reference
ALTER TABLE reports ADD COLUMN reference_id VARCHAR(32) NULL AFTER id;
-- Add document_file column to users table for client document upload
ALTER TABLE users ADD COLUMN document_file VARCHAR(255) NULL AFTER profile_picture;

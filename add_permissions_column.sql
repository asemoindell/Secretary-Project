-- Add permissions column to users table for secretary permissions
ALTER TABLE users ADD COLUMN permissions TEXT NULL AFTER role;

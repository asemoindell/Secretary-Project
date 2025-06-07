-- Add type column for registration type (consent, survey, building plan, c of o, land document)
ALTER TABLE users ADD COLUMN type VARCHAR(50) NULL AFTER reference_id;

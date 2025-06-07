-- Demo content for users table
INSERT INTO users (reference_id, surname, firstname, middlename, local_govt, country, fullname, username, password, profile_picture, document_file, role, type, permissions, created_at)
VALUES
('ref_1001', 'Smith', 'John', 'A.', 'Ikeja', 'Nigeria', 'John A. Smith', 'admin1', '$2y$10$examplehash1', 'admin1.jpg', '[]', 'admin', 'admin', '["view_users","view_reports","new_registration","reporting","add_report","edit_report","update_report","delete_report","show_report","login_location"]', NOW()),
('ref_1002', 'Doe', 'Jane', 'B.', 'Surulere', 'Nigeria', 'Jane B. Doe', 'secretary1', '$2y$10$examplehash2', 'secretary1.jpg', '[]', 'secretary', 'secretary', '["view_users","view_reports","new_registration"]', NOW()),
('ref_1003', 'Okafor', 'Chinedu', '', 'Enugu North', 'Nigeria', 'Chinedu Okafor', NULL, NULL, 'okafor.jpg', '["doc1.pdf","doc2.pdf"]', 'user', 'consent', NULL, NOW()),
('ref_1004', 'Asemota', 'Osasumwen', '', 'Oredo', 'Nigeria', 'Osasumwen Asemota', NULL, NULL, 'asemota.jpg', '["plan1.pdf"]', 'user', 'building_plan', NULL, NOW()),
('ref_1005', 'Bello', 'Fatima', '', 'Ilorin South', 'Nigeria', 'Fatima Bello', NULL, NULL, 'bello.jpg', '["landdoc1.pdf","landdoc2.pdf"]', 'user', 'land_document', NULL, NOW());

-- Demo content for reports table
INSERT INTO reports (reference_id, user_id, report_type, report_date, document_file, created_at)
VALUES
('ref_1003', 3, 'survey', '2025-06-06', '["survey1.pdf"]', NOW()),
('ref_1004', 4, 'building_plan', '2025-06-07', '["plan1.pdf","plan2.pdf"]', NOW()),
('ref_1005', 5, 'land_document', '2025-06-07', '["landdoc1.pdf"]', NOW());

-- Demo content for settings table
INSERT INTO settings (setting_key, setting_value)
VALUES
('admin_profile', 'Admin: John Smith, Email: admin@example.com'),
('board_name', 'Land Use and Allocation Committee'),
('frontend_name', 'Land Registry Portal'),
('welcome_message', 'Welcome to the Land Registry Portal. Please login to continue.');

-- Example for pause_reason (if used)
UPDATE users SET pause_reason = 'Verification pending' WHERE reference_id = 'ref_1005';

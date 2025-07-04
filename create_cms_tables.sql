-- Merge CMS tables with existing admin_dashboard database
-- This script adds website content management capabilities to your existing system

-- Company Information Table
CREATE TABLE IF NOT EXISTS company_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL DEFAULT 'LandPro Solutions',
    tagline VARCHAR(500) NOT NULL DEFAULT 'Your Trusted Land & C of O Processing Partner',
    logo VARCHAR(255),
    about_title VARCHAR(255) NOT NULL DEFAULT 'About LandPro Solutions',
    about_content TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    address TEXT,
    working_hours TEXT,
    facebook_url VARCHAR(255),
    twitter_url VARCHAR(255),
    instagram_url VARCHAR(255),
    linkedin_url VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hero Slides Table
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    button_text VARCHAR(100),
    button_link VARCHAR(255),
    background_color VARCHAR(20) DEFAULT '#667eea',
    background_gradient VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100) NOT NULL DEFAULT 'fas fa-home',
    features JSON,
    is_active TINYINT(1) DEFAULT 1,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Statistics/Features Table
CREATE TABLE IF NOT EXISTS company_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    value VARCHAR(20) NOT NULL,
    icon VARCHAR(100) NOT NULL,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Check and insert default company information only if table is empty
INSERT INTO company_info (
    company_name, tagline, about_content, phone, email, address, working_hours
) 
SELECT * FROM (SELECT 
    'LandPro Solutions' as company_name,
    'Your Trusted Land & C of O Processing Partner' as tagline,
    'We are Nigeria\'s leading land sales and Certificate of Occupancy processing company, dedicated to making your property ownership dreams a reality.\n\nWith over a decade of experience in the real estate industry, we have successfully helped thousands of clients acquire their dream properties and navigate the complex process of obtaining their Certificate of Occupancy.\n\nOur team of experienced professionals ensures that every transaction is handled with the utmost care, transparency, and efficiency.' as about_content,
    '+234 901 234 5678, +234 802 345 6789' as phone,
    'info@landprosolutions.com, sales@landprosolutions.com' as email,
    '123 Real Estate Avenue, Victoria Island, Lagos, Nigeria' as address,
    'Monday - Friday: 8:00 AM - 6:00 PM\nSaturday: 9:00 AM - 4:00 PM' as working_hours
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM company_info LIMIT 1
);

-- Check and insert default hero slides only if table is empty
INSERT INTO hero_slides (title, subtitle, button_text, button_link, background_gradient, display_order) 
SELECT * FROM (
    SELECT 'Your Dream Land Awaits' as title, 'Premium land sales with complete C of O processing services' as subtitle, 'Explore Our Services' as button_text, '#services' as button_link, 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' as background_gradient, 1 as display_order
    UNION ALL
    SELECT 'Certificate of Occupancy Processing', 'Fast, reliable, and professional C of O documentation services', 'Get Started Today', '#contact', 'linear-gradient(135deg, #2c5530 0%, #4a7c59 100%)', 2
    UNION ALL
    SELECT 'Trusted Real Estate Partner', 'Over 500+ successful land transactions and C of O processing', 'Learn More', '#about', 'linear-gradient(135deg, #8360c3 0%, #2ebf91 100%)', 3
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM hero_slides LIMIT 1
);

-- Check and insert default services only if table is empty
INSERT INTO services (title, description, icon, features, display_order) 
SELECT * FROM (
    SELECT 'Land Sales' as title, 'Premium residential and commercial plots in the best locations across Nigeria. We offer flexible payment plans and guaranteed genuine titles.' as description, 'fas fa-home' as icon, '["Verified land titles", "Flexible payment plans", "Prime locations"]' as features, 1 as display_order
    UNION ALL
    SELECT 'C of O Processing', 'Complete Certificate of Occupancy processing services. We handle all paperwork and government liaisons to ensure smooth processing.', 'fas fa-file-certificate', '["Complete documentation", "Government liaison", "Fast processing"]', 2
    UNION ALL
    SELECT 'Legal Advisory', 'Expert legal advice on property transactions, title verification, and dispute resolution. Our legal team ensures your investment is secure.', 'fas fa-handshake', '["Title verification", "Legal documentation", "Dispute resolution"]', 3
    UNION ALL
    SELECT 'Property Inspection', 'Comprehensive property inspection services to ensure you make informed decisions. We provide detailed reports on all properties.', 'fas fa-search', '["Detailed inspections", "Professional reports", "Site evaluation"]', 4
    UNION ALL
    SELECT 'Property Valuation', 'Professional property valuation services for investment decisions, insurance, and legal purposes. Get accurate market values.', 'fas fa-calculator', '["Market analysis", "Investment advice", "Certified reports"]', 5
    UNION ALL
    SELECT 'Property Management', 'Complete property management services including maintenance, tenant management, and investment optimization for property owners.', 'fas fa-cog', '["Maintenance services", "Tenant management", "Investment optimization"]', 6
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM services LIMIT 1
);

-- Check and insert default company statistics only if table is empty
INSERT INTO company_stats (title, value, icon, display_order) 
SELECT * FROM (
    SELECT 'Happy Clients' as title, '500+' as value, 'fas fa-users' as icon, 1 as display_order
    UNION ALL
    SELECT 'C of O Processed', '300+', 'fas fa-certificate', 2
    UNION ALL
    SELECT 'Prime Locations', '50+', 'fas fa-map-marked-alt', 3
    UNION ALL
    SELECT 'Years Experience', '10+', 'fas fa-clock', 4
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM company_stats LIMIT 1
);

<?php
/**
 * Website CMS Setup Script
 * Merges CMS tables with existing admin_dashboard database
 * Run this file once to add website content management to your existing system
 */

require_once 'includes/db.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('create_cms_tables.sql');
    
    // Split the SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $success_count = 0;
    $error_count = 0;
    $messages = [];
    
    echo "<!DOCTYPE html>";
    echo "<html><head><title>CMS Setup - Admin Dashboard</title>";
    echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:20px auto;padding:20px;background:#f8f9fa}";
    echo ".success{color:#155724;background:#d4edda;border:1px solid #c3e6cb;padding:10px;border-radius:5px;margin:5px 0}";
    echo ".error{color:#721c24;background:#f8d7da;border:1px solid #f5c6cb;padding:10px;border-radius:5px;margin:5px 0}";
    echo ".info{color:#0c5460;background:#d1ecf1;border:1px solid #b8daff;padding:10px;border-radius:5px;margin:5px 0}";
    echo ".warning{color:#856404;background:#fff3cd;border:1px solid #ffeaa7;padding:15px;border-radius:5px;margin:20px 0}";
    echo "h2{color:#2c5530;text-align:center;margin-bottom:30px}</style></head><body>";
    
    echo "<h2>🌐 Website CMS Setup - Admin Dashboard Integration</h2>";
    echo "<div class='info'><strong>Database:</strong> admin_dashboard | <strong>Status:</strong> Merging CMS tables...</div>";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $success_count++;
                
                // Identify what was created/updated
                if (strpos($statement, 'CREATE TABLE') !== false) {
                    if (strpos($statement, 'company_info') !== false) {
                        $messages[] = "<div class='success'>✓ Created company_info table for website settings</div>";
                    } elseif (strpos($statement, 'hero_slides') !== false) {
                        $messages[] = "<div class='success'>✓ Created hero_slides table for homepage slider</div>";
                    } elseif (strpos($statement, 'services') !== false) {
                        $messages[] = "<div class='success'>✓ Created services table for company services</div>";
                    } elseif (strpos($statement, 'company_stats') !== false) {
                        $messages[] = "<div class='success'>✓ Created company_stats table for achievements</div>";
                    }
                } elseif (strpos($statement, 'INSERT INTO') !== false) {
                    if (strpos($statement, 'company_info') !== false) {
                        $messages[] = "<div class='success'>✓ Added default company information</div>";
                    } elseif (strpos($statement, 'hero_slides') !== false) {
                        $messages[] = "<div class='success'>✓ Added default hero slides (3 slides)</div>";
                    } elseif (strpos($statement, 'services') !== false) {
                        $messages[] = "<div class='success'>✓ Added default services (6 services)</div>";
                    } elseif (strpos($statement, 'company_stats') !== false) {
                        $messages[] = "<div class='success'>✓ Added default statistics</div>";
                    }
                }
                
            } catch (PDOException $e) {
                $error_count++;
                if (strpos($e->getMessage(), 'already exists') !== false) {
                    // Table already exists - this is okay
                    $messages[] = "<div class='info'>ℹ Table already exists (skipped)</div>";
                } elseif (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    // Data already exists - this is okay
                    $messages[] = "<div class='info'>ℹ Default data already exists (skipped)</div>";
                } else {
                    $messages[] = "<div class='error'>✗ Error: " . $e->getMessage() . "</div>";
                }
            }
        }
    }
    
    // Display all messages
    foreach ($messages as $message) {
        echo $message;
    }
    
    echo "<hr style='margin:30px 0'>";
    echo "<h3 style='color:#2c5530'>Setup Results</h3>";
    echo "<p><strong>Statements Processed:</strong> " . count($statements) . "</p>";
    echo "<p><strong>Successful Operations:</strong> $success_count</p>";
    
    // Check if tables exist
    $tables_check = [
        'company_info' => 'Company Information',
        'hero_slides' => 'Hero Slides',
        'services' => 'Services',
        'company_stats' => 'Statistics'
    ];
    
    echo "<h4>Database Tables Status:</h4>";
    foreach ($tables_check as $table => $name) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
            echo "<div class='success'>✓ $name: Table exists with {$result['count']} records</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>✗ $name: Table missing or error</div>";
        }
    }
    
    // Check existing admin system
    try {
        $admin_check = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch();
        echo "<div class='success'>✓ Existing Admin System: {$admin_check['count']} admin users found</div>";
    } catch (PDOException $e) {
        echo "<div class='info'>ℹ Admin users table not found (normal for fresh install)</div>";
    }
    
    try {
        $reports_check = $pdo->query("SELECT COUNT(*) as count FROM reports")->fetch();
        echo "<div class='success'>✓ Existing Reports System: {$reports_check['count']} reports found</div>";
    } catch (PDOException $e) {
        echo "<div class='info'>ℹ Reports table not found (normal for fresh install)</div>";
    }
    
    if ($error_count <= 2) { // Allow some errors for existing data
        echo "<div class='warning'>";
        echo "<h4>🎉 CMS Integration Successful!</h4>";
        echo "<p>Your website is now fully admin-driven and integrated with your existing admin_dashboard system.</p>";
        echo "<h5>🚀 Next Steps:</h5>";
        echo "<ol>";
        echo "<li><strong>Access Admin Panel:</strong> <a href='auth/login.php' target='_blank'>Login with your existing credentials</a></li>";
        echo "<li><strong>Open CMS Dashboard:</strong> <a href='cms/cms_dashboard.php' target='_blank'>Website Content Management</a></li>";
        echo "<li><strong>View Your Website:</strong> <a href='index.php' target='_blank'>See the live website</a></li>";
        echo "<li><strong>Customize Content:</strong> Update company info, hero slides, services, and statistics</li>";
        echo "</ol>";
        echo "<h5>📋 Available CMS Features:</h5>";
        echo "<ul>";
        echo "<li>✅ Company Information Management</li>";
        echo "<li>✅ Hero Slides (Homepage Carousel)</li>";
        echo "<li>✅ Services Management</li>";
        echo "<li>✅ Statistics & Achievements</li>";
        echo "<li>✅ Contact Information</li>";
        echo "<li>✅ Social Media Links</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<p><strong>🔒 Security Note:</strong> For security, delete this setup.php file after setup is complete.</p>";
        echo "<p><strong>💾 Database:</strong> All CMS tables have been added to your existing 'admin_dashboard' database.</p>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>";
        echo "<h4>⚠️ Setup Issues Detected</h4>";
        echo "<p>Some operations failed. Please check the errors above and ensure:</p>";
        echo "<ul>";
        echo "<li>Your database connection is working</li>";
        echo "<li>You have proper database permissions</li>";
        echo "<li>The admin_dashboard database exists</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "</body></html>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px; font-family: Arial, sans-serif;'>";
    echo "<h3>Setup Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database connection and ensure the 'admin_dashboard' database exists.</p>";
    echo "</div>";
}
?>

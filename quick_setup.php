<?php
// Simple script to create CMS tables
require_once 'includes/db.php';

try {
    echo "Creating CMS tables in admin_dashboard database...\n";
    
    // Read SQL file
    $sql = file_get_contents('create_cms_tables.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed statement successfully\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'already exists') !== false || 
                    strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    echo "ℹ Skipped (already exists)\n";
                } else {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "\nVerifying tables...\n";
    $tables = ['company_info', 'hero_slides', 'services', 'company_stats'];
    foreach ($tables as $table) {
        try {
            $result = $pdo->query("SELECT COUNT(*) as count FROM $table")->fetch();
            echo "✓ $table: {$result['count']} records\n";
        } catch (PDOException $e) {
            echo "✗ $table: Not found\n";
        }
    }
    
    echo "\nSetup complete! CMS tables are ready.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

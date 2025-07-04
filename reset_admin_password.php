<?php
require_once 'includes/db.php';

// Reset admin password to 'admin123'
$username = 'admin';
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
    $stmt->execute([$hashed_password, $username]);
    
    echo "✅ Admin password reset successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "<br><a href='auth/login.php'>Login Now</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

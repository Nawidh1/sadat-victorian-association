<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif ($new_password !== $confirm_password) {
        $error = 'New passwords do not match';
    } elseif (strlen($new_password) < 6) {
        $error = 'New password must be at least 6 characters long';
    } else {
        try {
            $pdo = getDBConnection();
            $user_id = $_SESSION['admin_user_id'] ?? null;
            $username = $_SESSION['admin_username'] ?? 'admin';
            
            // Get current user's password hash
            $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE id = ? OR username = ?");
            $stmt->execute([$user_id, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $error = 'User not found';
            } elseif (!password_verify($current_password, $user['password_hash'])) {
                $error = 'Current password is incorrect';
            } else {
                // Update password
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE id = ? OR username = ?");
                $update_stmt->execute([$new_hash, $user_id, $username]);
                
                $success = 'Password changed successfully!';
            }
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            $error = 'Failed to change password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Change Password</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <form method="POST" class="admin-form" style="max-width: 600px;">
            <div class="admin-form-section">
                <div class="form-group">
                    <label>Current Password *</label>
                    <input type="password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" name="new_password" required minlength="6">
                    <small style="color: var(--text-light); display: block; margin-top: 0.5rem;">Minimum 6 characters</small>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password *</label>
                    <input type="password" name="confirm_password" required minlength="6">
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Change Password</button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
        </div>
    </div>
    <script src="admin.js"></script>
</body>
</html>

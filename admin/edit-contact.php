<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("INSERT INTO contact (field, value_en, value_fa) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE value_en=VALUES(value_en), value_fa=VALUES(value_fa)");
        
        $fields = ['address', 'phone', 'email', 'hours'];
        foreach ($fields as $field) {
            $stmt->execute([$field, $_POST[$field] ?? '', '']);
        }
        
        $success = 'Contact information updated successfully!';
    } catch (Exception $e) {
        error_log("Error updating contact info: " . $e->getMessage());
        $error = 'Failed to update contact information. Please try again.';
    }
}

// Load data from database
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT field, value_en, value_fa FROM contact");
    $contact_rows = $stmt->fetchAll();
    
    $contact_data = [
        'address' => '',
        'phone' => '',
        'email' => '',
        'hours' => ''
    ];
    
    foreach ($contact_rows as $row) {
        if (isset($contact_data[$row['field']])) {
            $contact_data[$row['field']] = $row['value_en'];
        }
    }
} catch (Exception $e) {
    error_log("Error loading contact data: " . $e->getMessage());
    $contact_data = [
        'address' => '',
        'phone' => '',
        'email' => '',
        'hours' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Info - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Edit Contact Information</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <form method="POST" class="admin-form" style="max-width: 700px;">
            <div class="admin-form-section">
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($contact_data['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($contact_data['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($contact_data['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Office Hours</label>
                    <textarea name="hours" rows="4"><?php echo htmlspecialchars($contact_data['hours'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </form>
        </div>
    </div>
    <script src="admin.js"></script>
</body>
</html>

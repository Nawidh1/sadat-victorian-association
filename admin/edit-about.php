<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        // Save mission
        $stmt = $pdo->prepare("INSERT INTO about (field, value_en, value_fa) VALUES (?, ?, ?)
                               ON DUPLICATE KEY UPDATE value_en=VALUES(value_en), value_fa=VALUES(value_fa)");
        $stmt->execute(['mission', $_POST['mission'] ?? '', '']);
        
        // Save values
        $values = [
            'faith' => $_POST['value_faith'] ?? '',
            'service' => $_POST['value_service'] ?? '',
            'unity' => $_POST['value_unity'] ?? '',
            'education' => $_POST['value_education'] ?? ''
        ];
        
        foreach ($values as $key => $value) {
            $stmt->execute(['values_' . $key, $value, '']);
        }
        
        // Save activities
        $stmt->execute(['activities', $_POST['activities'] ?? '', '']);
        
        $success = 'About page updated successfully!';
    } catch (Exception $e) {
        error_log("Error updating about page: " . $e->getMessage());
        $error = 'Failed to update about page. Please try again.';
    }
}

// Load data from database
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT field, value_en, value_fa FROM about");
    $about_rows = $stmt->fetchAll();
    
    // Convert to expected format
    $about_data = [
        'mission' => '',
        'values' => ['faith' => '', 'service' => '', 'unity' => '', 'education' => ''],
        'activities' => ''
    ];
    
    foreach ($about_rows as $row) {
        $field = $row['field'];
        if ($field === 'mission') {
            $about_data['mission'] = $row['value_en'];
        } elseif ($field === 'activities') {
            $about_data['activities'] = $row['value_en'];
        } elseif (strpos($field, 'values_') === 0) {
            $value_key = str_replace('values_', '', $field);
            if (isset($about_data['values'][$value_key])) {
                $about_data['values'][$value_key] = $row['value_en'];
            }
        }
    }
} catch (Exception $e) {
    error_log("Error loading about data: " . $e->getMessage());
    $about_data = [
        'mission' => '',
        'values' => ['faith' => '', 'service' => '', 'unity' => '', 'education' => ''],
        'activities' => ''
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Page - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Edit About Page</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <form method="POST" class="admin-form" style="max-width: 900px;">
            <div class="admin-form-section">
                <h2>Mission Statement</h2>
                <div class="form-group">
                    <label>Mission Text</label>
                    <textarea name="mission" rows="6"><?php echo htmlspecialchars($about_data['mission'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section" style="margin-top: 2rem;">
                <h2>Our Values</h2>
                <div class="form-group">
                    <label>Faith</label>
                    <textarea name="value_faith" rows="3"><?php echo htmlspecialchars($about_data['values']['faith'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Service</label>
                    <textarea name="value_service" rows="3"><?php echo htmlspecialchars($about_data['values']['service'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Unity</label>
                    <textarea name="value_unity" rows="3"><?php echo htmlspecialchars($about_data['values']['unity'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Education</label>
                    <textarea name="value_education" rows="3"><?php echo htmlspecialchars($about_data['values']['education'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section" style="margin-top: 2rem;">
                <h2>Activities</h2>
                <div class="form-group">
                    <label>Activities Description</label>
                    <textarea name="activities" rows="8"><?php echo htmlspecialchars($about_data['activities'] ?? ''); ?></textarea>
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

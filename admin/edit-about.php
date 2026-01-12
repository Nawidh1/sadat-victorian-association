<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        if (isset($_POST['action']) && $_POST['action'] === 'import_defaults') {
            // Import default about page content
            $default_content = [
                'who_we_are_1' => ['en' => 'The Sadat Victorian Association is a community organization dedicated to serving the Shia Muslim community in Victoria. Founded with a vision to preserve our rich Islamic heritage and strengthen community bonds, we work tirelessly to provide educational, spiritual, and social support to our members.', 'fa' => ''],
                'who_we_are_2' => ['en' => 'Our association takes its name from "Sadat," referring to the descendants of the Prophet Muhammad (PBUH) through his daughter Fatima (AS) and son-in-law Imam Ali (AS). This connection to the Ahl al-Bayt (People of the Household) guides our values and mission.', 'fa' => ''],
                'mission' => ['en' => "We are committed to:\n• Providing authentic Islamic education based on the teachings of the Ahl al-Bayt\n• Organizing religious commemorations and spiritual gatherings\n• Supporting community members in times of need\n• Promoting interfaith dialogue and understanding\n• Preserving and sharing Shia Islamic traditions and culture\n• Fostering unity and brotherhood within the Muslim community", 'fa' => ''],
                'values_faith' => ['en' => 'Rooted in the teachings of the Holy Quran and the Ahl al-Bayt', 'fa' => ''],
                'values_service' => ['en' => 'Dedicated to serving our community with compassion and integrity', 'fa' => ''],
                'values_unity' => ['en' => 'Building bridges and fostering harmony among all Muslims', 'fa' => ''],
                'values_education' => ['en' => 'Promoting knowledge and understanding of Islamic principles', 'fa' => ''],
                'activities' => ['en' => "Throughout the year, we organize various activities and programs:\n\n• Weekly Prayers: Regular Friday prayers and community gatherings\n• Religious Commemorations: Observing important dates in the Islamic calendar, including Muharram, Safar, and other significant occasions\n• Educational Programs: Classes and lectures on Islamic studies, Quranic interpretation, and Shia jurisprudence\n• Youth Programs: Activities and mentorship for young community members\n• Community Support: Assistance programs and social services for those in need\n• Cultural Events: Celebrations that honor our heritage and traditions", 'fa' => '']
            ];
            
            $stmt = $pdo->prepare("INSERT INTO about (field, value_en, value_fa) VALUES (?, ?, ?)
                                   ON DUPLICATE KEY UPDATE value_en=VALUES(value_en), value_fa=VALUES(value_fa)");
            
            foreach ($default_content as $field => $value_data) {
                $stmt->execute([$field, $value_data['en'], $value_data['fa']]);
            }
            
            $success = 'Default about page content imported successfully!';
        } else {
            // Save who we are sections
            $stmt = $pdo->prepare("INSERT INTO about (field, value_en, value_fa) VALUES (?, ?, ?)
                                   ON DUPLICATE KEY UPDATE value_en=VALUES(value_en), value_fa=VALUES(value_fa)");
            $stmt->execute(['who_we_are_1', $_POST['who_we_are_1'] ?? '', $_POST['who_we_are_1_fa'] ?? '']);
            $stmt->execute(['who_we_are_2', $_POST['who_we_are_2'] ?? '', $_POST['who_we_are_2_fa'] ?? '']);
            
            // Save mission
            $stmt->execute(['mission', $_POST['mission'] ?? '', $_POST['mission_fa'] ?? '']);
            
            // Save values
            $values = [
                'faith' => ['en' => $_POST['value_faith'] ?? '', 'fa' => $_POST['value_faith_fa'] ?? ''],
                'service' => ['en' => $_POST['value_service'] ?? '', 'fa' => $_POST['value_service_fa'] ?? ''],
                'unity' => ['en' => $_POST['value_unity'] ?? '', 'fa' => $_POST['value_unity_fa'] ?? ''],
                'education' => ['en' => $_POST['value_education'] ?? '', 'fa' => $_POST['value_education_fa'] ?? '']
            ];
            
            foreach ($values as $key => $value_data) {
                $stmt->execute(['values_' . $key, $value_data['en'], $value_data['fa']]);
            }
            
            // Save activities
            $stmt->execute(['activities', $_POST['activities'] ?? '', $_POST['activities_fa'] ?? '']);
            
            $success = 'About page updated successfully!';
        }
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
        'who_we_are_1' => '',
        'who_we_are_1_fa' => '',
        'who_we_are_2' => '',
        'who_we_are_2_fa' => '',
        'mission' => '',
        'mission_fa' => '',
        'values' => [
            'faith' => '',
            'faith_fa' => '',
            'service' => '',
            'service_fa' => '',
            'unity' => '',
            'unity_fa' => '',
            'education' => '',
            'education_fa' => ''
        ],
        'activities' => '',
        'activities_fa' => ''
    ];
    
    foreach ($about_rows as $row) {
        $field = $row['field'];
        if ($field === 'who_we_are_1' || $field === 'who_we_are_2') {
            $about_data[$field] = $row['value_en'];
            $about_data[$field . '_fa'] = $row['value_fa'];
        } elseif ($field === 'mission') {
            $about_data['mission'] = $row['value_en'];
            $about_data['mission_fa'] = $row['value_fa'];
        } elseif ($field === 'activities') {
            $about_data['activities'] = $row['value_en'];
            $about_data['activities_fa'] = $row['value_fa'];
        } elseif (strpos($field, 'values_') === 0) {
            $value_key = str_replace('values_', '', $field);
            if (isset($about_data['values'][$value_key])) {
                $about_data['values'][$value_key] = $row['value_en'];
                $about_data['values'][$value_key . '_fa'] = $row['value_fa'];
            }
        }
    }
} catch (Exception $e) {
    error_log("Error loading about data: " . $e->getMessage());
    $about_data = [
        'who_we_are_1' => '',
        'who_we_are_1_fa' => '',
        'who_we_are_2' => '',
        'who_we_are_2_fa' => '',
        'mission' => '',
        'mission_fa' => '',
        'values' => [
            'faith' => '',
            'faith_fa' => '',
            'service' => '',
            'service_fa' => '',
            'unity' => '',
            'unity_fa' => '',
            'education' => '',
            'education_fa' => ''
        ],
        'activities' => '',
        'activities_fa' => ''
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
                <h2>Who We Are</h2>
                <div class="form-group">
                    <label>Who We Are - Paragraph 1 (English)</label>
                    <textarea name="who_we_are_1" rows="4"><?php echo htmlspecialchars($about_data['who_we_are_1'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Who We Are - Paragraph 1 (Farsi)</label>
                    <textarea name="who_we_are_1_fa" rows="4" dir="rtl"><?php echo htmlspecialchars($about_data['who_we_are_1_fa'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Who We Are - Paragraph 2 (English)</label>
                    <textarea name="who_we_are_2" rows="4"><?php echo htmlspecialchars($about_data['who_we_are_2'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Who We Are - Paragraph 2 (Farsi)</label>
                    <textarea name="who_we_are_2_fa" rows="4" dir="rtl"><?php echo htmlspecialchars($about_data['who_we_are_2_fa'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section" style="margin-top: 2rem;">
                <h2>Mission Statement</h2>
                <div class="form-group">
                    <label>Mission Text (English)</label>
                    <textarea name="mission" rows="6"><?php echo htmlspecialchars($about_data['mission'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Mission Text (Farsi)</label>
                    <textarea name="mission_fa" rows="6" dir="rtl"><?php echo htmlspecialchars($about_data['mission_fa'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section" style="margin-top: 2rem;">
                <h2>Our Values</h2>
                <div class="form-group">
                    <label>Faith (English)</label>
                    <textarea name="value_faith" rows="3"><?php echo htmlspecialchars($about_data['values']['faith'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Faith (Farsi)</label>
                    <textarea name="value_faith_fa" rows="3" dir="rtl"><?php echo htmlspecialchars($about_data['values']['faith_fa'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Service (English)</label>
                    <textarea name="value_service" rows="3"><?php echo htmlspecialchars($about_data['values']['service'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Service (Farsi)</label>
                    <textarea name="value_service_fa" rows="3" dir="rtl"><?php echo htmlspecialchars($about_data['values']['service_fa'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Unity (English)</label>
                    <textarea name="value_unity" rows="3"><?php echo htmlspecialchars($about_data['values']['unity'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Unity (Farsi)</label>
                    <textarea name="value_unity_fa" rows="3" dir="rtl"><?php echo htmlspecialchars($about_data['values']['unity_fa'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Education (English)</label>
                    <textarea name="value_education" rows="3"><?php echo htmlspecialchars($about_data['values']['education'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Education (Farsi)</label>
                    <textarea name="value_education_fa" rows="3" dir="rtl"><?php echo htmlspecialchars($about_data['values']['education_fa'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section" style="margin-top: 2rem;">
                <h2>Activities</h2>
                <div class="form-group">
                    <label>Activities Description (English)</label>
                    <textarea name="activities" rows="8"><?php echo htmlspecialchars($about_data['activities'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Activities Description (Farsi)</label>
                    <textarea name="activities_fa" rows="8" dir="rtl"><?php echo htmlspecialchars($about_data['activities_fa'] ?? ''); ?></textarea>
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

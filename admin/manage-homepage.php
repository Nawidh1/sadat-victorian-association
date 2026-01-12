<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDBConnection();
        
        if (isset($_POST['action']) && $_POST['action'] === 'import_defaults') {
            // Import default homepage content from website
            $default_hero = [
                'title' => 'Welcome to Sadat Victorian Association',
                'title_fa' => 'به انجمن سادات ویکتوریایی خوش آمدید',
                'subtitle' => 'A community dedicated to preserving and sharing the rich heritage of Shia Islam',
                'subtitle_fa' => 'جامعه‌ای متعهد به حفظ و اشتراک‌گذاری میراث غنی اسلام شیعی',
                'learn_more_text' => 'Learn More',
                'learn_more_text_fa' => 'بیشتر بدانید',
                'events_text' => 'Upcoming Events',
                'events_text_fa' => 'رویدادهای آینده'
            ];
            
            $default_mission = [
                'title' => 'Our Mission',
                'title_fa' => 'ماموریت ما',
                'subtitle' => 'Connecting hearts, preserving traditions, and building community',
                'subtitle_fa' => 'اتصال قلب‌ها، حفظ سنت‌ها و ساخت جامعه'
            ];
            
            $default_features = [
                [
                    'icon' => '📚',
                    'title' => 'Education',
                    'title_fa' => 'آموزش',
                    'description' => 'Providing authentic Islamic education and resources for the Shia community',
                    'description_fa' => 'ارائه آموزش و منابع اصیل اسلامی برای جامعه شیعی'
                ],
                [
                    'icon' => '🤝',
                    'title' => 'Community',
                    'title_fa' => 'جامعه',
                    'description' => 'Building strong bonds and supporting one another in faith and daily life',
                    'description_fa' => 'ایجاد پیوندهای قوی و حمایت از یکدیگر در ایمان و زندگی روزمره'
                ],
                [
                    'icon' => '🕌',
                    'title' => 'Spirituality',
                    'title_fa' => 'معنویت',
                    'description' => 'Fostering spiritual growth through prayer, reflection, and religious observance',
                    'description_fa' => 'پرورش رشد معنوی از طریق نماز، تفکر و مراسم مذهبی'
                ],
                [
                    'icon' => '📅',
                    'title' => 'Events',
                    'title_fa' => 'رویدادها',
                    'description' => 'Organizing gatherings, lectures, and commemorations throughout the year',
                    'description_fa' => 'سازماندهی گردهمایی‌ها، سخنرانی‌ها و یادبودها در طول سال'
                ]
            ];
            
            $stmt = $pdo->prepare("INSERT INTO homepage (section, data) VALUES (?, ?)
                                  ON DUPLICATE KEY UPDATE data=VALUES(data)");
            $stmt->execute(['hero', json_encode($default_hero, JSON_UNESCAPED_UNICODE)]);
            $stmt->execute(['mission', json_encode($default_mission, JSON_UNESCAPED_UNICODE)]);
            $stmt->execute(['features', json_encode($default_features, JSON_UNESCAPED_UNICODE)]);
            
            $success = 'Default homepage content imported successfully!';
        } else {
            // Save hero section
        $hero_data = [
            'title' => $_POST['hero_title'] ?? '',
            'title_fa' => $_POST['hero_title_fa'] ?? '',
            'subtitle' => $_POST['hero_subtitle'] ?? '',
            'subtitle_fa' => $_POST['hero_subtitle_fa'] ?? '',
            'learn_more_text' => $_POST['learn_more_text'] ?? '',
            'learn_more_text_fa' => $_POST['learn_more_text_fa'] ?? '',
            'events_text' => $_POST['events_text'] ?? '',
            'events_text_fa' => $_POST['events_text_fa'] ?? ''
        ];
        
        $stmt = $pdo->prepare("INSERT INTO homepage (section, data) VALUES (?, ?)
                              ON DUPLICATE KEY UPDATE data=VALUES(data)");
        $stmt->execute(['hero', json_encode($hero_data, JSON_UNESCAPED_UNICODE)]);
        
        // Save mission section
        $mission_data = [
            'title' => $_POST['mission_title'] ?? '',
            'title_fa' => $_POST['mission_title_fa'] ?? '',
            'subtitle' => $_POST['mission_subtitle'] ?? '',
            'subtitle_fa' => $_POST['mission_subtitle_fa'] ?? ''
        ];
        $stmt->execute(['mission', json_encode($mission_data, JSON_UNESCAPED_UNICODE)]);
        
        // Save features section
        $features = [];
        if (isset($_POST['feature_icon']) && is_array($_POST['feature_icon'])) {
            for ($i = 0; $i < count($_POST['feature_icon']); $i++) {
                $features[] = [
                    'icon' => $_POST['feature_icon'][$i] ?? '',
                    'title' => $_POST['feature_title'][$i] ?? '',
                    'title_fa' => $_POST['feature_title_fa'][$i] ?? '',
                    'description' => $_POST['feature_description'][$i] ?? '',
                    'description_fa' => $_POST['feature_description_fa'][$i] ?? ''
                ];
            }
        }
        $stmt->execute(['features', json_encode($features, JSON_UNESCAPED_UNICODE)]);
        
        $success = 'Homepage content updated successfully!';
        }
    } catch (Exception $e) {
        error_log("Error updating homepage: " . $e->getMessage());
        $error = 'Failed to update homepage content. Please try again.';
    }
}

// Load data from database
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT section, data FROM homepage");
    $homepage_rows = $stmt->fetchAll();
    
    $homepage_data = [];
    foreach ($homepage_rows as $row) {
        $homepage_data[$row['section']] = json_decode($row['data'], true);
    }
    
    // Set defaults if empty
    if (empty($homepage_data['hero'])) {
        $homepage_data['hero'] = [
            'title' => '', 'title_fa' => '', 'subtitle' => '', 'subtitle_fa' => '',
            'learn_more_text' => '', 'learn_more_text_fa' => '',
            'events_text' => '', 'events_text_fa' => ''
        ];
    }
    if (empty($homepage_data['mission'])) {
        $homepage_data['mission'] = [
            'title' => '', 'title_fa' => '', 'subtitle' => '', 'subtitle_fa' => ''
        ];
    }
    if (empty($homepage_data['features'])) {
        $homepage_data['features'] = [
            ['icon' => '📚', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
            ['icon' => '🤝', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
            ['icon' => '🕌', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
            ['icon' => '📅', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => '']
        ];
    }
} catch (Exception $e) {
    error_log("Error loading homepage data: " . $e->getMessage());
    $homepage_data = [
        'hero' => ['title' => '', 'title_fa' => '', 'subtitle' => '', 'subtitle_fa' => '', 'learn_more_text' => '', 'learn_more_text_fa' => '', 'events_text' => '', 'events_text_fa' => ''],
        'mission' => ['title' => '', 'title_fa' => '', 'subtitle' => '', 'subtitle_fa' => ''],
        'features' => []
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Homepage - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Manage Homepage Content</h1>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <form method="POST" class="admin-form">
            <div class="admin-form-section">
                <h2>Hero Section</h2>
                <div class="form-group">
                    <label>Hero Title (English) *</label>
                    <input type="text" name="hero_title" required value="<?php echo htmlspecialchars($homepage_data['hero']['title'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Hero Title (Farsi) *</label>
                    <input type="text" name="hero_title_fa" required value="<?php echo htmlspecialchars($homepage_data['hero']['title_fa'] ?? ''); ?>" dir="rtl">
                </div>
                <div class="form-group">
                    <label>Hero Subtitle (English) *</label>
                    <textarea name="hero_subtitle" rows="2" required><?php echo htmlspecialchars($homepage_data['hero']['subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Hero Subtitle (Farsi) *</label>
                    <textarea name="hero_subtitle_fa" rows="2" required dir="rtl"><?php echo htmlspecialchars($homepage_data['hero']['subtitle_fa'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Learn More Button Text (English)</label>
                    <input type="text" name="learn_more_text" value="<?php echo htmlspecialchars($homepage_data['hero']['learn_more_text'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Learn More Button Text (Farsi)</label>
                    <input type="text" name="learn_more_text_fa" value="<?php echo htmlspecialchars($homepage_data['hero']['learn_more_text_fa'] ?? ''); ?>" dir="rtl">
                </div>
                <div class="form-group">
                    <label>Events Button Text (English)</label>
                    <input type="text" name="events_text" value="<?php echo htmlspecialchars($homepage_data['hero']['events_text'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Events Button Text (Farsi)</label>
                    <input type="text" name="events_text_fa" value="<?php echo htmlspecialchars($homepage_data['hero']['events_text_fa'] ?? ''); ?>" dir="rtl">
                </div>
            </div>
            
            <div class="admin-form-section">
                <h2>Mission Section</h2>
                <div class="form-group">
                    <label>Mission Title (English) *</label>
                    <input type="text" name="mission_title" required value="<?php echo htmlspecialchars($homepage_data['mission']['title'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Mission Title (Farsi) *</label>
                    <input type="text" name="mission_title_fa" required value="<?php echo htmlspecialchars($homepage_data['mission']['title_fa'] ?? ''); ?>" dir="rtl">
                </div>
                <div class="form-group">
                    <label>Mission Subtitle (English) *</label>
                    <textarea name="mission_subtitle" rows="2" required><?php echo htmlspecialchars($homepage_data['mission']['subtitle'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Mission Subtitle (Farsi) *</label>
                    <textarea name="mission_subtitle_fa" rows="2" required dir="rtl"><?php echo htmlspecialchars($homepage_data['mission']['subtitle_fa'] ?? ''); ?></textarea>
                </div>
            </div>
            
            <div class="admin-form-section">
                <h2>Features Section</h2>
                <div id="features-container">
                    <?php 
                    $features = $homepage_data['features'] ?? [];
                    if (empty($features)) {
                        $features = [
                            ['icon' => '📚', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
                            ['icon' => '🤝', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
                            ['icon' => '🕌', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => ''],
                            ['icon' => '📅', 'title' => '', 'title_fa' => '', 'description' => '', 'description_fa' => '']
                        ];
                    }
                    foreach ($features as $index => $feature): ?>
                        <div class="feature-edit-item" style="border: 1px solid #ddd; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                            <h3>Feature <?php echo $index + 1; ?></h3>
                            <div class="form-group">
                                <label>Icon (emoji)</label>
                                <input type="text" name="feature_icon[]" value="<?php echo htmlspecialchars($feature['icon'] ?? ''); ?>" maxlength="2">
                            </div>
                            <div class="form-group">
                                <label>Title (English) *</label>
                                <input type="text" name="feature_title[]" required value="<?php echo htmlspecialchars($feature['title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label>Title (Farsi) *</label>
                                <input type="text" name="feature_title_fa[]" required value="<?php echo htmlspecialchars($feature['title_fa'] ?? ''); ?>" dir="rtl">
                            </div>
                            <div class="form-group">
                                <label>Description (English) *</label>
                                <textarea name="feature_description[]" rows="2" required><?php echo htmlspecialchars($feature['description'] ?? ''); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Description (Farsi) *</label>
                                <textarea name="feature_description_fa[]" rows="2" required dir="rtl"><?php echo htmlspecialchars($feature['description_fa'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    <?php endforeach; ?>
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

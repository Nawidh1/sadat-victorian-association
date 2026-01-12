<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$page_title = 'Manage Resources';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'import_defaults') {
                // Import default resources from website
                $default_resources = [
                    // Understanding Shia Islam
                    ['id' => 'resource_ahl_bayt', 'category' => 'understanding', 'title' => 'The Ahl al-Bayt', 'title_fa' => 'اهل بیت', 'description' => 'The People of the Household of the Prophet (PBUH), including Imam Ali (AS), Fatima (AS), and the Twelve Imams. Learn about their lives, teachings, and significance in Shia Islam.', 'description_fa' => 'اهل بیت پیامبر (ص)، شامل امام علی (ع)، فاطمه (ع) و دوازده امام. درباره زندگی، تعالیم و اهمیت آن‌ها در اسلام شیعی بیاموزید.'],
                    ['id' => 'resource_shia_beliefs', 'category' => 'understanding', 'title' => 'Shia Beliefs & Principles', 'title_fa' => 'اعتقادات و اصول شیعه', 'description' => 'Core beliefs including Tawhid (Oneness of God), Nubuwwah (Prophethood), Imamate (Leadership), and the Day of Judgment.', 'description_fa' => 'اعتقادات اصلی شامل توحید (یگانگی خدا)، نبوت، امامت (رهبری) و روز قیامت.'],
                    ['id' => 'resource_history', 'category' => 'understanding', 'title' => 'History of Shia Islam', 'title_fa' => 'تاریخ اسلام شیعی', 'description' => 'The historical development of Shia Islam from the time of the Prophet (PBUH) through the events of Karbala and beyond.', 'description_fa' => 'توسعه تاریخی اسلام شیعی از زمان پیامبر (ص) تا رویدادهای کربلا و فراتر از آن.'],
                    ['id' => 'resource_twelve_imams', 'category' => 'understanding', 'title' => 'The Twelve Imams', 'title_fa' => 'دوازده امام', 'description' => 'Learn about the twelve divinely appointed Imams, their lives, teachings, and their role as guides for the Muslim community.', 'description_fa' => 'درباره دوازده امام منصوب الهی، زندگی، تعالیم و نقش آن‌ها به عنوان راهنمای جامعه مسلمانان بیاموزید.'],
                    // Prayers & Supplications
                    ['id' => 'resource_daily_prayers', 'category' => 'prayers', 'title' => 'Daily Prayers (Salat)', 'title_fa' => 'نمازهای روزانه (صلات)', 'description' => 'Guidance on performing the five daily prayers according to Shia jurisprudence, including timings, qibla direction, and prayer formats.', 'description_fa' => 'راهنمای انجام پنج نماز روزانه طبق فقه شیعه، شامل زمان‌ها، جهت قبله و فرمت‌های نماز.'],
                    ['id' => 'resource_duas', 'category' => 'prayers', 'title' => 'Duas & Supplications', 'title_fa' => 'دعاها و مناجات', 'description' => 'Collection of authentic duas from the Ahl al-Bayt, including Dua Kumayl, Dua Tawassul, and supplications for various occasions.', 'description_fa' => 'مجموعه دعاهای اصیل از اهل بیت، شامل دعای کمیل، دعای توسل و مناجات برای مناسبت‌های مختلف.'],
                    ['id' => 'resource_ziyarat', 'category' => 'prayers', 'title' => 'Ziyarat', 'title_fa' => 'زیارات', 'description' => 'Visitation prayers for the Imams and holy sites, including Ziyarat Ashura and Ziyarat Waritha.', 'description_fa' => 'زیارت‌نامه‌های امامان و اماکن مقدس، شامل زیارت عاشورا و زیارت وارثه.'],
                    ['id' => 'resource_night_prayers', 'category' => 'prayers', 'title' => 'Special Night Prayers', 'title_fa' => 'نمازهای شبانه ویژه', 'description' => 'Guidance for Laylat al-Qadr prayers, night prayers during Ramadan, and other special spiritual occasions.', 'description_fa' => 'راهنمای نمازهای شب قدر، نمازهای شبانه در رمضان و مناسبت‌های معنوی ویژه دیگر.'],
                    // Important Dates
                    ['id' => 'resource_islamic_new_year', 'category' => 'dates', 'title' => 'Islamic New Year - 1st Muharram', 'title_fa' => 'سال نو اسلامی - اول محرم', 'description' => 'Beginning of the Islamic calendar year', 'description_fa' => 'آغاز سال تقویم اسلامی', 'date' => '1st Muharram'],
                    ['id' => 'resource_ashura', 'category' => 'dates', 'title' => 'Day of Ashura - 10th Muharram', 'title_fa' => 'روز عاشورا - دهم محرم', 'description' => 'Martyrdom of Imam Hussain (AS) at Karbala (680 CE)', 'description_fa' => 'شهادت امام حسین (ع) در کربلا (680 میلادی)', 'date' => '10th Muharram'],
                    ['id' => 'resource_arbaeen', 'category' => 'dates', 'title' => 'Arbaeen - 20th Safar', 'title_fa' => 'اربعین - بیستم صفر', 'description' => '40th day after Ashura, commemorating the martyrdom of Imam Hussain (AS)', 'description_fa' => 'چهلمین روز پس از عاشورا، گرامیداشت شهادت امام حسین (ع)', 'date' => '20th Safar'],
                    // Recommended Reading
                    ['id' => 'resource_nahj', 'category' => 'reading', 'title' => 'Nahj al-Balagha', 'title_fa' => 'نهج البلاغه', 'description' => 'Collection of sermons, letters, and sayings of Imam Ali (AS)', 'description_fa' => 'مجموعه خطبه‌ها، نامه‌ها و سخنان امام علی (ع)', 'author' => 'Compiled by Sayyid al-Radi', 'author_fa' => 'گردآوری شده توسط سید رضی'],
                    ['id' => 'resource_sahifa', 'category' => 'reading', 'title' => 'Sahifa al-Sajjadiyya', 'title_fa' => 'صحیفه سجادیه', 'description' => 'Collection of supplications by the fourth Imam', 'description_fa' => 'مجموعه دعاهای امام چهارم', 'author' => 'Imam Zain al-Abidin (AS)', 'author_fa' => 'امام زین العابدین (ع)'],
                    // Community Services
                    ['id' => 'resource_marriage', 'category' => 'services', 'title' => 'Marriage Services', 'title_fa' => 'خدمات ازدواج', 'description' => 'Assistance with Islamic marriage ceremonies and documentation', 'description_fa' => 'کمک در مراسم و مستندات ازدواج اسلامی'],
                    ['id' => 'resource_guidance', 'category' => 'services', 'title' => 'Religious Guidance', 'title_fa' => 'راهنمایی مذهبی', 'description' => 'Access to scholars for religious questions and guidance', 'description_fa' => 'دسترسی به علماء برای سوالات و راهنمایی مذهبی'],
                    ['id' => 'resource_support', 'category' => 'services', 'title' => 'Community Support', 'title_fa' => 'حمایت جامعه', 'description' => 'Support services for community members in need', 'description_fa' => 'خدمات پشتیبانی برای اعضای نیازمند جامعه'],
                    ['id' => 'resource_library', 'category' => 'services', 'title' => 'Library Access', 'title_fa' => 'دسترسی به کتابخانه', 'description' => 'Community library with books on Islamic studies and Shia literature', 'description_fa' => 'کتابخانه جامعه با کتاب‌های مطالعات اسلامی و ادبیات شیعی']
                ];
                
                $imported = 0;
                $skipped = 0;
                
                foreach ($default_resources as $resource) {
                    // Check if resource already exists
                    $check_stmt = $pdo->prepare("SELECT id FROM resources WHERE id = ?");
                    $check_stmt->execute([$resource['id']]);
                    
                    if ($check_stmt->fetch()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Insert resource
                    $stmt = $pdo->prepare("INSERT INTO resources (id, category, title, title_fa, description, description_fa, date, author, author_fa) 
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $resource['id'],
                        $resource['category'],
                        $resource['title'],
                        $resource['title_fa'] ?? '',
                        $resource['description'] ?? '',
                        $resource['description_fa'] ?? '',
                        $resource['date'] ?? '',
                        $resource['author'] ?? '',
                        $resource['author_fa'] ?? ''
                    ]);
                    $imported++;
                }
                
                if ($imported > 0) {
                    $success = "Successfully imported $imported default resource(s)" . ($skipped > 0 ? " ($skipped already existed)" : "") . "!";
                } else {
                    $success = "All default resources already exist in the database.";
                }
                
            } elseif ($_POST['action'] === 'add') {
                $resource_id = uniqid('resource_');
                $stmt = $pdo->prepare(
                    "INSERT INTO resources (id, category, title, title_fa, description, description_fa) 
                     VALUES (:id, :category, :title, :title_fa, :description, :description_fa)"
                );
                $stmt->execute([
                    ':id' => $resource_id,
                    ':category' => $_POST['category'] ?? 'understanding',
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':description' => $_POST['description'] ?? '',
                    ':description_fa' => $_POST['description_fa'] ?? ''
                ]);
                $success = 'Resource added successfully!';
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $pdo->prepare(
                    "UPDATE resources SET category = :category, title = :title, title_fa = :title_fa, description = :description, description_fa = :description_fa WHERE id = :id"
                );
                $stmt->execute([
                    ':category' => $_POST['category'] ?? 'understanding',
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':description' => $_POST['description'] ?? '',
                    ':description_fa' => $_POST['description_fa'] ?? '',
                    ':id' => $_POST['id']
                ]);
                $success = 'Resource updated successfully!';
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM resources WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                $success = 'Resource deleted successfully!';
            }
        }
    }

    // Fetch all resources
    $stmt = $pdo->query("SELECT * FROM resources ORDER BY category, title");
    $resources = $stmt->fetchAll();

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $resources = []; // Fallback to empty array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resources - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Manage Resources</h1>
        
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <div class="admin-layout">
                <div class="admin-form-section">
                <h2>Add New Resource</h2>
                <form method="POST" class="admin-form" id="addResourceForm">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select name="category" id="category" required>
                            <option value="understanding">Understanding Shia Islam</option>
                            <option value="prayers">Prayers & Supplications</option>
                            <option value="dates">Important Dates</option>
                            <option value="reading">Recommended Reading</option>
                            <option value="services">Community Services</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="title">Title (English) *</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="title_fa">Title (Farsi) *</label>
                        <input type="text" name="title_fa" id="title_fa" required dir="rtl">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (English) *</label>
                        <textarea name="description" id="description" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_fa">Description (Farsi) *</label>
                        <textarea name="description_fa" id="description_fa" rows="5" required dir="rtl"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Resource</button>
                    </div>
                </form>
                </div>
            
                <div class="admin-list-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="margin: 0;">Existing Resources (<?php echo count($resources); ?>)</h2>
                </div>
                <div class="items-list">
                    <?php if (empty($resources)): ?>
                        <p class="empty-state">No resources yet. Add your first resource!</p>
                    <?php else: ?>
                        <?php foreach ($resources as $resource): ?>
                            <div class="item-card" 
                                data-id="<?php echo htmlspecialchars($resource['id']); ?>"
                                data-category="<?php echo htmlspecialchars($resource['category'] ?? 'understanding'); ?>"
                                data-title="<?php echo htmlspecialchars($resource['title']); ?>"
                                data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>"
                                data-description="<?php echo htmlspecialchars($resource['description'] ?? ''); ?>"
                                data-description-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? ''); ?>">
                                <div class="item-header">
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <span class="badge"><?php echo ucfirst($resource['category']); ?></span>
                                </div>
                                <div class="item-details">
                                    <?php 
                                    $description = $resource['description'] ?? '';
                                    if (!empty($description)) {
                                        $short_desc = substr($description, 0, 150);
                                        echo '<p>' . htmlspecialchars($short_desc);
                                        if (strlen($description) > 150) {
                                            echo '...';
                                        }
                                        echo '</p>';
                                    } else {
                                        echo '<p style="color: var(--text-light); font-style: italic;">No description</p>';
                                    }
                                    ?>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="btn btn-edit edit-item-btn" data-type="resource">✏️ Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this resource?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($resource['id']); ?>">
                                        <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="admin-modal">
        <div class="admin-modal-content">
            <span class="admin-modal-close">&times;</span>
            <h2>Edit Resource</h2>
            <form method="POST" class="admin-form" id="editResourceForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_category">Category *</label>
                    <select id="edit_category" name="category" required>
                        <option value="understanding">Understanding Shia Islam</option>
                        <option value="prayers">Prayers & Supplications</option>
                        <option value="dates">Important Dates</option>
                        <option value="reading">Recommended Reading</option>
                        <option value="services">Community Services</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_title">Title (English) *</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_title_fa">Title (Farsi) *</label>
                    <input type="text" id="edit_title_fa" name="title_fa" required dir="rtl">
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description (English) *</label>
                    <textarea id="edit_description" name="description" rows="5" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_description_fa">Description (Farsi) *</label>
                    <textarea id="edit_description_fa" name="description_fa" rows="5" required dir="rtl"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Resource</button>
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script src="admin-edit-modal.js"></script>
</body>
</html>


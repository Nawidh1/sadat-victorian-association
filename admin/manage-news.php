<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$page_title = 'Manage News';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'import_defaults') {
                // Import default news items from website
                $default_news = [
                    [
                        'id' => 'news_friday_prayers',
                        'title' => 'Weekly Friday Prayers',
                        'title_fa' => 'نماز جمعه هفتگی',
                        'date' => '2024-12-15',
                        'content' => "Join us every Friday for Jumu'ah prayers and community gathering. All are welcome.",
                        'content_fa' => 'هر جمعه برای نماز جمعه و گردهمایی جامعه به ما بپیوندید. همه خوش آمدند.'
                    ],
                    [
                        'id' => 'news_muharram',
                        'title' => 'Muharram Commemoration',
                        'title_fa' => 'یادبود محرم',
                        'date' => '2024-12-20',
                        'content' => 'Annual commemoration events honoring the martyrdom of Imam Hussain (AS) and his companions.',
                        'content_fa' => 'رویدادهای یادبود سالانه برای گرامیداشت شهادت امام حسین (ع) و یارانش.'
                    ],
                    [
                        'id' => 'news_islamic_studies',
                        'title' => 'Islamic Studies Program',
                        'title_fa' => 'برنامه مطالعات اسلامی',
                        'date' => '2024-12-25',
                        'content' => 'New educational program starting for adults and youth. Registration now open.',
                        'content_fa' => 'برنامه آموزشی جدید برای بزرگسالان و جوانان شروع می‌شود. ثبت‌نام هم اکنون باز است.'
                    ]
                ];
                
                $imported = 0;
                $skipped = 0;
                
                foreach ($default_news as $item) {
                    // Check if news already exists
                    $check_stmt = $pdo->prepare("SELECT id FROM news WHERE id = ?");
                    $check_stmt->execute([$item['id']]);
                    
                    if ($check_stmt->fetch()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Insert news
                    $stmt = $pdo->prepare("INSERT INTO news (id, title, title_fa, date, content, content_fa) 
                                          VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $item['id'],
                        $item['title'],
                        $item['title_fa'],
                        $item['date'],
                        $item['content'],
                        $item['content_fa']
                    ]);
                    $imported++;
                }
                
                if ($imported > 0) {
                    $success = "Successfully imported $imported default news item(s)" . ($skipped > 0 ? " ($skipped already existed)" : "") . "!";
                } else {
                    $success = "All default news items already exist in the database.";
                }
                
            } elseif ($_POST['action'] === 'add') {
                $news_id = uniqid('news_');
                
                // Handle image upload
                $image_path = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../uploads/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $file_name = 'news_' . $news_id . '_' . time() . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                            $image_path = 'uploads/images/' . $file_name;
                        } else {
                            $error = 'Failed to upload image.';
                        }
                    } else {
                        $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.';
                    }
                }
                
                $stmt = $pdo->prepare(
                    "INSERT INTO news (id, title, title_fa, date, content, content_fa, image) 
                     VALUES (:id, :title, :title_fa, :date, :content, :content_fa, :image)"
                );
                $stmt->execute([
                    ':id' => $news_id,
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':date' => $_POST['date'] ?? date('Y-m-d'),
                    ':content' => $_POST['content'] ?? '',
                    ':content_fa' => $_POST['content_fa'] ?? '',
                    ':image' => $image_path
                ]);
                $success = 'News item added successfully!';
            } elseif ($_POST['action'] === 'edit') {
                // Handle image upload for edit
                $image_path = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../uploads/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $file_name = 'news_' . $_POST['id'] . '_' . time() . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $file_path)) {
                            $image_path = 'uploads/images/' . $file_name;
                            
                            // Delete old image if exists
                            $old_stmt = $pdo->prepare("SELECT image FROM news WHERE id = :id");
                            $old_stmt->execute([':id' => $_POST['id']]);
                            $old_image = $old_stmt->fetchColumn();
                            if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                                unlink(__DIR__ . '/../' . $old_image);
                            }
                        } else {
                            $error = 'Failed to upload image.';
                        }
                    } else {
                        $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.';
                    }
                } elseif (isset($_POST['existing_image']) && !empty($_POST['existing_image'])) {
                    // Keep existing image if no new one uploaded
                    $image_path = $_POST['existing_image'];
                } else {
                    // Get current image from database
                    $current_stmt = $pdo->prepare("SELECT image FROM news WHERE id = :id");
                    $current_stmt->execute([':id' => $_POST['id']]);
                    $image_path = $current_stmt->fetchColumn() ?: '';
                }
                
                $stmt = $pdo->prepare(
                    "UPDATE news SET title = :title, title_fa = :title_fa, date = :date, content = :content, content_fa = :content_fa, image = :image WHERE id = :id"
                );
                $stmt->execute([
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':date' => $_POST['date'] ?? date('Y-m-d'),
                    ':content' => $_POST['content'] ?? '',
                    ':content_fa' => $_POST['content_fa'] ?? '',
                    ':image' => $image_path,
                    ':id' => $_POST['id']
                ]);
                $success = 'News item updated successfully!';
            } elseif ($_POST['action'] === 'upload_image_only') {
                // Handle image upload only for existing news item
                $news_id = $_POST['news_id'] ?? '';
                if (empty($news_id)) {
                    $error = 'News item ID is required.';
                } else {
                    $image_path = '';
                    if (isset($_FILES['news_image']) && $_FILES['news_image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/images/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = strtolower(pathinfo($_FILES['news_image']['name'], PATHINFO_EXTENSION));
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $file_name = 'news_' . $news_id . '_' . time() . '.' . $file_extension;
                            $file_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['news_image']['tmp_name'], $file_path)) {
                                $image_path = 'uploads/images/' . $file_name;
                                
                                // Delete old image if exists
                                $old_stmt = $pdo->prepare("SELECT image FROM news WHERE id = :id");
                                $old_stmt->execute([':id' => $news_id]);
                                $old_image = $old_stmt->fetchColumn();
                                if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                                    unlink(__DIR__ . '/../' . $old_image);
                                }
                                
                                // Update news item with new image
                                $stmt = $pdo->prepare("UPDATE news SET image = :image WHERE id = :id");
                                $stmt->execute([':image' => $image_path, ':id' => $news_id]);
                                $success = 'Image uploaded successfully!';
                            } else {
                                $error = 'Failed to upload image.';
                            }
                        } else {
                            $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WEBP images only.';
                        }
                    } else {
                        $error = 'Please select an image file.';
                    }
                }
            } elseif ($_POST['action'] === 'delete') {
                // Delete image file if exists
                $img_stmt = $pdo->prepare("SELECT image FROM news WHERE id = :id");
                $img_stmt->execute([':id' => $_POST['id']]);
                $image_path = $img_stmt->fetchColumn();
                if ($image_path && file_exists(__DIR__ . '/../' . $image_path)) {
                    unlink(__DIR__ . '/../' . $image_path);
                }
                
                $stmt = $pdo->prepare("DELETE FROM news WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                $success = 'News item deleted successfully!';
            }
        }
    }

    // Fetch all news items
    $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC");
    $news = $stmt->fetchAll();

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $news = []; // Fallback to empty array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Manage News</h1>
        
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <!-- Separate Form for Image Upload Only -->
            <div class="admin-form-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 2px solid var(--primary-color); border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
                <h2 style="color: var(--primary-color); margin-top: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">📷</span>
                    Upload Image for News Item
                </h2>
                <p style="margin-bottom: 1.5rem; color: var(--text-light);">Quick upload image for an existing news item. Select a news item and upload its image without editing other content.</p>
                
                <form method="POST" class="admin-form" enctype="multipart/form-data" id="imageUploadForm">
                    <input type="hidden" name="action" value="upload_image_only">
                    
                    <div class="form-group">
                        <label for="news_id_select">Select News Item *</label>
                        <select name="news_id" id="news_id_select" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; font-size: 1rem;">
                            <option value="">-- Select a news item --</option>
                            <?php foreach ($news as $item): ?>
                                <option value="<?php echo htmlspecialchars($item['id']); ?>" data-image="<?php echo htmlspecialchars($item['image'] ?? ''); ?>">
                                    <?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['date']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="currentImagePreview" style="margin-bottom: 1.5rem; display: none;">
                        <p style="margin-bottom: 0.5rem; font-weight: 600; color: var(--text-dark);">Current Image:</p>
                        <div style="position: relative; display: inline-block;">
                            <img id="currentImageDisplay" src="" alt="Current Image" style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <button type="button" onclick="removeNewsImageFromUpload()" class="btn-delete-image-small" style="position: absolute; top: 10px; right: 10px; padding: 0.5rem 1rem; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">
                                <span>🗑️</span> Remove
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="news_image">Upload New Image</label>
                        <input type="file" name="news_image" id="news_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                        <small style="display: block; margin-top: 0.5rem; color: var(--text-light);">Recommended size: 800x450px. Supported formats: JPG, PNG, GIF, WEBP</small>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
                        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">📤</span>
                            Upload Image Only
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="admin-layout">
                <div class="admin-form-section">
                <h2>Add New News Item</h2>
                <form method="POST" class="admin-form" id="addNewsForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="image">News Image</label>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <small style="display: block; margin-top: 0.5rem; color: var(--text-light);">Recommended size: 800x450px. Supported formats: JPG, PNG, GIF, WEBP</small>
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
                        <label for="date">Date *</label>
                        <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Content (English) *</label>
                        <textarea name="content" id="content" rows="6" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content_fa">Content (Farsi) *</label>
                        <textarea name="content_fa" id="content_fa" rows="6" required dir="rtl"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add News</button>
                    </div>
                </form>
                </div>
            
                <div class="admin-list-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="margin: 0;">Existing News (<?php echo count($news); ?>)</h2>
                </div>
                <div class="items-list">
                    <?php if (empty($news)): ?>
                        <p class="empty-state">No news items yet. Add your first news item!</p>
                    <?php else: ?>
                        <?php foreach ($news as $item): ?>
                            <div class="item-card" 
                                data-id="<?php echo htmlspecialchars($item['id']); ?>"
                                data-title="<?php echo htmlspecialchars($item['title']); ?>"
                                data-title-fa="<?php echo htmlspecialchars($item['title_fa'] ?? ''); ?>"
                                data-date="<?php echo htmlspecialchars($item['date']); ?>"
                                data-content="<?php echo htmlspecialchars($item['content'] ?? ''); ?>"
                                data-content-fa="<?php echo htmlspecialchars($item['content_fa'] ?? ''); ?>"
                                data-image="<?php echo htmlspecialchars($item['image'] ?? ''); ?>">
                                <div class="item-header">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                </div>
                                <?php if (!empty($item['image'])): ?>
                                <div class="item-image-preview">
                                    <img src="../<?php echo htmlspecialchars($item['image']); ?>" alt="News Image" style="width: 100%; max-width: 200px; height: auto; border-radius: 8px; margin-bottom: 0.5rem;">
                                </div>
                                <?php endif; ?>
                                <div class="item-details">
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($item['date']); ?></p>
                                    <p><?php echo htmlspecialchars(substr($item['content'], 0, 150)); ?>...</p>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="btn btn-edit edit-item-btn" data-type="news">✏️ Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this news item?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
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
            <h2>Edit News Item</h2>
            <form method="POST" class="admin-form" id="editNewsForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                
                <div class="form-group">
                    <label for="edit_image">News Image</label>
                    <div id="edit_image_preview" style="margin-bottom: 1rem; display: none;">
                        <p><strong>Current Image:</strong></p>
                        <img id="edit_image_display" src="" alt="Current Image" style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 0.5rem;">
                        <button type="button" onclick="removeNewsImage()" class="btn-delete-image-small" style="margin-bottom: 0.5rem; padding: 0.5rem 1rem; background: #dc3545; color: white; border: none; border-radius: 6px; cursor: pointer;">
                            <span>🗑️</span> Remove Image
                        </button>
                    </div>
                    <input type="file" name="image" id="edit_image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    <small style="display: block; margin-top: 0.5rem; color: var(--text-light);">Upload new image to replace current one. Recommended size: 800x450px.</small>
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
                    <label for="edit_date">Date *</label>
                    <input type="date" id="edit_date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_content">Content (English) *</label>
                    <textarea id="edit_content" name="content" rows="6" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_content_fa">Content (Farsi) *</label>
                    <textarea id="edit_content_fa" name="content_fa" rows="6" required dir="rtl"></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update News</button>
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script src="admin-edit-modal.js"></script>
    <script>
        // Remove news image function for edit modal
        function removeNewsImage() {
            if (confirm('Are you sure you want to remove this image?')) {
                const editExistingImage = document.getElementById('edit_existing_image');
                const editImagePreview = document.getElementById('edit_image_preview');
                const editImage = document.getElementById('edit_image');
                
                if (editExistingImage && editImagePreview && editImage) {
                    editExistingImage.value = '';
                    editImagePreview.style.display = 'none';
                    editImage.value = '';
                }
            }
        }
        
        // Show current image when news item is selected
        document.addEventListener('DOMContentLoaded', function() {
            const newsSelect = document.getElementById('news_id_select');
            const imagePreview = document.getElementById('currentImagePreview');
            const imageDisplay = document.getElementById('currentImageDisplay');
            
            if (newsSelect && imagePreview && imageDisplay) {
                newsSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const imagePath = selectedOption.getAttribute('data-image');
                    
                    if (imagePath && imagePath !== '') {
                        imageDisplay.src = '../' + imagePath;
                        imagePreview.style.display = 'block';
                    } else {
                        imagePreview.style.display = 'none';
                    }
                });
            }
        });
        
        // Remove image from image upload form
        function removeNewsImageFromUpload() {
            const newsId = document.getElementById('news_id_select').value;
            if (!newsId) {
                alert('Please select a news item first.');
                return;
            }
            
            if (confirm('Are you sure you want to remove this image? This will delete the image file.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.style.display = 'none';
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'edit';
                form.appendChild(actionInput);
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = newsId;
                form.appendChild(idInput);
                
                // Get current values to preserve them
                const currentTitle = document.querySelector('.item-card[data-id="' + newsId + '"]')?.getAttribute('data-title') || '';
                const currentTitleFa = document.querySelector('.item-card[data-id="' + newsId + '"]')?.getAttribute('data-title-fa') || '';
                const currentDate = document.querySelector('.item-card[data-id="' + newsId + '"]')?.getAttribute('data-date') || '';
                const currentContent = document.querySelector('.item-card[data-id="' + newsId + '"]')?.getAttribute('data-content') || '';
                const currentContentFa = document.querySelector('.item-card[data-id="' + newsId + '"]')?.getAttribute('data-content-fa') || '';
                
                const titleInput = document.createElement('input');
                titleInput.type = 'hidden';
                titleInput.name = 'title';
                titleInput.value = currentTitle;
                form.appendChild(titleInput);
                
                const titleFaInput = document.createElement('input');
                titleFaInput.type = 'hidden';
                titleFaInput.name = 'title_fa';
                titleFaInput.value = currentTitleFa;
                form.appendChild(titleFaInput);
                
                const dateInput = document.createElement('input');
                dateInput.type = 'hidden';
                dateInput.name = 'date';
                dateInput.value = currentDate;
                form.appendChild(dateInput);
                
                const contentInput = document.createElement('input');
                contentInput.type = 'hidden';
                contentInput.name = 'content';
                contentInput.value = currentContent;
                form.appendChild(contentInput);
                
                const contentFaInput = document.createElement('input');
                contentFaInput.type = 'hidden';
                contentFaInput.name = 'content_fa';
                contentFaInput.value = currentContentFa;
                form.appendChild(contentFaInput);
                
                const imageInput = document.createElement('input');
                imageInput.type = 'hidden';
                imageInput.name = 'image';
                imageInput.value = '';
                form.appendChild(imageInput);
                
                const existingImageInput = document.createElement('input');
                existingImageInput.type = 'hidden';
                existingImageInput.name = 'existing_image';
                existingImageInput.value = '';
                form.appendChild(existingImageInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>

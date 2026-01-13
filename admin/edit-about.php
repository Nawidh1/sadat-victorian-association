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
        } elseif (isset($_POST['action']) && $_POST['action'] === 'upload_images_only') {
            // Handle image upload only (separate action)
            $image_fields = ['about_image_1', 'about_image_2', 'about_image_3'];
            $stmt = $pdo->prepare("INSERT INTO about (field, value_en, value_fa) VALUES (?, ?, ?)
                                   ON DUPLICATE KEY UPDATE value_en=VALUES(value_en), value_fa=VALUES(value_fa)");
            
            foreach ($image_fields as $field) {
                $image_path = '';
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../uploads/images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    
                    if (in_array($file_extension, $allowed_extensions)) {
                        $file_name = 'about_' . $field . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {
                            $image_path = 'uploads/images/' . $file_name;
                            
                            // Delete old image if exists
                            $old_stmt = $pdo->prepare("SELECT value_en FROM about WHERE field = ?");
                            $old_stmt->execute([$field]);
                            $old_image = $old_stmt->fetchColumn();
                            if ($old_image && file_exists(__DIR__ . '/../' . $old_image)) {
                                unlink(__DIR__ . '/../' . $old_image);
                            }
                        } else {
                            $error = 'Failed to upload image for ' . $field . '.';
                        }
                    } else {
                        $error = 'Invalid file type for ' . $field . '. Please upload JPG, PNG, GIF, or WEBP images only.';
                    }
                } elseif (isset($_POST['existing_' . $field]) && !empty($_POST['existing_' . $field])) {
                    // Keep existing image if no new one uploaded
                    $image_path = $_POST['existing_' . $field];
                }
                
                if (!empty($image_path)) {
                    $stmt->execute([$field, $image_path, '']);
                }
            }
            
            // Handle delete actions for individual images
            foreach ($image_fields as $field) {
                if (isset($_POST['delete_' . $field]) && $_POST['delete_' . $field] === '1') {
                    try {
                        $stmt = $pdo->prepare("SELECT value_en FROM about WHERE field = ?");
                        $stmt->execute([$field]);
                        $image_row = $stmt->fetch();
                        
                        if ($image_row && !empty($image_row['value_en'])) {
                            $image_path = $image_row['value_en'];
                            $full_path = __DIR__ . '/../' . $image_path;
                            
                            if (file_exists($full_path)) {
                                unlink($full_path);
                            }
                            
                            $delete_stmt = $pdo->prepare("DELETE FROM about WHERE field = ?");
                            $delete_stmt->execute([$field]);
                        }
                    } catch (Exception $e) {
                        error_log("Error deleting image $field: " . $e->getMessage());
                    }
                }
            }
            
            if (empty($error)) {
                $success = 'Images uploaded successfully!';
            }
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
            
            // Save about images (3 columns) - only if not using images-only form
            if (!isset($_POST['action']) || $_POST['action'] !== 'upload_images_only') {
                $image_fields = ['about_image_1', 'about_image_2', 'about_image_3'];
                foreach ($image_fields as $field) {
                    $image_path = '';
                    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = __DIR__ . '/../uploads/images/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $file_name = 'about_' . $field . '_' . time() . '_' . uniqid() . '.' . $file_extension;
                            $file_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {
                                $image_path = 'uploads/images/' . $file_name;
                            }
                        }
                    } elseif (isset($_POST['existing_' . $field]) && !empty($_POST['existing_' . $field])) {
                        // Keep existing image if no new one uploaded
                        $image_path = $_POST['existing_' . $field];
                    }
                    
                    if (!empty($image_path)) {
                        $stmt->execute([$field, $image_path, '']);
                    }
                }
                
                // Also handle delete actions for individual images
                foreach ($image_fields as $field) {
                    if (isset($_POST['delete_' . $field]) && $_POST['delete_' . $field] === '1') {
                        try {
                            $stmt = $pdo->prepare("SELECT value_en FROM about WHERE field = ?");
                            $stmt->execute([$field]);
                            $image_row = $stmt->fetch();
                            
                            if ($image_row && !empty($image_row['value_en'])) {
                                $image_path = $image_row['value_en'];
                                $full_path = __DIR__ . '/../' . $image_path;
                                
                                if (file_exists($full_path)) {
                                    unlink($full_path);
                                }
                                
                                $delete_stmt = $pdo->prepare("DELETE FROM about WHERE field = ?");
                                $delete_stmt->execute([$field]);
                            }
                        } catch (Exception $e) {
                            error_log("Error deleting image $field: " . $e->getMessage());
                        }
                    }
                }
            }
            
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
        'activities_fa' => '',
        'about_image_1' => '',
        'about_image_2' => '',
        'about_image_3' => ''
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
        } elseif ($field === 'about_image_1' || $field === 'about_image_2' || $field === 'about_image_3') {
            $about_data[$field] = $row['value_en'];
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
            <!-- Separate Form for Images Only -->
            <div class="admin-form-section" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%); border: 2px solid var(--primary-color); border-radius: 12px; padding: 2rem; margin-bottom: 2rem;">
                <h2 style="color: var(--primary-color); margin-top: 0; display: flex; align-items: center; gap: 0.75rem;">
                    <span style="font-size: 1.5rem;">📷</span>
                    Upload Images Only
                </h2>
                <p style="margin-bottom: 1.5rem; color: var(--text-light);">Quick upload for the 3 column images on the about page. This form only uploads images without affecting other content.</p>
                
                <form method="POST" class="admin-form" enctype="multipart/form-data" id="imagesOnlyForm">
                    <input type="hidden" name="action" value="upload_images_only">
                    
                    <?php 
                    $image_fields = [
                        '1' => ['field' => 'about_image_1', 'label' => 'Column 1', 'data' => $about_data['about_image_1'] ?? ''],
                        '2' => ['field' => 'about_image_2', 'label' => 'Column 2', 'data' => $about_data['about_image_2'] ?? ''],
                        '3' => ['field' => 'about_image_3', 'label' => 'Column 3', 'data' => $about_data['about_image_3'] ?? '']
                    ];
                    ?>
                    
                    <div class="three-column-upload-grid">
                        <?php foreach ($image_fields as $num => $img_data): ?>
                            <div class="column-upload-section">
                                <h3><?php echo htmlspecialchars($img_data['label']); ?></h3>
                                
                                <?php if (!empty($img_data['data'])): ?>
                                    <div class="image-preview-container-small">
                                        <div class="image-preview-frame-small">
                                            <img src="../<?php echo htmlspecialchars($img_data['data']); ?>" alt="Column <?php echo $num; ?> Image" class="preview-image-small" onclick="openImageModal('../<?php echo htmlspecialchars($img_data['data']); ?>')" style="cursor: pointer;">
                                            <div class="image-overlay-small">
                                                <button type="button" onclick="openImageModal('../<?php echo htmlspecialchars($img_data['data']); ?>')" class="btn-view-image-small" title="View Full Size">
                                                    <span>👁️</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="image-actions-small">
                                            <button type="button" onclick="confirmDeleteImage('<?php echo $img_data['field']; ?>', 'imagesOnlyForm')" class="btn-delete-image-small">
                                                <span>🗑️</span> Delete
                                            </button>
                                        </div>
                                        <input type="hidden" name="existing_<?php echo $img_data['field']; ?>" value="<?php echo htmlspecialchars($img_data['data']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="file-upload-area-small" id="fileUploadAreaOnly<?php echo $num; ?>">
                                    <div class="file-upload-content-small">
                                        <div class="upload-icon-small">📷</div>
                                        <p class="upload-text-small">Click to upload</p>
                                        <input type="file" name="<?php echo $img_data['field']; ?>" id="imageFileInputOnly<?php echo $num; ?>" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" class="file-input-hidden">
                                        <button type="button" class="btn-select-file-small" onclick="document.getElementById('imageFileInputOnly<?php echo $num; ?>').click()">
                                            Choose File
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="upload-info" style="margin-top: 1.5rem;">
                        <span class="info-icon">ℹ️</span>
                        <span>Recommended size: 400x300px or larger. Images will be displayed in 3 equal columns.</span>
                    </div>
                    
                    <div class="form-actions" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
                        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">
                            <span style="font-size: 1.2rem; margin-right: 0.5rem;">📤</span>
                            Upload Images Only
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Main Form for All Content -->
            <form method="POST" class="admin-form" style="max-width: 900px;" enctype="multipart/form-data">
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
    
    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal(event)">
        <div class="image-modal-content" onclick="event.stopPropagation()">
            <span class="image-modal-close" onclick="closeImageModal(event)">&times;</span>
            <img id="modalImage" src="" alt="Full Size Image">
        </div>
    </div>
    
    <script src="admin.js"></script>
    <script>
        function confirmDeleteImage(fieldName, formId = null) {
            if (confirm('Are you sure you want to delete this image? This action cannot be undone.')) {
                if (formId) {
                    // Use existing form
                    const form = document.getElementById(formId);
                    if (form) {
                        const deleteInput = document.createElement('input');
                        deleteInput.type = 'hidden';
                        deleteInput.name = 'delete_' + fieldName;
                        deleteInput.value = '1';
                        form.appendChild(deleteInput);
                        form.submit();
                    }
                } else {
                    // Create a form to submit delete action
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';
                    
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'upload_images_only';
                    form.appendChild(actionInput);
                    
                    const deleteInput = document.createElement('input');
                    deleteInput.type = 'hidden';
                    deleteInput.name = 'delete_' + fieldName;
                    deleteInput.value = '1';
                    form.appendChild(deleteInput);
                    
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
        
        // File upload preview functionality for both forms
        document.addEventListener('DOMContentLoaded', function() {
            // Setup preview for images-only form
            for (let i = 1; i <= 3; i++) {
                const fileInput = document.getElementById('imageFileInputOnly' + i);
                const fileUploadArea = document.getElementById('fileUploadAreaOnly' + i);
                
                if (fileInput && fileUploadArea) {
                    setupFileUploadPreview(fileInput, fileUploadArea);
                }
            }
            
            // Setup preview for main form
            const fileInput = document.getElementById('imageFileInput');
            const fileUploadArea = document.getElementById('fileUploadArea');
            const filePreview = document.getElementById('filePreview');
            const filePreviewImage = document.getElementById('filePreviewImage');
            const fileName = document.getElementById('fileName');
            const uploadContent = fileUploadArea ? fileUploadArea.querySelector('.file-upload-content') : null;
            
            if (fileInput && fileUploadArea) {
                // Click to upload
                fileUploadArea.addEventListener('click', function(e) {
                    if (!e.target.closest('.file-preview') && !e.target.closest('.btn-remove-preview')) {
                        fileInput.click();
                    }
                });
                
                // Drag and drop
                fileUploadArea.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    fileUploadArea.classList.add('drag-over');
                });
                
                fileUploadArea.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    fileUploadArea.classList.remove('drag-over');
                });
                
                fileUploadArea.addEventListener('drop', function(e) {
                    e.preventDefault();
                    fileUploadArea.classList.remove('drag-over');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        handleFileSelect(files[0]);
                    }
                });
                
                // File input change
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        handleFileSelect(e.target.files[0]);
                    }
                });
            }
            
            function handleFileSelect(file) {
                // Validate file type
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, GIF, or WEBP)');
                    return;
                }
                
                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File size must be less than 10MB');
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (filePreviewImage) filePreviewImage.src = e.target.result;
                    if (fileName) fileName.textContent = file.name;
                    if (uploadContent) uploadContent.style.display = 'none';
                    if (filePreview) filePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
            
            window.removeFilePreview = function() {
                if (fileInput) fileInput.value = '';
                if (filePreview) filePreview.style.display = 'none';
                if (uploadContent) uploadContent.style.display = 'block';
            };
        });
        
        // Image Modal Functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            if (modal && modalImg) {
                modalImg.src = imageSrc;
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }
        
        function closeImageModal(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            const modal = document.getElementById('imageModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = ''; // Restore scrolling
            }
        }
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal(e);
            }
        });
    </script>
</body>
</html>

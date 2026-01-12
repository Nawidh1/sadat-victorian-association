<?php
require_once 'auth.php';
require_once '../config/database.php';

$page_title = 'Manage Resources';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare(
                    "INSERT INTO resources (category, title, title_fa, description, description_fa) 
                     VALUES (:category, :title, :title_fa, :description, :description_fa)"
                );
                $stmt->execute([
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
                <h2>Existing Resources (<?php echo count($resources); ?>)</h2>
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
    <script src="translate.js"></script>
    <script src="admin-edit-modal.js"></script>
</body>
</html>


<?php
require_once 'auth.php';
require_once '../config/database.php';

$page_title = 'Manage News';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare(
                    "INSERT INTO news (title, title_fa, date, content, content_fa) 
                     VALUES (:title, :title_fa, :date, :content, :content_fa)"
                );
                $stmt->execute([
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':date' => $_POST['date'] ?? date('Y-m-d'),
                    ':content' => $_POST['content'] ?? '',
                    ':content_fa' => $_POST['content_fa'] ?? ''
                ]);
                $success = 'News item added successfully!';
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $pdo->prepare(
                    "UPDATE news SET title = :title, title_fa = :title_fa, date = :date, content = :content, content_fa = :content_fa WHERE id = :id"
                );
                $stmt->execute([
                    ':title' => $_POST['title'] ?? '',
                    ':title_fa' => $_POST['title_fa'] ?? '',
                    ':date' => $_POST['date'] ?? date('Y-m-d'),
                    ':content' => $_POST['content'] ?? '',
                    ':content_fa' => $_POST['content_fa'] ?? '',
                    ':id' => $_POST['id']
                ]);
                $success = 'News item updated successfully!';
            } elseif ($_POST['action'] === 'delete') {
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
            <div class="admin-layout">
                <div class="admin-form-section">
                <h2>Add New News Item</h2>
                <form method="POST" class="admin-form" id="addNewsForm">
                    <input type="hidden" name="action" value="add">
                    
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
                <h2>Existing News (<?php echo count($news); ?>)</h2>
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
                                data-content-fa="<?php echo htmlspecialchars($item['content_fa'] ?? ''); ?>">
                                <div class="item-header">
                                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                                </div>
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
            <form method="POST" class="admin-form" id="editNewsForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
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
    <script src="translate.js"></script>
    <script src="admin-edit-modal.js"></script>
</body>
</html>

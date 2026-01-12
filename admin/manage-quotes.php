<?php
require_once 'auth.php';
require_once '../config/database.php';

$page_title = 'Manage Quotes';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare(
                    "INSERT INTO quotes (text, text_fa, author, author_fa) 
                     VALUES (:text, :text_fa, :author, :author_fa)"
                );
                $stmt->execute([
                    ':text' => $_POST['text'] ?? '',
                    ':text_fa' => $_POST['text_fa'] ?? '',
                    ':author' => $_POST['author'] ?? '',
                    ':author_fa' => $_POST['author_fa'] ?? ''
                ]);
                $success = 'Quote added successfully!';
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $pdo->prepare(
                    "UPDATE quotes SET text = :text, text_fa = :text_fa, author = :author, author_fa = :author_fa WHERE id = :id"
                );
                $stmt->execute([
                    ':text' => $_POST['text'] ?? '',
                    ':text_fa' => $_POST['text_fa'] ?? '',
                    ':author' => $_POST['author'] ?? '',
                    ':author_fa' => $_POST['author_fa'] ?? '',
                    ':id' => $_POST['id']
                ]);
                $success = 'Quote updated successfully!';
            } elseif ($_POST['action'] === 'delete') {
                $stmt = $pdo->prepare("DELETE FROM quotes WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                $success = 'Quote deleted successfully!';
            }
        }
    }

    // Fetch all quotes
    $stmt = $pdo->query("SELECT * FROM quotes ORDER BY created_at DESC");
    $quotes = $stmt->fetchAll();

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $quotes = []; // Fallback to empty array
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quotes - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Manage Quotes</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <div class="admin-layout">
                <div class="admin-form-section">
                <h2>Add New Quote</h2>
                <form method="POST" class="admin-form" id="addQuoteForm">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="text">Quote Text (English) *</label>
                        <textarea name="text" id="text" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="text_fa">Quote Text (Farsi) *</label>
                        <textarea name="text_fa" id="text_fa" rows="4" required dir="rtl"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="author">Author (English) *</label>
                        <input type="text" name="author" id="author" required placeholder="— Prophet Muhammad (PBUH)">
                    </div>
                    
                    <div class="form-group">
                        <label for="author_fa">Author (Farsi) *</label>
                        <input type="text" name="author_fa" id="author_fa" required dir="rtl">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Quote</button>
                    </div>
                </form>
                </div>
            
                <div class="admin-list-section">
                <h2>Existing Quotes (<?php echo count($quotes); ?>)</h2>
                <div class="items-list quotes-list">
                    <?php if (empty($quotes)): ?>
                        <p class="empty-state">No quotes yet. Add your first quote!</p>
                    <?php else: ?>
                        <?php foreach ($quotes as $quote): ?>
                            <div class="item-card quote-card" 
                                data-id="<?php echo htmlspecialchars($quote['id']); ?>"
                                data-text="<?php echo htmlspecialchars($quote['text'] ?? ''); ?>"
                                data-text-fa="<?php echo htmlspecialchars($quote['text_fa'] ?? ''); ?>"
                                data-author="<?php echo htmlspecialchars($quote['author'] ?? ''); ?>"
                                data-author-fa="<?php echo htmlspecialchars($quote['author_fa'] ?? ''); ?>">
                                <div class="item-details">
                                    <p><strong>"<?php echo htmlspecialchars($quote['text']); ?>"</strong></p>
                                    <p style="margin-top: 0.5rem; color: var(--primary-color);"><?php echo htmlspecialchars($quote['author']); ?></p>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="btn btn-edit edit-item-btn" data-type="quote">✏️ Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quote?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($quote['id']); ?>">
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
            <h2>Edit Quote</h2>
            <form method="POST" class="admin-form" id="editQuoteForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_text">Quote Text (English) *</label>
                    <textarea id="edit_text" name="text" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_text_fa">Quote Text (Farsi) *</label>
                    <textarea id="edit_text_fa" name="text_fa" rows="4" required dir="rtl"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_author">Author (English) *</label>
                    <input type="text" id="edit_author" name="author" required placeholder="— Prophet Muhammad (PBUH)">
                </div>
                
                <div class="form-group">
                    <label for="edit_author_fa">Author (Farsi) *</label>
                    <input type="text" id="edit_author_fa" name="author_fa" required dir="rtl">
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Quote</button>
                    <button type="button" class="btn btn-secondary" id="cancelEditBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="admin.js"></script>
    <script src="admin-edit-modal.js"></script>
</body>
</html>


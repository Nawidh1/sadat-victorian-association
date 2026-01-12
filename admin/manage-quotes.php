<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$page_title = 'Manage Quotes';
$success = '';
$error = '';

try {
    $pdo = getDBConnection();

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'import_defaults') {
                // Import default quotes from website
                $default_quotes = [
                    ['id' => 'quote_ahl_bayt', 'text' => "I leave behind me two weighty things: the Book of Allah and my Ahl al-Bayt. If you hold fast to them, you will never go astray.", 'text_fa' => 'من دو چیز گرانبها را برای شما به جا می‌گذارم: کتاب الله و اهل بیتم. اگر به آن‌ها تمسک کنید، هرگز گمراه نخواهید شد.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_beneficial', 'text' => 'The best of people are those who are most beneficial to others.', 'text_fa' => 'بهترین مردم کسانی هستند که برای دیگران سودمندترند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_knowledge', 'text' => 'Knowledge is a treasure, but practice is the key to it.', 'text_fa' => 'دانش گنج است، اما عمل کلید آن است.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_anger', 'text' => 'The strongest person is the one who controls his anger.', 'text_fa' => 'قوی‌ترین شخص کسی است که خشم خود را کنترل می‌کند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_freedom', 'text' => 'Do not be a slave to others when Allah has created you free.', 'text_fa' => 'وقتی الله تو را آزاد آفریده است، برده دیگران مباش.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_worship', 'text' => 'The best form of worship is to wait for relief (from Allah).', 'text_fa' => 'بهترین عبادت انتظار فرج (از الله) است.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_wealth', 'text' => "A person's true wealth is the good he does in this world.", 'text_fa' => 'ثروت واقعی هر شخص نیکی‌ای است که در این دنیا انجام می‌دهد.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_character', 'text' => 'The most complete of believers in faith are those with the best character.', 'text_fa' => 'کامل‌ترین مؤمنان در ایمان کسانی هستند که بهترین اخلاق را دارند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_patience', 'text' => 'Patience is of two kinds: patience over what pains you, and patience against what you covet.', 'text_fa' => 'صبر دو نوع است: صبر بر آنچه تو را می‌آزارد، و صبر در برابر آنچه آرزو می‌کنی.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_consistent', 'text' => 'The best of deeds is that which is done consistently, even if it is small.', 'text_fa' => 'بهترین اعمال آن است که به طور مداوم انجام شود، حتی اگر کوچک باشد.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_value', 'text' => 'The value of each person lies in the good he does.', 'text_fa' => 'ارزش هر شخص در نیکی‌ای است که انجام می‌دهد.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_thanks', 'text' => 'Whoever does not thank people does not thank Allah.', 'text_fa' => 'کسی که از مردم تشکر نمی‌کند، از الله تشکر نمی‌کند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_family', 'text' => 'The best of you are those who are best to their families.', 'text_fa' => 'بهترین شما کسانی هستند که با خانواده خود بهترین رفتار را دارند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)'],
                    ['id' => 'quote_knowledge_tree', 'text' => 'Knowledge without action is like a tree without fruit.', 'text_fa' => 'دانش بدون عمل مانند درختی بدون میوه است.', 'author' => '— Imam Ali (AS)', 'author_fa' => '— امام علی (ع)'],
                    ['id' => 'quote_beneficial_people', 'text' => 'The most beloved of people to Allah are those who are most beneficial to people.', 'text_fa' => 'محبوب‌ترین مردم نزد الله کسانی هستند که برای مردم سودمندترند.', 'author' => '— Prophet Muhammad (PBUH)', 'author_fa' => '— پیامبر محمد (ص)']
                ];
                
                $imported = 0;
                $skipped = 0;
                
                foreach ($default_quotes as $quote) {
                    // Check if quote already exists
                    $check_stmt = $pdo->prepare("SELECT id FROM quotes WHERE id = ?");
                    $check_stmt->execute([$quote['id']]);
                    
                    if ($check_stmt->fetch()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Insert quote
                    $stmt = $pdo->prepare("INSERT INTO quotes (id, text, text_fa, author, author_fa) 
                                          VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $quote['id'],
                        $quote['text'],
                        $quote['text_fa'],
                        $quote['author'],
                        $quote['author_fa']
                    ]);
                    $imported++;
                }
                
                if ($imported > 0) {
                    $success = "Successfully imported $imported default quote(s)" . ($skipped > 0 ? " ($skipped already existed)" : "") . "!";
                } else {
                    $success = "All default quotes already exist in the database.";
                }
                
            } elseif ($_POST['action'] === 'add') {
                $quote_id = uniqid('quote_');
                $stmt = $pdo->prepare(
                    "INSERT INTO quotes (id, text, text_fa, author, author_fa) 
                     VALUES (:id, :text, :text_fa, :author, :author_fa)"
                );
                $stmt->execute([
                    ':id' => $quote_id,
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
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="margin: 0;">Existing Quotes (<?php echo count($quotes); ?>)</h2>
                </div>
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


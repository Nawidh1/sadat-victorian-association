<?php
require_once 'auth.php';
require_once '../config/database.php';

$pdo = getDBConnection();
$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            if ($_POST['action'] === 'add') {
                // Validate input
                $title = trim($_POST['title'] ?? '');
                if (empty($title)) {
                    throw new Exception('Title is required');
                }
                
                $stmt = $pdo->prepare("INSERT INTO events (id, title, title_fa, date, time, location, location_fa, description, description_fa, category, featured) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    uniqid('event_'),
                    $title,
                    trim($_POST['title_fa'] ?? ''),
                    $_POST['date'] ?? date('Y-m-d'),
                    trim($_POST['time'] ?? ''),
                    trim($_POST['location'] ?? ''),
                    trim($_POST['location_fa'] ?? ''),
                    trim($_POST['description'] ?? ''),
                    trim($_POST['description_fa'] ?? ''),
                    $_POST['category'] ?? 'regular',
                    isset($_POST['featured']) ? 1 : 0
                ]);
                $success = 'Event added successfully!';
                
            } elseif ($_POST['action'] === 'edit') {
                $id = $_POST['id'] ?? '';
                if (empty($id)) {
                    throw new Exception('Invalid event ID');
                }
                
                $title = trim($_POST['title'] ?? '');
                if (empty($title)) {
                    throw new Exception('Title is required');
                }
                
                $stmt = $pdo->prepare("UPDATE events SET title=?, title_fa=?, date=?, time=?, location=?, location_fa=?, description=?, description_fa=?, category=?, featured=? WHERE id=?");
                $stmt->execute([
                    $title,
                    trim($_POST['title_fa'] ?? ''),
                    $_POST['date'] ?? date('Y-m-d'),
                    trim($_POST['time'] ?? ''),
                    trim($_POST['location'] ?? ''),
                    trim($_POST['location_fa'] ?? ''),
                    trim($_POST['description'] ?? ''),
                    trim($_POST['description_fa'] ?? ''),
                    $_POST['category'] ?? 'regular',
                    isset($_POST['featured']) ? 1 : 0,
                    $id
                ]);
                $success = 'Event updated successfully!';
                
            } elseif ($_POST['action'] === 'delete') {
                $id = $_POST['id'] ?? '';
                if (empty($id)) {
                    throw new Exception('Invalid event ID');
                }
                
                $stmt = $pdo->prepare("DELETE FROM events WHERE id=?");
                $stmt->execute([$id]);
                $success = 'Event deleted successfully!';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

// Get all events
$stmt = $pdo->query("SELECT * FROM events ORDER BY date DESC, created_at DESC");
$events = $stmt->fetchAll();

// Get event for editing (for initial page load only, modal handles editing)
$editing_event = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>
    
    <div class="admin-container">
        <h1>Manage Events</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="admin-content">
            <div class="admin-layout">
                <div class="admin-form-section">
                <h2>Add New Event</h2>
                <form method="POST" class="admin-form" id="addEventForm">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="form-group">
                        <label for="title">Title (English) *</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="title_fa">Title (Farsi)</label>
                        <input type="text" id="title_fa" name="title_fa">
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Date *</label>
                        <input type="date" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="text" id="time" name="time" placeholder="e.g., 1:00 PM - 2:30 PM">
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location (English)</label>
                        <input type="text" id="location" name="location">
                    </div>
                    
                    <div class="form-group">
                        <label for="location_fa">Location (Farsi)</label>
                        <input type="text" id="location_fa" name="location_fa">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select id="category" name="category" required>
                            <option value="regular">Regular Event</option>
                            <option value="special">Special Event</option>
                            <option value="annual">Annual Program</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description (English)</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="description_fa">Description (Farsi)</label>
                        <textarea id="description_fa" name="description_fa" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" value="1">
                            Featured Event
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Event</button>
                </form>
                </div>
            
                <div class="admin-list-section">
                <h2>Existing Events (<?php echo count($events); ?>)</h2>
                <div class="items-list">
                    <?php if (empty($events)): ?>
                        <p class="empty-state">No events yet. Add your first event!</p>
                    <?php else: ?>
                        <?php foreach ($events as $event): ?>
                            <div class="item-card" 
                                data-id="<?php echo htmlspecialchars($event['id']); ?>"
                                data-title="<?php echo htmlspecialchars($event['title']); ?>"
                                data-title-fa="<?php echo htmlspecialchars($event['title_fa'] ?? ''); ?>"
                                data-date="<?php echo htmlspecialchars($event['date']); ?>"
                                data-time="<?php echo htmlspecialchars($event['time'] ?? ''); ?>"
                                data-location="<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
                                data-location-fa="<?php echo htmlspecialchars($event['location_fa'] ?? ''); ?>"
                                data-category="<?php echo htmlspecialchars($event['category'] ?? 'regular'); ?>"
                                data-description="<?php echo htmlspecialchars($event['description'] ?? ''); ?>"
                                data-description-fa="<?php echo htmlspecialchars($event['description_fa'] ?? ''); ?>"
                                data-featured="<?php echo $event['featured'] ? '1' : '0'; ?>">
                                <div class="item-header">
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <?php if ($event['featured']): ?>
                                        <span class="badge">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date']); ?></p>
                                    <?php if (!empty($event['time'])): ?>
                                        <p><strong>Time:</strong> <?php echo htmlspecialchars($event['time']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($event['location'])): ?>
                                        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($event['description'])): ?>
                                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="item-actions">
                                    <button type="button" class="btn btn-edit edit-item-btn" data-type="event">✏️ Edit</button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($event['id']); ?>">
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
            <h2>Edit Event</h2>
            <form method="POST" class="admin-form" id="editEventForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-group">
                    <label for="edit_title">Title (English) *</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_title_fa">Title (Farsi)</label>
                    <input type="text" id="edit_title_fa" name="title_fa">
                </div>
                
                <div class="form-group">
                    <label for="edit_date">Date *</label>
                    <input type="date" id="edit_date" name="date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_time">Time</label>
                    <input type="text" id="edit_time" name="time" placeholder="e.g., 1:00 PM - 2:30 PM">
                </div>
                
                <div class="form-group">
                    <label for="edit_location">Location (English)</label>
                    <input type="text" id="edit_location" name="location">
                </div>
                
                <div class="form-group">
                    <label for="edit_location_fa">Location (Farsi)</label>
                    <input type="text" id="edit_location_fa" name="location_fa">
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Category *</label>
                    <select id="edit_category" name="category" required>
                        <option value="regular">Regular Event</option>
                        <option value="special">Special Event</option>
                        <option value="annual">Annual Program</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description (English)</label>
                    <textarea id="edit_description" name="description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_description_fa">Description (Farsi)</label>
                    <textarea id="edit_description_fa" name="description_fa" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="edit_featured" name="featured" value="1">
                        Featured Event
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Event</button>
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

<?php
require_once 'auth.php';
require_once __DIR__ . '/../config/database.php';

$page_title = 'Admin Dashboard';

// Get counts from database
try {
    $pdo = getDBConnection();
    
    $events_count = $pdo->query("SELECT COUNT(*) as count FROM events")->fetch()['count'];
    $news_count = $pdo->query("SELECT COUNT(*) as count FROM news")->fetch()['count'];
    $resources_count = $pdo->query("SELECT COUNT(*) as count FROM resources")->fetch()['count'];
    $quotes_count = $pdo->query("SELECT COUNT(*) as count FROM quotes")->fetch()['count'];
    
    // Get recent events
    $stmt = $pdo->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 3");
    $recent_events = $stmt->fetchAll();
    
    // Get recent news
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
    $recent_news = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Error loading dashboard data: " . $e->getMessage());
    $events_count = 0;
    $news_count = 0;
    $resources_count = 0;
    $quotes_count = 0;
    $recent_events = [];
    $recent_news = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sadat Victorian Association</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="admin-style.css?v=2.0">
</head>
<body>
    <?php include 'admin-nav.php'; ?>

    <div class="admin-container">
        <h1>Welcome to Admin Dashboard</h1>
        
        <div class="welcome-section">
            <p>Use the sidebar menu to navigate and manage your website content.</p>
            <p>You can manage events, news, resources, quotes, and update page content from the navigation menu.</p>
        </div>

        <div class="stats-overview">
            <h2>Content Overview</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $events_count; ?></div>
                        <div class="stat-label">Events</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📰</div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $news_count; ?></div>
                        <div class="stat-label">News Items</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $resources_count; ?></div>
                        <div class="stat-label">Resources</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💬</div>
                    <div class="stat-info">
                        <div class="stat-number"><?php echo $quotes_count; ?></div>
                        <div class="stat-label">Quotes</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="recent-activity">
            <div class="activity-section">
                <h2>Recent Events</h2>
                <?php if (empty($recent_events)): ?>
                    <p class="empty-message">No events yet. <a href="manage-events.php">Add your first event</a></p>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recent_events as $event): ?>
                            <li>
                                <span class="activity-title"><?php echo htmlspecialchars($event['title'] ?? 'Untitled Event'); ?></span>
                                <span class="activity-date"><?php echo htmlspecialchars($event['date'] ?? ''); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="manage-events.php" class="view-all-link">View All Events →</a>
                <?php endif; ?>
            </div>

            <div class="activity-section">
                <h2>Recent News</h2>
                <?php if (empty($recent_news)): ?>
                    <p class="empty-message">No news yet. <a href="manage-news.php">Add your first news item</a></p>
                <?php else: ?>
                    <ul class="activity-list">
                        <?php foreach ($recent_news as $item): ?>
                            <li>
                                <span class="activity-title"><?php echo htmlspecialchars($item['title'] ?? 'Untitled News'); ?></span>
                                <span class="activity-date"><?php echo htmlspecialchars($item['date'] ?? ''); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="manage-news.php" class="view-all-link">View All News →</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="admin.js"></script>
</body>
</html>

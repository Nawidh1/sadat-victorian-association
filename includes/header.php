<?php
// Load data from SQL database (if needed for header)
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT section, data FROM homepage");
    $homepage_rows = $stmt->fetchAll();
    
    // Convert section/data structure to associative array
    $homepage_data = [];
    foreach ($homepage_rows as $row) {
        $homepage_data[$row['section']] = json_decode($row['data'], true);
    }
} catch (Exception $e) {
    error_log("Error loading homepage data in header: " . $e->getMessage());
    $homepage_data = []; // Fallback to empty array
}

// Get current page name
$script_name = basename($_SERVER['PHP_SELF']);
$current_page = str_replace('.php', '', $script_name);
if (empty($current_page) || $current_page === 'index' || $script_name === 'index.php') {
    $current_page = 'index';
}

$page_titles = [
    'index' => 'Home',
    'about' => 'About Us',
    'events' => 'Events',
    'resources' => 'Resources',
    'news' => 'News',
    'contact' => 'Contact Us'
];
$page_title = $page_titles[$current_page] ?? 'Sadat Victorian Association';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Sadat Victorian Association</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <div class="logo-text">
                        <h1 id="logo-english">Sadat Victorian Association</h1>
                        <p class="tagline" id="logo-farsi">انجمن سادات ویکتوریایی</p>
                    </div>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>" data-translate="home">Home</a></li>
                        <li><a href="about.php" class="<?php echo $current_page == 'about' ? 'active' : ''; ?>" data-translate="about">About</a></li>
                        <li><a href="events.php" class="<?php echo $current_page == 'events' ? 'active' : ''; ?>" data-translate="events">Events</a></li>
                        <li><a href="resources.php" class="<?php echo $current_page == 'resources' ? 'active' : ''; ?>" data-translate="resources">Resources</a></li>
                        <li><a href="news.php" class="<?php echo $current_page == 'news' ? 'active' : ''; ?>" data-translate="news">News</a></li>
                        <li><a href="contact.php" class="<?php echo $current_page == 'contact' ? 'active' : ''; ?>" data-translate="contact">Contact</a></li>
                    </ul>
                </nav>
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>


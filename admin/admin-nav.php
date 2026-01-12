<?php
$page_title = isset($page_title) ? $page_title : 'Admin Panel';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="admin-sidebar">
    <div class="sidebar-header">
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
    </div>
    
    <div class="sidebar-user">
        <div class="user-avatar">
            <span><?php echo strtoupper(substr($_SESSION['admin_username'], 0, 1)); ?></span>
        </div>
        <div class="user-details">
            <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <span class="user-role">Administrator</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📊</span>
            <span class="nav-text">Dashboard</span>
        </a>
        <a href="manage-events.php" class="nav-item <?php echo $current_page == 'manage-events.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📅</span>
            <span class="nav-text">Events</span>
        </a>
        <a href="manage-news.php" class="nav-item <?php echo $current_page == 'manage-news.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📰</span>
            <span class="nav-text">News</span>
        </a>
        <a href="manage-resources.php" class="nav-item <?php echo $current_page == 'manage-resources.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📚</span>
            <span class="nav-text">Resources</span>
        </a>
        <a href="manage-quotes.php" class="nav-item <?php echo $current_page == 'manage-quotes.php' ? 'active' : ''; ?>">
            <span class="nav-icon">💬</span>
            <span class="nav-text">Quotes</span>
        </a>
        <a href="manage-homepage.php" class="nav-item <?php echo $current_page == 'manage-homepage.php' ? 'active' : ''; ?>">
            <span class="nav-icon">🏠</span>
            <span class="nav-text">Homepage</span>
        </a>
        <a href="edit-about.php" class="nav-item <?php echo $current_page == 'edit-about.php' ? 'active' : ''; ?>">
            <span class="nav-icon">ℹ️</span>
            <span class="nav-text">About Page</span>
        </a>
        <a href="edit-contact.php" class="nav-item <?php echo $current_page == 'edit-contact.php' ? 'active' : ''; ?>">
            <span class="nav-icon">📞</span>
            <span class="nav-text">Contact</span>
        </a>
        <a href="change-password.php" class="nav-item <?php echo $current_page == 'change-password.php' ? 'active' : ''; ?>">
            <span class="nav-icon">🔒</span>
            <span class="nav-text">Password</span>
        </a>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../index.php" class="nav-item">
            <span class="nav-icon">🌐</span>
            <span class="nav-text">View Website</span>
        </a>
        <a href="logout.php" class="nav-item logout">
            <span class="nav-icon">🚪</span>
            <span class="nav-text">Logout</span>
        </a>
    </div>
</div>

<button class="sidebar-toggle" aria-label="Toggle sidebar">
    <span></span>
    <span></span>
    <span></span>
</button>


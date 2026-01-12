<?php
// Load data from SQL database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Get homepage data
    $stmt = $pdo->query("SELECT section, data FROM homepage");
    $homepage_rows = $stmt->fetchAll();
    $homepage_data = [];
    foreach ($homepage_rows as $row) {
        $homepage_data[$row['section']] = json_decode($row['data'], true);
    }
    
    // Get latest 3 news items
    $stmt = $pdo->query("SELECT * FROM news ORDER BY date DESC, created_at DESC LIMIT 3");
    $latest_news = $stmt->fetchAll();
} catch (Exception $e) {
    // Fallback to empty arrays if database error
    error_log("Error loading homepage data: " . $e->getMessage());
    $homepage_data = [];
    $latest_news = [];
}

include 'includes/header.php';
?>

<main>
    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-layout">
                <div class="hero-logo-section">
                    <img src="logo.jpeg" alt="Sadat Victorian Association" class="hero-logo">
                </div>
                <div class="hero-content">
                    <h2 class="hero-title" id="hero-title"><?php echo htmlspecialchars($homepage_data['hero']['title'] ?? 'Welcome to Sadat Victorian Association'); ?></h2>
                    <p class="hero-subtitle" id="hero-subtitle"><?php echo htmlspecialchars($homepage_data['hero']['subtitle'] ?? 'A community dedicated to preserving and sharing the rich heritage of Shia Islam'); ?></p>
                    <div class="hero-buttons">
                        <a href="about.php" class="btn btn-primary" id="learn-more-btn"><?php echo htmlspecialchars($homepage_data['hero']['learn_more_text'] ?? 'Learn More'); ?></a>
                        <a href="events.php" class="btn btn-secondary" id="upcoming-events-btn"><?php echo htmlspecialchars($homepage_data['hero']['events_text'] ?? 'Upcoming Events'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <div class="section-header">
                <h2 id="mission-title"><?php echo htmlspecialchars($homepage_data['mission']['title'] ?? 'Our Mission'); ?></h2>
                <p class="section-subtitle" id="mission-subtitle"><?php echo htmlspecialchars($homepage_data['mission']['subtitle'] ?? 'Connecting hearts, preserving traditions, and building community'); ?></p>
            </div>
            <div class="features-grid">
                <?php 
                $features = $homepage_data['features'] ?? [];
                if (empty($features)) {
                    // Default features
                    $features = [
                        ['icon' => '📚', 'title' => 'Education', 'title_fa' => 'آموزش', 'description' => 'Providing authentic Islamic education and resources for the Shia community', 'description_fa' => 'ارائه آموزش و منابع اصیل اسلامی برای جامعه شیعی'],
                        ['icon' => '🤝', 'title' => 'Community', 'title_fa' => 'جامعه', 'description' => 'Building strong bonds and supporting one another in faith and daily life', 'description_fa' => 'ایجاد پیوندهای قوی و حمایت از یکدیگر در ایمان و زندگی روزمره'],
                        ['icon' => '🕌', 'title' => 'Spirituality', 'title_fa' => 'معنویت', 'description' => 'Fostering spiritual growth through prayer, reflection, and religious observance', 'description_fa' => 'پرورش رشد معنوی از طریق نماز، تفکر و مراسم مذهبی'],
                        ['icon' => '📅', 'title' => 'Events', 'title_fa' => 'رویدادها', 'description' => 'Organizing gatherings, lectures, and commemorations throughout the year', 'description_fa' => 'سازماندهی گردهمایی‌ها، سخنرانی‌ها و یادبودها در طول سال']
                    ];
                }
                foreach ($features as $feature): ?>
                    <div class="feature-card">
                        <div class="feature-icon"><?php echo htmlspecialchars($feature['icon'] ?? ''); ?></div>
                        <h3 data-translate="<?php echo strtolower($feature['title'] ?? ''); ?>"><?php echo htmlspecialchars($feature['title'] ?? ''); ?></h3>
                        <p data-translate="<?php echo strtolower($feature['title'] ?? '') . 'Desc'; ?>"><?php echo htmlspecialchars($feature['description'] ?? ''); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="news-section">
        <div class="container">
            <div class="section-header">
                <h2 data-translate="latestUpdates">Latest Updates</h2>
                <a href="news.php" class="view-all" data-translate="viewAll">View All →</a>
            </div>
            <div class="news-grid">
                <?php if (empty($latest_news)): ?>
                    <!-- Default news items if no news in admin -->
                    <article class="news-card">
                        <div class="news-date">15 Dec 2024</div>
                        <h3 data-translate="weeklyFridayPrayers">Weekly Friday Prayers</h3>
                        <p data-translate="weeklyFridayDesc">Join us every Friday for Jumu'ah prayers and community gathering. All are welcome.</p>
                        <a href="events.php" class="read-more" data-translate="readMore">Read More</a>
                    </article>
                    <article class="news-card">
                        <div class="news-date">20 Dec 2024</div>
                        <h3 data-translate="muharramCommemoration">Muharram Commemoration</h3>
                        <p data-translate="muharramDesc">Annual commemoration events honoring the martyrdom of Imam Hussain (AS) and his companions.</p>
                        <a href="events.php" class="read-more" data-translate="readMore">Read More</a>
                    </article>
                    <article class="news-card">
                        <div class="news-date">25 Dec 2024</div>
                        <h3 data-translate="islamicStudies">Islamic Studies Program</h3>
                        <p data-translate="islamicStudiesDesc">New educational program starting for adults and youth. Registration now open.</p>
                        <a href="events.php" class="read-more" data-translate="readMore">Read More</a>
                    </article>
                <?php else: ?>
                    <?php foreach ($latest_news as $item): 
                        $date = new DateTime($item['date']);
                        $formatted_date = $date->format('d M Y');
                    ?>
                        <article class="news-card" 
                            data-title="<?php echo htmlspecialchars($item['title'] ?? ''); ?>"
                            data-title-fa="<?php echo htmlspecialchars($item['title_fa'] ?? ''); ?>"
                            data-content="<?php echo htmlspecialchars($item['content'] ?? ''); ?>"
                            data-content-fa="<?php echo htmlspecialchars($item['content_fa'] ?? ''); ?>">
                            <div class="news-date"><?php echo htmlspecialchars($formatted_date); ?></div>
                            <h3><?php echo htmlspecialchars($item['title'] ?? ''); ?></h3>
                            <p><?php echo htmlspecialchars($item['content'] ?? ''); ?></p>
                            <a href="news.php" class="read-more" data-translate="readMore">Read More</a>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="quote-section">
        <div class="container">
            <blockquote>
                <p id="quoteText" style="opacity: 1; transition: opacity 0.5s;">"A person's true wealth is the good he does in this world."</p>
                <cite id="quoteAuthor" style="opacity: 1; transition: opacity 0.5s;">— Prophet Muhammad (PBUH)</cite>
            </blockquote>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>


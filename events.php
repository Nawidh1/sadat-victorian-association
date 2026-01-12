<?php
// Load data from SQL database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    
    // Get all events from database
    $stmt = $pdo->query("SELECT * FROM events ORDER BY date ASC, created_at ASC");
    $events_data = $stmt->fetchAll();
    
    // Group events by category
    $regular_events = [];
    $special_events = [];
    $annual_events = [];
    
    foreach ($events_data as $event) {
        $category = $event['category'] ?? 'regular';
        if ($category === 'regular') {
            $regular_events[] = $event;
        } elseif ($category === 'special') {
            $special_events[] = $event;
        } elseif ($category === 'annual') {
            $annual_events[] = $event;
        }
    }
} catch (Exception $e) {
    // Fallback to empty arrays if database error
    error_log("Error loading events: " . $e->getMessage());
    $regular_events = [];
    $special_events = [];
    $annual_events = [];
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 data-translate="eventsTitle">Events & Programs</h1>
            <p class="page-subtitle" data-translate="eventsSubtitle">Join us for prayers, commemorations, and community gatherings</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="events-container">
                <div class="event-category">
                    <h2 data-translate="regularEvents">Regular Events</h2>
                    <div class="events-list">
                        <?php if (empty($regular_events)): ?>
                            <!-- Default content if no events -->
                            <article class="event-card clickable-event" 
                                data-title="Friday Prayers (Jumu'ah)" 
                                data-title-fa="نماز جمعه" 
                                data-description="Join us for weekly Friday prayers followed by community gathering and refreshments. All members of the community are welcome." 
                                data-description-fa="هر جمعه برای نماز جمعه و سپس گردهمایی جامعه و پذیرایی به ما بپیوندید. همه اعضای جامعه خوش آمدند." 
                                data-location="Main Hall" 
                                data-location-fa="سالن اصلی"
                                data-date="Every Friday"
                                data-time="1:00 PM - 2:30 PM"
                                data-category="regular">
                                <div class="event-date">
                                    <span class="day" data-translate="every">Every</span>
                                    <span class="month" data-translate="friday">Friday</span>
                                </div>
                                <div class="event-content">
                                    <h3 data-translate="fridayPrayers">Friday Prayers (Jumu'ah)</h3>
                                    <p class="event-time">1:00 PM - 2:30 PM</p>
                                    <p data-translate="fridayPrayersDesc">Join us for weekly Friday prayers followed by community gathering and refreshments. All members of the community are welcome.</p>
                                    <p class="event-location">📍 <span data-translate="mainHall">Main Hall</span></p>
                                </div>
                            </article>
                        <?php else: ?>
                            <?php foreach ($regular_events as $event): 
                                $date = new DateTime($event['date']);
                                $day = $date->format('d');
                                $month = $date->format('M');
                                $full_date = $date->format('F d, Y');
                            ?>
                                <article class="event-card clickable-event" 
                                    data-title="<?php echo htmlspecialchars($event['title']); ?>" 
                                    data-title-fa="<?php echo htmlspecialchars($event['title_fa'] ?? ''); ?>" 
                                    data-description="<?php echo htmlspecialchars($event['description']); ?>" 
                                    data-description-fa="<?php echo htmlspecialchars($event['description_fa'] ?? ''); ?>" 
                                    data-location="<?php echo htmlspecialchars($event['location'] ?? ''); ?>" 
                                    data-location-fa="<?php echo htmlspecialchars($event['location_fa'] ?? ''); ?>"
                                    data-date="<?php echo htmlspecialchars($full_date); ?>"
                                    data-time="<?php echo htmlspecialchars($event['time'] ?? ''); ?>"
                                    data-category="<?php echo htmlspecialchars($event['category'] ?? 'regular'); ?>">
                                    <div class="event-date">
                                        <span class="day"><?php echo htmlspecialchars($day); ?></span>
                                        <span class="month"><?php echo htmlspecialchars($month); ?></span>
                                    </div>
                                    <div class="event-content">
                                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <?php if (!empty($event['time'])): ?>
                                            <p class="event-time"><?php echo htmlspecialchars($event['time']); ?></p>
                                        <?php endif; ?>
                                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                                        <?php if (!empty($event['location'])): ?>
                                            <p class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="event-category">
                    <h2 data-translate="upcomingSpecialEvents">Upcoming Special Events</h2>
                    <div class="events-list">
                        <?php if (empty($special_events)): ?>
                            <!-- Default content -->
                            <article class="event-card clickable-event featured" 
                                data-title="Muharram Commemoration - Day of Ashura" 
                                data-title-fa="یادبود محرم - روز عاشورا" 
                                data-description="Annual commemoration of the martyrdom of Imam Hussain (AS), his family, and companions at Karbala. The day includes lectures, mourning processions, and community iftar." 
                                data-description-fa="یادبود سالانه شهادت امام حسین (ع)، خانواده و یارانش در کربلا. این روز شامل سخنرانی‌ها، دسته‌های عزاداری و افطار جامعه است." 
                                data-location="Main Hall & Community Center" 
                                data-location-fa="سالن اصلی و مرکز جامعه"
                                data-date="December 20, 2024"
                                data-time="10:00 AM - 8:00 PM"
                                data-category="special">
                                <div class="event-date">
                                    <span class="day">20</span>
                                    <span class="month">Dec</span>
                                </div>
                                <div class="event-content">
                                    <h3 data-translate="dayOfAshura">Muharram Commemoration - Day of Ashura</h3>
                                    <p class="event-time">10:00 AM - 8:00 PM</p>
                                    <p data-translate="dayOfAshuraDesc">Annual commemoration of the martyrdom of Imam Hussain (AS), his family, and companions at Karbala.</p>
                                    <p class="event-location">📍 <span data-translate="mainHall">Main Hall</span></p>
                                </div>
                            </article>
                        <?php else: ?>
                            <?php foreach ($special_events as $event): 
                                $date = new DateTime($event['date']);
                                $day = $date->format('d');
                                $month = $date->format('M');
                                $full_date = $date->format('F d, Y');
                                $featured_class = ($event['featured'] == 1 || $event['featured'] === true) ? 'featured' : '';
                            ?>
                                <article class="event-card clickable-event <?php echo $featured_class; ?>" 
                                    data-title="<?php echo htmlspecialchars($event['title']); ?>" 
                                    data-title-fa="<?php echo htmlspecialchars($event['title_fa'] ?? ''); ?>" 
                                    data-description="<?php echo htmlspecialchars($event['description']); ?>" 
                                    data-description-fa="<?php echo htmlspecialchars($event['description_fa'] ?? ''); ?>" 
                                    data-location="<?php echo htmlspecialchars($event['location'] ?? ''); ?>" 
                                    data-location-fa="<?php echo htmlspecialchars($event['location_fa'] ?? ''); ?>"
                                    data-date="<?php echo htmlspecialchars($full_date); ?>"
                                    data-time="<?php echo htmlspecialchars($event['time'] ?? ''); ?>"
                                    data-category="<?php echo htmlspecialchars($event['category'] ?? 'special'); ?>">
                                    <div class="event-date">
                                        <span class="day"><?php echo htmlspecialchars($day); ?></span>
                                        <span class="month"><?php echo htmlspecialchars($month); ?></span>
                                    </div>
                                    <div class="event-content">
                                        <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <?php if (!empty($event['time'])): ?>
                                            <p class="event-time"><?php echo htmlspecialchars($event['time']); ?></p>
                                        <?php endif; ?>
                                        <p><?php echo htmlspecialchars($event['description']); ?></p>
                                        <?php if (!empty($event['location'])): ?>
                                            <p class="event-location">📍 <?php echo htmlspecialchars($event['location']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="event-category">
                    <h2 data-translate="annualPrograms">Annual Programs</h2>
                    <div class="programs-grid">
                        <?php if (empty($annual_events)): ?>
                            <!-- Default content -->
                            <div class="program-card">
                                <h3 data-translate="ramadanProgram">Ramadan Program</h3>
                                <p data-translate="ramadanProgramDesc">Daily iftar gatherings, Taraweeh prayers, and special nightly programs throughout the holy month of Ramadan.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($annual_events as $event): ?>
                                <div class="program-card">
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($event['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Event Modal -->
<div id="eventModal" class="event-modal">
    <div class="event-modal-content">
        <span class="event-modal-close">&times;</span>
        <div class="event-modal-header">
            <h2 id="eventModalTitle"></h2>
            <div class="event-modal-badge" id="eventModalCategory"></div>
        </div>
        <div class="event-modal-body">
            <div class="event-modal-info">
                <div class="event-info-item">
                    <span class="info-icon">📅</span>
                    <div class="info-content">
                        <strong>Date</strong>
                        <p id="eventModalDate"></p>
                    </div>
                </div>
                <div class="event-info-item" id="eventModalTimeItem" style="display: none;">
                    <span class="info-icon">🕐</span>
                    <div class="info-content">
                        <strong>Time</strong>
                        <p id="eventModalTime"></p>
                    </div>
                </div>
                <div class="event-info-item" id="eventModalLocationItem" style="display: none;">
                    <span class="info-icon">📍</span>
                    <div class="info-content">
                        <strong>Location</strong>
                        <p id="eventModalLocation"></p>
                    </div>
                </div>
            </div>
            <div class="event-modal-description">
                <h3>Description</h3>
                <p id="eventModalDescription"></p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/event-modal.js"></script>


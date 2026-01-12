<?php
// Load data from SQL database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM resources ORDER BY category, title");
    $resources_data = $stmt->fetchAll();
    
    // Group resources by category
    $resources_by_category = [
        'understanding' => [],
        'prayers' => [],
        'dates' => [],
        'reading' => [],
        'services' => []
    ];
    
    foreach ($resources_data as $resource) {
        $category = $resource['category'] ?? 'understanding';
        if (isset($resources_by_category[$category])) {
            $resources_by_category[$category][] = $resource;
        }
    }
} catch (Exception $e) {
    error_log("Error loading resources: " . $e->getMessage());
    $resources_by_category = [
        'understanding' => [],
        'prayers' => [],
        'dates' => [],
        'reading' => [],
        'services' => []
    ]; // Fallback to empty arrays
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 data-translate="resourcesTitle">Resources</h1>
            <p class="page-subtitle" data-translate="resourcesSubtitle">Educational materials, prayers, and information about Shia Islam</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="resources-container">
                <!-- Understanding Shia Islam -->
                <div class="resource-category">
                    <h2 data-translate="understandingShia">Understanding Shia Islam</h2>
                    <div class="resources-grid">
                        <?php if (!empty($resources_by_category['understanding'])): ?>
                            <?php foreach ($resources_by_category['understanding'] as $resource): ?>
                                <div class="resource-card" data-title="<?php echo htmlspecialchars($resource['title']); ?>" data-content="<?php echo htmlspecialchars($resource['description'] ?? $resource['content'] ?? ''); ?>" data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>" data-content-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? $resource['content_fa'] ?? ''); ?>">
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($resource['description'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default content if no resources in admin -->
                            <div class="resource-card" data-title="The Ahl al-Bayt" data-content="The People of the Household (Ahl al-Bayt) of the Prophet Muhammad (PBUH) hold a central position in Shia Islam.">
                                <h3 data-translate="ahlBayt">The Ahl al-Bayt</h3>
                                <p data-translate="ahlBaytDesc">The People of the Household of the Prophet (PBUH), including Imam Ali (AS), Fatima (AS), and the Twelve Imams. Learn about their lives, teachings, and significance in Shia Islam.</p>
                            </div>
                            <div class="resource-card" data-title="Shia Beliefs & Principles" data-content="Shia Islam is built upon fundamental beliefs that guide the faith and practice of its followers.">
                                <h3 data-translate="shiaBeliefs">Shia Beliefs & Principles</h3>
                                <p data-translate="shiaBeliefsDesc">Core beliefs including Tawhid (Oneness of God), Nubuwwah (Prophethood), Imamate (Leadership), and the Day of Judgment.</p>
                            </div>
                            <div class="resource-card" data-title="History of Shia Islam" data-content="The history of Shia Islam begins with the life of Prophet Muhammad (PBUH) and the question of leadership after his passing.">
                                <h3 data-translate="historyShia">History of Shia Islam</h3>
                                <p data-translate="historyShiaDesc">The historical development of Shia Islam from the time of the Prophet (PBUH) through the events of Karbala and beyond.</p>
                            </div>
                            <div class="resource-card" data-title="The Twelve Imams" data-content="The Twelve Imams are the divinely appointed successors to Prophet Muhammad (PBUH) according to Shia Islam.">
                                <h3 data-translate="twelveImams">The Twelve Imams</h3>
                                <p data-translate="twelveImamsDesc">Learn about the twelve divinely appointed Imams, their lives, teachings, and their role as guides for the Muslim community.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Prayers & Supplications -->
                <div class="resource-category">
                    <h2 data-translate="prayersSupplications">Prayers & Supplications</h2>
                    <div class="resources-grid">
                        <?php if (!empty($resources_by_category['prayers'])): ?>
                            <?php foreach ($resources_by_category['prayers'] as $resource): ?>
                                <div class="resource-card" data-title="<?php echo htmlspecialchars($resource['title']); ?>" data-content="<?php echo htmlspecialchars($resource['description'] ?? $resource['content'] ?? ''); ?>" data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>" data-content-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? $resource['content_fa'] ?? ''); ?>">
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($resource['description'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default content -->
                            <div class="resource-card" data-title="Daily Prayers (Salat)" data-content="Performing the five daily prayers (Salat) is one of the fundamental pillars of Islam.">
                                <h3 data-translate="dailyPrayers">Daily Prayers (Salat)</h3>
                                <p data-translate="dailyPrayersDesc">Guidance on performing the five daily prayers according to Shia jurisprudence, including timings, qibla direction, and prayer formats.</p>
                            </div>
                            <div class="resource-card" data-title="Duas & Supplications" data-content="Duas (supplications) are personal prayers and invocations to Allah.">
                                <h3 data-translate="duasSupplications">Duas & Supplications</h3>
                                <p data-translate="duasSupplicationsDesc">Collection of authentic duas from the Ahl al-Bayt, including Dua Kumayl, Dua Tawassul, and supplications for various occasions.</p>
                            </div>
                            <div class="resource-card" data-title="Ziyarat" data-content="Ziyarat are visitation prayers and salutations to the Imams and holy sites.">
                                <h3 data-translate="ziyarat">Ziyarat</h3>
                                <p data-translate="ziyaratDesc">Visitation prayers for the Imams and holy sites, including Ziyarat Ashura and Ziyarat Waritha.</p>
                            </div>
                            <div class="resource-card" data-title="Special Night Prayers" data-content="Special night prayers hold great significance in Shia Islam, particularly during the holy month of Ramadan.">
                                <h3 data-translate="specialNightPrayers">Special Night Prayers</h3>
                                <p data-translate="specialNightPrayersDesc">Guidance for Laylat al-Qadr prayers, night prayers during Ramadan, and other special spiritual occasions.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Important Dates -->
                <div class="resource-category">
                    <h2 data-translate="importantDates">Important Dates</h2>
                    <div class="calendar-section">
                        <?php if (!empty($resources_by_category['dates'])): ?>
                            <?php foreach ($resources_by_category['dates'] as $resource): ?>
                                <div class="calendar-item" data-title="<?php echo htmlspecialchars($resource['title']); ?>" data-content="<?php echo htmlspecialchars($resource['description'] ?? $resource['content'] ?? ''); ?>" data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>" data-content-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? $resource['content_fa'] ?? ''); ?>">
                                    <div class="calendar-date"><?php echo htmlspecialchars($resource['date'] ?? ''); ?></div>
                                    <div class="calendar-content">
                                        <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                        <p><?php echo htmlspecialchars($resource['description'] ?? ''); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default content -->
                            <div class="calendar-item" data-title="Islamic New Year - 1st Muharram" data-content="The Islamic New Year begins on the first day of Muharram, the first month of the Islamic lunar calendar.">
                                <div class="calendar-date">1st Muharram</div>
                                <div class="calendar-content">
                                    <h3 data-translate="islamicNewYear">Islamic New Year</h3>
                                    <p data-translate="islamicNewYearDesc">Beginning of the Islamic calendar year</p>
                                </div>
                            </div>
                            <div class="calendar-item" data-title="Day of Ashura - 10th Muharram" data-content="The Day of Ashura, the 10th of Muharram, is one of the most significant days in Shia Islam.">
                                <div class="calendar-date">10th Muharram</div>
                                <div class="calendar-content">
                                    <h3 data-translate="dayOfAshura">Day of Ashura</h3>
                                    <p data-translate="dayOfAshuraDesc">Martyrdom of Imam Hussain (AS) at Karbala (680 CE)</p>
                                </div>
                            </div>
                            <div class="calendar-item" data-title="Arbaeen - 20th Safar" data-content="Arbaeen marks the 40th day after the martyrdom of Imam Hussain (AS) on Ashura.">
                                <div class="calendar-date">20th Safar</div>
                                <div class="calendar-content">
                                    <h3 data-translate="arbaeen">Arbaeen</h3>
                                    <p data-translate="arbaeenDesc">40th day after Ashura, commemorating the martyrdom of Imam Hussain (AS)</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recommended Reading -->
                <div class="resource-category">
                    <h2 data-translate="recommendedReading">Recommended Reading</h2>
                    <div class="reading-list">
                        <?php if (!empty($resources_by_category['reading'])): ?>
                            <?php foreach ($resources_by_category['reading'] as $resource): ?>
                                <div class="reading-item" data-title="<?php echo htmlspecialchars($resource['title']); ?>" data-content="<?php echo htmlspecialchars($resource['description'] ?? $resource['content'] ?? ''); ?>" data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>" data-content-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? $resource['content_fa'] ?? ''); ?>">
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <?php if (!empty($resource['author'])): ?>
                                        <p class="author"><?php echo htmlspecialchars($resource['author']); ?></p>
                                    <?php endif; ?>
                                    <p><?php echo htmlspecialchars($resource['description'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default content -->
                            <div class="reading-item" data-title="Nahj al-Balagha" data-content="Nahj al-Balagha (The Peak of Eloquence) is a collection of sermons, letters, and sayings of Imam Ali ibn Abi Talib (AS).">
                                <h3 data-translate="nahjBalagha">Nahj al-Balagha</h3>
                                <p class="author" data-translate="nahjBalaghaAuthor">Compiled by Sayyid al-Radi</p>
                                <p data-translate="nahjBalaghaDesc">Collection of sermons, letters, and sayings of Imam Ali (AS)</p>
                            </div>
                            <div class="reading-item" data-title="Sahifa al-Sajjadiyya" data-content="Sahifa al-Sajjadiyya (The Scroll of al-Sajjad) is a collection of supplications attributed to Imam Zain al-Abidin (AS).">
                                <h3 data-translate="sahifaSajjadiyya">Sahifa al-Sajjadiyya</h3>
                                <p class="author" data-translate="sahifaSajjadiyyaAuthor">Imam Zain al-Abidin (AS)</p>
                                <p data-translate="sahifaSajjadiyyaDesc">Collection of supplications by the fourth Imam</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Community Services -->
                <div class="resource-category">
                    <h2 data-translate="communityServices">Community Services</h2>
                    <div class="services-grid">
                        <?php if (!empty($resources_by_category['services'])): ?>
                            <?php foreach ($resources_by_category['services'] as $resource): ?>
                                <div class="service-card" data-title="<?php echo htmlspecialchars($resource['title']); ?>" data-content="<?php echo htmlspecialchars($resource['description'] ?? $resource['content'] ?? ''); ?>" data-title-fa="<?php echo htmlspecialchars($resource['title_fa'] ?? ''); ?>" data-content-fa="<?php echo htmlspecialchars($resource['description_fa'] ?? $resource['content_fa'] ?? ''); ?>">
                                    <h3><?php echo htmlspecialchars($resource['title']); ?></h3>
                                    <p><?php echo htmlspecialchars($resource['description'] ?? ''); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- Default content -->
                            <div class="service-card">
                                <h3 data-translate="marriageServices">Marriage Services</h3>
                                <p data-translate="marriageServicesDesc">Assistance with Islamic marriage ceremonies and documentation</p>
                            </div>
                            <div class="service-card">
                                <h3 data-translate="religiousGuidance">Religious Guidance</h3>
                                <p data-translate="religiousGuidanceDesc">Access to scholars for religious questions and guidance</p>
                            </div>
                            <div class="service-card">
                                <h3 data-translate="communitySupportService">Community Support</h3>
                                <p data-translate="communitySupportServiceDesc">Support services for community members in need</p>
                            </div>
                            <div class="service-card">
                                <h3 data-translate="libraryAccess">Library Access</h3>
                                <p data-translate="libraryAccessDesc">Community library with books on Islamic studies and Shia literature</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Resource Modal -->
<div id="resourceModal" class="resource-modal">
    <div class="resource-modal-content">
        <span class="resource-modal-close">&times;</span>
        <h2 id="modalTitle"></h2>
        <div id="modalContent" class="modal-text"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/modals.js"></script>


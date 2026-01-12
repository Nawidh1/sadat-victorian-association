<?php
// Load data from SQL database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT field, value_en, value_fa FROM about");
    $about_rows = $stmt->fetchAll();
    
    // Convert field/value structure to associative array
    $about_data = [];
    foreach ($about_rows as $row) {
        $field = $row['field'];
        // Handle nested fields like values_faith, values_service, etc.
        if (strpos($field, '_') !== false) {
            list($parent, $child) = explode('_', $field, 2);
            // Check if parent key exists and is not already an array
            if (!isset($about_data[$parent])) {
                $about_data[$parent] = [];
            } elseif (!is_array($about_data[$parent])) {
                // If parent is a string, convert it to array with a 'main' key
                $old_value = $about_data[$parent];
                $about_data[$parent] = ['main' => $old_value];
            }
            $about_data[$parent][$child] = $row['value_en'];
            if (!empty($row['value_fa'])) {
                $about_data[$parent][$child . '_fa'] = $row['value_fa'];
            }
        } else {
            // Only set if not already set as array (from nested fields)
            if (!isset($about_data[$field]) || !is_array($about_data[$field])) {
                $about_data[$field] = $row['value_en'];
                if (!empty($row['value_fa'])) {
                    $about_data[$field . '_fa'] = $row['value_fa'];
                }
            }
        }
    }
} catch (Exception $e) {
    error_log("Error loading about us data: " . $e->getMessage());
    $about_data = []; // Fallback to empty array
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 data-translate="aboutTitle">About Us</h1>
            <p class="page-subtitle" data-translate="aboutSubtitle">Our story, mission, and commitment to the Shia community</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="content-wrapper">
                <div class="content-main">
                    <h2 data-translate="whoWeAre">Who We Are</h2>
                    <?php 
                    $who_we_are_1 = $about_data['who_we_are_1'] ?? '';
                    $who_we_are_2 = $about_data['who_we_are_2'] ?? '';
                    if (!empty($who_we_are_1)): ?>
                        <p><?php echo nl2br(htmlspecialchars($who_we_are_1)); ?></p>
                    <?php else: ?>
                        <p data-translate="whoWeAreDesc1">The Sadat Victorian Association is a community organization dedicated to serving the Shia Muslim community in Victoria. Founded with a vision to preserve our rich Islamic heritage and strengthen community bonds, we work tirelessly to provide educational, spiritual, and social support to our members.</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($who_we_are_2)): ?>
                        <p><?php echo nl2br(htmlspecialchars($who_we_are_2)); ?></p>
                    <?php else: ?>
                        <p data-translate="whoWeAreDesc2">Our association takes its name from "Sadat," referring to the descendants of the Prophet Muhammad (PBUH) through his daughter Fatima (AS) and son-in-law Imam Ali (AS). This connection to the Ahl al-Bayt (People of the Household) guides our values and mission.</p>
                    <?php endif; ?>

                    <h2 data-translate="ourMission">Our Mission</h2>
                    <?php 
                    $mission_text = '';
                    $mission_text_fa = '';
                    $has_mission_db = false;
                    if (isset($about_data['mission']) && !empty($about_data['mission'])) {
                        if (is_array($about_data['mission'])) {
                            // If it's an array, try to get 'main' or use first value
                            $mission_text = $about_data['mission']['main'] ?? (is_array($about_data['mission']) ? reset($about_data['mission']) : '');
                            $mission_text_fa = $about_data['mission']['main_fa'] ?? '';
                        } else {
                            $mission_text = $about_data['mission'];
                            $mission_text_fa = $about_data['mission_fa'] ?? '';
                        }
                        $has_mission_db = !empty($mission_text);
                    }
                    if ($has_mission_db): ?>
                        <p data-mission-en="<?php echo htmlspecialchars($mission_text); ?>" data-mission-fa="<?php echo htmlspecialchars($mission_text_fa); ?>" class="mission-db-content"><?php echo nl2br(htmlspecialchars($mission_text)); ?></p>
                    <?php endif; ?>
                    <!-- Default mission list (always rendered, hidden if database content exists) -->
                    <div class="mission-default-content" style="<?php echo $has_mission_db ? 'display: none;' : ''; ?>">
                        <p data-translate="missionCommitment">We are committed to:</p>
                        <ul class="mission-list">
                            <li data-translate="mission1">Providing authentic Islamic education based on the teachings of the Ahl al-Bayt</li>
                            <li data-translate="mission2">Organizing religious commemorations and spiritual gatherings</li>
                            <li data-translate="mission3">Supporting community members in times of need</li>
                            <li data-translate="mission4">Promoting interfaith dialogue and understanding</li>
                            <li data-translate="mission5">Preserving and sharing Shia Islamic traditions and culture</li>
                            <li data-translate="mission6">Fostering unity and brotherhood within the Muslim community</li>
                        </ul>
                    </div>

                    <h2 data-translate="ourValues">Our Values</h2>
                    <div class="values-grid">
                        <div class="value-item" 
                            data-value-en="<?php echo htmlspecialchars($about_data['values']['faith'] ?? 'Rooted in the teachings of the Holy Quran and the Ahl al-Bayt'); ?>"
                            data-value-fa="<?php echo htmlspecialchars($about_data['values']['faith_fa'] ?? ''); ?>">
                            <h3 data-translate="faith">Faith</h3>
                            <p><?php echo htmlspecialchars($about_data['values']['faith'] ?? 'Rooted in the teachings of the Holy Quran and the Ahl al-Bayt'); ?></p>
                        </div>
                        <div class="value-item"
                            data-value-en="<?php echo htmlspecialchars($about_data['values']['service'] ?? 'Dedicated to serving our community with compassion and integrity'); ?>"
                            data-value-fa="<?php echo htmlspecialchars($about_data['values']['service_fa'] ?? ''); ?>">
                            <h3 data-translate="service">Service</h3>
                            <p><?php echo htmlspecialchars($about_data['values']['service'] ?? 'Dedicated to serving our community with compassion and integrity'); ?></p>
                        </div>
                        <div class="value-item"
                            data-value-en="<?php echo htmlspecialchars($about_data['values']['unity'] ?? 'Building bridges and fostering harmony among all Muslims'); ?>"
                            data-value-fa="<?php echo htmlspecialchars($about_data['values']['unity_fa'] ?? ''); ?>">
                            <h3 data-translate="unity">Unity</h3>
                            <p><?php echo htmlspecialchars($about_data['values']['unity'] ?? 'Building bridges and fostering harmony among all Muslims'); ?></p>
                        </div>
                        <div class="value-item"
                            data-value-en="<?php echo htmlspecialchars($about_data['values']['education'] ?? 'Promoting knowledge and understanding of Islamic principles'); ?>"
                            data-value-fa="<?php echo htmlspecialchars($about_data['values']['education_fa'] ?? ''); ?>">
                            <h3 data-translate="educationValue">Education</h3>
                            <p><?php echo htmlspecialchars($about_data['values']['education'] ?? 'Promoting knowledge and understanding of Islamic principles'); ?></p>
                        </div>
                    </div>

                    <h2 data-translate="whatWeDo">What We Do</h2>
                    <?php 
                    $activities_text = '';
                    $activities_text_fa = '';
                    $has_activities_db = false;
                    if (isset($about_data['activities']) && !empty($about_data['activities'])) {
                        if (is_array($about_data['activities'])) {
                            // If it's an array, try to get 'main' or use first value
                            $activities_text = $about_data['activities']['main'] ?? (is_array($about_data['activities']) ? reset($about_data['activities']) : '');
                            $activities_text_fa = $about_data['activities']['main_fa'] ?? '';
                        } else {
                            $activities_text = $about_data['activities'];
                            $activities_text_fa = $about_data['activities_fa'] ?? '';
                        }
                        $has_activities_db = !empty($activities_text);
                    }
                    if ($has_activities_db): ?>
                        <p data-activities-en="<?php echo htmlspecialchars($activities_text); ?>" data-activities-fa="<?php echo htmlspecialchars($activities_text_fa); ?>" class="activities-db-content"><?php echo nl2br(htmlspecialchars($activities_text)); ?></p>
                    <?php endif; ?>
                    <!-- Default activities list (always rendered, hidden if database content exists) -->
                    <div class="activities-default-content" style="<?php echo $has_activities_db ? 'display: none;' : ''; ?>">
                        <p data-translate="whatWeDoDesc">Throughout the year, we organize various activities and programs:</p>
                        <ul>
                            <li data-translate="activity1"><strong>Weekly Prayers:</strong> Regular Friday prayers and community gatherings</li>
                            <li data-translate="activity2"><strong>Religious Commemorations:</strong> Observing important dates in the Islamic calendar, including Muharram, Safar, and other significant occasions</li>
                            <li data-translate="activity3"><strong>Educational Programs:</strong> Classes and lectures on Islamic studies, Quranic interpretation, and Shia jurisprudence</li>
                            <li data-translate="activity4"><strong>Youth Programs:</strong> Activities and mentorship for young community members</li>
                            <li data-translate="activity5"><strong>Community Support:</strong> Assistance programs and social services for those in need</li>
                            <li data-translate="activity6"><strong>Cultural Events:</strong> Celebrations that honor our heritage and traditions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="quote-section">
        <div class="container">
            <blockquote>
                <p id="quoteText">"I leave behind me two weighty things: the Book of Allah and my Ahl al-Bayt. If you hold fast to them, you will never go astray."</p>
                <cite id="quoteAuthor">— Prophet Muhammad (PBUH)</cite>
            </blockquote>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>


<?php
// Load data from SQL database
require_once __DIR__ . '/config/database.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT field, value_en, value_fa FROM contact");
    $contact_rows = $stmt->fetchAll();
    
    // Convert field/value structure to associative array
    $contact_data = [];
    foreach ($contact_rows as $row) {
        $contact_data[$row['field']] = $row['value_en'];
        $contact_data[$row['field'] . '_fa'] = $row['value_fa'];
    }
} catch (Exception $e) {
    error_log("Error loading contact data: " . $e->getMessage());
    $contact_data = []; // Fallback to empty array
}

include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 data-translate="contactTitle">Contact Us</h1>
            <p class="page-subtitle" data-translate="contactSubtitle">Get in touch with us - we're here to help</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-info">
                    <h2 data-translate="getInTouch">Get in Touch</h2>
                    <p data-translate="getInTouchDesc">We welcome your questions, feedback, and inquiries. Whether you're looking to join our community, attend an event, or learn more about Shia Islam, we're here to help.</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <h3 data-translate="address">Address</h3>
                            <p><?php echo nl2br(htmlspecialchars($contact_data['address'] ?? '123 Community Street\nVictoria, VIC 3000\nAustralia')); ?></p>
                        </div>
                        
                        <div class="contact-item">
                            <h3 data-translate="phone">Phone</h3>
                            <p><?php echo htmlspecialchars($contact_data['phone'] ?? '+61 (0)3 1234 5678'); ?></p>
                        </div>
                        
                        <div class="contact-item">
                            <h3 data-translate="email">Email</h3>
                            <p><?php echo htmlspecialchars($contact_data['email'] ?? 'info@sadatvictorian.org.au'); ?></p>
                        </div>
                        
                        <div class="contact-item">
                            <h3 data-translate="officeHours">Office Hours</h3>
                            <p><?php echo nl2br(htmlspecialchars($contact_data['hours'] ?? 'Monday - Friday: 9:00 AM - 5:00 PM\nSaturday: 10:00 AM - 2:00 PM\nSunday: Closed')); ?></p>
                        </div>
                    </div>

                    <div class="social-media">
                        <h3 data-translate="followUs">Follow Us</h3>
                        <div class="social-links">
                            <a href="#" class="social-link">Facebook</a>
                            <a href="#" class="social-link">Instagram</a>
                            <a href="#" class="social-link">Email Newsletter</a>
                        </div>
                    </div>
                </div>

                <div class="contact-form-wrapper">
                    <h2 data-translate="sendMessage">Send us a Message</h2>
                    <form class="contact-form" id="contactForm">
                        <div class="form-group">
                            <label for="name" data-translate="yourName">Your Name *</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" data-translate="emailAddress">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" data-translate="phoneNumber">Phone Number</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" data-translate="subject">Subject *</label>
                            <select id="subject" name="subject" required>
                                <option value="" data-translate="selectSubject">Select a subject</option>
                                <option value="general" data-translate="generalInquiry">General Inquiry</option>
                                <option value="events" data-translate="eventsPrograms">Events & Programs</option>
                                <option value="membership" data-translate="membership">Membership</option>
                                <option value="support" data-translate="communitySupport">Community Support</option>
                                <option value="education" data-translate="educationPrograms">Education Programs</option>
                                <option value="other" data-translate="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" data-translate="message">Message *</label>
                            <textarea id="message" name="message" rows="6" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary" data-translate="send">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="map-section">
        <div class="container">
            <h2 data-translate="findUs">Find Us</h2>
            <div class="map-placeholder">
                <p>Map location will be displayed here</p>
                <p class="map-note"><?php echo htmlspecialchars($contact_data['address'] ?? '123 Community Street, Victoria, VIC 3000'); ?></p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>


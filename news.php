<?php
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 data-translate="newsTitle">News & Updates</h1>
            <p class="page-subtitle" data-translate="newsSubtitle">Stay informed about our latest announcements and community news</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div id="news-container" class="news-list">
                <!-- News items will be loaded dynamically from admin panel -->
                <div class="loading-message" data-translate="loading">Loading news...</div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
<script src="js/news-loader.js"></script>


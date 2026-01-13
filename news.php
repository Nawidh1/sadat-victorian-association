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

    <section class="content-section news-page-section">
        <div class="container">
            <div id="news-container" class="news-container-modern">
                <!-- News items will be loaded dynamically from admin panel -->
                <div class="loading-message" data-translate="loading">Loading news...</div>
            </div>
        </div>
    </section>
</main>

<!-- News Modal -->
<div id="newsModal" class="news-modal" onclick="closeNewsModal(event)">
    <div class="news-modal-content" onclick="event.stopPropagation()">
        <span class="news-modal-close" onclick="closeNewsModal(event)">&times;</span>
        <h2 id="newsModalTitle"></h2>
        <div id="newsModalContent" class="news-modal-body"></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="js/news-loader.js"></script>


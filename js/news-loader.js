// Load and display news from admin panel
(function() {
    'use strict';
    
    const newsFile = 'api/news.php';
    const newsContainer = document.getElementById('news-container');
    
    // Get current language
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }
    
    // Format date
    function formatDate(dateString) {
        if (!dateString) return '';
        const lang = getCurrentLanguage();
        const isFarsi = lang === 'fa';
        
        if (isFarsi && typeof window.formatDateToPersian === 'function') {
            // Use Persian date converter
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            const englishDate = date.toLocaleDateString('en-US', options);
            return window.formatDateToPersian(englishDate);
        } else {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }
    }
    
    // Load and display news
    async function loadNews() {
        try {
            const response = await fetch(newsFile);
            if (!response.ok) {
                throw new Error('News file not found');
            }
            
            const news = await response.json();
            const lang = getCurrentLanguage();
            const isFarsi = lang === 'fa';
            
            if (!news || news.length === 0) {
                newsContainer.innerHTML = '<div class="empty-state" data-translate="noNews">No news items available yet.</div>';
                return;
            }
            
            // Sort news by date (newest first)
            const sortedNews = news.sort((a, b) => new Date(b.date) - new Date(a.date));
            
            // Clear container
            newsContainer.innerHTML = '';
            
            // Display featured news (first item) in large format
            if (sortedNews.length > 0) {
                const featured = sortedNews[0];
                const featuredTitle = isFarsi && featured.title_fa ? featured.title_fa : featured.title;
                const featuredContent = isFarsi && featured.content_fa ? featured.content_fa : featured.content;
                const featuredPreview = featuredContent.length > 200 ? featuredContent.substring(0, 200) + '...' : featuredContent;
                const featuredImage = featured.image || 'uploads/images/news-placeholder.jpg';
                
                const featuredCard = document.createElement('article');
                featuredCard.className = 'news-featured';
                
                if (featured.title_fa) {
                    featuredCard.setAttribute('data-title', featured.title);
                    featuredCard.setAttribute('data-title-fa', featured.title_fa);
                }
                if (featured.content_fa) {
                    featuredCard.setAttribute('data-content', featured.content);
                    featuredCard.setAttribute('data-content-fa', featured.content_fa);
                }
                
                featuredCard.innerHTML = `
                    <div class="news-featured-image">
                        <img src="${featuredImage}" alt="${escapeHtml(featuredTitle)}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'400\'%3E%3Crect fill=\'%23f0f0f0\' width=\'800\' height=\'400\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-family=\'Arial\' font-size=\'24\'%3ENews Image%3C/text%3E%3C/svg%3E'">
                        <div class="news-featured-overlay">
                            <span class="news-badge">Latest News</span>
                        </div>
                    </div>
                    <div class="news-featured-content">
                        <div class="news-meta">
                            <span class="news-date-large">${formatDate(featured.date)}</span>
                            <span class="news-category">Community</span>
                        </div>
                        <h2 class="news-featured-title">${escapeHtml(featuredTitle)}</h2>
                        <p class="news-featured-excerpt">${escapeHtml(featuredPreview)}</p>
                        <a href="#" class="news-read-more-btn" onclick="event.preventDefault(); openNewsModal('${escapeHtml(featuredTitle)}', '${escapeHtml(featuredContent.replace(/'/g, "\\'"))}'); return false;">
                            Read Full Story <span>→</span>
                        </a>
                    </div>
                `;
                
                newsContainer.appendChild(featuredCard);
            }
            
            // Display other news items in grid
            if (sortedNews.length > 1) {
                const gridContainer = document.createElement('div');
                gridContainer.className = 'news-grid-container';
                
                sortedNews.slice(1).forEach((item, index) => {
                    const newsCard = document.createElement('article');
                    newsCard.className = 'news-card-modern';
                    
                    // Add data attributes for bilingual support
                    if (item.title_fa) {
                        newsCard.setAttribute('data-title', item.title);
                        newsCard.setAttribute('data-title-fa', item.title_fa);
                    }
                    if (item.content_fa) {
                        newsCard.setAttribute('data-content', item.content);
                        newsCard.setAttribute('data-content-fa', item.content_fa);
                    }
                    
                    const title = isFarsi && item.title_fa ? item.title_fa : item.title;
                    const content = isFarsi && item.content_fa ? item.content_fa : item.content;
                    const previewContent = content.length > 120 ? content.substring(0, 120) + '...' : content;
                    const newsImage = item.image || 'uploads/images/news-placeholder.jpg';
                    
                    newsCard.innerHTML = `
                        <div class="news-card-image">
                            <img src="${newsImage}" alt="${escapeHtml(title)}" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'250\'%3E%3Crect fill=\'%23f0f0f0\' width=\'400\' height=\'250\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%23999\' font-family=\'Arial\' font-size=\'18\'%3ENews%3C/text%3E%3C/svg%3E'">
                            <div class="news-card-overlay"></div>
                        </div>
                        <div class="news-card-body">
                            <div class="news-card-meta">
                                <span class="news-date-small">${formatDate(item.date)}</span>
                            </div>
                            <h3 class="news-card-title">${escapeHtml(title)}</h3>
                            <p class="news-card-excerpt">${escapeHtml(previewContent)}</p>
                            <a href="#" class="news-card-link" onclick="event.preventDefault(); openNewsModal('${escapeHtml(title)}', '${escapeHtml(content.replace(/'/g, "\\'"))}'); return false;">
                                Read More →
                            </a>
                        </div>
                    `;
                    
                    gridContainer.appendChild(newsCard);
                });
                
                newsContainer.appendChild(gridContainer);
            }
            
            // Update dates after loading
            if (typeof window.updateDates === 'function') {
                setTimeout(() => {
                    window.updateDates(isFarsi);
                }, 100);
            }
            
            // Update admin content after loading
            if (typeof window.updateAdminContent === 'function') {
                setTimeout(window.updateAdminContent, 100);
            }
            
        } catch (error) {
            console.error('Error loading news:', error);
            newsContainer.innerHTML = '<div class="empty-state" data-translate="noNews">No news items available yet.</div>';
        }
    }
    
    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Open news modal
    window.openNewsModal = function(title, content) {
        const modal = document.getElementById('newsModal');
        const modalTitle = document.getElementById('newsModalTitle');
        const modalContent = document.getElementById('newsModalContent');
        
        if (modal && modalTitle && modalContent) {
            modalTitle.textContent = title;
            modalContent.innerHTML = '<p>' + content.replace(/\n/g, '</p><p>') + '</p>';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };
    
    // Close news modal
    window.closeNewsModal = function(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const modal = document.getElementById('newsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNews();
        
        // Reload when language changes
        window.addEventListener('languageChanged', function() {
            loadNews();
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeNewsModal();
            }
        });
    });
})();


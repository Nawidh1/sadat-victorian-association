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
            
            // Display each news item
            sortedNews.forEach(item => {
                const newsCard = document.createElement('article');
                newsCard.className = 'news-card';
                
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
                
                newsCard.innerHTML = `
                    <div class="news-date">${formatDate(item.date)}</div>
                    <h3>${escapeHtml(title)}</h3>
                    <p>${escapeHtml(content)}</p>
                `;
                
                newsContainer.appendChild(newsCard);
            });
            
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
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadNews();
        
        // Reload when language changes
        window.addEventListener('languageChanged', function() {
            loadNews();
        });
    });
})();


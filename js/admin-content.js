// Handle bilingual content from admin panel
// This script displays the correct language for admin-managed content

(function() {
    'use strict';
    
    // Get current language
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }
    
    // Update admin-managed content based on language
    function updateAdminContent() {
        const lang = getCurrentLanguage();
        const isFarsi = lang === 'fa';
        
        // Update news items
        updateNewsItems(isFarsi);
        
        // Update events
        updateEvents(isFarsi);
        
        // Update resources
        updateResources(isFarsi);
    }
    
    // Update news items
    function updateNewsItems(isFarsi) {
        const newsCards = document.querySelectorAll('.news-card[data-title-fa]');
        newsCards.forEach(card => {
            const titleEl = card.querySelector('h3');
            const contentEl = card.querySelector('p');
            
            if (titleEl && card.dataset.titleFa) {
                titleEl.textContent = isFarsi ? card.dataset.titleFa : card.dataset.title;
            }
            
            if (contentEl && card.dataset.contentFa) {
                contentEl.textContent = isFarsi ? card.dataset.contentFa : card.dataset.content;
            }
        });
    }
    
    // Update events
    function updateEvents(isFarsi) {
        const eventCards = document.querySelectorAll('.event-card[data-title-fa]');
        eventCards.forEach(card => {
            const titleEl = card.querySelector('h3');
            const descEl = card.querySelector('.event-content p:not(.event-time):not(.event-location)');
            const locationEl = card.querySelector('.event-location');
            
            if (titleEl && card.dataset.titleFa) {
                titleEl.textContent = isFarsi ? card.dataset.titleFa : card.dataset.title;
            }
            
            if (descEl && card.dataset.descriptionFa) {
                descEl.textContent = isFarsi ? card.dataset.descriptionFa : card.dataset.description;
            }
            
            if (locationEl && card.dataset.locationFa) {
                const locationText = locationEl.textContent.replace(/📍\s*/, '');
                const newLocation = isFarsi && card.dataset.locationFa ? card.dataset.locationFa : card.dataset.location;
                locationEl.textContent = '📍 ' + newLocation;
            }
        });
    }
    
    // Update resources
    function updateResources(isFarsi) {
        // Update resource cards
        const resourceCards = document.querySelectorAll('.resource-card[data-title-fa]');
        resourceCards.forEach(card => {
            const titleEl = card.querySelector('h3');
            const descEl = card.querySelector('p');
            
            if (titleEl && card.dataset.titleFa) {
                titleEl.textContent = isFarsi ? card.dataset.titleFa : card.dataset.title;
            }
            
            if (descEl && (card.dataset.descriptionFa || card.dataset.contentFa)) {
                descEl.textContent = isFarsi ? (card.dataset.descriptionFa || card.dataset.contentFa) : (card.dataset.description || card.dataset.content);
            }
        });
        
        // Update calendar items
        const calendarItems = document.querySelectorAll('.calendar-item[data-title]');
        calendarItems.forEach(item => {
            const titleEl = item.querySelector('.calendar-content h3');
            const contentEl = item.querySelector('.calendar-content p');
            
            if (titleEl) {
                const titleEn = item.getAttribute('data-title');
                const titleFa = item.getAttribute('data-title-fa');
                if (isFarsi && titleFa) {
                    titleEl.textContent = titleFa;
                } else if (!isFarsi && titleEn) {
                    titleEl.textContent = titleEn;
                }
            }
            
            if (contentEl) {
                const contentEn = item.getAttribute('data-content');
                const contentFa = item.getAttribute('data-content-fa');
                if (isFarsi && contentFa) {
                    contentEl.textContent = contentFa;
                } else if (!isFarsi && contentEn) {
                    contentEl.textContent = contentEn;
                }
            }
        });
        
        // Update reading items
        const readingItems = document.querySelectorAll('.reading-item[data-title]');
        readingItems.forEach(item => {
            const titleEl = item.querySelector('h3');
            const contentEl = item.querySelector('p:not(.author)');
            
            if (titleEl) {
                const titleEn = item.getAttribute('data-title');
                const titleFa = item.getAttribute('data-title-fa');
                if (isFarsi && titleFa) {
                    titleEl.textContent = titleFa;
                } else if (!isFarsi && titleEn) {
                    titleEl.textContent = titleEn;
                }
            }
            
            if (contentEl) {
                const contentEn = item.getAttribute('data-content');
                const contentFa = item.getAttribute('data-content-fa');
                if (isFarsi && contentFa) {
                    contentEl.textContent = contentFa;
                } else if (!isFarsi && contentEn) {
                    contentEl.textContent = contentEn;
                }
            }
        });
        
        // Update service cards
        const serviceCards = document.querySelectorAll('.service-card[data-title]');
        serviceCards.forEach(card => {
            const titleEl = card.querySelector('h3');
            const contentEl = card.querySelector('p');
            
            if (titleEl) {
                const titleEn = card.getAttribute('data-title');
                const titleFa = card.getAttribute('data-title-fa');
                if (isFarsi && titleFa) {
                    titleEl.textContent = titleFa;
                } else if (!isFarsi && titleEn) {
                    titleEl.textContent = titleEn;
                }
            }
            
            if (contentEl) {
                const contentEn = card.getAttribute('data-content');
                const contentFa = card.getAttribute('data-content-fa');
                if (isFarsi && contentFa) {
                    contentEl.textContent = contentFa;
                } else if (!isFarsi && contentEn) {
                    contentEl.textContent = contentEn;
                }
            }
        });
    }
    
    // Make updateAdminContent available globally
    window.updateAdminContent = updateAdminContent;
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateAdminContent();
        
        // Listen for language changes
        window.addEventListener('languageChanged', function() {
            updateAdminContent();
        });
    });
})();


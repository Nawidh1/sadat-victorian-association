// Resource Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('resourceModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    const closeBtn = document.querySelector('.resource-modal-close');
    const resourceCards = document.querySelectorAll('.resource-card[data-title], .calendar-item[data-title], .reading-item[data-title]');

    // Mapping of data-title to translation keys
    const titleMap = {
        'The Ahl al-Bayt': 'ahlBayt',
        'Shia Beliefs & Principles': 'shiaBeliefs',
        'History of Shia Islam': 'historyShia',
        'The Twelve Imams': 'twelveImams',
        'Daily Prayers (Salat)': 'dailyPrayers',
        'Duas & Supplications': 'duasSupplications',
        'Ziyarat': 'ziyarat',
        'Special Night Prayers': 'specialNightPrayers',
        'Islamic New Year - 1st Muharram': 'islamicNewYear',
        'Day of Ashura - 10th Muharram': 'dayOfAshura',
        'Arbaeen - 20th Safar': 'arbaeen',
        'Birthday of Imam Ali (AS) - 13th Rajab': 'imamAliBirthday',
        'Birthday of Imam Mahdi (AS) - 15th Sha\'ban': 'imamMahdiBirthday',
        'Martyrdom of Imam Ali (AS) - 21st Ramadan': 'martyrdomImamAli',
        'Nahj al-Balagha': 'nahjBalagha',
        'Sahifa al-Sajjadiyya': 'sahifaSajjadiyya',
        'Peshawar Nights': 'peshawarNights',
        'Then I Was Guided': 'thenIWasGuided'
    };

    const contentMap = {
        'The Ahl al-Bayt': 'ahlBaytContent',
        'Shia Beliefs & Principles': 'shiaBeliefsContent',
        'History of Shia Islam': 'historyShiaContent',
        'The Twelve Imams': 'twelveImamsContent',
        'Daily Prayers (Salat)': 'dailyPrayersContent',
        'Duas & Supplications': 'duasSupplicationsContent',
        'Ziyarat': 'ziyaratContent',
        'Special Night Prayers': 'specialNightPrayersContent',
        'Islamic New Year - 1st Muharram': 'islamicNewYearContent',
        'Day of Ashura - 10th Muharram': 'dayOfAshuraContent',
        'Arbaeen - 20th Safar': 'arbaeenContent',
        'Birthday of Imam Ali (AS) - 13th Rajab': 'imamAliBirthdayContent',
        'Birthday of Imam Mahdi (AS) - 15th Sha\'ban': 'imamMahdiBirthdayContent',
        'Martyrdom of Imam Ali (AS) - 21st Ramadan': 'martyrdomImamAliContent',
        'Nahj al-Balagha': 'nahjBalaghaContent',
        'Sahifa al-Sajjadiyya': 'sahifaSajjadiyyaContent',
        'Peshawar Nights': 'peshawarNightsContent',
        'Then I Was Guided': 'thenIWasGuidedContent'
    };

    // Get current language
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }

    // Get translation
    function getTranslation(key) {
        const lang = getCurrentLanguage();
        // Access the translations object from the global scope
        if (typeof translations !== 'undefined' && translations[lang] && translations[lang][key]) {
            return translations[lang][key];
        }
        // Fallback: try to access from window if available
        if (typeof window !== 'undefined' && window.translations && window.translations[lang] && window.translations[lang][key]) {
            return window.translations[lang][key];
        }
        return null;
    }

    // Open modal when clicking on a resource card
    resourceCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const dataTitle = this.getAttribute('data-title');
            const lang = getCurrentLanguage();
            
            // Get translated title and content
            let title = dataTitle;
            let content = this.getAttribute('data-content');
            
            // Try to get translated title
            const titleKey = titleMap[dataTitle];
            if (titleKey) {
                const translatedTitle = getTranslation(titleKey);
                if (translatedTitle) {
                    title = translatedTitle;
                }
            }
            
            // Try to get translated content
            const contentKey = contentMap[dataTitle];
            if (contentKey) {
                const translatedContent = getTranslation(contentKey);
                if (translatedContent) {
                    content = translatedContent;
                }
            }
            
            modalTitle.textContent = title;
            modalContent.textContent = content;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        });
    });

    // Close modal when clicking the X button
    if (closeBtn) {
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }

    // Close modal when clicking outside the modal content
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});


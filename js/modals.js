// Combined Modal Functionality for Events and Resources
document.addEventListener('DOMContentLoaded', function() {
    // ========== EVENT MODAL ==========
    const eventModal = document.getElementById('eventModal');
    if (eventModal) {
        const modalTitle = document.getElementById('eventModalTitle');
        const modalDate = document.getElementById('eventModalDate');
        const modalTime = document.getElementById('eventModalTime');
        const modalTimeItem = document.getElementById('eventModalTimeItem');
        const modalLocation = document.getElementById('eventModalLocation');
        const modalLocationItem = document.getElementById('eventModalLocationItem');
        const modalDescription = document.getElementById('eventModalDescription');
        const modalCategory = document.getElementById('eventModalCategory');
        const closeBtn = eventModal.querySelector('.event-modal-close');
        const eventCards = document.querySelectorAll('.event-card.clickable-event');

        function getCurrentLanguage() {
            return localStorage.getItem('language') || 'en';
        }

        eventCards.forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const lang = getCurrentLanguage();
                const isFarsi = lang === 'fa';
                
                const title = isFarsi && this.getAttribute('data-title-fa') ? 
                    this.getAttribute('data-title-fa') : 
                    this.getAttribute('data-title');
                
                const description = isFarsi && this.getAttribute('data-description-fa') ? 
                    this.getAttribute('data-description-fa') : 
                    this.getAttribute('data-description');
                
                const location = isFarsi && this.getAttribute('data-location-fa') ? 
                    this.getAttribute('data-location-fa') : 
                    this.getAttribute('data-location');
                
                const date = this.getAttribute('data-date') || '';
                const time = this.getAttribute('data-time') || '';
                const category = this.getAttribute('data-category') || 'regular';
                
                modalTitle.textContent = title;
                modalDate.textContent = date;
                modalDescription.textContent = description;
                
                if (time) {
                    modalTime.textContent = time;
                    modalTimeItem.style.display = 'flex';
                } else {
                    modalTimeItem.style.display = 'none';
                }
                
                if (location) {
                    modalLocation.textContent = location;
                    modalLocationItem.style.display = 'flex';
                } else {
                    modalLocationItem.style.display = 'none';
                }
                
                const categoryLabels = {
                    'regular': { en: 'Regular Event', fa: 'رویداد منظم' },
                    'special': { en: 'Special Event', fa: 'رویداد ویژه' },
                    'annual': { en: 'Annual Program', fa: 'برنامه سالانه' }
                };
                
                if (categoryLabels[category]) {
                    modalCategory.textContent = isFarsi ? categoryLabels[category].fa : categoryLabels[category].en;
                    modalCategory.style.display = 'inline-block';
                } else {
                    modalCategory.style.display = 'none';
                }
                
                eventModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                eventModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        eventModal.addEventListener('click', function(e) {
            if (e.target === eventModal) {
                eventModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // ========== RESOURCE MODAL ==========
    const resourceModal = document.getElementById('resourceModal');
    if (resourceModal) {
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        const closeBtn = resourceModal.querySelector('.resource-modal-close');
        const resourceCards = document.querySelectorAll('.resource-card[data-title], .calendar-item[data-title], .reading-item[data-title]');

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

        function getCurrentLanguage() {
            return localStorage.getItem('language') || 'en';
        }

        function getTranslation(key) {
            const lang = getCurrentLanguage();
            if (typeof translations !== 'undefined' && translations[lang] && translations[lang][key]) {
                return translations[lang][key];
            }
            if (typeof window !== 'undefined' && window.translations && window.translations[lang] && window.translations[lang][key]) {
                return window.translations[lang][key];
            }
            return null;
        }

        resourceCards.forEach(card => {
            card.style.cursor = 'pointer';
            card.addEventListener('click', function() {
                const dataTitle = this.getAttribute('data-title');
                const lang = getCurrentLanguage();
                
                let title = dataTitle;
                let content = this.getAttribute('data-content');
                
                const titleKey = titleMap[dataTitle];
                if (titleKey) {
                    const translatedTitle = getTranslation(titleKey);
                    if (translatedTitle) {
                        title = translatedTitle;
                    }
                }
                
                const contentKey = contentMap[dataTitle];
                if (contentKey) {
                    const translatedContent = getTranslation(contentKey);
                    if (translatedContent) {
                        content = translatedContent;
                    }
                }
                
                modalTitle.textContent = title;
                modalContent.textContent = content;
                resourceModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });

        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                resourceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        }

        resourceModal.addEventListener('click', function(e) {
            if (e.target === resourceModal) {
                resourceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (eventModal && eventModal.style.display === 'flex') {
                eventModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            if (resourceModal && resourceModal.style.display === 'flex') {
                resourceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        }
    });
});

// Event Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('eventModalTitle');
    const modalDate = document.getElementById('eventModalDate');
    const modalTime = document.getElementById('eventModalTime');
    const modalTimeItem = document.getElementById('eventModalTimeItem');
    const modalLocation = document.getElementById('eventModalLocation');
    const modalLocationItem = document.getElementById('eventModalLocationItem');
    const modalDescription = document.getElementById('eventModalDescription');
    const modalCategory = document.getElementById('eventModalCategory');
    const closeBtn = document.querySelector('.event-modal-close');
    const eventCards = document.querySelectorAll('.event-card.clickable-event');

    // Get current language
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }

    // Open modal when clicking on an event card
    eventCards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            const lang = getCurrentLanguage();
            const isFarsi = lang === 'fa';
            
            // Get event data
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
            
            // Set modal content
            modalTitle.textContent = title;
            modalDate.textContent = date;
            modalDescription.textContent = description;
            
            // Show/hide time
            if (time) {
                modalTime.textContent = time;
                modalTimeItem.style.display = 'flex';
            } else {
                modalTimeItem.style.display = 'none';
            }
            
            // Show/hide location
            if (location) {
                modalLocation.textContent = location;
                modalLocationItem.style.display = 'flex';
            } else {
                modalLocationItem.style.display = 'none';
            }
            
            // Set category badge
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
            
            // Show modal
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
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
});

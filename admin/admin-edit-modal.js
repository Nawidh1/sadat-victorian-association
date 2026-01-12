// Admin Edit Modal Functionality
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editModal');
    const closeBtn = document.querySelector('.admin-modal-close');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const editButtons = document.querySelectorAll('.edit-item-btn');

    // Open modal when clicking Edit button
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const itemCard = this.closest('.item-card');
            if (!itemCard) return;

            const type = this.getAttribute('data-type');
            
            if (type === 'event') {
                // Populate event form
                document.getElementById('edit_id').value = itemCard.getAttribute('data-id');
                document.getElementById('edit_title').value = itemCard.getAttribute('data-title') || '';
                document.getElementById('edit_title_fa').value = itemCard.getAttribute('data-title-fa') || '';
                document.getElementById('edit_date').value = itemCard.getAttribute('data-date') || '';
                document.getElementById('edit_time').value = itemCard.getAttribute('data-time') || '';
                document.getElementById('edit_location').value = itemCard.getAttribute('data-location') || '';
                document.getElementById('edit_location_fa').value = itemCard.getAttribute('data-location-fa') || '';
                document.getElementById('edit_category').value = itemCard.getAttribute('data-category') || 'regular';
                document.getElementById('edit_description').value = itemCard.getAttribute('data-description') || '';
                document.getElementById('edit_description_fa').value = itemCard.getAttribute('data-description-fa') || '';
                document.getElementById('edit_featured').checked = itemCard.getAttribute('data-featured') === '1';
            } else if (type === 'news') {
                // Populate news form
                document.getElementById('edit_id').value = itemCard.getAttribute('data-id');
                document.getElementById('edit_title').value = itemCard.getAttribute('data-title') || '';
                document.getElementById('edit_title_fa').value = itemCard.getAttribute('data-title-fa') || '';
                document.getElementById('edit_date').value = itemCard.getAttribute('data-date') || '';
                document.getElementById('edit_content').value = itemCard.getAttribute('data-content') || '';
                document.getElementById('edit_content_fa').value = itemCard.getAttribute('data-content-fa') || '';
            } else if (type === 'resource') {
                // Populate resource form
                document.getElementById('edit_id').value = itemCard.getAttribute('data-id');
                document.getElementById('edit_category').value = itemCard.getAttribute('data-category') || 'understanding';
                document.getElementById('edit_title').value = itemCard.getAttribute('data-title') || '';
                document.getElementById('edit_title_fa').value = itemCard.getAttribute('data-title-fa') || '';
                document.getElementById('edit_description').value = itemCard.getAttribute('data-description') || '';
                document.getElementById('edit_description_fa').value = itemCard.getAttribute('data-description-fa') || '';
            } else if (type === 'quote') {
                // Populate quote form
                document.getElementById('edit_id').value = itemCard.getAttribute('data-id');
                document.getElementById('edit_text').value = itemCard.getAttribute('data-text') || '';
                document.getElementById('edit_text_fa').value = itemCard.getAttribute('data-text-fa') || '';
                document.getElementById('edit_author').value = itemCard.getAttribute('data-author') || '';
                document.getElementById('edit_author_fa').value = itemCard.getAttribute('data-author-fa') || '';
            }

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });

    // Close modal
    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
            closeModal();
        }
    });
});

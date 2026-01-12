// Sidebar Toggle for Mobile
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    const adminContainer = document.querySelector('.admin-container');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 968) {
                if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                    sidebar.classList.remove('open');
                }
            }
        });
        
        // Close sidebar when clicking on a nav item on mobile
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                if (window.innerWidth <= 968) {
                    sidebar.classList.remove('open');
                }
            });
        });
    }
});

// ========== TRANSLATION FUNCTIONALITY ==========
async function translateToFarsi(text, targetFieldId, buttonElement) {
    if (!text || text.trim() === '') {
        alert('Please enter text in English first');
        return;
    }

    const targetField = document.getElementById(targetFieldId) || document.querySelector(`[name="${targetFieldId}"]`);
    if (!targetField) {
        console.error('Target field not found:', targetFieldId);
        return;
    }

    const originalValue = targetField.value;
    const originalButtonText = buttonElement.innerHTML;
    targetField.value = 'Translating...';
    targetField.disabled = true;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '⏳ Translating...';

    try {
        const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|fa`);
        const data = await response.json();

        if (data.responseStatus === 200 && data.responseData && data.responseData.translatedText) {
            targetField.value = data.responseData.translatedText;
            targetField.disabled = false;
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalButtonText;
            showTranslationMessage('Translation successful!', 'success', targetField);
        } else {
            throw new Error('Translation failed');
        }
    } catch (error) {
        console.error('Translation error:', error);
        targetField.value = originalValue;
        targetField.disabled = false;
        buttonElement.disabled = false;
        buttonElement.innerHTML = originalButtonText;
        showTranslationMessage('Translation failed. Please translate manually.', 'error', targetField);
    }
}

function showTranslationMessage(message, type, targetField) {
    const existing = targetField.parentNode.querySelector('.translation-message');
    if (existing) {
        existing.remove();
    }

    const messageEl = document.createElement('div');
    messageEl.className = `translation-message translation-${type}`;
    messageEl.textContent = message;
    messageEl.style.cssText = `
        padding: 0.75rem 1rem;
        margin-top: 0.5rem;
        border-radius: 6px;
        font-size: 0.875rem;
        ${type === 'success' 
            ? 'background: #d4edda; color: #155724; border: 1px solid #c3e6cb;' 
            : 'background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;'
        }
    `;

    if (targetField && targetField.parentNode) {
        targetField.parentNode.insertBefore(messageEl, targetField.nextSibling);
        
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const farsiFields = document.querySelectorAll('input[name$="_fa"], textarea[name$="_fa"]');
    
    farsiFields.forEach(field => {
        if (!field.id) {
            field.id = field.name;
        }
        
        if (field.parentNode.querySelector('.btn-translate')) {
            return;
        }
        
        const translateBtn = document.createElement('button');
        translateBtn.type = 'button';
        translateBtn.className = 'btn-translate';
        translateBtn.innerHTML = '🌐 Translate to Farsi';
        translateBtn.onclick = function() {
            const englishFieldName = field.name.replace('_fa', '');
            const englishField = document.querySelector(`input[name="${englishFieldName}"], textarea[name="${englishFieldName}"]`);
            
            if (englishField && englishField.value && englishField.value.trim() !== '') {
                translateToFarsi(englishField.value, field.id, translateBtn);
            } else {
                alert('Please enter text in the English field first');
            }
        };
        
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'translate-button-container';
            buttonContainer.appendChild(translateBtn);
            formGroup.appendChild(buttonContainer);
        }
    });
});

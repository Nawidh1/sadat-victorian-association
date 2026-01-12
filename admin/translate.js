// Translation functionality for admin forms
// Uses MyMemory Translation API (free tier)

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

    // Show loading state
    const originalValue = targetField.value;
    const originalButtonText = buttonElement.innerHTML;
    targetField.value = 'Translating...';
    targetField.disabled = true;
    buttonElement.disabled = true;
    buttonElement.innerHTML = '⏳ Translating...';

    try {
        // Using MyMemory Translation API (free, no API key needed for small requests)
        const response = await fetch(`https://api.mymemory.translated.net/get?q=${encodeURIComponent(text)}&langpair=en|fa`);
        const data = await response.json();

        if (data.responseStatus === 200 && data.responseData && data.responseData.translatedText) {
            targetField.value = data.responseData.translatedText;
            targetField.disabled = false;
            buttonElement.disabled = false;
            buttonElement.innerHTML = originalButtonText;
            
            // Show success message
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
    // Remove existing message if any
    const existing = targetField.parentNode.querySelector('.translation-message');
    if (existing) {
        existing.remove();
    }

    // Create message element
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

    // Insert after the target field
    if (targetField && targetField.parentNode) {
        targetField.parentNode.insertBefore(messageEl, targetField.nextSibling);
        
        // Remove message after 3 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 3000);
    }
}

// Initialize translation buttons when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Add translation buttons to all Farsi fields
    const farsiFields = document.querySelectorAll('input[name$="_fa"], textarea[name$="_fa"]');
    
    farsiFields.forEach(field => {
        // Ensure field has an ID
        if (!field.id) {
            field.id = field.name;
        }
        
        // Skip if button already exists
        if (field.parentNode.querySelector('.btn-translate')) {
            return;
        }
        
        // Create translate button
        const translateBtn = document.createElement('button');
        translateBtn.type = 'button';
        translateBtn.className = 'btn-translate';
        translateBtn.innerHTML = '🌐 Translate to Farsi';
        translateBtn.onclick = function() {
            // Find corresponding English field
            const englishFieldName = field.name.replace('_fa', '');
            const englishField = document.querySelector(`input[name="${englishFieldName}"], textarea[name="${englishFieldName}"]`);
            
            if (englishField && englishField.value && englishField.value.trim() !== '') {
                translateToFarsi(englishField.value, field.id, translateBtn);
            } else {
                alert('Please enter text in the English field first');
            }
        };
        
        // Insert button after the field
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            const buttonContainer = document.createElement('div');
            buttonContainer.className = 'translate-button-container';
            buttonContainer.appendChild(translateBtn);
            formGroup.appendChild(buttonContainer);
        }
    });
});


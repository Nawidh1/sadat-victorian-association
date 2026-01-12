// Date converter for Gregorian to Jalali (Persian) calendar
// This script converts dates to Persian calendar when language is Farsi

(function() {
    'use strict';
    
    // Persian month names
    const persianMonths = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];
    
    // English month abbreviations
    const englishMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const englishMonthsFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Convert Gregorian date to Jalali (Persian) date
    function gregorianToJalali(gy, gm, gd) {
        const g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
        let jy = (gy <= 1600) ? 0 : 979;
        let gy2 = (gy > 1600) ? gy - 1600 : gy - 621;
        let days = (365 * gy2) + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400) - 80 + gd + g_d_m[gm - 1];
        
        jy += 33 * Math.floor(days / 12053);
        days %= 12053;
        jy += 4 * Math.floor(days / 1461);
        days %= 1461;
        
        if (days > 365) {
            jy += Math.floor((days - 1) / 365);
            days = (days - 1) % 365;
        }
        
        let jm = (days < 186) ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
        let jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
        
        return [jy, jm, jd];
    }
    
    // Parse date string and convert to Jalali
    function convertToJalali(dateString) {
        if (!dateString) return '';
        
        // Try to parse different date formats
        let date;
        if (dateString.includes('-')) {
            // Format: YYYY-MM-DD
            date = new Date(dateString);
        } else if (dateString.match(/\d{1,2}\s+\w+\s+\d{4}/)) {
            // Format: "15 Dec 2024"
            date = new Date(dateString);
        } else {
            date = new Date(dateString);
        }
        
        if (isNaN(date.getTime())) return dateString;
        
        const gy = date.getFullYear();
        const gm = date.getMonth() + 1;
        const gd = date.getDate();
        
        const [jy, jm, jd] = gregorianToJalali(gy, gm, gd);
        
        return `${jd} ${persianMonths[jm - 1]} ${jy}`;
    }
    
    // Convert English month abbreviation to Persian
    function convertMonthToPersian(monthText) {
        const index = englishMonths.indexOf(monthText);
        if (index !== -1) {
            return persianMonths[index];
        }
        // Try full month names
        const fullIndex = englishMonthsFull.findIndex(m => m.toLowerCase().startsWith(monthText.toLowerCase()));
        if (fullIndex !== -1) {
            return persianMonths[fullIndex];
        }
        return monthText;
    }
    
    // Format date string (e.g., "15 Dec 2024") to Persian
    function formatDateToPersian(dateString) {
        if (!dateString) return '';
        
        // Match format like "15 Dec 2024" or "15 December 2024"
        const match = dateString.match(/(\d{1,2})\s+(\w+)\s+(\d{4})/);
        if (match) {
            const day = parseInt(match[1]);
            const month = match[2];
            const year = parseInt(match[3]);
            
            // Find month index
            let monthIndex = englishMonths.indexOf(month);
            if (monthIndex === -1) {
                // Try full month names
                const fullMonthIndex = englishMonthsFull.findIndex(m => 
                    m.toLowerCase().startsWith(month.toLowerCase())
                );
                if (fullMonthIndex !== -1) {
                    monthIndex = fullMonthIndex;
                }
            }
            
            if (monthIndex !== -1) {
                // Convert to Jalali
                const date = new Date(year, monthIndex, day);
                if (!isNaN(date.getTime())) {
                    const gy = date.getFullYear();
                    const gm = date.getMonth() + 1;
                    const gd = date.getDate();
                    const [jy, jm, jd] = gregorianToJalali(gy, gm, gd);
                    return `${jd} ${persianMonths[jm - 1]} ${jy}`;
                }
            }
        }
        
        // Try to parse as ISO date or other formats
        try {
            const date = new Date(dateString);
            if (!isNaN(date.getTime())) {
                const gy = date.getFullYear();
                const gm = date.getMonth() + 1;
                const gd = date.getDate();
                const [jy, jm, jd] = gregorianToJalali(gy, gm, gd);
                return `${jd} ${persianMonths[jm - 1]} ${jy}`;
            }
        } catch (e) {
            // If parsing fails, return original
        }
        
        return dateString;
    }
    
    // Update all dates on the page
    function updateDates(isFarsi) {
        // Update news-date elements
        const newsDates = document.querySelectorAll('.news-date');
        newsDates.forEach(el => {
            const originalDate = el.getAttribute('data-original-date') || el.textContent.trim();
            if (!el.getAttribute('data-original-date')) {
                el.setAttribute('data-original-date', originalDate);
            }
            
            if (isFarsi) {
                el.textContent = formatDateToPersian(originalDate);
            } else {
                el.textContent = originalDate;
            }
        });
        
        // Update event-date elements (with day and month spans)
        const eventDates = document.querySelectorAll('.event-date');
        eventDates.forEach(el => {
            const daySpan = el.querySelector('.day');
            const monthSpan = el.querySelector('.month');
            
            if (daySpan && monthSpan) {
                const dayText = daySpan.textContent.trim();
                const monthText = monthSpan.textContent.trim();
                
                // Skip if it's "Every" or already translated day names
                if (dayText === 'Every' || dayText === 'هر' || 
                    monthText === 'Friday' || monthText === 'Sunday' || 
                    monthText === 'جمعه' || monthText === 'یکشنبه' ||
                    // Skip if month is already in Persian
                    persianMonths.includes(monthText)) {
                    return;
                }
                
                // Store original values
                if (!daySpan.getAttribute('data-original-day')) {
                    daySpan.setAttribute('data-original-day', dayText);
                    monthSpan.setAttribute('data-original-month', monthText);
                }
                
                if (isFarsi) {
                    // Try to convert the date
                    const originalDay = daySpan.getAttribute('data-original-day');
                    const originalMonth = monthSpan.getAttribute('data-original-month');
                    
                    // Get current year (or use 2024 as default)
                    const currentYear = new Date().getFullYear();
                    let monthIndex = englishMonths.indexOf(originalMonth);
                    
                    // Try full month names if abbreviation not found
                    if (monthIndex === -1) {
                        const fullMonthIndex = englishMonthsFull.findIndex(m => 
                            m.toLowerCase().startsWith(originalMonth.toLowerCase())
                        );
                        if (fullMonthIndex !== -1) {
                            monthIndex = fullMonthIndex;
                        }
                    }
                    
                    if (monthIndex !== -1 && !isNaN(parseInt(originalDay))) {
                        const date = new Date(currentYear, monthIndex, parseInt(originalDay));
                        if (!isNaN(date.getTime())) {
                            const [jy, jm, jd] = gregorianToJalali(date.getFullYear(), date.getMonth() + 1, date.getDate());
                            daySpan.textContent = jd.toString();
                            monthSpan.textContent = persianMonths[jm - 1];
                        }
                    }
                } else {
                    // Restore original English values
                    const originalDay = daySpan.getAttribute('data-original-day');
                    const originalMonth = daySpan.getAttribute('data-original-month');
                    if (originalDay && originalMonth) {
                        daySpan.textContent = originalDay;
                        monthSpan.textContent = originalMonth;
                    }
                }
            }
        });
    }
    
    // Get current language
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const lang = getCurrentLanguage();
        updateDates(lang === 'fa');
    });
    
    // Listen for language changes
    window.addEventListener('languageChanged', function(event) {
        const lang = event.detail ? event.detail.lang : getCurrentLanguage();
        updateDates(lang === 'fa');
    });
    
    // Make functions available globally
    window.convertToJalali = convertToJalali;
    window.formatDateToPersian = formatDateToPersian;
    window.updateDates = updateDates;
})();


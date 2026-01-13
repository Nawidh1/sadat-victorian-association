// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mainNav.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        });
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Contact Form Handling
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            // Simple validation
            if (!data.name || !data.email || !data.subject || !data.message) {
                alert('Please fill in all required fields.');
                return;
            }

            // Here you would typically send the data to a server
            // For now, we'll just show a success message
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });
    }

    // Add active class to current page link
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.main-nav a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage || (currentPage === '' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });

    // Fade in animation on scroll - optimized for faster loading
    const observerOptions = {
        threshold: 0.05,
        rootMargin: '0px 0px 100px 0px' // Start animations earlier
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                entry.target.style.willChange = 'auto'; // Remove will-change after animation
            }
        });
    }, observerOptions);

    // Observe elements for fade-in animation with staggered effect
    const animatedElements = document.querySelectorAll(
        '.feature-card, .news-card, .event-card, .resource-card, .program-card, ' +
        '.value-item, .calendar-item, .reading-item, .service-card, .contact-item'
    );
    
    animatedElements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)'; // Reduced from 30px
        el.style.willChange = 'opacity, transform'; // Optimize for animation
        el.style.transition = `opacity 0.4s ease ${index * 0.05}s, transform 0.4s ease ${index * 0.05}s`; // Faster: 0.8s -> 0.4s, delay 0.1s -> 0.05s
        observer.observe(el);
    });
    
    // Enhanced scroll animation for page headers
    const pageHeaders = document.querySelectorAll('.page-header');
    pageHeaders.forEach(header => {
        const headerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, { threshold: 0.1 });
        headerObserver.observe(header);
    });
    
    // Add hover ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function(e) {
            const ripple = document.createElement('span');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.left = (e.offsetX - 10) + 'px';
            ripple.style.top = (e.offsetY - 10) + 'px';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});

// ========== DATE CONVERTER (Gregorian to Jalali/Persian) ==========
(function() {
    'use strict';
    
    const persianMonths = [
        'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
        'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
    ];
    
    const englishMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const englishMonthsFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    
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
    
    function formatDateToPersian(dateString) {
        if (!dateString) return '';
        
        const match = dateString.match(/(\d{1,2})\s+(\w+)\s+(\d{4})/);
        if (match) {
            const day = parseInt(match[1]);
            const month = match[2];
            const year = parseInt(match[3]);
            
            let monthIndex = englishMonths.indexOf(month);
            if (monthIndex === -1) {
                const fullMonthIndex = englishMonthsFull.findIndex(m => 
                    m.toLowerCase().startsWith(month.toLowerCase())
                );
                if (fullMonthIndex !== -1) {
                    monthIndex = fullMonthIndex;
                }
            }
            
            if (monthIndex !== -1) {
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
        
        try {
            const date = new Date(dateString);
            if (!isNaN(date.getTime())) {
                const gy = date.getFullYear();
                const gm = date.getMonth() + 1;
                const gd = date.getDate();
                const [jy, jm, jd] = gregorianToJalali(gy, gm, gd);
                return `${jd} ${persianMonths[jm - 1]} ${jy}`;
            }
        } catch (e) {}
        
        return dateString;
    }
    
    function updateDates(isFarsi) {
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
        
        const eventDates = document.querySelectorAll('.event-date');
        eventDates.forEach(el => {
            const daySpan = el.querySelector('.day');
            const monthSpan = el.querySelector('.month');
            
            if (daySpan && monthSpan) {
                const dayText = daySpan.textContent.trim();
                const monthText = monthSpan.textContent.trim();
                
                if (dayText === 'Every' || dayText === 'هر' || 
                    monthText === 'Friday' || monthText === 'Sunday' || 
                    monthText === 'جمعه' || monthText === 'یکشنبه' ||
                    persianMonths.includes(monthText)) {
                    return;
                }
                
                if (!daySpan.getAttribute('data-original-day')) {
                    daySpan.setAttribute('data-original-day', dayText);
                    monthSpan.setAttribute('data-original-month', monthText);
                }
                
                if (isFarsi) {
                    const originalDay = daySpan.getAttribute('data-original-day');
                    const originalMonth = monthSpan.getAttribute('data-original-month');
                    const currentYear = new Date().getFullYear();
                    let monthIndex = englishMonths.indexOf(originalMonth);
                    
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
    
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const lang = getCurrentLanguage();
        updateDates(lang === 'fa');
    });
    
    window.addEventListener('languageChanged', function(event) {
        const lang = event.detail ? event.detail.lang : getCurrentLanguage();
        updateDates(lang === 'fa');
    });
    
    window.convertToJalali = gregorianToJalali;
    window.formatDateToPersian = formatDateToPersian;
    window.updateDates = updateDates;
})();

// ========== QUOTES SYSTEM ==========
const quotes = [
    {
        text: "I leave behind me two weighty things: the Book of Allah and my Ahl al-Bayt. If you hold fast to them, you will never go astray.",
        textFa: "من دو چیز گرانبها را برای شما به جا می‌گذارم: کتاب الله و اهل بیتم. اگر به آن‌ها تمسک کنید، هرگز گمراه نخواهید شد.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "The best of people are those who are most beneficial to others.",
        textFa: "بهترین مردم کسانی هستند که برای دیگران سودمندترند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "Knowledge is a treasure, but practice is the key to it.",
        textFa: "دانش گنج است، اما عمل کلید آن است.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "The strongest person is the one who controls his anger.",
        textFa: "قوی‌ترین شخص کسی است که خشم خود را کنترل می‌کند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "Do not be a slave to others when Allah has created you free.",
        textFa: "وقتی الله تو را آزاد آفریده است، برده دیگران مباش.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "The best form of worship is to wait for relief (from Allah).",
        textFa: "بهترین عبادت انتظار فرج (از الله) است.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "A person's true wealth is the good he does in this world.",
        textFa: "ثروت واقعی هر شخص نیکی‌ای است که در این دنیا انجام می‌دهد.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "The most complete of believers in faith are those with the best character.",
        textFa: "کامل‌ترین مؤمنان در ایمان کسانی هستند که بهترین اخلاق را دارند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "Patience is of two kinds: patience over what pains you, and patience against what you covet.",
        textFa: "صبر دو نوع است: صبر بر آنچه تو را می‌آزارد، و صبر در برابر آنچه آرزو می‌کنی.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "The best of deeds is that which is done consistently, even if it is small.",
        textFa: "بهترین اعمال آن است که به طور مداوم انجام شود، حتی اگر کوچک باشد.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "The value of each person lies in the good he does.",
        textFa: "ارزش هر شخص در نیکی‌ای است که انجام می‌دهد.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "Whoever does not thank people does not thank Allah.",
        textFa: "کسی که از مردم تشکر نمی‌کند، از الله تشکر نمی‌کند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "The best of you are those who are best to their families.",
        textFa: "بهترین شما کسانی هستند که با خانواده خود بهترین رفتار را دارند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    },
    {
        text: "Knowledge without action is like a tree without fruit.",
        textFa: "دانش بدون عمل مانند درختی بدون میوه است.",
        author: "— Imam Ali (AS)",
        authorFa: "— امام علی (ع)"
    },
    {
        text: "The most beloved of people to Allah are those who are most beneficial to people.",
        textFa: "محبوب‌ترین مردم نزد الله کسانی هستند که برای مردم سودمندترند.",
        author: "— Prophet Muhammad (PBUH)",
        authorFa: "— پیامبر محمد (ص)"
    }
];

let currentQuote = null;

function getRandomQuote(quotesArray) {
    if (!quotesArray || quotesArray.length === 0) {
        return quotes[Math.floor(Math.random() * quotes.length)];
    }
    const randomIndex = Math.floor(Math.random() * quotesArray.length);
    return quotesArray[randomIndex];
}

function displayQuote(quote) {
    const quoteText = document.getElementById('quoteText');
    const quoteAuthor = document.getElementById('quoteAuthor');
    
    if (quoteText && quoteAuthor && quote) {
        currentQuote = quote;
        const lang = localStorage.getItem('language') || 'en';
        
        if (lang === 'fa' && quote.textFa) {
            quoteText.textContent = `"${quote.textFa}"`;
            quoteAuthor.textContent = quote.authorFa || quote.author;
        } else {
            quoteText.textContent = `"${quote.text}"`;
            quoteAuthor.textContent = quote.author;
        }
        
        quoteText.style.opacity = '0';
        quoteAuthor.style.opacity = '0';
        quoteText.style.transition = 'opacity 0.5s ease';
        quoteAuthor.style.transition = 'opacity 0.5s ease';
        
        setTimeout(() => {
            quoteText.style.opacity = '1';
            quoteAuthor.style.opacity = '1';
        }, 100);
    }
}

function updateQuoteLanguage() {
    if (currentQuote) {
        displayQuote(currentQuote);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Use default quotes (quotes are now managed via admin panel database)
    displayQuote(getRandomQuote(quotes));
    
    window.addEventListener('languageChanged', function(event) {
        updateQuoteLanguage();
    });
});

// ========== ADMIN CONTENT UPDATES ==========
(function() {
    'use strict';
    
    function getCurrentLanguage() {
        return localStorage.getItem('language') || 'en';
    }
    
    function updateAdminContent() {
        const lang = getCurrentLanguage();
        const isFarsi = lang === 'fa';
        
        updateHomepageContent(isFarsi);
        updateNewsItems(isFarsi);
        updateEvents(isFarsi);
        updateResources(isFarsi);
        updateAboutValues(isFarsi);
    }
    
    function updateAboutValues(isFarsi) {
        const valueItems = document.querySelectorAll('.value-item');
        valueItems.forEach(valueItem => {
            const valueP = valueItem.querySelector('p');
            if (valueP) {
                const valueEn = valueItem.getAttribute('data-value-en');
                const valueFa = valueItem.getAttribute('data-value-fa');
                if (isFarsi && valueFa && valueFa.trim() !== '') {
                    valueP.textContent = valueFa;
                } else if (valueEn) {
                    valueP.textContent = valueEn;
                }
            }
        });
    }
    
    function updateHomepageContent(isFarsi) {
        const heroTitle = document.getElementById('hero-title');
        if (heroTitle) {
            const titleEn = heroTitle.getAttribute('data-title-en');
            const titleFa = heroTitle.getAttribute('data-title-fa');
            if (isFarsi && titleFa) {
                heroTitle.textContent = titleFa;
            } else if (!isFarsi && titleEn) {
                heroTitle.textContent = titleEn;
            }
        }
        
        const heroSubtitle = document.getElementById('hero-subtitle');
        if (heroSubtitle) {
            const subtitleEn = heroSubtitle.getAttribute('data-subtitle-en');
            const subtitleFa = heroSubtitle.getAttribute('data-subtitle-fa');
            if (isFarsi && subtitleFa) {
                heroSubtitle.textContent = subtitleFa;
            } else if (!isFarsi && subtitleEn) {
                heroSubtitle.textContent = subtitleEn;
            }
        }
        
        const learnMoreBtn = document.getElementById('learn-more-btn');
        if (learnMoreBtn) {
            const textEn = learnMoreBtn.getAttribute('data-text-en');
            const textFa = learnMoreBtn.getAttribute('data-text-fa');
            if (isFarsi && textFa) {
                learnMoreBtn.textContent = textFa;
            } else if (!isFarsi && textEn) {
                learnMoreBtn.textContent = textEn;
            }
        }
        
        const eventsBtn = document.getElementById('upcoming-events-btn');
        if (eventsBtn) {
            const textEn = eventsBtn.getAttribute('data-text-en');
            const textFa = eventsBtn.getAttribute('data-text-fa');
            if (isFarsi && textFa) {
                eventsBtn.textContent = textFa;
            } else if (!isFarsi && textEn) {
                eventsBtn.textContent = textEn;
            }
        }
        
        const missionTitle = document.getElementById('mission-title');
        if (missionTitle) {
            const titleEn = missionTitle.getAttribute('data-title-en');
            const titleFa = missionTitle.getAttribute('data-title-fa');
            if (isFarsi && titleFa) {
                missionTitle.textContent = titleFa;
            } else if (!isFarsi && titleEn) {
                missionTitle.textContent = titleEn;
            }
        }
        
        const missionSubtitle = document.getElementById('mission-subtitle');
        if (missionSubtitle) {
            const subtitleEn = missionSubtitle.getAttribute('data-subtitle-en');
            const subtitleFa = missionSubtitle.getAttribute('data-subtitle-fa');
            if (isFarsi && subtitleFa) {
                missionSubtitle.textContent = subtitleFa;
            } else if (!isFarsi && subtitleEn) {
                missionSubtitle.textContent = subtitleEn;
            }
        }
        
        const featureCards = document.querySelectorAll('.feature-card');
        featureCards.forEach((card) => {
            const titleEl = card.querySelector('h3');
            const descEl = card.querySelector('p');
            
            if (titleEl) {
                const titleEn = titleEl.getAttribute('data-title-en');
                const titleFa = titleEl.getAttribute('data-title-fa');
                if (isFarsi && titleFa) {
                    titleEl.textContent = titleFa;
                } else if (!isFarsi && titleEn) {
                    titleEl.textContent = titleEn;
                }
            }
            
            if (descEl) {
                const descEn = descEl.getAttribute('data-desc-en');
                const descFa = descEl.getAttribute('data-desc-fa');
                if (isFarsi && descFa) {
                    descEl.textContent = descFa;
                } else if (!isFarsi && descEn) {
                    descEl.textContent = descEn;
                }
            }
        });
    }
    
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
                const newLocation = isFarsi && card.dataset.locationFa ? card.dataset.locationFa : card.dataset.location;
                locationEl.textContent = '📍 ' + newLocation;
            }
        });
    }
    
    function updateResources(isFarsi) {
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
    
    window.updateAdminContent = updateAdminContent;
    
    document.addEventListener('DOMContentLoaded', function() {
        updateAdminContent();
        window.addEventListener('languageChanged', function() {
            updateAdminContent();
        });
    });
})();

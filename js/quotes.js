// Dynamic Quotes System - Fetches from JSON file or uses fallback
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

// Function to get a random quote
function getRandomQuote(quotesArray) {
    if (!quotesArray || quotesArray.length === 0) {
        return quotes[Math.floor(Math.random() * quotes.length)];
    }
    const randomIndex = Math.floor(Math.random() * quotesArray.length);
    return quotesArray[randomIndex];
}

// Store current quote globally
let currentQuote = null;

// Function to get current language
function getCurrentLanguage() {
    return localStorage.getItem('language') || 'en';
}

// Function to display quote
function displayQuote(quote) {
    const quoteText = document.getElementById('quoteText');
    const quoteAuthor = document.getElementById('quoteAuthor');
    
    if (quoteText && quoteAuthor && quote) {
        currentQuote = quote; // Store the quote
        
        const lang = getCurrentLanguage();
        
        // Display quote in current language
        if (lang === 'fa' && quote.textFa) {
            quoteText.textContent = `"${quote.textFa}"`;
            quoteAuthor.textContent = quote.authorFa || quote.author;
        } else {
            quoteText.textContent = `"${quote.text}"`;
            quoteAuthor.textContent = quote.author;
        }
        
        // Add fade-in animation
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

// Function to update quote language
function updateQuoteLanguage() {
    if (currentQuote) {
        displayQuote(currentQuote);
    }
}

// Try to fetch quotes from JSON file, fallback to default quotes
document.addEventListener('DOMContentLoaded', function() {
    // Try to fetch from data/quotes.json (can be managed via admin panel)
    fetch('data/quotes.json')
        .then(response => {
            if (response.ok) {
                return response.json();
            }
            throw new Error('Quotes file not found');
        })
        .then(data => {
            // If quotes array exists in JSON, use it
            const quotesArray = Array.isArray(data) ? data : (data.quotes || []);
            if (quotesArray.length > 0) {
                const randomQuote = getRandomQuote(quotesArray);
                displayQuote(randomQuote);
            } else {
                // Fallback to default quotes
                const randomQuote = getRandomQuote(quotes);
                displayQuote(randomQuote);
            }
        })
        .catch(error => {
            // If fetch fails, use default quotes
            console.log('Using default quotes:', error.message);
            const randomQuote = getRandomQuote(quotes);
            displayQuote(randomQuote);
        });
    
    // Listen for language changes
    window.addEventListener('languageChanged', function(event) {
        updateQuoteLanguage();
    });
});

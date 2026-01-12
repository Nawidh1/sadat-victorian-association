# Sadat Victorian Association Website

A bilingual (English/Farsi) website for the Sadat Victorian Association, built with PHP, MySQL, and modern web technologies.

## Features

- 🌐 **Bilingual Support**: Full English and Farsi (Persian) language support with RTL layout
- 📅 **Event Management**: Admin panel for managing events with featured events
- 📰 **News System**: Dynamic news management with bilingual content
- 📚 **Resources**: Educational resources, prayers, important dates, and recommended reading
- 💬 **Quotes**: Inspirational quotes management
- 📞 **Contact Form**: Contact page with form handling
- 🎨 **Modern UI**: Beautiful, responsive design with smooth animations

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (XAMPP recommended)
- Modern web browser

## Installation

1. Clone this repository:
```bash
git clone <your-repo-url>
cd information
```

2. Set up the database:
   - Create a MySQL database (e.g., `information_db`)
   - Import the schema from `config/schema.sql`

3. Configure database connection:
   - Copy `config/database.php.example` to `config/database.php`
   - Update database credentials in `config/database.php`

4. Set up web server:
   - Place files in your web server directory (e.g., `htdocs/information` for XAMPP)
   - Ensure Apache mod_rewrite is enabled

5. Access the website:
   - Frontend: `http://localhost/information`
   - Admin: `http://localhost/information/admin/login.php`
   - Default admin credentials: (set up in database)

## Project Structure

```
information/
├── admin/              # Admin panel files
├── api/                # API endpoints
├── config/             # Configuration files
├── css/                # Stylesheets
├── includes/           # Header and footer includes
├── js/                 # JavaScript files
└── index.php           # Main homepage
```

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Libraries**: PDO for database operations

## Features in Detail

### Admin Panel
- Dashboard with statistics
- Event management (CRUD operations)
- News management
- Resource management
- Quote management
- Homepage content editing
- About page editing
- Contact information management

### Frontend Features
- Responsive design
- Language toggle (English/Farsi)
- Event calendar
- News feed
- Resource library
- Contact form
- Islamic date converter

## License

This project is proprietary software for the Sadat Victorian Association.

## Support

For support, please contact the website administrator.

# HR Computer - Educational Institute Website

A professional PHP-based website for HR Computer educational institute, featuring responsive design, admin dashboard, and admission management system.

## Features

- Responsive design using Bootstrap 5
- Admin dashboard for managing admissions
- Admission form with database storage
- Gallery with image filtering
- Contact form with Google Maps integration
- About page with institute information
- Secure admin authentication
- Export functionality (CSV/PDF)

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (for PDF generation)

## Installation

1. Clone the repository to your web server directory:
```bash
git clone https://github.com/yourusername/hr-computer.git
```

2. Create a MySQL database and import the database schema:
```bash
mysql -u root -p < database.sql
```

3. Install required dependencies using Composer:
```bash
composer require tecnickcom/tcpdf
```

4. Configure database connection:
   - Open `includes/db.php`
   - Update database credentials if needed

5. Set up the web server:
   - For Apache, ensure mod_rewrite is enabled
   - Point the document root to the project directory

6. Set proper permissions:
```bash
chmod 755 -R /path/to/hr-computer
chmod 777 -R /path/to/hr-computer/assets/images
```

## Default Admin Login

- Username: admin
- Password: admin123

**Important:** Change the default admin password after first login.

## Directory Structure

```
hr-computer/
├── admin/
│   ├── login.php
│   ├── dashboard.php
│   ├── download.php
│   └── logout.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── db.php
├── index.php
├── about.php
├── gallery.php
├── contact.php
├── admission.php
├── database.sql
└── README.md
```

## Security Features

- Password hashing using PHP's password_hash()
- PDO with prepared statements to prevent SQL injection
- Input validation and sanitization
- Session-based authentication
- XSS protection through output escaping

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@hrcomputer.edu or create an issue in the repository. 
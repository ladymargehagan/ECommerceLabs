# ECommerce Labs - Taste of Africa

An e-commerce web application built with PHP and MySQL for managing and purchasing African products.

## Features

- **User Management**: Registration, login, and role-based access (Admin/Customer)
- **Product Catalog**: Browse products with search, category, and brand filters
- **Shopping Cart**: Add, update, and remove items from cart
- **Checkout**: Process orders and payments
- **Admin Dashboard**: Manage products, categories, and brands
- **Customer Dashboard**: View account information and order history

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB 10.4 or higher
- Apache/Nginx web server
- Web browser with JavaScript enabled

## Installation

1. **Clone or download the project** to your web server directory

2. **Configure Database**:
   - Create a MySQL database
   - Import the database schema from `db/dbforlab.sql`
   - Update database credentials in `settings/db_cred.php`:
     ```php
     define("SERVER", "localhost");
     define("USERNAME", "your_username");
     define("PASSWD", "your_password");
     define("DATABASE", "your_database_name");
     ```

3. **Set Permissions**:
   - Ensure the `uploads/` directory is writable for product image uploads

4. **Access the Application**:
   - Navigate to `index.php` in your web browser
   - Register a new account or use existing credentials

## Project Structure

```
ECommerceLabs/
├── actions/          # Action handlers for AJAX requests
├── admin/            # Admin dashboard pages
├── classes/          # Business logic classes
├── controllers/      # Controller classes
├── css/              # Stylesheets
├── customer/         # Customer dashboard pages
├── db/               # Database schema file
├── js/               # JavaScript files
├── login/            # Authentication pages
├── settings/         # Configuration files
└── uploads/          # Product image uploads
```

## Key Technologies

- **Backend**: PHP (Object-oriented)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Bootstrap 5.3
- **JavaScript**: jQuery 3.6, SweetAlert2
- **Icons**: Font Awesome 6.4

## Usage

1. **As a Customer**:
   - Browse products on the homepage
   - Use search and filters to find products
   - Add products to cart
   - Proceed to checkout

2. **As an Admin**:
   - Access admin dashboard after login
   - Manage categories, brands, and products
   - View and manage orders

## Notes

- Ensure error reporting is disabled in production (`settings/core.php`)
- Product images should be uploaded to the `uploads/` directory
- Session management is handled via PHP sessions


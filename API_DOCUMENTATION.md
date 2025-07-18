# D-Proxy API Documentation

## Overview

D-Proxy is a web-based interface for managing Squid proxy servers. It provides user authentication, proxy configuration management, traffic monitoring, and administrative controls.

![App Screenshot](https://i.ibb.co/nP74zrY/Picture1.png)

## Table of Contents

1. [Authentication APIs](#authentication-apis)
2. [Admin Panel APIs](#admin-panel-apis)
3. [Proxy Management APIs](#proxy-management-apis)
4. [Logging and Monitoring APIs](#logging-and-monitoring-apis)
5. [Configuration Components](#configuration-components)
6. [Frontend Components](#frontend-components)
7. [Database Schema](#database-schema)
8. [System Requirements](#system-requirements)

---

## Authentication APIs

### User Registration

**Endpoint:** `Auth/register.php`  
**Method:** POST  
**Description:** Register a new user account with email validation.

#### Parameters
- `useremail` (string, required): User email address
- `username` (string, required): Username (3+ characters)
- `password` (string, required): Password (6+ characters)
- `confirm_password` (string, required): Password confirmation

#### Example Request
```html
<form action="Auth/register.php" method="POST">
    <input type="email" name="useremail" required>
    <input type="text" name="username" required>
    <input type="password" name="password" required>
    <input type="password" name="confirm_password" required>
    <button type="submit">Register</button>
</form>
```

#### Response
- **Success:** Redirects to login page with success message
- **Error:** Returns form with validation errors

#### Validation Rules
- Email must be valid format and unique
- Username must be 3+ characters and unique
- Password must be 6+ characters
- Passwords must match

---

### User Login

**Endpoint:** `Auth/login.php`  
**Method:** POST  
**Description:** Authenticate user and create session.

#### Parameters
- `username` (string, required): Username
- `password` (string, required): Password

#### Example Request
```html
<form action="Auth/login.php" method="POST">
    <input type="text" name="username" value="Henry">
    <input type="password" name="password" value="123456">
    <button type="submit">Log In</button>
</form>
```

#### Response
- **Success:** Redirects to admin dashboard
- **Error:** Returns login form with error messages

#### Session Variables Created
```php
$_SESSION["loggedin"] = true;
$_SESSION["id"] = $user_id;
$_SESSION["username"] = $username;
```

---

### Password Recovery

**Endpoint:** `Auth/recoverpw.php`  
**Method:** POST  
**Description:** Send password reset email to user.

#### Parameters
- `useremail` (string, required): User email address

#### Example Usage
```html
<form action="Auth/recoverpw.php" method="POST">
    <input type="email" name="useremail" required>
    <button type="submit" name="submit">Send Reset Link</button>
</form>
```

#### Email Configuration Required
```php
$gmailid = 'your-email@gmail.com';
$gmailpassword = 'your-app-password';
$gmailusername = 'Your Name';
```

---

## Admin Panel APIs

### Dashboard

**Endpoint:** `Admin/index.php`  
**Method:** GET  
**Description:** Main admin dashboard with system overview.

#### Features
- System status monitoring
- Quick navigation to all features
- User session management

#### Access Control
```php
// Session validation required
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth-login.php");
    exit;
}
```

---

### Proxy Service Control

#### Start Proxy Service
**Endpoint:** `Admin/startCommand.php`  
**Method:** POST  
**Description:** Start the Squid proxy service.

```php
// Usage
$output = shell_exec('sudo systemctl start squid');
```

#### Stop Proxy Service
**Endpoint:** `Admin/stopCommand.php`  
**Method:** POST  
**Description:** Stop the Squid proxy service.

```php
// Usage
$output = shell_exec('sudo systemctl stop squid');
```

#### Restart Proxy Service
**Endpoint:** `Admin/restartCommand.php`  
**Method:** POST  
**Description:** Restart the Squid proxy service.

```php
// Usage
$output = shell_exec('sudo systemctl restart squid');
```

---

## Proxy Management APIs

### IP Access Control

#### Read Allowed IPs
**Endpoint:** `Admin/readAllowIP.php`  
**Method:** GET  
**Description:** Retrieve list of allowed IP addresses.

#### Implementation
```php
<?php
$file_path = '/etc/squid/allowed_ips.txt';
if(file_exists($file_path)) {
    $content = file_get_contents($file_path);
    $content = nl2br($content);
    echo $content;
} else {
    echo "File không tồn tại.";
}
?>
```

#### Update Allowed IPs
**Endpoint:** `Admin/replaceAllowIP.php`  
**Method:** POST  
**Description:** Update the list of allowed IP addresses.

#### Parameters
- `editor` (string, required): HTML content with IP addresses

#### Example Usage
```javascript
// Frontend integration
fetch('Admin/replaceAllowIP.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
    body: 'editor=' + encodeURIComponent(ipListContent)
});
```

---

### URL Blocking Management

#### Read Blocked URLs
**Endpoint:** `Admin/readBlockURL.php`  
**Method:** GET  
**Description:** Retrieve list of blocked URLs.

#### Update Blocked URLs
**Endpoint:** `Admin/replaceBlockURL.php`  
**Method:** POST  
**Description:** Update the list of blocked URLs.

#### Parameters
- `editor` (string, required): HTML content with blocked URLs

#### File Location
```
/etc/squid/blocked_sites.txt
```

---

### Rules Management Interface

#### Allow Clients Rules
**Endpoint:** `Admin/rules-allow-clients.php`  
**Method:** GET  
**Description:** Interface for managing allowed client IP addresses.

#### Features
- Add/Edit/Delete IP addresses
- One IP per line format
- Real-time validation
- Visual feedback

#### Block URL Rules
**Endpoint:** `Admin/rules-blockurl.php`  
**Method:** GET  
**Description:** Interface for managing blocked URLs.

#### Features
- Add/Edit/Delete URLs
- One URL per line format
- Pattern matching support
- Wildcard support

---

## Logging and Monitoring APIs

### Access Denied Logs

**Endpoint:** `Admin/read-log/readDenied.php`  
**Method:** POST  
**Description:** Retrieve access denied logs from proxy.

#### Implementation
```php
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $output = shell_exec('python /root/log.py -d');
    $output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
    $output = nl2br($output);
}
echo $output;
?>
```

### Bandwidth Monitoring

#### Per-IP Bandwidth
**Endpoint:** `Admin/read-log/readTotalBandwidthPerIP.php`  
**Method:** POST  
**Description:** Get bandwidth usage statistics per IP address.

#### Per-Website Bandwidth  
**Endpoint:** `Admin/read-log/readTotalBandwidthPerWeb.php`  
**Method:** POST  
**Description:** Get bandwidth usage statistics per website.

#### Log Processing Script
```bash
# External Python script dependency
python /root/log.py -d    # Denied access logs
python /root/log.py -b    # Bandwidth logs
```

---

## Configuration Components

### Database Configuration

**File:** `Admin/layouts/config.php`

#### Database Connection
```php
define('DB_SERVER', 'your-server.com');
define('DB_USERNAME', 'username');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'DProxy');
define('DB_PORT', '3306');

$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
```

#### Email Configuration
```php
$gmailid = 'your-email@gmail.com';
$gmailpassword = 'your-app-password';
$gmailusername = 'Your Display Name';
```

### Session Management

**File:** `Admin/layouts/session.php`

#### Session Validation
```php
<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth-login.php");
    exit;
}
?>
```

#### Usage in Protected Pages
```php
<?php include 'layouts/session.php'; ?>
// Page content here - user is guaranteed to be authenticated
```

---

## Frontend Components

### Layout Components

#### Header Component
**File:** `Admin/layouts/head.php`
- Meta tags configuration
- CSS includes
- Responsive viewport settings

#### Navigation Menu
**File:** `Admin/layouts/vertical-menu.php`
- Sidebar navigation
- User profile section
- Feature access links
- Logout functionality

#### Footer Component
**File:** `Admin/layouts/footer.php`
- Copyright information
- Additional links
- Version information

### JavaScript Integration

#### AJAX Language Loading
```javascript
// From app.js
$.getJSON('assets/lang/' + language + '.json', function (lang) {
    // Load language-specific text
});
```

#### Form Validation
```javascript
// Bootstrap validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
```

### Asset Management

#### CSS Libraries
- Bootstrap 5
- FontAwesome icons
- Custom theme styles
- Chart.js for analytics

#### JavaScript Libraries
- jQuery
- Bootstrap JS
- ECharts for visualization
- CKEditor for rich text
- Dropzone for file uploads

---

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    useremail VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    token VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Example User Creation
```php
$sql = "INSERT INTO users (username, useremail, password, token) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "ssss", $username, $useremail, $hashed_password, $token);
```

---

## System Requirements

### Server Requirements
- **OS:** Linux (Ubuntu/CentOS recommended)
- **Web Server:** Apache/Nginx with PHP support
- **PHP:** Version 7.4 or higher
- **Database:** MySQL/MariaDB
- **Proxy:** Squid proxy server

### PHP Extensions Required
- mysqli (for database connectivity)
- session (for user sessions)
- filter (for email validation)
- json (for API responses)

### File System Permissions
```bash
# Squid configuration files
/etc/squid/allowed_ips.txt (read/write)
/etc/squid/blocked_sites.txt (read/write)

# Service control (requires sudo)
systemctl start/stop/restart squid
```

### Installation Steps

1. **Install Dependencies**
```bash
sudo apt update
sudo apt install apache2 php mysql-server squid
```

2. **Configure Database**
```bash
mysql -u root -p
CREATE DATABASE DProxy;
CREATE USER 'dproxy'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON DProxy.* TO 'dproxy'@'localhost';
```

3. **Set Up File Permissions**
```bash
sudo chown www-data:www-data /etc/squid/allowed_ips.txt
sudo chown www-data:www-data /etc/squid/blocked_sites.txt
sudo chmod 664 /etc/squid/allowed_ips.txt
sudo chmod 664 /etc/squid/blocked_sites.txt
```

4. **Configure Sudoers (for service control)**
```bash
# Add to /etc/sudoers
www-data ALL=NOPASSWD: /bin/systemctl start squid
www-data ALL=NOPASSWD: /bin/systemctl stop squid
www-data ALL=NOPASSWD: /bin/systemctl restart squid
```

---

## Security Considerations

### Input Validation
- All user inputs are sanitized using `mysqli_real_escape_string()`
- Password hashing using PHP's `password_hash()`
- Email validation using `filter_var()`

### Session Security
- Session regeneration on login
- Secure session timeout
- CSRF protection on forms

### File Access Control
- Restricted file system access
- Proper file permissions
- Input sanitization for file operations

### SQL Injection Prevention
```php
// Always use prepared statements
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "s", $username);
```

---

## Troubleshooting

### Common Issues

#### Database Connection Failed
```php
// Check config.php settings
if ($link === false) {
    die("ERROR: Could not connect: " . mysqli_connect_error());
}
```

#### File Permission Errors
```bash
# Fix file permissions
sudo chown -R www-data:www-data /path/to/dproxy
sudo chmod -R 644 /path/to/dproxy
sudo chmod -R 755 /path/to/dproxy/directories
```

#### Service Control Issues
```bash
# Check sudo permissions
sudo -u www-data systemctl status squid
```

### Log Files
- **Apache Error Log:** `/var/log/apache2/error.log`
- **PHP Error Log:** `/var/log/php_errors.log`
- **Squid Access Log:** `/var/log/squid/access.log`
- **Squid Error Log:** `/var/log/squid/cache.log`

---

## API Response Formats

### Success Response
```json
{
    "status": "success",
    "message": "Operation completed successfully",
    "data": {
        // Response data
    }
}
```

### Error Response
```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": "Field-specific error message"
    }
}
```

---

## Version Information

- **Current Version:** 1.0
- **Last Updated:** 2024
- **Compatibility:** PHP 7.4+, MySQL 5.7+, Squid 3.5+

---

## Support and Contact

For technical support and bug reports, please refer to the project repository or contact the development team.

---

*This documentation covers all public APIs, functions, and components available in the D-Proxy application. For additional implementation details, refer to the source code and inline comments.*
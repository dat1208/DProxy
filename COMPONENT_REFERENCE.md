# D-Proxy Component Reference Guide

## Overview

This document provides detailed implementation reference for all components, functions, and utilities in the D-Proxy application. Use this alongside the main API documentation for comprehensive development guidance.

## Table of Contents

1. [Authentication Components](#authentication-components)
2. [Admin Layout Components](#admin-layout-components)
3. [File Management Utilities](#file-management-utilities)
4. [Service Control Functions](#service-control-functions)
5. [Logging Components](#logging-components)
6. [Frontend Utilities](#frontend-utilities)
7. [Configuration Helpers](#configuration-helpers)

---

## Authentication Components

### Session Manager

**File:** `Admin/layouts/session.php`

#### Purpose
Provides centralized session validation for all protected admin pages.

#### Implementation
```php
<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: auth-login.php");
    exit;
}
?>
```

#### Usage Pattern
```php
// Include at the top of any protected page
<?php include 'layouts/session.php'; ?>
// Rest of page content
```

#### Session Variables Available After Authentication
- `$_SESSION["loggedin"]` - Boolean authentication status
- `$_SESSION["id"]` - User ID from database
- `$_SESSION["username"]` - Username string

---

### Login Handler

**File:** `Auth/login.php`

#### Core Authentication Function
```php
function authenticateUser($username, $password, $mysqli_connection) {
    // Prepare SQL statement
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    
    if ($stmt = mysqli_prepare($mysqli_connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_store_result($stmt);
            
            // Check if username exists
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                
                if (mysqli_stmt_fetch($stmt)) {
                    if (password_verify($password, $hashed_password)) {
                        // Authentication successful
                        return [
                            'success' => true,
                            'user_id' => $id,
                            'username' => $username
                        ];
                    }
                }
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    return ['success' => false];
}
```

#### Session Creation Pattern
```php
// After successful authentication
session_start();
$_SESSION["loggedin"] = true;
$_SESSION["id"] = $user_id;
$_SESSION["username"] = $username;

// Redirect to dashboard
header("location: ../Admin/index.php");
exit;
```

---

### Registration Handler

**File:** `Auth/register.php`

#### User Creation Function
```php
function createUser($useremail, $username, $password, $mysqli_connection) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32)); // For password reset
    
    $sql = "INSERT INTO users (useremail, username, password, token) VALUES (?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($mysqli_connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $useremail, $username, $hashed_password, $token);
        
        if (mysqli_stmt_execute($stmt)) {
            return ['success' => true, 'user_id' => mysqli_insert_id($mysqli_connection)];
        } else {
            return ['success' => false, 'error' => mysqli_stmt_error($stmt)];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return ['success' => false, 'error' => 'Prepared statement failed'];
}
```

#### Validation Helpers
```php
function validateEmail($email, $mysqli_connection) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'error' => 'Invalid email format'];
    }
    
    // Check if email already exists
    $sql = "SELECT id FROM users WHERE useremail = ?";
    if ($stmt = mysqli_prepare($mysqli_connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            return ['valid' => false, 'error' => 'Email already exists'];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return ['valid' => true];
}

function validateUsername($username, $mysqli_connection) {
    if (strlen(trim($username)) < 3) {
        return ['valid' => false, 'error' => 'Username must be at least 3 characters'];
    }
    
    // Check if username already exists
    $sql = "SELECT id FROM users WHERE username = ?";
    if ($stmt = mysqli_prepare($mysqli_connection, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            return ['valid' => false, 'error' => 'Username already taken'];
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return ['valid' => true];
}

function validatePassword($password, $confirm_password) {
    if (strlen($password) < 6) {
        return ['valid' => false, 'error' => 'Password must be at least 6 characters'];
    }
    
    if ($password !== $confirm_password) {
        return ['valid' => false, 'error' => 'Passwords do not match'];
    }
    
    return ['valid' => true];
}
```

---

## Admin Layout Components

### Navigation Menu Component

**File:** `Admin/layouts/vertical-menu.php`

#### Menu Structure Implementation
```php
function renderNavigationMenu($current_page = '') {
    $menu_items = [
        'dashboard' => [
            'url' => 'index.php',
            'icon' => 'bx-home-circle',
            'title' => 'Dashboard',
            'active' => $current_page === 'dashboard'
        ],
        'proxy_list' => [
            'url' => 'proxy-list.php',
            'icon' => 'bx-list-ul',
            'title' => 'Proxy List',
            'active' => $current_page === 'proxy-list'
        ],
        'rules' => [
            'title' => 'Rules Management',
            'icon' => 'bx-cog',
            'submenu' => [
                'allow_clients' => [
                    'url' => 'rules-allow-clients.php',
                    'title' => 'Allow Clients',
                    'active' => $current_page === 'allow-clients'
                ],
                'block_urls' => [
                    'url' => 'rules-blockurl.php',
                    'title' => 'Block URLs',
                    'active' => $current_page === 'block-urls'
                ]
            ]
        ],
        'logs' => [
            'title' => 'Logs & Monitoring',
            'icon' => 'bx-chart',
            'submenu' => [
                'denied_access' => [
                    'url' => '#',
                    'title' => 'Denied Access',
                    'onclick' => 'loadDeniedLogs()'
                ],
                'bandwidth_ip' => [
                    'url' => '#',
                    'title' => 'Bandwidth per IP',
                    'onclick' => 'loadBandwidthByIP()'
                ],
                'bandwidth_web' => [
                    'url' => '#',
                    'title' => 'Bandwidth per Website',
                    'onclick' => 'loadBandwidthByWebsite()'
                ]
            ]
        ]
    ];
    
    // Render menu HTML
    echo '<div id="sidebar-menu">';
    echo '<ul class="metismenu list-unstyled" id="side-menu">';
    
    foreach ($menu_items as $key => $item) {
        if (isset($item['submenu'])) {
            renderSubmenu($item);
        } else {
            renderMenuItem($item);
        }
    }
    
    echo '</ul>';
    echo '</div>';
}

function renderMenuItem($item) {
    $active_class = $item['active'] ? 'mm-active' : '';
    echo '<li class="' . $active_class . '">';
    echo '<a href="' . $item['url'] . '" class="waves-effect">';
    echo '<i class="bx ' . $item['icon'] . '"></i>';
    echo '<span key="t-' . strtolower(str_replace(' ', '-', $item['title'])) . '">' . $item['title'] . '</span>';
    echo '</a>';
    echo '</li>';
}

function renderSubmenu($item) {
    echo '<li>';
    echo '<a href="javascript: void(0);" class="has-arrow waves-effect">';
    echo '<i class="bx ' . $item['icon'] . '"></i>';
    echo '<span key="t-' . strtolower(str_replace(' ', '-', $item['title'])) . '">' . $item['title'] . '</span>';
    echo '</a>';
    echo '<ul class="sub-menu" aria-expanded="false">';
    
    foreach ($item['submenu'] as $subitem) {
        echo '<li>';
        if (isset($subitem['onclick'])) {
            echo '<a href="#" onclick="' . $subitem['onclick'] . '">';
        } else {
            echo '<a href="' . $subitem['url'] . '">';
        }
        echo '<span key="t-' . strtolower(str_replace(' ', '-', $subitem['title'])) . '">' . $subitem['title'] . '</span>';
        echo '</a>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</li>';
}
```

---

### Header Component

**File:** `Admin/layouts/head.php`

#### Meta Tags Configuration
```php
function renderMetaTags($page_title = 'DProxy', $description = 'Squid Proxy Management Interface') {
    echo '<meta charset="utf-8" />';
    echo '<title>' . htmlspecialchars($page_title) . '</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<meta content="' . htmlspecialchars($description) . '" name="description" />';
    echo '<meta content="DProxy Team" name="author" />';
    echo '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
    
    // Favicon
    echo '<link rel="shortcut icon" href="assets/images/favicon.ico">';
}
```

#### CSS Includes Helper
```php
function includeCSSFiles($additional_css = []) {
    $core_css = [
        'assets/css/bootstrap.min.css',
        'assets/css/icons.min.css',
        'assets/css/app.min.css'
    ];
    
    $all_css = array_merge($core_css, $additional_css);
    
    foreach ($all_css as $css_file) {
        echo '<link href="' . $css_file . '" rel="stylesheet" type="text/css" />';
    }
}
```

---

## File Management Utilities

### IP Address Management

**File:** `Admin/readAllowIP.php`

#### Read Allowed IPs Function
```php
function readAllowedIPs($file_path = '/etc/squid/allowed_ips.txt') {
    try {
        if (!file_exists($file_path)) {
            throw new Exception('IP file does not exist');
        }
        
        if (!is_readable($file_path)) {
            throw new Exception('IP file is not readable');
        }
        
        $content = file_get_contents($file_path);
        
        if ($content === false) {
            throw new Exception('Failed to read IP file');
        }
        
        // Convert line breaks to HTML breaks for display
        $formatted_content = nl2br(htmlspecialchars($content));
        
        return [
            'success' => true,
            'content' => $formatted_content,
            'raw_content' => $content
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

#### Update Allowed IPs Function
```php
function updateAllowedIPs($editor_content, $file_path = '/etc/squid/allowed_ips.txt') {
    try {
        // Sanitize input - remove HTML tags and convert to plain text
        $clean_content = strip_tags($editor_content);
        $clean_content = str_replace(['<p>', '</p>', '<br>', '&nbsp;'], "\n", $clean_content);
        $clean_content = html_entity_decode($clean_content);
        
        // Validate IP addresses
        $lines = explode("\n", $clean_content);
        $valid_ips = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Validate IP address or CIDR notation
            if (filter_var($line, FILTER_VALIDATE_IP) || preg_match('/^(?:[0-9]{1,3}\.){3}[0-9]{1,3}\/[0-9]{1,2}$/', $line)) {
                $valid_ips[] = $line;
            } else {
                throw new Exception("Invalid IP address format: " . $line);
            }
        }
        
        $final_content = implode("\n", $valid_ips);
        
        // Create backup before writing
        if (file_exists($file_path)) {
            $backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
            copy($file_path, $backup_path);
        }
        
        // Write to file
        if (file_put_contents($file_path, $final_content) === false) {
            throw new Exception('Failed to write to IP file');
        }
        
        return [
            'success' => true,
            'message' => 'IP addresses updated successfully',
            'count' => count($valid_ips)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

---

### URL Blocking Management

**File:** `Admin/readBlockURL.php` & `Admin/replaceBlockURL.php`

#### URL Validation Function
```php
function validateURL($url) {
    // Remove protocol if present
    $url = preg_replace('#^https?://#', '', $url);
    
    // Check for wildcard patterns
    if (strpos($url, '*') !== false) {
        // Validate wildcard pattern
        return preg_match('/^[\w\-\.\*]+$/', $url);
    }
    
    // Validate domain name
    return filter_var('http://' . $url, FILTER_VALIDATE_URL) !== false;
}

function updateBlockedURLs($editor_content, $file_path = '/etc/squid/blocked_sites.txt') {
    try {
        $clean_content = strip_tags($editor_content);
        $clean_content = str_replace(['<p>', '</p>', '<br>', '&nbsp;'], "\n", $clean_content);
        $clean_content = html_entity_decode($clean_content);
        
        $lines = explode("\n", $clean_content);
        $valid_urls = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            if (validateURL($line)) {
                $valid_urls[] = $line;
            } else {
                throw new Exception("Invalid URL format: " . $line);
            }
        }
        
        $final_content = implode("\n", $valid_urls);
        
        // Create backup
        if (file_exists($file_path)) {
            $backup_path = $file_path . '.backup.' . date('Y-m-d-H-i-s');
            copy($file_path, $backup_path);
        }
        
        if (file_put_contents($file_path, $final_content) === false) {
            throw new Exception('Failed to write to URL file');
        }
        
        return [
            'success' => true,
            'message' => 'Blocked URLs updated successfully',
            'count' => count($valid_urls)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

---

## Service Control Functions

### Squid Service Manager

#### Service Control Helper
```php
function controlSquidService($action) {
    $allowed_actions = ['start', 'stop', 'restart', 'status'];
    
    if (!in_array($action, $allowed_actions)) {
        return [
            'success' => false,
            'error' => 'Invalid action specified'
        ];
    }
    
    try {
        $command = "sudo systemctl {$action} squid 2>&1";
        $output = shell_exec($command);
        $exit_code = shell_exec("echo $?");
        
        return [
            'success' => intval(trim($exit_code)) === 0,
            'output' => $output,
            'action' => $action
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getSquidStatus() {
    $result = controlSquidService('status');
    
    if ($result['success']) {
        $is_active = strpos($result['output'], 'active (running)') !== false;
        return [
            'success' => true,
            'is_running' => $is_active,
            'status_text' => $is_active ? 'Running' : 'Stopped',
            'detailed_status' => $result['output']
        ];
    }
    
    return $result;
}
```

#### Service Control Endpoints
```php
// Admin/startCommand.php
function startSquidService() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $result = controlSquidService('start');
        
        if ($result['success']) {
            $_SESSION['success_message'] = 'Squid service started successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to start Squid service: ' . $result['error'];
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Admin/stopCommand.php
function stopSquidService() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $result = controlSquidService('stop');
        
        if ($result['success']) {
            $_SESSION['success_message'] = 'Squid service stopped successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to stop Squid service: ' . $result['error'];
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Admin/restartCommand.php
function restartSquidService() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $result = controlSquidService('restart');
        
        if ($result['success']) {
            $_SESSION['success_message'] = 'Squid service restarted successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to restart Squid service: ' . $result['error'];
        }
        
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
```

---

## Logging Components

### Log Reading Utilities

#### Denied Access Log Reader
```php
function readDeniedLogs($script_path = '/root/log.py') {
    try {
        if (!file_exists($script_path)) {
            throw new Exception('Log processing script not found');
        }
        
        $command = "python {$script_path} -d 2>&1";
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new Exception('Failed to execute log script');
        }
        
        // Remove ANSI color codes
        $clean_output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
        
        // Convert line breaks to HTML
        $formatted_output = nl2br(htmlspecialchars($clean_output));
        
        return [
            'success' => true,
            'logs' => $formatted_output,
            'raw_logs' => $clean_output
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

#### Bandwidth Monitoring Functions
```php
function readBandwidthByIP($script_path = '/root/log.py') {
    try {
        $command = "python {$script_path} -b -i 2>&1";
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new Exception('Failed to execute bandwidth script');
        }
        
        $clean_output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
        $formatted_output = nl2br(htmlspecialchars($clean_output));
        
        // Parse output for JSON data if available
        $parsed_data = parseLogOutput($clean_output);
        
        return [
            'success' => true,
            'logs' => $formatted_output,
            'data' => $parsed_data
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function readBandwidthByWebsite($script_path = '/root/log.py') {
    try {
        $command = "python {$script_path} -b -w 2>&1";
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new Exception('Failed to execute bandwidth script');
        }
        
        $clean_output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
        $formatted_output = nl2br(htmlspecialchars($clean_output));
        
        $parsed_data = parseLogOutput($clean_output);
        
        return [
            'success' => true,
            'logs' => $formatted_output,
            'data' => $parsed_data
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function parseLogOutput($output) {
    // Try to extract structured data from log output
    $lines = explode("\n", $output);
    $data = [];
    
    foreach ($lines as $line) {
        // Look for patterns like "IP: 192.168.1.1 - Bandwidth: 1.2 MB"
        if (preg_match('/IP:\s*([^\s]+)\s*-\s*Bandwidth:\s*([^\s]+\s*[^\s]+)/', $line, $matches)) {
            $data[] = [
                'ip' => $matches[1],
                'bandwidth' => $matches[2]
            ];
        }
        // Look for patterns like "Website: example.com - Bandwidth: 5.6 GB"
        elseif (preg_match('/Website:\s*([^\s]+)\s*-\s*Bandwidth:\s*([^\s]+\s*[^\s]+)/', $line, $matches)) {
            $data[] = [
                'website' => $matches[1],
                'bandwidth' => $matches[2]
            ];
        }
    }
    
    return $data;
}
```

---

## Frontend Utilities

### AJAX Helper Functions

#### JavaScript API Interface
```javascript
// Frontend API helper class
class DProxyAPI {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
        this.csrf_token = this.getCSRFToken();
    }
    
    getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }
    
    async makeRequest(endpoint, method = 'GET', data = null) {
        const url = this.baseUrl + endpoint;
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (data) {
            if (method === 'GET') {
                url += '?' + new URLSearchParams(data);
            } else {
                options.body = new URLSearchParams(data);
            }
        }
        
        try {
            const response = await fetch(url, options);
            const result = await response.text();
            return {
                success: response.ok,
                data: result,
                status: response.status
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }
    
    // Service control methods
    async startService() {
        return await this.makeRequest('startCommand.php', 'POST');
    }
    
    async stopService() {
        return await this.makeRequest('stopCommand.php', 'POST');
    }
    
    async restartService() {
        return await this.makeRequest('restartCommand.php', 'POST');
    }
    
    // File management methods
    async readAllowedIPs() {
        return await this.makeRequest('readAllowIP.php');
    }
    
    async updateAllowedIPs(content) {
        return await this.makeRequest('replaceAllowIP.php', 'POST', {
            editor: content
        });
    }
    
    async readBlockedURLs() {
        return await this.makeRequest('readBlockURL.php');
    }
    
    async updateBlockedURLs(content) {
        return await this.makeRequest('replaceBlockURL.php', 'POST', {
            editor: content
        });
    }
    
    // Log reading methods
    async readDeniedLogs() {
        return await this.makeRequest('read-log/readDenied.php', 'POST');
    }
    
    async readBandwidthByIP() {
        return await this.makeRequest('read-log/readTotalBandwidthPerIP.php', 'POST');
    }
    
    async readBandwidthByWebsite() {
        return await this.makeRequest('read-log/readTotalBandwidthPerWeb.php', 'POST');
    }
}

// Initialize API instance
const api = new DProxyAPI();
```

#### Form Handling Utilities
```javascript
// Form validation and submission helper
class FormHandler {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        this.setupValidation();
    }
    
    setupValidation() {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => {
            if (!this.form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.form.classList.add('was-validated');
        });
    }
    
    showMessage(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = this.form.querySelector('.message-container') || this.form;
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    setFieldError(fieldName, message) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            let feedback = field.parentNode.querySelector('.invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                field.parentNode.appendChild(feedback);
            }
            feedback.textContent = message;
        }
    }
    
    clearErrors() {
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        const feedbacks = this.form.querySelectorAll('.invalid-feedback');
        feedbacks.forEach(feedback => {
            feedback.remove();
        });
    }
}
```

---

## Configuration Helpers

### Database Connection Manager

#### Connection Helper Class
```php
class DatabaseManager {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $database;
    private $port;
    
    public function __construct($host, $username, $password, $database, $port = 3306) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }
    
    public function connect() {
        try {
            $this->connection = new mysqli(
                $this->host, 
                $this->username, 
                $this->password, 
                $this->database, 
                $this->port
            );
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset
            $this->connection->set_charset("utf8");
            
            return true;
            
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getConnection() {
        if (!$this->connection) {
            $this->connect();
        }
        return $this->connection;
    }
    
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->connection->error);
            }
            
            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assume all strings for simplicity
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            return [
                'success' => true,
                'result' => $result,
                'affected_rows' => $this->connection->affected_rows,
                'insert_id' => $this->connection->insert_id
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}

// Usage example
$db = new DatabaseManager(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
if ($db->connect()) {
    $result = $db->query("SELECT * FROM users WHERE username = ?", [$username]);
    if ($result['success']) {
        // Process result
    }
}
```

### Email Configuration Manager

#### Email Service Class
```php
class EmailService {
    private $mailer;
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $from_email;
    private $from_name;
    
    public function __construct($config) {
        $this->smtp_host = $config['smtp_host'] ?? 'smtp.gmail.com';
        $this->smtp_port = $config['smtp_port'] ?? 587;
        $this->smtp_username = $config['smtp_username'];
        $this->smtp_password = $config['smtp_password'];
        $this->from_email = $config['from_email'];
        $this->from_name = $config['from_name'];
        
        $this->initializeMailer();
    }
    
    private function initializeMailer() {
        $this->mailer = new PHPMailer(true);
        
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->smtp_host;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->smtp_username;
            $this->mailer->Password = $this->smtp_password;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $this->smtp_port;
            
            // Default sender
            $this->mailer->setFrom($this->from_email, $this->from_name);
            
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }
    
    public function sendPasswordReset($to_email, $username, $reset_token, $base_url) {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to_email, $username);
            
            $this->mailer->Subject = 'Password Reset Request - DProxy';
            
            $reset_link = $base_url . "/auth-reset-password.php?token=" . $reset_token;
            
            $body = "
                <h2>Password Reset Request</h2>
                <p>Hello {$username},</p>
                <p>You have requested to reset your password for your DProxy account.</p>
                <p>Click the link below to reset your password:</p>
                <p><a href='{$reset_link}' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                <p>If you did not request this reset, please ignore this email.</p>
                <p>This link will expire in 24 hours.</p>
                <br>
                <p>Best regards,<br>DProxy Team</p>
            ";
            
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            
            $this->mailer->send();
            
            return [
                'success' => true,
                'message' => 'Password reset email sent successfully'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to send email: ' . $e->getMessage()
            ];
        }
    }
}

// Usage example
$email_config = [
    'smtp_username' => $gmailid,
    'smtp_password' => $gmailpassword,
    'from_email' => $gmailid,
    'from_name' => $gmailusername
];

$email_service = new EmailService($email_config);
$result = $email_service->sendPasswordReset($user_email, $username, $token, $base_url);
```

---

## Error Handling and Logging

### Error Handler Class
```php
class ErrorHandler {
    private static $log_file = '/var/log/dproxy/error.log';
    
    public static function logError($message, $context = []) {
        $timestamp = date('Y-m-d H:i:s');
        $context_str = !empty($context) ? json_encode($context) : '';
        $log_entry = "[{$timestamp}] ERROR: {$message} {$context_str}" . PHP_EOL;
        
        // Ensure log directory exists
        $log_dir = dirname(self::$log_file);
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    public static function handleException($exception) {
        self::logError($exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ]);
        
        // Show user-friendly error in production
        if (!defined('DEBUG') || !DEBUG) {
            $error_message = "An error occurred. Please try again later.";
        } else {
            $error_message = $exception->getMessage();
        }
        
        return $error_message;
    }
    
    public static function validateInput($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule_set) {
            $value = $data[$field] ?? null;
            
            foreach ($rule_set as $rule) {
                if ($rule === 'required' && empty($value)) {
                    $errors[$field] = "Field {$field} is required";
                    break;
                }
                
                if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Field {$field} must be a valid email";
                    break;
                }
                
                if (strpos($rule, 'min:') === 0) {
                    $min_length = intval(substr($rule, 4));
                    if (strlen($value) < $min_length) {
                        $errors[$field] = "Field {$field} must be at least {$min_length} characters";
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
}
```

---

This component reference guide provides detailed implementation examples and utility functions that complement the main API documentation. Use these patterns and functions as building blocks for extending the D-Proxy application functionality.
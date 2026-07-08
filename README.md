# 🛡️ CyberShield

**A Multi-Tool Security Web Application for Sri Lankan Internet Users**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-ff2d20?style=flat&logo=laravel)](https://laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38bdf8?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479a1?style=flat&logo=mysql)](https://mysql.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777bb4?style=flat&logo=php)](https://php.net)

---

## 📌 Overview

CyberShield is a free, all-in-one security toolkit designed to help Sri Lankan internet users protect themselves from online threats. It provides multiple security tools in one platform, making security accessible to everyone.

### 🎯 Why CyberShield?

| Problem | Solution |
|---------|----------|
| Phishing links are everywhere | URL Safety Checker with VirusTotal + Google Safe Browsing |
| Weak passwords are common | Password Strength Analyzer with breach detection |
| Unsecured websites are risky | SSL & Headers Checker with TLS handshake |
| QR code phishing is growing | QR Code Phishing Checker |
| Smishing scams are increasing | Smishing/Scam Analyzer |
| Students need cybersecurity tools | Dedicated Student dashboard with 7 educational tools |

---

## ✨ Features

### 👤 User Tools (7 Tools)
- **Password Analyzer** – Check strength, crack time, breach status
- **URL Safety Checker** – Detect phishing/malware with VirusTotal + Google Safe Browsing
- **SSL & Headers Checker** – TLS handshake, security headers, grade (A+ to F)
- **QR Code Phishing Checker** – Upload/paste QR codes to check safety
- **Smishing/Scam Analyzer** – Analyze messages for scam indicators
- **Metadata Remover** – Remove sensitive EXIF/GPS data from images

### 🎓 Student Tools (7 Tools)
- DNS Lookup – View A, MX, CNAME, NS, TXT records
- Whois Lookup – Domain registration details
- IP Reputation – Check malicious IPs via AbuseIPDB
- Metadata Extractor – Extract EXIF/document metadata
- Base64 & URL Encoder/Decoder – Encoding utilities
- Hash Tool – Educational hash generation

### 👑 Admin Features
- 📊 Dashboard Analytics – Charts and statistics
- 👥 User Management – View, block, delete users
- 📋 Scan Management – View, filter, delete scans
- 📝 Feedback Management – View and resolve user feedback
- 📄 CSV Export – Export users and scans
- 🩺 System Health – PHP, database, extensions status
- 📋 System Logs – View Laravel logs

---

## 🏗️ Architecture
cybershield/
├── app/
│   ├── Http/Controllers/     # All controllers
│   ├── Models/               # User, Scan models
│   ├── Services/             # API integrations
│   ├── DTOs/                 # Data Transfer Objects
│   └── Jobs/                 # Background jobs
├── resources/views/
│   ├── admin/                # Admin dashboard
│   ├── student/              # Student tools
│   ├── user/                 # User tools
│   ├── auth/                 # Authentication pages
│   └── layouts/              # Master layout
├── routes/web.php            # All routes
└── config/services.php       # API configurations

---

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 12.x (PHP 8.2+) |
| **Frontend** | Tailwind CSS 3.x |
| **Database** | MySQL 8.x |
| **Authentication** | Laravel Breeze |
| **API Integrations** | VirusTotal, Google Safe Browsing, HaveIBeenPwned, AbuseIPDB |
| **PDF Export** | dompdf |
| **QR Decoding** | QR Server API (fallback) |
| **JavaScript** | Alpine.js, Vanilla JS |
| **Build Tool** | Vite |

---
## Author
-Mohamed Naflan
-Advanced Technological Institute,Nawalapitiya
-HND IT
-2023/2024

## Licence
-This project for educational purpose only.

## 🚀 Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 8.x
- Node.js 16+

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/YOUR_USERNAME/cybershield-laravel.git
cd cybershield-laravel

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cybershield
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. Install Node dependencies
npm install

# 8. Compile assets
npm run build

# 9. Start the server
php artisan serve

# 10. In another terminal, run Vite (for development)
npm run dev
---


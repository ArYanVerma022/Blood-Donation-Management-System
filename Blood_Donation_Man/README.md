# 🩸 JeevanRakt – Blood Donor Management System

**Developer:** Aryan Verma  
**Student ID:** 241306064  
**Course:** Bachelor of Computer Applications (BCA)  
**Technology Stack:** PHP, MySQL (PDO), HTML5, CSS3, Bootstrap 5, JavaScript, XAMPP  

---

## 📌 Project Overview

**JeevanRakt** is a full-featured, college-level web application designed to help blood banks, non-profit healthcare organizations, and hospitals manage blood donors, donor pre-donation health screening records, blood donations, real-time blood stock levels, and patient blood requests.

The application follows a clean, student-project aesthetic with a medical blood theme (white background, light blue containers, dark blue navigation bar, and red accents).

---

## 🏗️ Folder Structure

```text
Blood_Donation_Man/
│
├── index.php                 # Public Landing Page (Stats, Blood Groups, Guidelines)
├── register.php              # Donor Registration Form (Redirects to Health Screening)
├── screening.php             # Pre-Donation Health Questionnaire
├── screening-result.php      # Eligibility Status (🟢/🟡/🔴) & Reason Breakdown
├── search.php                # Public Donor Search & Availability Directory
├── blood-stock.php           # Public Blood Inventory Status & Low-Stock Alerts
├── request-blood.php         # Patient/Hospital Emergency Blood Request Form
├── about.php                 # About Project & Developer Credentials
├── contact.php               # Contact Form & Emergency Helpline
├── login.php                 # Dual Login Page (User & Admin access)
├── logout.php                # Public Session Logout
│
├── config/
│   └── database.php          # Database Connection (PDO) & Helper Functions
│
├── includes/
│   ├── header.php            # HTML Head, Bootstrap 5 & FontAwesome CDN Links
│   ├── navbar.php            # Responsive Header Navigation Bar
│   ├── footer.php            # Footer with Student Identity & Quick Links
│   ├── auth.php              # Session & Role Verification Helpers
│   └── screening_engine.php  # Automated Pre-Donation Health Screening Engine
│
├── css/
│   └── style.css             # Custom CSS Stylesheet & Blood Theme Accents
│
├── js/
│   └── script.js             # Client-side Form Validation & UX Enhancements
│
├── images/
│   ├── blood-drop.svg        # Vector Blood Drop Graphics
│   ├── hero-illustration.svg # Landing Page Hero Graphics
│   └── medical-cross.svg
│
├── database/
│   └── blood_donor_management.sql # MySQL Database Schema & Sample Data Dump
│
├── admin/
│   ├── login.php             # Dedicated Admin Portal Login
│   ├── dashboard.php         # Admin Dashboard (4 main stat cards, tables, alerts)
│   ├── donors.php            # Manage Donors & Availability Statuses
│   ├── donor-screening.php   # Detailed Health Screening Questionnaire Logs
│   ├── donations.php         # Record New Donation & Donation History (Auto Stock Sync)
│   ├── blood-stock.php       # Manage Blood Inventory & Manual Stock Updates
│   ├── requests.php          # Manage Patient Requests (Approve/Reject/Complete & Auto Deduct)
│   ├── reports.php           # Summary Analytics Matrix & Printable Report
│   └── logout.php            # Admin Session Logout
│
├── README.md                 # System Architecture & Detailed Technical Guide
└── SETUP.md                  # Step-by-Step Installation & Local XAMPP Setup Guide
```

---

## 🗄️ Database Architecture & Schemas

The MySQL database `blood_donor_management` contains 7 relational tables:

1. **`admins`**: Stores admin credentials (`username`, `password` hashed using BCrypt).
2. **`users`**: Stores general registered user accounts.
3. **`donors`**: Stores donor profiles (`name`, `gender`, `age`, `blood_group`, `phone`, `email`, `city`, `address`, `last_donation_date`, `status`).
4. **`donor_screening`**: Stores health questionnaire responses, final `screening_result` (🟢/🟡/🔴), and generated `deferral_reason`.
5. **`donations`**: Logs every blood donation transaction (`donor_id`, `donation_date`, `blood_group`, `quantity`).
6. **`blood_stock`**: Tracks available blood units per blood group (`A+`, `A-`, `B+`, `B-`, `AB+`, `AB-`, `O+`, `O-`).
7. **`blood_requests`**: Logs patient requests (`requester_name`, `blood_group`, `quantity`, `hospital`, `city`, `phone`, `reason`, `request_date`, `status`).

---

## 🩺 Pre-Donation Health Screening Engine Logic

The health screening engine (`includes/screening_engine.php`) evaluates donor responses into three distinct outcomes:

### 1. 🔴 Temporarily Deferred
* **Triggers:** Active fever, flu-like illness, active infection, taking antibiotics, or recent alcohol consumption/intoxication.
* **Outcome:** Recommends postponing donation and lists automated reasons for deferral.

### 2. 🟡 Medical Review Required
* **Triggers:** History of HIV, Hepatitis B/C, bleeding disorders, heart conditions, diabetes, prescription medications, blood thinners, recent surgery, tattoo, piercing, blood transfusion, or travel to endemic regions.
* **Outcome:** Requires consultation and review by blood bank screening staff before donation.

### 3. 🟢 Proceed to Screening
* **Triggers:** No preliminary risk factors flagged.
* **Outcome:** Donor is cleared for professional screening at the blood donation center.

---

## 🔒 Security Practices

* **Prepared Statements:** Built using PHP Data Objects (PDO) to protect against SQL Injection attacks.
* **Password Hashing:** Uses `password_hash()` (BCrypt) for administrative passwords.
* **Input Sanitization:** Custom `sanitize_input()` and `htmlspecialchars()` output escaping against Cross-Site Scripting (XSS).
* **Protected Routes:** `require_admin()` function verifies admin sessions on every `/admin/` page.

---

## 👨‍💻 Developer Information

* **Developer:** Aryan Verma
* **Student ID:** 241306064
* **Academic Level:** BCA (Bachelor of Computer Applications)
* **Project Name:** JeevanRakt – Blood Donor Management System

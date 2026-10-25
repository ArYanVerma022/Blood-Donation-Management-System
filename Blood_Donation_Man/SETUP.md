# 🛠️ JeevanRakt Setup & Installation Guide

**Project:** JeevanRakt – Blood Donor Management System  
**Developer:** Aryan Verma (Student ID: 241306064)  
**Environment:** XAMPP (Apache + MySQL / MariaDB + PHP)  

---

## 🚀 Quick Launch Link

Once XAMPP services are running and the database is imported, click the link below to open the application in your browser:

👉 [**Open JeevanRakt Application (http://localhost/Blood_Donation_Man/)**](http://localhost/Blood_Donation_Man/)

👉 [**Open Admin Portal (http://localhost/Blood_Donation_Man/admin/login.php)**](http://localhost/Blood_Donation_Man/admin/login.php)

---

## 📋 Prerequisites

Make sure you have **XAMPP** installed on your system:
* Download XAMPP for Windows from [Apache Friends](https://www.apachefriends.org/).
* Required XAMPP components: **Apache** and **MySQL**.

---

## ⚙️ Step-by-Step Installation Instructions

### Step 1: Move Project to `htdocs`
Place the project folder `Blood_Donation_Man` inside your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\Blood_Donation_Man
```

---

### Step 2: Start XAMPP Control Panel
1. Open **XAMPP Control Panel** on your system.
2. Click **Start** next to **Apache**.
3. Click **Start** next to **MySQL**.
4. Ensure both module labels turn green.

---

### Step 3: Create Database in phpMyAdmin
1. Open your browser and go to [**http://localhost/phpmyadmin/**](http://localhost/phpmyadmin/).
2. Click on **Databases** tab at the top.
3. In the "Create database" field, enter:
   ```text
   blood_donor_management
   ```
4. Choose `utf8mb4_unicode_ci` collation and click **Create**.

---

### Step 4: Import Database Schema & Data
1. Select the `blood_donor_management` database from the left panel in phpMyAdmin.
2. Click on the **Import** tab in the top navigation bar.
3. Click **Choose File** / **Browse** and select the SQL file from the project directory:
   ```text
   C:\xampp\htdocs\Blood_Donation_Man\database\blood_donor_management.sql
   ```
4. Scroll down and click **Import** (or **Go**).
5. Verify that all 7 tables (`admins`, `users`, `donors`, `donor_screening`, `donations`, `blood_stock`, `blood_requests`) are imported successfully.

---

### Step 5: Verify Database Configuration
The database connection configuration is stored in `config/database.php`:
```php
$host = 'localhost';
$dbname = 'blood_donor_management';
$username = 'root';
$password = ''; // Default XAMPP MySQL password is empty
```
If your XAMPP MySQL password is not empty, update the `$password` variable in `config/database.php`.

---

## 🔑 Login Credentials

### 🛡️ Admin Portal Credentials
* **URL:** [http://localhost/Blood_Donation_Man/admin/login.php](http://localhost/Blood_Donation_Man/admin/login.php)
* **Username:** `admin`
* **Password:** `admin123`

### 👤 Sample Registered Donor Account
* **URL:** [http://localhost/Blood_Donation_Man/login.php](http://localhost/Blood_Donation_Man/login.php)
* **Email:** `rahul.sharma@example.com`
* **Password:** `user123`

---

## 🧪 Testing System Workflows

1. **Register a Donor:** Go to [Register Page](http://localhost/Blood_Donation_Man/register.php) and fill out donor details.
2. **Complete Screening:** Submit the Health Screening questionnaire to view preliminary eligibility (🟢, 🟡, or 🔴).
3. **Submit Blood Request:** Go to [Request Blood Page](http://localhost/Blood_Donation_Man/request-blood.php) to submit a patient blood request.
4. **Admin Management:** Log in as Admin to record donations (auto-increments stock) and approve/complete requests (auto-deducts stock).
5. **View Reports:** Visit [Reports Page](http://localhost/Blood_Donation_Man/admin/reports.php) to generate summary matrix reports.

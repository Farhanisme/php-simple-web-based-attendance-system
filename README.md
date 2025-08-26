# Web-Based Student Attendance System

A simple, procedural PHP application designed for managing student attendance. This system provides distinct interfaces for students, lecturers, and administrators and is built to run on a standard XAMPP local server environment.

## Table of Contents

1.  [About The Project]
2.  [Key Features]
3.  [Technology Stack]
4.  [Getting Started]
      - [Prerequisites]
      - [Installation]
5.  [Database Schema and Setup]
      - [Database Schema]
      - [Setup Instructions]
6.  [Project Structure and File Descriptions]
7.  [User Flow]
8.  [Security Recommendations]
9.  [Contributing]


-----

## About The Project

This project is an implementation of a digital attendance system that facilitates attendance tracking for educational institutions. It is designed with three primary user roles: students, lecturers, and administrators, each with a dedicated set of functionalities.

The application is developed using native PHP without a framework, making it a valuable resource for understanding core web development principles. It is intended for local development and demonstration purposes on an Apache and MySQL server stack.

-----

## Key Features

### Administrator / Lecturer

  - **Dashboard**: Provides an overview and quick access to management modules.
  - **CRUD Operations**: Full Create, Read, Update, and Delete capabilities for lecturer data, course schedules, and all student attendance records.
  - **Statistics**: View reports and statistics related to student attendance.

### Student

  - **Dashboard**: Displays a personal summary of attendance records and upcoming schedules.
  - **Attendance Submission**: Allows students to mark their attendance for scheduled classes, including an option to upload a photo as proof.
  - **Attendance History**: View, edit (if permitted), and download personal attendance records.

-----

## Technology Stack

  - **Backend**: PHP (7.x or 8.x)
  - **Database**: MySQL / MariaDB
  - **Web Server**: Apache (via XAMPP)

-----

## Getting Started

Follow these instructions to get a copy of the project up and running on your local machine.

### Prerequisites

  - **XAMPP**: Ensure you have XAMPP installed, which provides Apache, PHP, and MySQL. You can download it from the [official Apache Friends website](https://www.apachefriends.org).

### Installation

1.  **Clone the Repository**

    ```sh
    git clone https://github.com/your-username/your-repository-name.git
    ```

    Alternatively, download the ZIP file and extract it.

2.  **Position the Project Folder**
    Move the project folder (named `web`) into the `htdocs` directory of your XAMPP installation. The typical path is `C:\xampp\htdocs\`. The final project path should be `C:\xampp\htdocs\web`.

3.  **Start XAMPP Services**
    Open the XAMPP Control Panel and start the **Apache** and **MySQL** services.

4.  **Configure Database Connection**

      - Open the file `includes/config.php` in a code editor.
      - Update the database connection variables (`$host`, `$db`, `$user`, `$pass`) to match your local MySQL configuration.

    <!-- end list -->

    ```php
    // in includes/config.php
    $host = 'localhost';
    $db   = 'absensi_db'; // The name of the database you will create
    $user = 'root';       // Default XAMPP username
    $pass = '';           // Default XAMPP password is empty
    ```

5.  **Access the Application**
    Open your web browser and navigate to `http://localhost/web/`.

-----

## Database Schema and Setup

The application requires a MySQL or MariaDB database to store all data related to students, lecturers, schedules, and attendance. The following section details the required database structure and provides instructions for setting it up.

### Database Schema

**Build your database using the structure defined in the SQL script below. You must save the new database with the name `absensi_db`** to ensure it matches the default application configuration. This script will create all the necessary tables and relationships.

```sql
--
-- Table structure for `mahasiswa` (Students)
--
CREATE TABLE `mahasiswa` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nama` VARCHAR(200) NOT NULL,
  `nomor_absen` VARCHAR(16) NOT NULL UNIQUE
);

--
-- Table structure for `dosen` (Lecturers)
--
CREATE TABLE `dosen` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `nama` VARCHAR(200) NOT NULL,
  `email` VARCHAR(200) NULL,
  `kontak` VARCHAR(32) NULL
);

--
-- Table structure for `mata_kuliah` (Courses)
--
CREATE TABLE `mata_kuliah` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `kode_matkul` VARCHAR(32) NOT NULL UNIQUE,
  `nama_matkul` VARCHAR(200) NOT NULL,
  `sks` TINYINT NOT NULL
);

--
-- Table structure for `jadwal` (Schedules)
--
CREATE TABLE `jadwal` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `mata_kuliah_id` INT NOT NULL,
  `dosen_id` INT NOT NULL,
  `hari` VARCHAR(16) NOT NULL,
  `jam_mulai` TIME NOT NULL,
  `jam_selesai` TIME NOT NULL,
  `ruangan` VARCHAR(64) NULL,
  FOREIGN KEY (`mata_kuliah_id`) REFERENCES `mata_kuliah`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`dosen_id`) REFERENCES `dosen`(`id`) ON DELETE CASCADE
);

--
-- Table structure for `absensi` (Attendance)
--
CREATE TABLE `absensi` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `mahasiswa_id` INT NOT NULL,
  `jadwal_id` INT NOT NULL,
  `materi` TEXT NULL,
  `foto` VARCHAR(255) NULL, -- Filename in uploads/ directory
  `tanggal_absensi` DATE NOT NULL,
  FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal`(`id`) ON DELETE CASCADE
);
```

### Setup Instructions

#### Creating a Database

To ensure that other developers can easily replicate the database, you should provide a `.sql` file containing the schema above.

1.  Set up the database locally using the SQL script provided.
2.  Open phpMyAdmin by navigating to `http://localhost/phpmyadmin`.
3.  Select your database (e.g., `absensi_db`) from the left sidebar.
4.  Navigate to the **"Export"** tab.
5.  Choose the **"Quick"** export method and ensure the format is set to **"SQL"**.
6.  Click the **"Export"** button to download the file.
7.  Rename the downloaded file to `database.sql` and place it in the project's root directory.

#### Setting Up the Database from a File

Follow these steps to create and populate the database using the provided `database.sql` file.

1.  **Ensure XAMPP is Running**: Make sure the **Apache** and **MySQL** services are active in the XAMPP Control Panel.
2.  **Create the Database**:
      - Navigate to `http://localhost/phpmyadmin` in your browser.
      - Click **"New"** in the left sidebar.
      - Enter the database name exactly as specified in your `config.php` file (e.g., `absensi_db`).
      - Select a collation, such as `utf8mb4_general_ci`, and click **"Create"**.
3.  **Import the SQL File**:
      - After creating the database, select it from the left sidebar.
      - Click on the **"Import"** tab at the top.
      - Click **"Choose File"** and select the `database.sql` file from the project's root directory.
      - Click **"Import"** at the bottom of the page to execute the script.

Upon successful import, all necessary tables will be created, and the application will be ready to connect to the database.

-----

## Project Structure and File Descriptions

  - `index.php`
      - The public entry point of the application. This page typically directs users to the login area or a public landing page.

  - `admin/` — **Admin Area**
      - `dashboard.php`: The main dashboard for administrators, displaying metrics and management scopes.
      - `absensi_crud.php`: Handles CRUD (Create, Read, Update, Delete) operations for all attendance data from the admin's perspective.
      - `dosen_crud.php`: Manages CRUD operations for lecturer data.
      - `jadwal_crud.php`: Manages CRUD operations for course schedules.
      - `statistik.php`: Displays attendance reports and statistics, potentially using graphs or tables.

  - `mahasiswa/` — **Student Area**
      - `dashboard.php`: The student's personal dashboard, showing a summary of their attendance, schedule, etc.
      - `absensi_add.php`: The form where students can submit a new attendance entry.
      - `absensi_list.php`: Displays a list of attendance records.
      - `absensi_edit.php`: Allows a student to edit an attendance entry (if permitted).
      - `absensi_my.php`: Shows the attendance records belonging only to the currently logged-in student.
      - `absensi_kelas.php`: Lists or filters attendance records by class.
      - `absensi_download.php`: Implements the feature to download attendance data.

  - `includes/` — **Utility and Configuration Files**
      - `config.php`: Contains the database connection configuration and other global settings. This file must be configured before running the application.
      - `auth_admin.php`: An authentication check (middleware) for all pages within the admin area.
      - `auth_user.php`: An authentication check (middleware) for all pages within the student area.
      - `header.php` & `footer.php`: Reusable HTML template partials for the page header and footer.

  - `assets/`
      - `index.css`: The global stylesheet for the application's visual appearance.

  - `uploads/`
      - The directory where uploaded image files (e.g., profile pictures or attendance proof photos in JPEG/PNG format) are stored.

-----

## User Flow

The typical flow of interaction for different user roles is as follows:

1.  **Login**: A visitor navigates to `index.php` and logs in as an administrator, lecturer, or student.
2.  **Admin Actions**: After logging in, an administrator accesses the `admin/` area to manage master data, such as adding or updating lecturer information (`dosen_crud.php`) and course schedules (`jadwal_crud.php`).
3.  **Monitoring**: A lecturer or administrator views attendance statistics and lists via `admin/statistik.php` or the `admin/dashboard.php`.
4.  **Student Actions**: A student logs in and is directed to the `mahasiswa/` area to add, view, or edit their personal attendance records.

-----

## Security Recommendations

  - **Database Credentials**: The `includes/config.php` file should not be committed to a public repository with production credentials. It is recommended to add this file to `.gitignore` and use a template file (e.g., `config.example.php`) instead.
  - **Password Management**: Any hardcoded passwords should be replaced with a secure system that stores hashed passwords in the database (e.g., using `password_hash()` and `password_verify()`).
  - **Input Validation**: All user-provided data must be validated and sanitized on the server side to prevent security vulnerabilities like SQL Injection and Cross-Site Scripting (XSS).
  - **Upload Directory**: The `uploads/` directory should be configured to prevent the execution of PHP scripts to mitigate risks from malicious file uploads.
  - **CSRF Protection**: Implement Cross-Site Request Forgery (CSRF) tokens on all forms that perform state-changing actions (e.g., create, update, delete).

-----

## Contributing

Contributions are welcome. To contribute, please follow these steps:

1.  Fork the repository.
2.  Create a new branch for your feature (`git checkout -b feature/AmazingFeature`).
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`).
4.  Push to the branch (`git push origin feature/AmazingFeature`).
5.  Open a Pull Request.
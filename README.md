# Educational Web Portal

A PHP and MySQL web application for managing an educational course portal.

## Features

- User authentication and session handling
- Role-based pages for students and tutors
- Course announcements
- Educational documents and resources
- Homework and assignment management
- Communication form and message logging

## Technologies

- PHP
- MySQL
- HTML
- CSS
- PDO

## Requirements

- PHP with PDO MySQL enabled
- MySQL or MariaDB
- A local web server such as XAMPP, MAMP, or Apache

## Setup

1. Create a MySQL database (for example, `educational_web_portal`).
2. Import `database/schema.sql` into the database.
3. Copy `config.example.php` to `config.php`.
4. Edit `config.php` and enter your own local database host, port, database name, username, and password.
5. Place the project in your local web server's document root.
6. Open `login.php` through your local server.

> `config.php` is intentionally excluded from version control. Never commit real passwords or private credentials.

## Database

`database/schema.sql` contains table definitions only. It does not include the original users, email logs, or other records.

## Uploaded files

The original uploaded documents are not included in this public repository. The `files/` directory is kept as an empty upload location. Add only documents that you have permission to publish.

## Notes

This repository is a student project. Review and configure authentication, password hashing, upload validation, and production security settings before deploying it publicly.

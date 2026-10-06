# Expense Tracker

A full-stack Expense Tracker application developed as part of the iauro Systems Pvt. Ltd. assignment.

The application allows authenticated users to securely manage their personal expenses through a simple web interface.

---

## Features

- User registration
- User login
- JWT-based authentication
- Add expenses
- View expenses
- Edit expenses
- Delete expenses
- User-specific expense records
- Newest expenses displayed first
- Created At and Updated At timestamps
- Total expense summary
- Total amount summary
- Category-wise expense distribution using a pie chart
- Responsive user interface

---

## Technology Stack

### Frontend

- HTML5
- CSS3
- JavaScript
- Chart.js

### Backend

- PHP 8+
- REST-style API endpoints
- JWT authentication

### Database

- MySQL
- PDO for database connectivity

### Development Environment

- XAMPP
- Apache
- MySQL
- phpMyAdmin
- Visual Studio Code

---

## Project Structure

```text
Expense-Tracker/
│
├── api/
│   ├── config/
│   │   ├── database.php
│   │   ├── jwt.php
│   │   └── auth.php
│   │
│   ├── auth/
│   │   ├── signup.php
│   │   └── login.php
│   │
│   ├── expenses/
│   │   ├── create.php
│   │   ├── list.php
│   │   ├── update.php
│   │   ├── delete.php
│   │   └── summary.php
│   │
│   └── test-db.php
│
├── frontend/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   ├── auth.js
│   │   ├── expenses.js
│   │   └── dashboard.js
│   ├── login.html
│   ├── signup.html
│   ├── dashboard.html
│   ├── expenses.html
│   ├── add-expense.html
│   └── test-auth.html
│
└── README.md
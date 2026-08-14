# Task 6: Automated Logging using Triggers & Views

## Overview
This task demonstrates automated database audit logging using MySQL Triggers and database Views in an enterprise context.

## Features
- **AFTER INSERT Trigger**: Logs every new employee record inserted into `employees` table automatically into `audit_logs`.
- **AFTER UPDATE Trigger**: Logs every salary modification into `audit_logs` capturing previous and new salary values.
- **Daily Activity View**: Provides dynamic aggregated daily report counts by date and action type.

## Project Structure
```text
Task 6/
└── audit-logging/
    ├── config/
    │   └── db.php
    ├── database/
    │   └── schema.sql
    ├── index.php
    ├── style.css
    └── README.md
```

## Database
- Name: `audit_logging`
- Service: Standalone MySQL 8.0 (`MySQL80` on port 3306)

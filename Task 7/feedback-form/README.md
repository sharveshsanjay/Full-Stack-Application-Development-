# Task 7: Interactive Web Form with Events & Functions

## Overview
This task demonstrates building an interactive customer feedback form with client-side JavaScript event listeners and reusable validation functions.

## Features
- **Live Keypress Validation**: Validates inputs real-time on `input`/`keyup` events with visual feedback.
- **Mouse Hover Effects**: Dynamically highlights form control containers on `mouseenter` and `mouseleave`.
- **Double-Click Submission**: Prevents accidental single-click submits using `dblclick` event handling.
- **Reusable Validation Logic**: Modular JS functions for validating names, emails, and comments.

## Project Structure
```text
Task 7/
└── feedback-form/
    ├── config/
    │   └── db.php
    ├── database/
    │   └── schema.sql
    ├── js/
    │   └── script.js
    ├── index.php
    ├── style.css
    └── README.md
```

## Database
- Name: `feedback_system`
- Service: Standalone MySQL 8.0 (`MySQL80` on port 3306)





### 🛠️ Technical Specifications

    Core: PHP 8.x (PDO SQLite)

    Styling: Tailwind CSS (Dark Mode optimized)

    Icons: Heroicons (SVG based)

    JS: Vanilla JavaScript (No heavy frameworks)

    Protection: CSRF-ready, IDOR-protected, and SQLi-immune.

---

# make 🛡️ Passman - Ultimate Secure PHP Password Manager

Passman is a professional-grade, lightweight password management system. It is designed with a "Security-First" mindset, combining robust AES-256 encryption with a modern, responsive interface built using Tailwind CSS.

---

## 🛡️ Passman: Ultimate Secure PHP Password Manager

Passman is a professional-grade, lightweight password management system. It is designed with a "Security-First" mindset, combining robust AES-256 encryption with a modern, responsive interface built using Tailwind CSS.

## ✨ Full Feature List & Functionality

1. 🔐 Advanced Encryption Engine (AES-256-CBC)

    Industry Standard: Every password you save is encrypted using the AES-256-CBC algorithm before it ever touches the database.

    Data Breach Protection: Even if an attacker steals the config.db file, your actual passwords remain unreadable without the unique encryption key.

2. ⏳ Intelligent Auto-Logout (Security Timeout)

    Idle Monitoring: The system actively tracks user activity, including mouse movements, clicks, and scrolls.

    Graceful Warning: If no activity is detected for 4.5 minutes, a 30-second warning modal appears with a live countdown.

    Auto-Termination: If the countdown reaches zero, the session is instantly destroyed and redirected to logout.php to prevent unauthorized physical access.

3. 🎲 Smart Password Generator

    Integrated Tool: Built directly into the "Add" and "Edit" modals for a seamless workflow.

    Complexity: Generates 16-character complex strings including uppercase, lowercase, numbers, and symbols with a single click.

    Auto-Fill: Automatically fills the input field and triggers the real-time strength meter.

4. 📊 Real-time Password Strength Meter

    Dynamic Evaluation: Uses a custom JavaScript engine to evaluate password entropy as you type.

    Visual Feedback: Features a color-coded progress bar:

        🔴 Red: Weak

        🟠 Orange: Fair

        🔵 Blue: Good

        🟢 Emerald: Strong (includes glowing shadow effects).

5. 🛠️ CRUD Management (Create, Read, Update, Delete)

    Secure Edit: Update credentials via sleek overlay modals without ever leaving the dashboard.

    Quick Copy: One-click "Copy to Clipboard" button with a success checkmark animation.

    Visual Toggle: Interactive Eye Icons allow you to hide or show passwords instantly.

    Categorization: Organize your vault into "Personal," "Work," or "Social" folders.

6. 🔍 Global Search & Filter

    Instant Search: A high-speed search bar that filters your vault as you type—no page refresh required.

    Category Pills: Quickly switch between specific vaults using interactive UI pills.

7. 📥 CSV Data Portability

    Bulk Import: Easily migrate from other managers (like Chrome or LastPass) by uploading a CSV.

    Secure Export: Download your entire encrypted vault into a CSV file for offline backup.



## Setup


Run setup.php in your browser to initialize the SQLite database.


SECURITY WARNING: After the database is created, you must delete setup.php from your server to prevent unauthorized resets:
   
```bash
rm setup.php
```

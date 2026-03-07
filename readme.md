🛡️ Passman - Ultimate Secure PHP Password Manager

Passman is a professional-grade, lightweight password management system. It is designed with a "Security-First" mindset, combining robust AES-256 encryption with a modern, responsive interface built using Tailwind CSS.
✨ Full Feature List & Functionality
1. Advanced Encryption Engine (AES-256-CBC)

    Every password you save is encrypted using the industry-standard AES-256-CBC algorithm before it ever touches the database.

    Even if an attacker steals the config.db file, your actual passwords remain unreadable without the unique encryption key.

2. Intelligent Auto-Logout (Security Timeout)

    Idle Monitoring: The system tracks user activity (mouse movement, clicks, scrolls).

    Graceful Timeout: If no activity is detected for 4.5 minutes, a 30-second warning modal appears with a countdown.

    Auto-Termination: If the countdown reaches zero, the session is destroyed, and the user is redirected to logout.php to prevent unauthorized physical access.

3. Smart Password Generator

    Built directly into the "Add" and "Edit" modals.

    Generates 16-character complex strings including uppercase, lowercase, numbers, and symbols with a single click.

    Automatically fills the input field and triggers the strength meter.

4. Real-time Password Strength Meter

    Uses a dynamic JavaScript engine to evaluate passwords as you type.

    Visual Feedback: A color-coded progress bar (Red = Weak, Orange = Fair, Blue = Good, Emerald = Strong) with glowing shadow effects.

5. CRUD Management (Create, Read, Update, Delete)

    Secure Edit: Update credentials via sleek overlay modals without leaving the dashboard.

    Quick Copy: A "Copy to Clipboard" button with a success animation (Checkmark icon) for instant use.

    Visual Toggle: Hide/Show password functionality with interactive eye icons.

    Categorization: Organize passwords into folders like "Personal," "Work," or "Social" for faster access.

6. Global Search & Filter

    Instant Search: A real-time search bar that filters your vault as you type (no page refresh required).

    Category Pills: Quickly switch between "All," "Work," or "Personal" categories using interactive pills.

7. CSV Data Portability

    Bulk Import: Migrate from other managers easily by uploading a CSV.

    Secure Export: Download your entire vault into a CSV file for offline backup.

🔒 Mandatory Security Configuration
1. Enabling .htaccess (The Directory Guard)

By default, Apache often ignores security files. You must enable AllowOverride to ensure the /includes and /actions folders are hidden from the public.

Follow these steps:

    Open your Apache config:

    sudo nano /etc/apache2/apache2.conf

    Search for the <Directory /var/www/> block.

    Change AllowOverride None to AllowOverride All:
    Apache

    <Directory /var/www/>
        Options Indexes FollowSymLinks
        AllowOverride All    <-- IMPORTANT: Change this here
        Require all granted
    </Directory>

    Activate the rewrite module and restart:
    sudo a2enmod rewrite && sudo systemctl restart apache2

2. Finalizing Installation

    Run setup.php in your browser to initialize the SQLite database.

    SECURITY WARNING: After the database is created, you must delete setup.php from your server to prevent unauthorized resets:
    rm setup.php

🛠️ Technical Specifications

    Core: PHP 8.x (PDO SQLite)

    Styling: Tailwind CSS (Dark Mode optimized)

    Icons: Heroicons (SVG based)

    JS: Vanilla JavaScript (No heavy frameworks)

    Protection: CSRF-ready, IDOR-protected, and SQLi-immune.

Would you like me to help you generate the LICENSE file text to protect your code rights as well?
أنت قلت

make 🛡️ Passman - Ultimate Secure PHP Password Manager

Passman is a professional-grade, lightweight password management system. It is designed with a "Security-First" mindset, combining robust AES-256 encryption with a modern, responsive interface built using Tailwind CSS.

✨ Full Feature List & Functionality

1. Advanced Encryption Engine (AES-256-CBC)

Every password you save is encrypted using the industry-standard AES-256-CBC algorithm before it ever touches the database.

Even if an attacker steals the config.db file, your actual passwords remain unreadable without the unique encryption key.

2. Intelligent Auto-Logout (Security Timeout)

Idle Monitoring: The system tracks user activity (mouse movement, clicks, scrolls).

Graceful Timeout: If no activity is detected for 4.5 minutes, a 30-second warning modal appears with a countdown.

Auto-Termination: If the countdown reaches zero, the session is destroyed, and the user is redirected to logout.php to prevent unauthorized physical access.

3. Smart Password Generator

Built directly into the "Add" and "Edit" modals.

Generates 16-character complex strings including uppercase, lowercase, numbers, and symbols with a single click.

Automatically fills the input field and triggers the strength meter.

4. Real-time Password Strength Meter

Uses a dynamic JavaScript engine to evaluate passwords as you type.

Visual Feedback: A color-coded progress bar (Red = Weak, Orange = Fair, Blue = Good, Emerald = Strong) with glowing shadow effects.

5. CRUD Management (Create, Read, Update, Delete)

Secure Edit: Update credentials via sleek overlay modals without leaving the dashboard.

Quick Copy: A "Copy to Clipboard" button with a success animation (Checkmark icon) for instant use.

Visual Toggle: Hide/Show password functionality with interactive eye icons.

Categorization: Organize passwords into folders like "Personal," "Work," or "Social" for faster access.

6. Global Search & Filter

Instant Search: A real-time search bar that filters your vault as you type (no page refresh required).

Category Pills: Quickly switch between "All," "Work," or "Personal" categories using interactive pills.

7. CSV Data Portability

Bulk Import: Migrate from other managers easily by uploading a CSV.

Secure Export: Download your entire vault into a CSV file for offline backup.

🔒 Mandatory Security Configuration

1. Enabling .htaccess (The Directory Guard)

By default, Apache often ignores security files. You must enable AllowOverride to ensure the /includes and /actions folders are hidden from the public.

Follow these steps:


Open your Apache config:

sudo nano /etc/apache2/apache2.conf

Search for the <Directory /var/www/> block.

Change AllowOverride None to AllowOverride All:


Apache

it style with titles and emojies and arrang
قال Gemini
🛡️ Passman: Ultimate Secure PHP Password Manager

Passman is a professional-grade, lightweight password management system. It is designed with a "Security-First" mindset, combining robust AES-256 encryption with a modern, responsive interface built using Tailwind CSS.
✨ Full Feature List & Functionality
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

🔒 Mandatory Security Configuration
🛡️ Enabling .htaccess (The Directory Guard)

By default, Apache often ignores security files. You must enable AllowOverride to ensure the /includes and /actions folders are strictly hidden from public access.
Follow these steps:

    Open your Apache config:
    Bash

sudo nano /etc/apache2/apache2.conf

Search for the /var/www/ block:
Look for the <Directory /var/www/> section.

Update the Override Policy:
Change AllowOverride None to AllowOverride All:
Apache

<Directory /var/www/>
    Options Indexes FollowSymLinks
    AllowOverride All    <-- Change this word
    Require all granted
</Directory>

Restart Apache:
Bash

    sudo a2enmod rewrite
    sudo systemctl restart apache2

🚀 Post-Installation Security

    [!IMPORTANT]
    After running the setup.php file to create your database, you must delete it immediately to prevent unauthorized database resets.
    Bash

    rm setup.php

Would you like me to generate a specific "How to Contribute" section or a License file for this repository?

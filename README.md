To ensure the text formats correctly and doesn't appear as a single block or "two lines" when you paste it, it is best to use a clean Markdown structure with explicit line breaks.

Here is the **README.md** content, formatted specifically for easy copying into a text editor (like VS Code, Notepad++, or Vim).

---

# 🛡️ Passman Vault | Advanced Password Manager

**Passman** is a sophisticated, self-hosted password management system built with PHP. It prioritizes high-grade security and a seamless user experience, utilizing **AES-256-CBC** encryption to ensure your sensitive data remains private. The interface is designed with a modern, "cyber-style" aesthetic using **Tailwind CSS**.

---

## ✨ Features

*   **Zero-Knowledge Encryption:** All sensitive data (URLs, passwords, notes, emails) is encrypted using your unique Master Key. Your data is never stored as plain text in the database.
*   **Anti-Bot Protection:** Integrated CAPTCHA system in the login flow to prevent automated brute-force attacks.
*   **Extreme Danger Zone:** A secure account termination feature that wipes your entire vault and profile. It requires manual username verification to prevent accidental deletion.
*   **Smart Import/Export:** Export your vault to CSV (either as encrypted strings for backup or plain text for migration). The smart import recognizes if a file is already encrypted or needs encryption during upload.
*   **Security Hardened:** Built-in protection against **CSRF**, **SQL Injection**, and **XSS** attacks.
*   **File Vault:** A dedicated area for uploading and managing sensitive files within a secure environment.
*   **Isolated Notes:** Encrypted note-taking module where each user's notes are cryptographically separated.

---

## 🚀 Installation & Setup

### 1. Requirements
*   **Web Server:** Apache or Nginx.
*   **PHP:** Version 8.x or higher.
*   **Database:** MySQL or MariaDB.
*   **PHP Extensions:** 
    *   `openssl` (Required for AES-256 encryption)
    *   `pdo_mysql` (Required for database communication)
    *   `mbstring` (Required for multi-byte string handling)
    *   `gd` Library (**CRITICAL**: Required for generating CAPTCHA images in `login.php`)

### 2. Permissions & Directories
Certain directories require specific permissions for file uploads and secure storage.

| Directory | Suggested Permission | Purpose |
| :--- | :--- | :--- |
| `/uploads` | `777` or `www-data` | Temporary storage for file uploads. |
| `/vault` | `775` or `www-data` | Main storage for encrypted vault assets. |
| `/actions` | `755` | Backend logic for import, export, and deletion. |
| `/includes` | `755` | Database connection and core functions. |

**Setup Commands (Linux):**

```bash
# Install the GD library for CAPTCHA support
sudo apt-get update
sudo apt-get install php-gd
sudo systemctl restart apache2

# Set ownership to the web user
sudo chown -R www-data:www-data /var/www/html/passman

# Set general permissions
sudo chmod -R 755 /var/www/html/passman

# Grant write access to upload/vault folders
sudo chmod 777 /var/www/html/passman/uploads
sudo chmod 777 /var/www/html/passman/vault
```

### 3. Database Configuration
1.  Create a new database named `passman`.
2.  Update the credentials in `includes/db.php`.
3.  Run the `setup.php` script via your browser to automatically generate the tables:
    `[http://your-domain.com/setup.php](http://your-domain.com/setup.php)`

---

## 🛠️ How it Works

### Encryption Logic
When saving data, Passman combines your session-based Master Key with the **AES-256-CBC** algorithm.

*   **Isolated Encryption:** Each user's vault is encrypted with their own unique key. Even if the database is leaked, data remains unreadable without the specific user's Master Key.
*   **Exporting:** Choose **Encrypted CSV** (safe for backups, only readable by Passman) or **Plain Text** (readable by Excel).
*   **Importing:** The system is context-aware. If you upload an "Encrypted" file, it maps data directly. If you upload "Plain Text", it encrypts fields on-the-fly.

### Termination Protocol
The **Danger Zone** is an irreversible action. To delete an account, the user must type their exact username into the confirmation modal. This triggers a `POST` request (protected by CSRF tokens) that executes a database transaction to wipe all relational data.

---

## 📂 Project Structure

*   `dashboard.php`: The main hub for managing vault entries.
*   `login.php`: Secure entry point with Anti-bot (GD) protection.
*   `actions/`: Backend processors for export, import, and account management.
*   `includes/`: Core files including `db.php` and `functions.php`.
*   `assets/`: Frontend assets (CSS, images, and custom JS like `export.js`).
*   `files.php` & `notes.php`: Specialized modules for non-password data.

---

## ⚠️ Security Notice
Always change the default **Secret Key** in your configuration files before production use. Remember: your **Master Key** is the only way to recover your data. If you lose it, the encrypted data is mathematically unrecoverable.

---

**Developed by:** Hashem Ali Kahil (Orion7715)  

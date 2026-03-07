<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$root_user = "root";
$root_pass = "NewStrongRootPass123!";

try {
    $pdo = new PDO("mysql:host=localhost", $root_user, $root_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mypassman");

    // Create app user
    $pdo->exec("CREATE USER IF NOT EXISTS 'passman_user'@'localhost' IDENTIFIED BY 'StrongPassword123!'");
    $pdo->exec("GRANT ALL PRIVILEGES ON mypassman.* TO 'passman_user'@'localhost'");
    $pdo->exec("FLUSH PRIVILEGES");

    // Connect to new DB
    $pdo = new PDO("mysql:host=localhost;dbname=mypassman", "passman_user", "StrongPassword123!");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            master_password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
   // Create passwords table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS passwords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category VARCHAR(50) NOT NULL,
    domain VARCHAR(100),
    username VARCHAR(100),
    password TEXT,
    email VARCHAR(100),
    note TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )
");


    
    echo "Setup completed successfully. DELETE THIS FILE NOW.";
    echo "<a href='register.php'>Register here</a>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>


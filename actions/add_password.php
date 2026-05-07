<?php
require_once '../includes/protect.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['flash_error'] = "Invalid CSRF token.";
        header("Location: ../dashboard.php");
        exit;
    }

    $user_id    = $_SESSION['user_id'];
    $master_key = $_SESSION['master_key'];
    $username   = $_POST["username"] ?? "";
    $password   = $_POST["password"] ?? "";
    $domain     = $_POST["domain"] ?? "";
    $note       = $_POST["note"] ?? "";
    $email      = $_POST["email"] ?? "";
    $category   = $_POST["category"] ?? "General";

    
    if (!empty($password) && !empty($domain) && !empty($email)) {
        
        $stmt = $pdo->prepare("INSERT INTO passwords (user_id, category, domain, username, password, email, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $success = $stmt->execute([
            $user_id,
            $category,
            encrypt_with_master($domain, $master_key),
            $username,
            encrypt_with_master($password, $master_key),
            encrypt_with_master($email, $master_key),
            encrypt_with_master($note, $master_key)
        ]);

        if ($success) {
            $_SESSION["flash_success"] = "Credential added successfully!";
        } else {
            $_SESSION["flash_error"] = "An error occurred while saving.";
        }
        
    } else {
        $_SESSION["flash_error"] = 'Please fill in all required fields';
    }
}


header("Location: ../dashboard.php");
exit;
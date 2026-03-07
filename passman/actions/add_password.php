<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/protect.php';

// التحقق من وجود الجلسة ومفتاح التشفير
if (!isset($_SESSION['user_id']) || !isset($_SESSION['master_key'])) {
    header("Location: ../logout.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_password'])) {
    $user_id = $_SESSION['user_id'];
    $master_key = $_SESSION['master_key'];
    
    $username = $_POST["username"] ?? "";
    $password = $_POST["password"] ?? "";
    $domain   = $_POST["domain"] ?? "";
    $note     = $_POST["note"] ?? "";
    $email    = $_POST["email"] ?? "";
    $category = $_POST["category"] ?? "General";

    if (!empty($password) && !empty($domain)) {
        $stmt = $pdo->prepare("INSERT INTO passwords (user_id, category, domain, username, password, email, note) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_id,
            $category,
            encrypt_with_master($domain, $master_key),
            $username,
            encrypt_with_master($password, $master_key),
            encrypt_with_master($email, $master_key),
            encrypt_with_master($note, $master_key)
        ]);
        $_SESSION["flash_success"] = "Password added successfully!";
    }
}

header("Location: ../dashboard.php");
exit;

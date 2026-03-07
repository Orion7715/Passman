<?php
require_once '../includes/protect.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../logout.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user_id'];
    $master_key = $_SESSION['master_key'];
    $id = intval($_POST["id"]);


    
    $stmt = $pdo->prepare("UPDATE passwords SET category = ?, domain = ?, username = ?, password = ?, email = ?, note = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([
        $_POST["category"] ?? "General",
        encrypt_with_master($_POST["domain"], $master_key),
        $_POST["username"],
        encrypt_with_master($_POST["password"], $master_key),
        encrypt_with_master($_POST["email"], $master_key),
        encrypt_with_master($_POST["note"], $master_key),
        $id,
        $user_id
    ]);
    
    $_SESSION["flash_success"] = "Credential updated successfully!";
}

header("Location: ../dashboard.php");
exit;

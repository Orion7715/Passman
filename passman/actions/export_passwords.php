<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../includes/session_config.php'; 
session_start();
ob_start();


if (!isset($_SESSION['master_key'])) {
    header("Location: ../logout.php");
    exit;
}

require "../includes/db.php";
require "../includes/functions.php";


$_SESSION['flash_success'] = "Passwords exported successfully!";


if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: ../login.php");
    exit;
}


$stmt_user = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt_user->execute([$_SESSION["username"]]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) { 
    die("User not found"); 
}

$user_id = $user['id'];
$master_key = $_SESSION['master_key'];


ob_end_clean();


header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="passman_vault_export.csv"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
fputcsv($output, ['Category', 'Domain', 'Username', 'Password', 'Email', 'Note']);


$stmt = $pdo->prepare("SELECT category, domain, username, password, email, note FROM passwords WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    $category = !empty($row['category']) ? $row['category'] : 'General';
    
    fputcsv($output, [
        $category,
        decrypt_with_master($row['domain'], $master_key),
        $row['username'],
        decrypt_with_master($row['password'], $master_key),
        decrypt_with_master($row['email'], $master_key),
        decrypt_with_master($row['note'], $master_key)
    ]);
}

fclose($output);
exit;


?>

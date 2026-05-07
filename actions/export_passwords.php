<?php
require_once '../includes/protect.php'; 
require "../includes/db.php";
require "../includes/functions.php";

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Session expired']);
    exit;
}

// استقبال نوع التصدير من الفورم (encrypted أو plain)
$export_type = $_POST['export_type'] ?? 'plain';

$stmt_user = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt_user->execute([$_SESSION["username"]]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$user) { 
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user_id = $user['id'];
$master_key = $_SESSION['master_key'];

$stmt = $pdo->prepare("SELECT category, domain, username, password, email, note FROM passwords WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($rows)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No passwords to export.']);
    exit;
}

// إعداد ملف التحميل
header('Content-Type: text/csv; charset=utf-8');
$filename = ($export_type === 'encrypted') ? "passman_vault_encrypted.csv" : "passman_vault_plain.csv";
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['Category', 'Domain', 'Username', 'Password', 'Email', 'Note'], ',', '"', '\\');

foreach ($rows as $row) {
    $category = !empty($row['category']) ? $row['category'] : 'General';
    
    // إذا اختار المستخدم مشفر، نرسل البيانات كما هي من القاعدة
    // إذا اختار نص عادي، نقوم بفك التشفير باستخدام دالتك
    if ($export_type === 'encrypted') {
        fputcsv($output, [
            $category,
            $row['domain'],
            $row['username'],
            $row['password'],
            $row['email'],
            $row['note']
        ], ',', '"', '\\');
    } else {
        fputcsv($output, [
            $category,
            decrypt_with_master($row['domain'], $master_key),
            $row['username'],
            decrypt_with_master($row['password'], $master_key),
            decrypt_with_master($row['email'], $master_key),
            decrypt_with_master($row['note'], $master_key)
        ], ',', '"', '\\'); 
    }
}

fclose($output);
exit;

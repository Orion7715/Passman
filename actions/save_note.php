<?php
require_once '../includes/protect.php';
require_once '../includes/functions.php';

// التأكد من وجود جلسة ومفتاح
if (!isset($_SESSION['master_key'])) {
    die("Master key not found in session.");
}

$master_key = $_SESSION['master_key'];
$notes_file = '../vault/secure_notes.bin';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['note_content'])) {
    $note_text = $_POST['note_content'];
    $timestamp = date("Y-m-d H:i:s");
    
    // تنسيق الملاحظة قبل التشفير
    $plain_data = "---" . PHP_EOL . "Date: $timestamp" . PHP_EOL . "Note: $note_text" . PHP_EOL;

    // استخدام دالتك الموجودة في functions.php لتشفير البيانات
    $encrypted_entry = encrypt_with_master($plain_data, $master_key);

    // الحفظ في ملف بصيغة سطر واحد لكل ملاحظة (Base64)
    if (file_put_contents($notes_file, $encrypted_entry . PHP_EOL, FILE_APPEND | LOCK_EX)) {
        header("Location: ../dashboard.php?note=success");
    } else {
        header("Location: ../dashboard.php?note=error");
    }
    exit();
}

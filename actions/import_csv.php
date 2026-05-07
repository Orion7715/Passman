<?php
require_once '../includes/protect.php'; 
require "../includes/db.php";
require "../includes/functions.php";

// التحقق من الجلسة
if (!isset($_SESSION['user_id']) || !isset($_SESSION['master_key'])) {
    die(json_encode(['status' => 'error', 'message' => 'Unauthorized access.']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $master_key = $_SESSION['master_key'];
    $import_type = $_POST['import_type'] ?? 'plain';

    // 1. التحقق من وجود الملف
    if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        die("Error: No file uploaded or upload error occurred.");
    }

    $file_path = $_FILES['csv_file']['tmp_name'];
    $file_ext = pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION);

    if (strtolower($file_ext) !== 'csv') {
        die("Error: Please upload a valid CSV file.");
    }

    // 2. فتح الملف للقرأة
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        
        // معالجة الـ BOM الخاص بـ UTF-8 إذا وجد (لضمان قراءة اللغة العربية)
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        // قراءة السطر الأول (العناوين) لتجاوزه
        fgetcsv($handle, 1000, ",", "\"", "\\");

        // بدء عملية الإدخال في قاعدة البيانات (Transaction لضمان السرعة والأمان)
        try {
            $pdo->beginTransaction();

            $sql = "INSERT INTO passwords (user_id, category, domain, username, password, email, note) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);

            while (($data = fgetcsv($handle, 1000, ",", "\"", "\\")) !== FALSE) {
                // التأكد من أن السطر يحتوي على بيانات كافية
                if (count($data) < 6) continue;

                $category = !empty($data[0]) ? $data[0] : 'General';
                $domain   = $data[1];
                $username = $data[2];
                $password = $data[3];
                $email    = $data[4];
                $note     = $data[5];

                // المنطق: إذا كان النص "عادي"، نقوم بتشفيره قبل الحفظ
                // إذا كان "مشفر"، نحفظه كما هو لأنه مفترض أن يكون مشفراً بنفس الـ Master Key
                if ($import_type === 'plain') {
                    $domain   = encrypt_with_master($domain, $master_key);
                    $password = encrypt_with_master($password, $master_key);
                    $email    = encrypt_with_master($email, $master_key);
                    $note     = encrypt_with_master($note, $master_key);
                }

                $stmt->execute([
                    $user_id,
                    $category,
                    $domain,
                    $username,
                    $password,
                    $email,
                    $note
                ]);
            }

            $pdo->commit();
            fclose($handle);

            header("Location: ../dashboard.php?import=success");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            fclose($handle);
            die("Error processing CSV: " . $e->getMessage());
        }
    } else {
        die("Error: Could not open the file.");
    }
} else {
    header("Location: ../dashboard.php");
    exit;
}


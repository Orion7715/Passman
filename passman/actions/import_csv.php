<?php
require_once '../includes/session_config.php'; 
session_start();
require "../includes/db.php";
require "../includes/functions.php";

if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"] || !isset($_SESSION['master_key'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['id'] ?? null; 
if (!$user_id) {
    $stmt_user = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt_user->execute([$_SESSION["username"]]);
    $user_id = $stmt_user->fetchColumn();
}

$master_key = $_SESSION['master_key'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["csv_file"])) {
    $file = $_FILES["csv_file"]["tmp_name"];

    if (is_uploaded_file($file) && $_FILES["csv_file"]["size"] > 0) {
        $handle = fopen($file, "r");


        $bom = fread($handle, 3);
        if ($bom != "\xEF\xBB\xBF") {
            rewind($handle); 
        }


        $header = fgetcsv($handle);
        

        if (!$header || strtolower(trim($header[0])) !== 'category') {
            fclose($handle);
            $_SESSION["flash_error"] = "Error: Invalid CSV format. The first column must be 'Category'.";
            header("Location: ../dashboard.php");
            exit;
        }

        $count = 0;
        $error_rows = 0;


        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            if (count($data) < 2 || (empty($data[0]) && empty($data[1]))) continue;


            $category = !empty($data[0]) ? $data[0] : 'General';
            $domain   = $data[1] ?? '';
            $username = $data[2] ?? '';
            $password = $data[3] ?? '';
            $email    = $data[4] ?? '';
            $note     = $data[5] ?? '';

            // Ensure critical data exists
            if (!empty($domain) && !empty($password)) {
                try {
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
                    $count++;
                } catch (Exception $e) {
                    $error_rows++;
                }
            }
        }
        
        fclose($handle);
        $_SESSION["flash_success"] = "Import complete! $count passwords added successfully.";
        if ($error_rows > 0) {
            $_SESSION["flash_error"] = "Note: $error_rows rows failed to import due to data errors.";
        }
        
    } else {
        $_SESSION["flash_error"] = "The uploaded file is empty or invalid.";
    }
}

header("Location: ../dashboard.php");
exit;

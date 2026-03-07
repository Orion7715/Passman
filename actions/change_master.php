<?php
require_once "../includes/protect.php"
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_master = $_POST['old_master'];
    $new_master = $_POST['new_master'];
    $confirm_master = $_POST['confirm_master'];


    if ($new_master !== $confirm_master) {
        $_SESSION['flash_error'] = "New passwords do not match";
        header("Location: ../dashboard.php");
        exit();
    }

    $strength_check = is_strong_password($new_master);
    if (!empty($strength_check)) {
        $_SESSION['flash_error'] = implode(" ", $strength_check); 
        header("Location: ../dashboard.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT master_password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($old_master, $user['master_password'])) {
        $_SESSION['flash_error'] = "Incorrect old master password.";
        header("Location: ../dashboard.php");
        exit();
    }


    $current_master = $_SESSION['master_key'] ?? null;
    if (!$current_master) {
        $_SESSION['flash_error'] = "Encryption key missing in session.";
        header("Location: ../dashboard.php");
        exit();
    }

    try {

        $rows = $pdo->prepare("SELECT id, domain, password, email, note FROM passwords WHERE user_id = ?");
        $rows->execute([$_SESSION['user_id']]);
        $rows_data = $rows->fetchAll();

        if (!$rows_data) {
            throw new Exception("No passwords found for this user.");
        }
	$new_master_key = hash('sha256', $new_master, true);
        $decrypted_rows = [];
        foreach ($rows_data as $row) {
            $d = decrypt_with_master($row['domain'], $current_master);
            $p = decrypt_with_master($row['password'], $current_master);
            $e = decrypt_with_master($row['email'], $current_master);
            $n = decrypt_with_master($row['note'], $current_master);

            if ($d === false || $p === false || $e === false || $n === false) {
                throw new Exception("Decryption failed for record ID ".$row['id']);
            }

            $decrypted_rows[] = [
                'id' => $row['id'],
                'domain' => $d,
                'password' => $p,
                'email' => $e,
                'note' => $n
            ];
        }

        $pdo->beginTransaction();

        foreach ($decrypted_rows as $row) {
	    $new_d = encrypt_with_master($row['domain'], $new_master_key);
	    $new_p = encrypt_with_master($row['password'], $new_master_key);
            $new_e = encrypt_with_master($row['email'], $new_master_key);
	    $new_n = encrypt_with_master($row['note'], $new_master_key);

            $update = $pdo->prepare("UPDATE passwords SET domain=?, password=?, email=?, note=? WHERE id=?");
            $update->execute([$new_d, $new_p, $new_e, $new_n, $row['id']]);
        }

        // 4. تحديث كلمة المرور الرئيسية الجديدة في جدول المستخدمين
        $new_hash = password_hash($new_master, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET master_password = ? WHERE id = ?")->execute([$new_hash, $_SESSION['user_id']]);

        $pdo->commit();

	$_SESSION['master_key'] = $new_master_key;
        $_SESSION['flash_success'] = "Master password updated successfully.";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['flash_error'] = "Error: " . $e->getMessage();
    }
    header("Location: ../dashboard.php");
    exit();
}

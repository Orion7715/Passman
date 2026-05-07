<?php
require_once '../includes/protect.php';
require_once '../includes/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];

    try {

        $pdo->beginTransaction();


        $stmt1 = $pdo->prepare("DELETE FROM passwords WHERE user_id = ?");
        $stmt1->execute([$user_id]);


        $stmt2 = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt2->execute([$user_id]);

        $pdo->commit();
        header("Location: ../logout.php");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
	$_SESSION['flash_error'] = "An error occurred while trying to delete your account.";
        header("Location: ../dashboard.php");
        exit();
    }
}

<?php
if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['master_key'])) {
    header("Location: logout.php");
    exit;
}

?>

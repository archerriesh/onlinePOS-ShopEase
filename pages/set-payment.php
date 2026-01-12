<?php
session_start();

if (!isset($_POST['payment'])) {
    header("Location: co-langsung.php");
    exit;
}

$_SESSION['payment'] = $_POST['payment'];
$_SESSION['sub_payment'] = $_POST['sub_payment'] ?? '';

header("Location: co-langsung.php");
exit;

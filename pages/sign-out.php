<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: sign-in.php");
exit;

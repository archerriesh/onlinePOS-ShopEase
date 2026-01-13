<?php
$conn = mysqli_connect("localhost", "root", "", "dbOnlinePOS");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
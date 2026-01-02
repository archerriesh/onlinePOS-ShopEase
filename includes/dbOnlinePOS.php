<?php
$conn = mysqli_connect("localhost", "root", "", "dbOnlinePOS");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
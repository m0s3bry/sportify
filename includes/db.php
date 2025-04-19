<?php
$host = "localhost";
$user = "username";  // استخدم اسم المستخدم الصحيح
$pass = "password";  // استخدم كلمة المرور الصحيحة
$db = "sportify";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
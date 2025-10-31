<?php
$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";
?>

// $host = 'localhost';  // Host
// $dbname = 'rentroomdb';  // Database name
// $usernamedb = 'root';  // Database username
// $password = '';  // Database password

// try {
//     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $usernamedb, $password);
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     // echo "เชื่อมต่อฐานข้อมูลสำเร็จ"; // เพิ่มข้อความนี้เพื่อตรวจสอบ
// } catch (PDOException $e) {
//     echo "Connection failed: " . $e->getMessage();
//     die();
// }

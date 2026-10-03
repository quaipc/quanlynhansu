<?php
// Mui gio Viet Nam: moi ham date()/time() trong he thong dung gio VN (khop may cham cong thuc te)
date_default_timezone_set('Asia/Ho_Chi_Minh');
$host = 'localhost';
$dbname = 'hrm_system';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
}

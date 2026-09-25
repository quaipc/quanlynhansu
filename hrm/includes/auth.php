<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['vai_tro']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ?page=login');
        exit;
    }
}

function requireRole($roles) {
    requireLogin();
    if (!in_array($_SESSION['vai_tro'], (array)$roles)) {
        header('Location: ?page=dashboard');
        exit;
    }
}

function getUserName() {
    return $_SESSION['ho_va_ten'] ?? '';
}

function getUserRole() {
    return $_SESSION['vai_tro'] ?? '';
}

function getMaNV() {
    return $_SESSION['ma_nv'] ?? '';
}

function getVaiTroLabel($vaiTro) {
    $labels = [
        'Admin' => 'Quản trị viên',
        'Manager' => 'Quản lý',
        'Employee' => 'Nhân viên'
    ];
    return $labels[$vaiTro] ?? $vaiTro;
}

function formatCurrency($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}

function formatDate($date) {
    if (!$date) return '';
    return date('d/m/Y', strtotime($date));
}

function formatDateTime($datetime) {
    if (!$datetime) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

<?php
require_once 'config/db.php';
require_once 'includes/auth.php';
require_once 'includes/layout.php';

$page = $_GET['page'] ?? 'dashboard';

/*
 * PHAN QUYEN DOC LAP (RBAC) THEO BAO CAO:
 * - Admin (ky thuat): UC12 co cau (phongban/chucvu), UC13 taikhoan & phan quyen, UC14 cau hinh & giam sat.
 *   KHONG can thiep nghiep vu: khong duyet nghi phep, khong tinh luong, khong quan ly ho so.
 * - Manager (dieu hanh): UC06 duyet don nghi, UC07 ho so & hop dong, UC08 dieu chuyen,
 *   UC09 bang luong, UC10 dao tao, UC11 bao cao.
 * - Employee (self-service): UC01 ho so ca nhan (gioi han), UC02 cham cong,
 *   UC03 gui don nghi, UC04 phieu luong, UC05 dang ky dao tao.
 */

switch ($page) {
    case 'login':
        require 'pages/login.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: ?page=login');
        exit;
    case 'dashboard':
        requireLogin();
        require 'pages/dashboard.php';
        break;
    // ---- ADMIN: quan tri he thong ----
    case 'phongban':      // UC12
        requireRole(['Admin']);
        require 'pages/phongban.php';
        break;
    case 'chucvu':        // UC12
        requireRole(['Admin']);
        require 'pages/chucvu.php';
        break;
    case 'taikhoan':      // UC13
        requireRole(['Admin']);
        require 'pages/taikhoan.php';
        break;
    case 'hethong':       // UC14
        requireRole(['Admin']);
        require 'pages/hethong.php';
        break;
    // ---- MANAGER: quan ly dieu hanh ----
    case 'nhanvien':      // UC07
        requireRole(['Manager']);
        require 'pages/nhanvien.php';
        break;
    case 'hopdong':       // UC07 (Manager quan ly; Employee chi xem cua minh)
        requireRole(['Manager', 'Employee']);
        require 'pages/hopdong.php';
        break;
    case 'dieuchuyen':    // UC08 - quyet dinh do Manager ban hanh
        requireRole(['Manager']);
        require 'pages/dieuchuyen.php';
        break;
    case 'donxinnghi':    // UC06 - Manager duyet; Employee gui don
        requireRole(['Manager', 'Employee']);
        require 'pages/donxinnghi.php';
        break;
    case 'bangluong':     // UC09 - Manager chay tinh & chot; Employee tra cuu
        requireRole(['Manager', 'Employee']);
        require 'pages/bangluong.php';
        break;
    case 'daotao':        // UC10 - Manager quan ly; Employee dang ky
        requireRole(['Manager', 'Employee']);
        require 'pages/daotao.php';
        break;
    case 'chamcong':      // Manager giam sat; Employee check-in/out
        requireRole(['Manager', 'Employee']);
        require 'pages/chamcong.php';
        break;
    case 'baocao':        // UC11
        requireRole(['Manager']);
        require 'pages/baocao.php';
        break;
    // ---- EMPLOYEE: cong tu phuc vu ----
    case 'hosocanhan':    // UC01 - gioi han quyen
        requireRole(['Employee']);
        require 'pages/hosocanhan.php';
        break;
    default:
        requireLogin();
        require 'pages/dashboard.php';
        break;
}

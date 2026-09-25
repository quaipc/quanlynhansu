<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenDangNhap = trim($_POST['username'] ?? '');
    $matKhau = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM TaiKhoan WHERE TenDangNhap = ? AND TrangThai = 1");
    $stmt->execute([$tenDangNhap]);
    $user = $stmt->fetch();

    if ($user && password_verify($matKhau, $user['MatKhau'])) {
        $_SESSION['user_id'] = $user['MaTaiKhoan'];
        $_SESSION['vai_tro'] = $user['VaiTro'];
        $_SESSION['ten_dang_nhap'] = $user['TenDangNhap'];

        $stmt2 = $pdo->prepare("SELECT MaNV, HoVaTen FROM NhanVien WHERE MaTaiKhoan = ?");
        $stmt2->execute([$user['MaTaiKhoan']]);
        $nv = $stmt2->fetch();
        if ($nv) {
            $_SESSION['ma_nv'] = $nv['MaNV'];
            $_SESSION['ho_va_ten'] = $nv['HoVaTen'];
        } else {
            $_SESSION['ho_va_ten'] = $user['TenDangNhap'];
        }

        header('Location: ?page=dashboard');
        exit;
    } else {
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - HRM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-800 via-slate-900 to-blue-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-building text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white">HRM System</h1>
            <p class="text-slate-400 mt-2">Hệ thống Quản lý Nhân sự</p>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6 text-center">Đăng nhập</h2>
            <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
            <?php endif; ?>
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                </button>
            </form>
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 font-medium mb-2">Tài khoản mẫu:</p>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><b>Admin:</b> admin / 123456</p>
                    <p><b>Manager:</b> manager1 / 123456</p>
                    <p><b>Employee:</b> employee1 / 123456</p>
                </div>
            </div>
        </div>
        <p class="text-center text-slate-500 text-xs mt-6">© 2026 HRM System - Cao đẳng Nghề Đà Nẵng</p>
    </div>
</body>
</html>

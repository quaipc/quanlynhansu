<?php
// UC01: Quan ly ho so ca nhan - Employee (nguyen tac Least Privilege)
// Nhan vien CHI duoc xem va sua thong tin LIEN LAC ca nhan (SĐT, Dia chi, Email).
// Cac truong phan loai quan trong (MaPhongBan, MaChucVu, TrangThaiLamViec) do Manager quan ly qua quy trinh dieu chuyen.
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_contact') {
        $pdo->prepare("UPDATE NhanVien SET SoDienThoai=?, Email=?, DiaChiThuongTru=? WHERE MaNV=?")->execute([
            trim($_POST['so_dien_thoai']), trim($_POST['email']), trim($_POST['dia_chi']), $maNV
        ]);
        header('Location: ?page=hosocanhan&msg=updated'); exit;
    } elseif ($action === 'change_pass') {
        $stmt = $pdo->prepare("SELECT MatKhau FROM TaiKhoan WHERE MaTaiKhoan=?");
        $stmt->execute([$_SESSION['user_id']]);
        $tk = $stmt->fetch();
        if ($tk && password_verify($_POST['mat_khau_cu'] ?? '', $tk['MatKhau'])) {
            if (($_POST['mat_khau_moi'] ?? '') === ($_POST['mat_khau_xn'] ?? '') && strlen($_POST['mat_khau_moi']) >= 6) {
                $pdo->prepare("UPDATE TaiKhoan SET MatKhau=? WHERE MaTaiKhoan=?")->execute([password_hash($_POST['mat_khau_moi'], PASSWORD_DEFAULT), $_SESSION['user_id']]);
                header('Location: ?page=hosocanhan&msg=pass_ok'); exit;
            } else { header('Location: ?page=hosocanhan&msg=pass_mismatch'); exit; }
        } else { header('Location: ?page=hosocanhan&msg=pass_wrong'); exit; }
    }
}

$stmt = $pdo->prepare("SELECT n.*, pb.TenPhongBan, cv.TenChucVu, t.TenDangNhap FROM NhanVien n LEFT JOIN PhongBan pb ON n.MaPhongBan=pb.MaPhongBan LEFT JOIN ChucVu cv ON n.MaChucVu=cv.MaChucVu LEFT JOIN TaiKhoan t ON n.MaTaiKhoan=t.MaTaiKhoan WHERE n.MaNV=?");
$stmt->execute([$maNV]);
$nv = $stmt->fetch();

renderHeader('Hồ sơ cá nhân');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['updated'=>['green','Cập nhật thông tin liên lạc thành công!'],'pass_ok'=>['green','Đổi mật khẩu thành công!'],'pass_wrong'=>['red','Mật khẩu cũ không đúng!'],'pass_mismatch'=>['red','Mật khẩu mới không khớp hoặc ngắn hơn 6 ký tự!']];
    [$cls,$txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}

if (!$nv) { echo '<div class="bg-yellow-50 text-yellow-700 p-4 rounded-lg">Chưa có hồ sơ nhân sự liên kết. Liên hệ Manager.</div>'; renderFooter(); return; }
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-id-card text-blue-500 mr-2"></i>Lý lịch (chỉ xem)</h3>
        <div class="space-y-2 text-sm">
            <p>Mã NV: <b><?= htmlspecialchars($nv['MaNV']) ?></b></p>
            <p>Họ tên: <b><?= htmlspecialchars($nv['HoVaTen']) ?></b></p>
            <p>Ngày sinh: <b><?= formatDate($nv['NgaySinh']) ?></b> | Giới tính: <b><?= $nv['GioiTinh'] ?></b></p>
            <p>CCCD: <b><?= htmlspecialchars($nv['CCCD']) ?></b></p>
            <p>Tài khoản: <b><?= htmlspecialchars($nv['TenDangNhap'] ?? 'N/A') ?></b></p>
            <p>Ngày vào làm: <b><?= formatDate($nv['NgayVaoLam']) ?></b></p>
        </div>
        <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm space-y-1">
            <p class="text-gray-500 text-xs uppercase font-medium">Do Manager quản lý</p>
            <p>Phòng ban: <b><?= htmlspecialchars($nv['TenPhongBan'] ?? 'N/A') ?></b> <span class="text-gray-400">(liên hệ Manager để điều chuyển)</span></p>
            <p>Chức vụ: <b><?= htmlspecialchars($nv['TenChucVu'] ?? 'N/A') ?></b></p>
            <p>Trạng thái: <b><?= $nv['TrangThaiLamViec'] ?></b></p>
        </div>
    </div>
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-address-book text-green-500 mr-2"></i>Thông tin liên lạc (được phép sửa)</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_contact">
                <div><label class="block text-sm font-medium mb-1">Số điện thoại *</label><input name="so_dien_thoai" value="<?= htmlspecialchars($nv['SoDienThoai']) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" value="<?= htmlspecialchars($nv['Email']) ?>" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">Địa chỉ thường trú</label><textarea name="dia_chi" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"><?= htmlspecialchars($nv['DiaChiThuongTru'] ?? '') ?></textarea></div>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-save mr-1"></i>Cập nhật liên lạc</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="font-semibold mb-4"><i class="fas fa-key text-yellow-500 mr-2"></i>Đổi mật khẩu</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="change_pass">
                <div><label class="block text-sm font-medium mb-1">Mật khẩu cũ *</label><input type="password" name="mat_khau_cu" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Mật khẩu mới *</label><input type="password" name="mat_khau_moi" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                    <div><label class="block text-sm font-medium mb-1">Xác nhận *</label><input type="password" name="mat_khau_xn" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                </div>
                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-key mr-1"></i>Đổi mật khẩu</button>
            </form>
        </div>
    </div>
</div>
<?php renderFooter(); ?>

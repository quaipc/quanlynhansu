<?php
$role = getUserRole();
$maNV = getMaNV();

$stats = [];
if ($role === 'Admin') {
    // Dashboard Quan tri he thong: thong so ky thuat - UC14
    $totalTK = $pdo->query("SELECT COUNT(*) FROM TaiKhoan")->fetchColumn();
    $tkActive = $pdo->query("SELECT COUNT(*) FROM TaiKhoan WHERE TrangThai=1")->fetchColumn();
    $tkLocked = $pdo->query("SELECT COUNT(*) FROM TaiKhoan WHERE TrangThai=0")->fetchColumn();
    $phongBan = $pdo->query("SELECT COUNT(*) FROM PhongBan")->fetchColumn();
    $chucVu = $pdo->query("SELECT COUNT(*) FROM ChucVu")->fetchColumn();
    $adminCount = $pdo->query("SELECT COUNT(*) FROM TaiKhoan WHERE VaiTro='Admin'")->fetchColumn();
    $managerCount = $pdo->query("SELECT COUNT(*) FROM TaiKhoan WHERE VaiTro='Manager'")->fetchColumn();
    $empCount = $pdo->query("SELECT COUNT(*) FROM TaiKhoan WHERE VaiTro='Employee'")->fetchColumn();
    $stats = [
        ['label' => 'Tài khoản đang hoạt động', 'value' => $tkActive . '/' . $totalTK, 'color' => 'border-blue-500', 'icon' => 'fas fa-user-cog', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600'],
        ['label' => 'Tài khoản bị khóa', 'value' => $tkLocked, 'color' => 'border-red-500', 'icon' => 'fas fa-user-lock', 'iconBg' => 'bg-red-100', 'iconColor' => 'text-red-600'],
        ['label' => 'Phòng ban / Chức vụ', 'value' => $phongBan . ' / ' . $chucVu, 'color' => 'border-purple-500', 'icon' => 'fas fa-sitemap', 'iconBg' => 'bg-purple-100', 'iconColor' => 'text-purple-600'],
        ['label' => 'Admin / Manager / Employee', 'value' => "$adminCount / $managerCount / $empCount", 'color' => 'border-green-500', 'icon' => 'fas fa-users-cog', 'iconBg' => 'bg-green-100', 'iconColor' => 'text-green-600'],
    ];
} elseif ($role === 'Manager') {
    // Dashboard Quan ly dieu hanh: nhan su, don cho duyet, luong, dao tao
    $totalNV = $pdo->query("SELECT COUNT(*) FROM NhanVien WHERE TrangThaiLamViec='DangLamViec'")->fetchColumn();
    $choDuyet = $pdo->query("SELECT COUNT(*) FROM DonXinNghi WHERE TrangThaiDuyet='ChoDuyet'")->fetchColumn();
    $thangNam = date('Y-m');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM BangLuong WHERE ThangNam=?");
    $stmt->execute([$thangNam]);
    $daTinhLuong = $stmt->fetchColumn();
    $stmt2 = $pdo->prepare("SELECT COUNT(*) FROM BangLuong WHERE ThangNam=? AND TrangThaiThanhToan='DaThanhToan'");
    $stmt2->execute([$thangNam]);
    $daThanhToan = $stmt2->fetchColumn();
    $khoaHoc = $pdo->query("SELECT COUNT(*) FROM KhoaHoc")->fetchColumn();
    $hopDongHL = $pdo->query("SELECT COUNT(*) FROM HopDongLaoDong WHERE TrangThaiHopDong='HieuLuc'")->fetchColumn();
    $stats = [
        ['label' => 'Nhân sự đang làm việc', 'value' => $totalNV, 'color' => 'border-blue-500', 'icon' => 'fas fa-users', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600'],
        ['label' => 'Đơn chờ duyệt', 'value' => $choDuyet, 'color' => 'border-yellow-500', 'icon' => 'fas fa-calendar-check', 'iconBg' => 'bg-yellow-100', 'iconColor' => 'text-yellow-600'],
        ['label' => "Lương $thangNam (đã tính / đã trả)", 'value' => "$daTinhLuong / $daThanhToan", 'color' => 'border-green-500', 'icon' => 'fas fa-money-bill-wave', 'iconBg' => 'bg-green-100', 'iconColor' => 'text-green-600'],
        ['label' => 'Hợp đồng hiệu lực', 'value' => $hopDongHL, 'color' => 'border-purple-500', 'icon' => 'fas fa-file-contract', 'iconBg' => 'bg-purple-100', 'iconColor' => 'text-purple-600'],
    ];
} else {
    $today = date('Y-m-d');
    $cc = $pdo->prepare("SELECT * FROM ChamCong WHERE MaNV=? AND NgayChamCong=?");
    $cc->execute([$maNV, $today]);
    $ccData = $cc->fetch();
    $soNgayCong = $pdo->prepare("SELECT COUNT(*) FROM ChamCong WHERE MaNV=? AND MONTH(NgayChamCong)=? AND YEAR(NgayChamCong)=?");
    $soNgayCong->execute([$maNV, date('m'), date('Y')]);
    $ngayCong = $soNgayCong->fetchColumn();
    $donCho = $pdo->prepare("SELECT COUNT(*) FROM DonXinNghi WHERE MaNV=? AND TrangThaiDuyet='ChoDuyet'");
    $donCho->execute([$maNV]);
    $soDonCho = $donCho->fetchColumn();
    $thangNam = date('Y-m');
    $luong = $pdo->prepare("SELECT ThucLinh FROM BangLuong WHERE MaNV=? AND ThangNam=?");
    $luong->execute([$maNV, $thangNam]);
    $luongData = $luong->fetch();
    $stats = [
        ['label' => 'Ngày công tháng này', 'value' => $ngayCong, 'color' => 'border-blue-500', 'icon' => 'fas fa-calendar', 'iconBg' => 'bg-blue-100', 'iconColor' => 'text-blue-600'],
        ['label' => 'Đơn chờ duyệt', 'value' => $soDonCho, 'color' => 'border-yellow-500', 'icon' => 'fas fa-clock', 'iconBg' => 'bg-yellow-100', 'iconColor' => 'text-yellow-600'],
        ['label' => 'Lương tháng này', 'value' => $luongData ? formatCurrency($luongData['ThucLinh']) : 'Chưa tính', 'color' => 'border-green-500', 'icon' => 'fas fa-money-bill-wave', 'iconBg' => 'bg-green-100', 'iconColor' => 'text-green-600'],
        ['label' => 'Trạng thái hôm nay', 'value' => $ccData ? 'Đã chấm công' : 'Chưa chấm công', 'color' => 'border-purple-500', 'icon' => 'fas fa-check-circle', 'iconBg' => 'bg-purple-100', 'iconColor' => 'text-purple-600'],
    ];
}

renderHeader('Dashboard');
renderStats($stats);

// Noi dung chi tiet theo vai tro
if ($role === 'Admin') {
    // Ky thuat: tai khoan moi, danh muc co cau, nhat ky truy cap
    $recentTK = $pdo->query("SELECT TenDangNhap, VaiTro, TrangThai, NgayTao FROM TaiKhoan ORDER BY NgayTao DESC LIMIT 5")->fetchAll();
    $coCau = $pdo->query("SELECT pb.MaPhongBan, pb.TenPhongBan, COUNT(n.MaNV) AS SoNV FROM PhongBan pb LEFT JOIN NhanVien n ON pb.MaPhongBan=n.MaPhongBan GROUP BY pb.MaPhongBan, pb.TenPhongBan")->fetchAll();
?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-user-plus text-blue-500 mr-2"></i>Tài khoản mới nhất</h3>
        <div class="space-y-3">
            <?php foreach ($recentTK as $tk): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-sm"><?= htmlspecialchars($tk['TenDangNhap']) ?> <span class="text-xs text-gray-500">(<?= $tk['VaiTro'] ?>)</span></p>
                    <p class="text-xs text-gray-500">Tạo: <?= formatDateTime($tk['NgayTao']) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full <?= $tk['TrangThai'] ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>"><?= $tk['TrangThai'] ? 'Hoạt động' : 'Khóa' ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="?page=taikhoan" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Quản trị tài khoản & phân quyền →</a>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-sitemap text-purple-500 mr-2"></i>Cơ cấu tổ chức</h3>
        <div class="space-y-3">
            <?php foreach ($coCau as $pb): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <p class="font-medium text-sm"><?= htmlspecialchars($pb['TenPhongBan']) ?> <span class="text-xs text-gray-400">(<?= $pb['MaPhongBan'] ?>)</span></p>
                <span class="text-xs px-2 py-1 rounded-full bg-purple-100 text-purple-700"><?= $pb['SoNV'] ?> NV</span>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="?page=hethong" class="inline-block mt-4 text-sm text-blue-600 hover:underline">Cấu hình & giám sát hệ thống →</a>
    </div>
</div>
<?php
} elseif ($role === 'Manager') {
    $recentDon = $pdo->query("SELECT d.*, n.HoVaTen FROM DonXinNghi d JOIN NhanVien n ON d.MaNV=n.MaNV ORDER BY d.NgayTao DESC LIMIT 5")->fetchAll();
    $luongMoi = $pdo->query("SELECT l.*, n.HoVaTen FROM BangLuong l JOIN NhanVien n ON l.MaNV=n.MaNV ORDER BY l.NgayTao DESC LIMIT 5")->fetchAll();
?>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="?page=nhanvien" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition"><i class="fas fa-users text-2xl text-blue-500"></i><p class="text-sm font-medium mt-2">Hồ sơ & Hợp đồng</p></a>
    <a href="?page=donxinnghi" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition"><i class="fas fa-calendar-check text-2xl text-yellow-500"></i><p class="text-sm font-medium mt-2">Duyệt đơn nghỉ</p></a>
    <a href="?page=dieuchuyen" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition"><i class="fas fa-exchange-alt text-2xl text-purple-500"></i><p class="text-sm font-medium mt-2">Điều chuyển</p></a>
    <a href="?page=bangluong" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-md transition"><i class="fas fa-money-bill-wave text-2xl text-green-500"></i><p class="text-sm font-medium mt-2">Tính lương</p></a>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-calendar-check text-yellow-500 mr-2"></i>Đơn xin nghỉ gần đây</h3>
        <div class="space-y-3">
            <?php if (empty($recentDon)): ?>
            <p class="text-gray-400 text-sm text-center py-4">Không có đơn nào</p>
            <?php else: ?>
            <?php foreach ($recentDon as $d): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-sm"><?= htmlspecialchars($d['HoVaTen']) ?></p>
                    <p class="text-xs text-gray-500"><?= formatDate($d['TuNgay']) ?> - <?= formatDate($d['DenNgay']) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full <?= $d['TrangThaiDuyet'] === 'DaDuyet' ? 'bg-green-100 text-green-700' : ($d['TrangThaiDuyet'] === 'TuChoi' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?>">
                    <?= $d['TrangThaiDuyet'] === 'DaDuyet' ? 'Đã duyệt' : ($d['TrangThaiDuyet'] === 'TuChoi' ? 'Từ chối' : 'Chờ duyệt') ?>
                </span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-money-bill-wave text-green-500 mr-2"></i>Bảng lương mới nhất</h3>
        <div class="space-y-3">
            <?php if (empty($luongMoi)): ?>
            <p class="text-gray-400 text-sm text-center py-4">Chưa có bảng lương</p>
            <?php else: ?>
            <?php foreach ($luongMoi as $l): ?>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="font-medium text-sm"><?= htmlspecialchars($l['HoVaTen']) ?> <span class="text-xs text-gray-400">(<?= $l['ThangNam'] ?>)</span></p>
                    <p class="text-xs text-gray-500"><?= formatCurrency($l['ThucLinh']) ?></p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full <?= $l['TrangThaiThanhToan'] === 'DaThanhToan' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>"><?= $l['TrangThaiThanhToan'] === 'DaThanhToan' ? 'Đã trả' : 'Chưa trả' ?></span>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
} else {
    $today = date('Y-m-d');
    $cc = $pdo->prepare("SELECT * FROM ChamCong WHERE MaNV=? AND NgayChamCong=?");
    $cc->execute([$maNV, $today]);
    $ccData = $cc->fetch();
?>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-clock text-blue-500 mr-2"></i>Chấm công hôm nay</h3>
        <?php if ($ccData): ?>
        <div class="space-y-2">
            <p class="text-sm">Giờ vào: <b class="text-green-600"><?= $ccData['ThoiGianVao'] ?></b></p>
            <p class="text-sm">Giờ ra: <b class="text-red-600"><?= $ccData['ThoiGianRa'] ?: 'Chưa check-out' ?></b></p>
            <p class="text-sm">Trạng thái: <span class="px-2 py-1 rounded-full text-xs <?= $ccData['TrangThaiCong'] === 'DungGio' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>"><?= $ccData['TrangThaiCong'] ?></span></p>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-sm text-center py-4">Bạn chưa chấm công hôm nay</p>
        <a href="?page=chamcong" class="inline-block mt-2 text-sm text-blue-600 hover:underline">Đi đến chấm công →</a>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-info-circle text-purple-500 mr-2"></i>Thông tin của tôi</h3>
        <?php
        $nvInfo = $pdo->prepare("SELECT n.*, pb.TenPhongBan, cv.TenChucVu FROM NhanVien n LEFT JOIN PhongBan pb ON n.MaPhongBan=pb.MaPhongBan LEFT JOIN ChucVu cv ON n.MaChucVu=cv.MaChucVu WHERE n.MaNV=?");
        $nvInfo->execute([$maNV]);
        $nvInfo = $nvInfo->fetch();
        if ($nvInfo):
        ?>
        <div class="space-y-2 text-sm">
            <p>Họ tên: <b><?= htmlspecialchars($nvInfo['HoVaTen']) ?></b></p>
            <p>Phòng ban: <b><?= htmlspecialchars($nvInfo['TenPhongBan'] ?? 'N/A') ?></b></p>
            <p>Chức vụ: <b><?= htmlspecialchars($nvInfo['TenChucVu'] ?? 'N/A') ?></b></p>
            <p>Ngày vào: <b><?= formatDate($nvInfo['NgayVaoLam']) ?></b></p>
        </div>
        <a href="?page=hosocanhan" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Cập nhật thông tin liên lạc →</a>
        <?php endif; ?>
    </div>
</div>
<?php
}
renderFooter();

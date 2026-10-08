<?php
$role = getUserRole();
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'checkin' && $role === 'Employee') {
        $today = date('Y-m-d');
        $check = $pdo->prepare("SELECT ID_ChamCong FROM ChamCong WHERE MaNV=? AND NgayChamCong=?");
        $check->execute([$maNV, $today]);
        if ($check->fetch()) {
            // TC06: chan diem danh trung lap trong cung ngay
            header('Location: ?page=chamcong&msg=duplicate');
            exit;
        }
        // Moc gio chuan lay tu tham so he thong, mac dinh 08:30 theo bao cao
        $paramsFile = __DIR__ . '/../config/params.php';
        $gioChuan = '08:30';
        if (file_exists($paramsFile)) { $p = include $paramsFile; if (!empty($p['gio_chuan'])) $gioChuan = $p['gio_chuan']; }
        $now = date('H:i:s');
        $trangThai = (strtotime($now) > strtotime($gioChuan . ':00')) ? 'DiMuon' : 'DungGio';
        $pdo->prepare("INSERT INTO ChamCong(MaNV, NgayChamCong, ThoiGianVao, TrangThaiCong) VALUES(?,?,?,?)")->execute([$maNV, $today, $now, $trangThai]);
        header('Location: ?page=chamcong&msg=checkin');
        exit;
    } elseif ($action === 'checkout' && $role === 'Employee') {
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $pdo->prepare("UPDATE ChamCong SET ThoiGianRa=? WHERE MaNV=? AND NgayChamCong=? AND ThoiGianRa IS NULL")->execute([$now, $maNV, $today]);
        header('Location: ?page=chamcong&msg=checkout');
        exit;
    } elseif ($action === 'approve_cc' && $role === 'Manager') {
        // Manager duyet / tu choi tung ban ghi cham cong
        $pdo->prepare("UPDATE ChamCong SET TrangThaiDuyet=?, MaNguoiDuyet=? WHERE ID_ChamCong=?")->execute([$_POST['trang_thai_duyet'], $maNV, $_POST['id_cham_cong']]);
        header('Location: ?page=chamcong&msg=approved');
        exit;
    } elseif ($action === 'approve_all' && $role === 'Manager') {
        // Duyet hang loat cac ban ghi dang cho
        $pdo->prepare("UPDATE ChamCong SET TrangThaiDuyet='DaDuyet', MaNguoiDuyet=? WHERE TrangThaiDuyet='ChoDuyet'")->execute([$maNV]);
        header('Location: ?page=chamcong&msg=approved_all');
        exit;
    }
}

// Bo loc thoi gian (GET de giu filter khi reload / chia se link)
$fTuNgay = $_GET['tu_ngay'] ?? '';
$fDenNgay = $_GET['den_ngay'] ?? '';
$fThang = $_GET['thang'] ?? '';
$fTrangThai = $_GET['trang_thai'] ?? '';
$fTenNV = trim($_GET['ten_nv'] ?? '');
$fDuyet = $_GET['duyet'] ?? '';
if (!in_array($fDuyet, ['ChoDuyet', 'DaDuyet', 'TuChoi'])) $fDuyet = '';

// Xem chấm công: Employee xem lich su cua minh; Manager giam sat TOAN BO (phuc vu UC11)
if ($role === 'Employee') {
    $where = ["c.MaNV=?"]; $params = [$maNV];
    if ($fThang !== '' && preg_match('/^\d{4}-\d{2}$/', $fThang)) { $where[] = "DATE_FORMAT(c.NgayChamCong,'%Y-%m')=?"; $params[] = $fThang; }
    if ($fTuNgay !== '') { $where[] = "c.NgayChamCong>=?"; $params[] = $fTuNgay; }
    if ($fDenNgay !== '') { $where[] = "c.NgayChamCong<=?"; $params[] = $fDenNgay; }
    if ($fTrangThai !== '') { $where[] = "c.TrangThaiCong=?"; $params[] = $fTrangThai; }
    $stmt = $pdo->prepare("SELECT c.* FROM ChamCong c WHERE " . implode(' AND ', $where) . " ORDER BY c.NgayChamCong DESC LIMIT 100");
    $stmt->execute($params);
    $ccList = $stmt->fetchAll();
} else {
    $where = ["1=1"]; $params = [];
    if ($fThang !== '' && preg_match('/^\d{4}-\d{2}$/', $fThang)) { $where[] = "DATE_FORMAT(c.NgayChamCong,'%Y-%m')=?"; $params[] = $fThang; }
    if ($fTuNgay !== '') { $where[] = "c.NgayChamCong>=?"; $params[] = $fTuNgay; }
    if ($fDenNgay !== '') { $where[] = "c.NgayChamCong<=?"; $params[] = $fDenNgay; }
    if ($fTrangThai !== '') { $where[] = "c.TrangThaiCong=?"; $params[] = $fTrangThai; }
    if ($fDuyet !== '') { $where[] = "c.TrangThaiDuyet=?"; $params[] = $fDuyet; }
    if ($fTenNV !== '') { $where[] = "n.HoVaTen LIKE ?"; $params[] = "%$fTenNV%"; }
    $stmt = $pdo->prepare("SELECT c.*, n.HoVaTen FROM ChamCong c JOIN NhanVien n ON c.MaNV=n.MaNV WHERE " . implode(' AND ', $where) . " ORDER BY c.NgayChamCong DESC LIMIT 500");
    $stmt->execute($params);
    $ccList = $stmt->fetchAll();
}

$today = date('Y-m-d');
$ccToday = null;
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT * FROM ChamCong WHERE MaNV=? AND NgayChamCong=?");
    $stmt->execute([$maNV, $today]);
    $ccToday = $stmt->fetch();
}

renderHeader('Chấm công');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['checkin' => ['green', 'Check-in thành công! Bản ghi đang chờ Manager duyệt.'], 'checkout' => ['blue', 'Check-out thành công!'], 'duplicate' => ['red', 'Bạn đã thực hiện check-in hôm nay'], 'approved' => ['green', 'Duyệt chấm công thành công!'], 'approved_all' => ['green', 'Đã duyệt hàng loạt các bản ghi chờ!']];
    [$cls, $txt] = $texts[$msg] ?? ['gray', ''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}

// Dem ban ghi cho duyet cho Manager
$choDuyetCount = 0;
if ($role === 'Manager') {
    $choDuyetCount = $pdo->query("SELECT COUNT(*) FROM ChamCong WHERE TrangThaiDuyet='ChoDuyet'")->fetchColumn();
}
?>

<?php if ($role === 'Employee'): ?>
<div class="bg-white rounded-xl shadow-sm p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Chấm công hôm nay - <?= date('d/m/Y') ?></h3>
    <?php if ($ccToday): ?>
    <div class="flex items-center gap-6">
        <div class="text-center p-4 bg-green-50 rounded-xl">
            <p class="text-sm text-gray-500">Giờ vào</p>
            <p class="text-2xl font-bold text-green-600"><?= $ccToday['ThoiGianVao'] ?></p>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-xl">
            <p class="text-sm text-gray-500">Giờ ra</p>
            <p class="text-2xl font-bold text-red-600"><?= $ccToday['ThoiGianRa'] ?: '--:--' ?></p>
        </div>
        <div>
            <span class="px-3 py-1 rounded-full text-sm <?= $ccToday['TrangThaiCong'] === 'DungGio' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                <?= match($ccToday['TrangThaiCong']) { 'DungGio' => 'Đúng giờ', 'DiMuon' => 'Đi muộn', 'VeSom' => 'Về sớm', default => $ccToday['TrangThaiCong'] } ?>
            </span>
        </div>
    </div>
    <?php if (!$ccToday['ThoiGianRa']): ?>
    <form method="POST" class="mt-4"><input type="hidden" name="action" value="checkout">
        <button class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold"><i class="fas fa-sign-out-alt mr-2"></i>Check-out</button>
    </form>
    <?php endif; ?>
    <?php else: ?>
    <form method="POST"><input type="hidden" name="action" value="checkin">
        <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-lg font-semibold text-lg"><i class="fas fa-sign-in-alt mr-2"></i>Check-in ngay</button>
    </form>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php $ttLabels = ['DungGio' => 'Đúng giờ', 'DiMuon' => 'Đi muộn', 'VeSom' => 'Về sớm', 'NghiKhongPhep' => 'Nghỉ không phép', 'NghiCoPhep' => 'Nghỉ có phép']; ?>
<form method="GET" class="bg-white p-4 rounded-xl shadow-sm mb-4 flex flex-wrap items-end gap-3">
    <input type="hidden" name="page" value="chamcong">
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Từ ngày</label><input type="date" name="tu_ngay" value="<?= htmlspecialchars($fTuNgay) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Đến ngày</label><input type="date" name="den_ngay" value="<?= htmlspecialchars($fDenNgay) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Tháng</label><input type="month" name="thang" value="<?= htmlspecialchars($fThang) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Trạng thái</label>
        <select name="trang_thai" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">-- Tất cả --</option>
            <?php foreach ($ttLabels as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= $fTrangThai === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($role !== 'Employee'): ?>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Tên nhân viên</label><input type="text" name="ten_nv" value="<?= htmlspecialchars($fTenNV) ?>" placeholder="Tìm theo tên..." class="border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-xs font-medium text-gray-500 mb-1">Duyệt công</label>
        <select name="duyet" class="border rounded-lg px-3 py-2 text-sm">
            <option value="">-- Tất cả --</option>
            <option value="ChoDuyet" <?= $fDuyet === 'ChoDuyet' ? 'selected' : '' ?>>Chờ duyệt</option>
            <option value="DaDuyet" <?= $fDuyet === 'DaDuyet' ? 'selected' : '' ?>>Đã duyệt</option>
            <option value="TuChoi" <?= $fDuyet === 'TuChoi' ? 'selected' : '' ?>>Từ chối</option>
        </select>
    </div>
    <?php endif; ?>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-filter mr-1"></i>Lọc</button>
    <a href="?page=chamcong" class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">Xóa lọc</a>
</form>
<?php if ($role === 'Manager' && $choDuyetCount > 0): ?>
<div class="flex items-center justify-between bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 mb-4 text-sm">
    <span class="text-yellow-800"><i class="fas fa-exclamation-circle mr-2"></i>Có <b><?= $choDuyetCount ?></b> bản ghi chấm công đang chờ duyệt (lương chỉ tính công đã duyệt).</span>
    <form method="POST" class="inline" onsubmit="return confirm('Duyệt tất cả <?= $choDuyetCount ?> bản ghi chờ?')"><input type="hidden" name="action" value="approve_all">
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-1.5 rounded-lg text-sm"><i class="fas fa-check-double mr-1"></i>Duyệt tất cả</button>
    </form>
</div>
<?php endif; ?>
<p class="text-sm text-gray-500 mb-3">Hiển thị <b><?= count($ccList) ?></b> bản ghi chấm công</p>

<?php
$duyetBadge = function($tt) {
    return match($tt) {
        'DaDuyet' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đã duyệt</span>',
        'TuChoi' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Từ chối</span>',
        default => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Chờ duyệt</span>'
    };
};
$headers = $role === 'Employee' ? ['Ngày', 'Giờ vào', 'Giờ ra', 'Trạng thái', 'Duyệt'] : ['Nhân viên', 'Ngày', 'Giờ vào', 'Giờ ra', 'Trạng thái', 'Duyệt', 'Hành động'];
$rows = [];
foreach ($ccList as $cc) {
    $trangThai = match($cc['TrangThaiCong']) { 'DungGio' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đúng giờ</span>', 'DiMuon' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Đi muộn</span>', 'VeSom' => '<span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700">Về sớm</span>', default => $cc['TrangThaiCong'] };
    if ($role === 'Employee') {
        $rows[] = [formatDate($cc['NgayChamCong']), $cc['ThoiGianVao'] ?? '', $cc['ThoiGianRa'] ?? '', $trangThai, $duyetBadge($cc['TrangThaiDuyet'])];
    } else {
        $duyetAction = '';
        if (($cc['TrangThaiDuyet'] ?? 'ChoDuyet') === 'ChoDuyet') {
            $duyetAction = '<form method="POST" class="inline"><input type="hidden" name="action" value="approve_cc"><input type="hidden" name="id_cham_cong" value="'.$cc['ID_ChamCong'].'"><button name="trang_thai_duyet" value="DaDuyet" class="text-green-500 hover:text-green-700 mr-1" title="Duyệt"><i class="fas fa-check-circle"></i></button><button name="trang_thai_duyet" value="TuChoi" class="text-red-500 hover:text-red-700" title="Từ chối"><i class="fas fa-times-circle"></i></button></form>';
        }
        $rows[] = [htmlspecialchars($cc['HoVaTen'] ?? ''), formatDate($cc['NgayChamCong']), $cc['ThoiGianVao'] ?? '', $cc['ThoiGianRa'] ?? '', $trangThai, $duyetBadge($cc['TrangThaiDuyet'] ?? 'ChoDuyet'), $duyetAction];
    }
}
renderTable($headers, $rows);
renderFooter();

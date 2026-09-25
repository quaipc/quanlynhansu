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
    }
}

// Xem chấm công: Employee xem lich su cua minh; Manager giam sat TOAN BO (phuc vu UC11)
if ($role === 'Employee') {
    $ccList = $pdo->prepare("SELECT * FROM ChamCong WHERE MaNV=? ORDER BY NgayChamCong DESC LIMIT 30");
    $ccList->execute([$maNV]);
    $ccList = $ccList->fetchAll();
} else {
    $stmt = $pdo->query("SELECT c.*, n.HoVaTen FROM ChamCong c JOIN NhanVien n ON c.MaNV=n.MaNV ORDER BY c.NgayChamCong DESC LIMIT 200");
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
    $texts = ['checkin' => ['green', 'Check-in thành công!'], 'checkout' => ['blue', 'Check-out thành công!'], 'duplicate' => ['red', 'Bạn đã thực hiện check-in hôm nay']];
    [$cls, $txt] = $texts[$msg] ?? ['gray', ''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
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

<?php
$headers = $role === 'Employee' ? ['Ngày', 'Giờ vào', 'Giờ ra', 'Trạng thái'] : ['Nhân viên', 'Ngày', 'Giờ vào', 'Giờ ra', 'Trạng thái'];
$rows = [];
foreach ($ccList as $cc) {
    $trangThai = match($cc['TrangThaiCong']) { 'DungGio' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đúng giờ</span>', 'DiMuon' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Đi muộn</span>', 'VeSom' => '<span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700">Về sớm</span>', default => $cc['TrangThaiCong'] };
    if ($role === 'Employee') {
        $rows[] = [formatDate($cc['NgayChamCong']), $cc['ThoiGianVao'] ?? '', $cc['ThoiGianRa'] ?? '', $trangThai];
    } else {
        $rows[] = [htmlspecialchars($cc['HoVaTen'] ?? ''), formatDate($cc['NgayChamCong']), $cc['ThoiGianVao'] ?? '', $cc['ThoiGianRa'] ?? '', $trangThai];
    }
}
renderTable($headers, $rows);
renderFooter();

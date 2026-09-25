<?php
// UC14: Cau hinh tham so & Giam sat he thong - Admin only
$paramsFile = __DIR__ . '/../config/params.php';
$defaultParams = ['gio_chuan' => '08:30', 'ngay_cong_chuan' => 26, 'ten_cong_ty' => 'HRM System'];
$params = $defaultParams;
if (file_exists($paramsFile)) {
    $loaded = include $paramsFile;
    if (is_array($loaded)) $params = array_merge($defaultParams, $loaded);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_params') {
    $params = [
        'gio_chuan' => $_POST['gio_chuan'] ?? '08:30',
        'ngay_cong_chuan' => (int)($_POST['ngay_cong_chuan'] ?? 26),
        'ten_cong_ty' => trim($_POST['ten_cong_ty'] ?? 'HRM System'),
    ];
    file_put_contents($paramsFile, "<?php\nreturn " . var_export($params, true) . ";\n");
    header('Location: ?page=hethong&msg=saved'); exit;
}

// Giam sat: dem so ban ghi 11 bang
$tables = ['TaiKhoan','ChucVu','PhongBan','NhanVien','DieuChuyenNhanVien','HopDongLaoDong','ChamCong','DonXinNghi','BangLuong','KhoaHoc','ChiTietDaoTao'];
$counts = [];
foreach ($tables as $t) {
    try { $counts[$t] = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn(); }
    catch (Exception $e) { $counts[$t] = ' lỗi'; }
}
$dbSize = $pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1024, 2) AS kb FROM information_schema.tables WHERE table_schema = 'hrm_system'")->fetchColumn();
$mysqlVer = $pdo->query("SELECT VERSION()")->fetchColumn();

renderHeader('Cấu hình tham số & Giám sát hệ thống');
if (($_GET['msg'] ?? '') === 'saved') echo '<div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4">Lưu tham số hệ thống thành công!</div>';
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-cogs text-blue-500 mr-2"></i>Tham số hệ thống</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="save_params">
            <div><label class="block text-sm font-medium mb-1">Tên công ty / hệ thống</label><input name="ten_cong_ty" value="<?= htmlspecialchars($params['ten_cong_ty']) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Giờ chuẩn check-in (mốc Đúng giờ/Đi muộn)</label><input type="time" name="gio_chuan" value="<?= htmlspecialchars($params['gio_chuan']) ?>" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">Số ngày công chuẩn / tháng</label><input type="number" name="ngay_cong_chuan" value="<?= (int)$params['ngay_cong_chuan'] ?>" min="1" max="31" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            </div>
            <p class="text-xs text-gray-500">Các mốc này được dùng trong UC02 (so sánh giờ check-in với mốc <?= htmlspecialchars($params['gio_chuan']) ?>) và UC09 (công thức Thực lĩnh = LươngCB / Ngày chuẩn × Ngày thực tế + PC + Thưởng − Khấu trừ).</p>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-save mr-1"></i>Lưu tham số</button>
        </form>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-heartbeat text-green-500 mr-2"></i>Giám sát vận hành</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div class="p-3 bg-gray-50 rounded-lg"><p class="text-gray-500">PHP version</p><p class="font-bold"><?= phpversion() ?></p></div>
            <div class="p-3 bg-gray-50 rounded-lg"><p class="text-gray-500">MySQL version</p><p class="font-bold"><?= htmlspecialchars($mysqlVer) ?></p></div>
            <div class="p-3 bg-gray-50 rounded-lg"><p class="text-gray-500">Dung lượng CSDL</p><p class="font-bold"><?= $dbSize ?> KB</p></div>
            <div class="p-3 bg-gray-50 rounded-lg"><p class="text-gray-500">Số bảng</p><p class="font-bold">11 bảng chuẩn hóa</p></div>
        </div>
        <h4 class="font-medium mt-5 mb-2 text-sm">Số bản ghi theo bảng:</h4>
        <div class="space-y-1 text-sm max-h-64 overflow-y-auto">
            <?php foreach ($counts as $tbl => $cnt): ?>
            <div class="flex justify-between px-3 py-1.5 bg-gray-50 rounded"><span><?= $tbl ?></span><b><?= $cnt ?></b></div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6 mt-6">
    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-history text-purple-500 mr-2"></i>Nhật ký tài khoản (giám sát an ninh)</h3>
    <?php
    $logs = $pdo->query("SELECT TenDangNhap, VaiTro, TrangThai, NgayTao FROM TaiKhoan ORDER BY NgayTao DESC LIMIT 10")->fetchAll();
    $headers = ['Tài khoản', 'Vai trò', 'Trạng thái', 'Ngày tạo'];
    $rows = [];
    foreach ($logs as $l) $rows[] = [htmlspecialchars($l['TenDangNhap']), $l['VaiTro'], $l['TrangThai'] ? 'Hoạt động' : 'Khóa', formatDateTime($l['NgayTao'])];
    renderTable($headers, $rows);
    ?>
</div>
<?php renderFooter(); ?>

<?php
$role = getUserRole();
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Manager') {
    $action = $_POST['action'] ?? '';
    if ($action === 'calculate') {
        // Cong thuc bao cao 3.3: ThucLinh = (LuongCoBan / SoNgayCongChuan) * SoNgayThucTe + PhuCap + Thuong - KhauTru
        $thangNam = $_POST['thang_nam'];
        $paramsFile = __DIR__ . '/../config/params.php';
        $soNgayChuan = 26;
        if (file_exists($paramsFile)) { $p = include $paramsFile; if (!empty($p['ngay_cong_chuan'])) $soNgayChuan = (int)$p['ngay_cong_chuan']; }
        // Giu lai trang thai khoa so chi tra: NV da tra roi thi tinh lai van giu DaThanhToan
        $stmtPaid = $pdo->prepare("SELECT MaNV FROM BangLuong WHERE ThangNam=? AND TrangThaiThanhToan='DaThanhToan'");
        $stmtPaid->execute([$thangNam]);
        $paidNVs = $stmtPaid->fetchAll(PDO::FETCH_COLUMN);
        $pdo->prepare("DELETE FROM BangLuong WHERE ThangNam=?")->execute([$thangNam]);
        $nvList = $pdo->query("SELECT n.MaNV, n.HoVaTen, h.LuongCoBan FROM NhanVien n JOIN HopDongLaoDong h ON n.MaNV=h.MaNV WHERE n.TrangThaiLamViec='DangLamViec' AND h.TrangThaiHopDong='HieuLuc'")->fetchAll();
        $stmtIns = $pdo->prepare("INSERT INTO BangLuong(MaNV,ThangNam,SoNgayCongChuan,SoNgayThucTe,LuongCoBan,ThucLinh,TrangThaiThanhToan) VALUES(?,?,?,?,?,?,?)");
        foreach ($nvList as $nv) {
            // Chi tinh nhung ngay cong da duoc Manager duyet
            $cc = $pdo->prepare("SELECT COUNT(*) FROM ChamCong WHERE MaNV=? AND MONTH(NgayChamCong)=? AND YEAR(NgayChamCong)=? AND TrangThaiCong IN ('DungGio','DiMuon') AND TrangThaiDuyet='DaDuyet'");
            $cc->execute([$nv['MaNV'], explode('-', $thangNam)[1], explode('-', $thangNam)[0]]);
            $soNgayCong = $cc->fetchColumn();
            $luongCB = $nv['LuongCoBan'];
            $thucLinh = ($luongCB / $soNgayChuan) * $soNgayCong;
            $trangThaiTT = in_array($nv['MaNV'], $paidNVs) ? 'DaThanhToan' : 'ChuaThanhToan';
            $stmtIns->execute([$nv['MaNV'], $thangNam, $soNgayChuan, $soNgayCong, $luongCB, $thucLinh, $trangThaiTT]);
        }
        header('Location: ?page=bangluong&msg=calculated');
        exit;
    } elseif ($action === 'pay') {
        $pdo->prepare("UPDATE BangLuong SET TrangThaiThanhToan='DaThanhToan' WHERE MaBangLuong=?")->execute([$_POST['ma_bang_luong']]);
        header('Location: ?page=bangluong&msg=paid');
        exit;
    }
}

// Lấy danh sách lương
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT * FROM BangLuong WHERE MaNV=? ORDER BY ThangNam DESC");
    $stmt->execute([$maNV]);
    $luongList = $stmt->fetchAll();
} else {
    $luongList = $pdo->query("SELECT l.*, n.HoVaTen FROM BangLuong l JOIN NhanVien n ON l.MaNV=n.MaNV ORDER BY l.ThangNam DESC, n.HoVaTen")->fetchAll();
}

renderHeader('Bảng lương');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['calculated' => ['green', 'Tính lương thành công!'], 'paid' => ['blue', 'Đánh dấu đã thanh toán!']];
    [$cls, $txt] = $texts[$msg] ?? ['gray', ''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<?php if ($role === 'Manager'): ?>
<form method="POST" class="flex items-end gap-3 mb-6 bg-white p-4 rounded-xl shadow-sm">
    <input type="hidden" name="action" value="calculate">
    <div><label class="block text-sm font-medium mb-1">Tháng/Năm</label><input type="month" name="thang_nam" value="<?= date('Y-m') ?>" required class="border rounded-lg px-3 py-2 text-sm"></div>
    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-calculator mr-1"></i>Chạy tính lương</button>
</form>
<?php endif; ?>

<?php
$headers = $role === 'Employee' ? ['Tháng', 'Ngày công', 'Lương cơ bản', 'Thực lĩnh', 'Trạng thái'] : ['Nhân viên', 'Tháng', 'Ngày công', 'Lương CB', 'Thực lĩnh', 'Trạng thái', 'Hành động'];
$rows = [];
foreach ($luongList as $l) {
    $badge = $l['TrangThaiThanhToan'] === 'DaThanhToan' ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đã thanh toán</span>' : '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Chưa thanh toán</span>';
    $action = '';
    if ($role === 'Manager' && $l['TrangThaiThanhToan'] === 'ChuaThanhToan') {
        $action = '<form method="POST" class="inline"><input type="hidden" name="action" value="pay"><input type="hidden" name="ma_bang_luong" value="'.$l['MaBangLuong'].'"><button class="text-green-500 hover:text-green-700" title="Đánh dấu đã trả"><i class="fas fa-check-double"></i></button></form>';
    }
    if ($role === 'Employee') {
        $rows[] = [$l['ThangNam'], $l['SoNgayThucTe'] . '/' . $l['SoNgayCongChuan'], formatCurrency($l['LuongCoBan']), formatCurrency($l['ThucLinh']), $badge];
    } else {
        $rows[] = [htmlspecialchars($l['HoVaTen'] ?? ''), $l['ThangNam'], $l['SoNgayThucTe'] . '/' . $l['SoNgayCongChuan'], formatCurrency($l['LuongCoBan']), formatCurrency($l['ThucLinh']), $badge, $action];
    }
}
renderTable($headers, $rows, 'Chưa có bảng lương');
renderFooter();

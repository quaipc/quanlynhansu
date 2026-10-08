<?php
// UC11: Bao cao thong ke toan dien doanh nghiep - Manager
$thangNam = $_GET['thang'] ?? date('Y-m');
if (!preg_match('/^\d{4}-\d{2}$/', $thangNam)) $thangNam = date('Y-m');
// Chi tiet drill-down: xem AI thuoc trang thai chuyen can nao
$chiTiet = $_GET['chitiet'] ?? '';
$validTT = ['DungGio', 'DiMuon', 'VeSom', 'NghiKhongPhep', 'NghiCoPhep'];
if (!in_array($chiTiet, $validTT)) $chiTiet = '';

// 1. Nhan su theo phong ban
$nvPhongBan = $pdo->query("SELECT pb.TenPhongBan, COUNT(n.MaNV) AS SoNV FROM PhongBan pb LEFT JOIN NhanVien n ON pb.MaPhongBan=n.MaPhongBan AND n.TrangThaiLamViec='DangLamViec' GROUP BY pb.MaPhongBan, pb.TenPhongBan")->fetchAll();

// 2. Chuyen can thang
$stmtCC = $pdo->prepare("SELECT TrangThaiCong, COUNT(*) AS SoLuong FROM ChamCong WHERE DATE_FORMAT(NgayChamCong,'%Y-%m')=? GROUP BY TrangThaiCong");
$stmtCC->execute([$thangNam]);
$chuyenCan = $stmtCC->fetchAll();

// 2b. Danh sach chi tiet theo trang thai (drill-down)
$chiTietList = [];
if ($chiTiet !== '') {
    $stmtDT = $pdo->prepare("SELECT c.NgayChamCong, c.ThoiGianVao, c.ThoiGianRa, n.MaNV, n.HoVaTen FROM ChamCong c JOIN NhanVien n ON c.MaNV=n.MaNV WHERE DATE_FORMAT(c.NgayChamCong,'%Y-%m')=? AND c.TrangThaiCong=? ORDER BY c.NgayChamCong DESC");
    $stmtDT->execute([$thangNam, $chiTiet]);
    $chiTietList = $stmtDT->fetchAll();
}

// 3. Quy luong thang
$quyLuong = $pdo->prepare("SELECT COUNT(*) AS SoPhieu, COALESCE(SUM(ThucLinh),0) AS TongQuy, COALESCE(SUM(CASE WHEN TrangThaiThanhToan='DaThanhToan' THEN ThucLinh ELSE 0 END),0) AS DaTra FROM BangLuong WHERE ThangNam=?");
$quyLuong->execute([$thangNam]);
$quyLuong = $quyLuong->fetch();

// 4. Don nghi theo trang thai
$donNghi = $pdo->query("SELECT TrangThaiDuyet, COUNT(*) AS SoLuong FROM DonXinNghi GROUP BY TrangThaiDuyet")->fetchAll();

// 5. Hop dong theo loai
$hopDong = $pdo->query("SELECT LoaiHopDong, COUNT(*) AS SoLuong FROM HopDongLaoDong WHERE TrangThaiHopDong='HieuLuc' GROUP BY LoaiHopDong")->fetchAll();

// 6. Dieu chuyen gan day
$dieuchuyen = $pdo->query("SELECT dc.*, n.HoVaTen FROM DieuChuyenNhanVien dc JOIN NhanVien n ON dc.MaNV=n.MaNV ORDER BY dc.NgayCoHieuLuc DESC LIMIT 5")->fetchAll();

renderHeader('Báo cáo thống kê toàn diện');
?>

<form method="GET" class="flex items-end gap-3 mb-6 bg-white p-4 rounded-xl shadow-sm">
    <input type="hidden" name="page" value="baocao">
    <div><label class="block text-sm font-medium mb-1">Tháng báo cáo</label><input type="month" name="thang" value="<?= htmlspecialchars($thangNam) ?>" class="border rounded-lg px-3 py-2 text-sm"></div>
    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm"><i class="fas fa-filter mr-1"></i>Xem báo cáo</button>
    <button onclick="window.print()" type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm ml-auto"><i class="fas fa-print mr-1"></i>In báo cáo</button>
</form>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-users text-blue-500 mr-2"></i>Nhân sự theo phòng ban</h3>
        <?php
        $h = ['Phòng ban', 'Số NV đang làm']; $r = [];
        foreach ($nvPhongBan as $x) $r[] = [htmlspecialchars($x['TenPhongBan']), $x['SoNV']];
        renderTable($h, $r);
        ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-clock text-green-500 mr-2"></i>Chuyên cần tháng <?= htmlspecialchars($thangNam) ?></h3>
        <?php
        $labels = ['DungGio'=>'Đúng giờ','DiMuon'=>'Đi muộn','VeSom'=>'Về sớm','NghiKhongPhep'=>'Nghỉ không phép','NghiCoPhep'=>'Nghỉ có phép'];
        $h = ['Trạng thái', 'Số lượt']; $r = [];
        foreach ($chuyenCan as $x) {
            $link = '<a href="?page=baocao&thang='.htmlspecialchars($thangNam).'&chitiet='.$x['TrangThaiCong'].'" class="text-blue-600 font-bold hover:underline" title="Xem danh sách">' . $x['SoLuong'] . ' <i class="fas fa-search text-xs"></i></a>';
            $r[] = [$labels[$x['TrangThaiCong']] ?? $x['TrangThaiCong'], $link];
        }
        renderTable($h, $r, 'Chưa có dữ liệu chấm công tháng này');
        ?>
        <?php if ($chiTiet !== ''): ?>
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold text-sm">Danh sách "<?= $labels[$chiTiet] ?>" tháng <?= htmlspecialchars($thangNam) ?> (<?= count($chiTietList) ?> lượt)</h4>
                <a href="?page=baocao&thang=<?= htmlspecialchars($thangNam) ?>" class="text-xs text-gray-500 hover:text-gray-700"><i class="fas fa-times mr-1"></i>Đóng</a>
            </div>
            <?php
            $hd = ['Nhân viên', 'Ngày', 'Giờ vào', 'Giờ ra']; $rd = [];
            foreach ($chiTietList as $ct) $rd[] = [htmlspecialchars($ct['HoVaTen']) . ' <span class="text-gray-400 text-xs">(' . $ct['MaNV'] . ')</span>', formatDate($ct['NgayChamCong']), $ct['ThoiGianVao'] ?? '--', $ct['ThoiGianRa'] ?? '--'];
            renderTable($hd, $rd);
            ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-money-bill-wave text-yellow-500 mr-2"></i>Biến động quỹ lương tháng <?= htmlspecialchars($thangNam) ?></h3>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="p-4 bg-gray-50 rounded-lg"><p class="text-xs text-gray-500">Số phiếu</p><p class="text-2xl font-bold"><?= $quyLuong['SoPhieu'] ?></p></div>
            <div class="p-4 bg-blue-50 rounded-lg"><p class="text-xs text-gray-500">Tổng quỹ</p><p class="text-lg font-bold text-blue-700"><?= formatCurrency($quyLuong['TongQuy']) ?></p></div>
            <div class="p-4 bg-green-50 rounded-lg"><p class="text-xs text-gray-500">Đã chi trả</p><p class="text-lg font-bold text-green-700"><?= formatCurrency($quyLuong['DaTra']) ?></p></div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-calendar-check text-purple-500 mr-2"></i>Đơn nghỉ phép theo trạng thái</h3>
        <?php
        $ttLabels = ['ChoDuyet'=>'Chờ duyệt','DaDuyet'=>'Đã duyệt','TuChoi'=>'Từ chối'];
        $h = ['Trạng thái', 'Số đơn']; $r = [];
        foreach ($donNghi as $x) $r[] = [$ttLabels[$x['TrangThaiDuyet']] ?? $x['TrangThaiDuyet'], $x['SoLuong']];
        renderTable($h, $r);
        ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-file-contract text-indigo-500 mr-2"></i>Hợp đồng hiệu lực theo loại</h3>
        <?php
        $hdLabels = ['ThuViec'=>'Thử việc','XacDinhThoiHan'=>'Xác định thời hạn','KhongXacDinhThoiHan'=>'Không xác định'];
        $h = ['Loại hợp đồng', 'Số lượng']; $r = [];
        foreach ($hopDong as $x) $r[] = [$hdLabels[$x['LoaiHopDong']] ?? $x['LoaiHopDong'], $x['SoLuong']];
        renderTable($h, $r);
        ?>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="font-semibold mb-4"><i class="fas fa-exchange-alt text-orange-500 mr-2"></i>Biến động điều chuyển gần đây</h3>
        <?php
        $h = ['Nhân viên', 'Ngày hiệu lực']; $r = [];
        foreach ($dieuchuyen as $x) $r[] = [htmlspecialchars($x['HoVaTen']), formatDate($x['NgayCoHieuLuc'])];
        renderTable($h, $r, 'Chưa có điều chuyển');
        ?>
    </div>
</div>
<?php renderFooter(); ?>

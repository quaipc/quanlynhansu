<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $maNV = $_POST['ma_nv'];
        $nv = $pdo->prepare("SELECT MaPhongBan, MaChucVu FROM NhanVien WHERE MaNV=?")->execute([$maNV]) ? $pdo->prepare("SELECT MaPhongBan, MaChucVu FROM NhanVien WHERE MaNV=?")->fetch() : null;
        $stmt = $pdo->prepare("SELECT MaPhongBan, MaChucVu FROM NhanVien WHERE MaNV=?");
        $stmt->execute([$maNV]);
        $nv = $stmt->fetch();
        $phongBanCu = $nv['MaPhongBan'] ?? null;
        $chucVuCu = $nv['MaChucVu'] ?? null;

        $pdo->prepare("UPDATE NhanVien SET MaPhongBan=?, MaChucVu=? WHERE MaNV=?")->execute([$_POST['phong_ban_moi'] ?: null, $_POST['chuc_vu_moi'] ?: null, $maNV]);
        $pdo->prepare("INSERT INTO DieuChuyenNhanVien(MaNV,PhongBanCu,PhongBanMoi,ChucVuCu,ChucVuMoi,NgayCoHieuLuc,LyDoDieuChuyen,MaNguoiQuyetDinh) VALUES(?,?,?,?,?,?,?,?)")->execute([
            $maNV, $phongBanCu, $_POST['phong_ban_moi'] ?: null, $chucVuCu, $_POST['chuc_vu_moi'] ?: null, $_POST['ngay_hieu_luc'], $_POST['ly_do'], getMaNV()
        ]);
        header('Location: ?page=dieuchuyen&msg=added'); exit;
    }
}

$nhanViens = $pdo->query("SELECT MaNV, HoVaTen FROM NhanVien ORDER BY HoVaTen")->fetchAll();
$phongBans = $pdo->query("SELECT * FROM PhongBan ORDER BY TenPhongBan")->fetchAll();
$chucVus = $pdo->query("SELECT * FROM ChucVu ORDER BY TenChucVu")->fetchAll();
$dieuchuyens = $pdo->query("SELECT dc.*, nv.HoVaTen, pb1.TenPhongBan AS TenPBCu, pb2.TenPhongBan AS TenPBMoi, cv1.TenChucVu AS TenCVCu, cv2.TenChucVu AS TenCVMoi FROM DieuChuyenNhanVien dc JOIN NhanVien nv ON dc.MaNV=nv.MaNV LEFT JOIN PhongBan pb1 ON dc.PhongBanCu=pb1.MaPhongBan LEFT JOIN PhongBan pb2 ON dc.PhongBanMoi=pb2.MaPhongBan LEFT JOIN ChucVu cv1 ON dc.ChucVuCu=cv1.MaChucVu LEFT JOIN ChucVu cv2 ON dc.ChucVuMoi=cv2.MaChucVu ORDER BY dc.NgayCoHieuLuc DESC")->fetchAll();

renderHeader('Điều chuyển nhân sự');

if ($msg = $_GET['msg'] ?? '') {
    echo "<div class=\"bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4\">Tạo quyết định điều chuyển thành công!</div>";
}
?>

<button onclick="toggleModal('modal-add-dc', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Tạo quyết định điều chuyển</button>

<?php
$headers = ['Nhân viên', 'Phòng ban cũ', 'Phòng ban mới', 'Chức vụ cũ', 'Chức vụ mới', 'Ngày hiệu lực', 'Lý do'];
$rows = [];
foreach ($dieuchuyens as $dc) {
    $rows[] = [
        htmlspecialchars($dc['HoVaTen']),
        htmlspecialchars($dc['TenPBCu'] ?? 'N/A'),
        htmlspecialchars($dc['TenPBMoi'] ?? 'N/A'),
        htmlspecialchars($dc['TenCVCu'] ?? 'N/A'),
        htmlspecialchars($dc['TenCVMoi'] ?? 'N/A'),
        formatDate($dc['NgayCoHieuLuc']),
        htmlspecialchars(mb_substr($dc['LyDoDieuChuyen'] ?? '', 0, 30))
    ];
}
renderTable($headers, $rows);
?>

<?php
$nvOpts = '<option value="">-- Chọn --</option>'; foreach($nhanViens as $nv) $nvOpts .= '<option value="'.$nv['MaNV'].'">'.htmlspecialchars($nv['HoVaTen']).'</option>';
$pbOpts = '<option value="">-- Giữ nguyên --</option>'; foreach($phongBans as $pb) $pbOpts .= '<option value="'.$pb['MaPhongBan'].'">'.htmlspecialchars($pb['TenPhongBan']).'</option>';
$cvOpts = '<option value="">-- Giữ nguyên --</option>'; foreach($chucVus as $cv) $cvOpts .= '<option value="'.$cv['MaChucVu'].'">'.htmlspecialchars($cv['TenChucVu']).'</option>';
?>
<?php renderModal('modal-add-dc', 'Tạo Quyết định điều chuyển', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div><label class="block text-sm font-medium mb-1">Nhân viên *</label><select name="ma_nv" required class="w-full border rounded-lg px-3 py-2 text-sm">'.$nvOpts.'</select></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Phòng ban mới</label><select name="phong_ban_moi" class="w-full border rounded-lg px-3 py-2 text-sm">'.$pbOpts.'</select></div>
<div><label class="block text-sm font-medium mb-1">Chức vụ mới</label><select name="chuc_vu_moi" class="w-full border rounded-lg px-3 py-2 text-sm">'.$cvOpts.'</select></div>
</div>
<div><label class="block text-sm font-medium mb-1">Ngày hiệu lực *</label><input type="date" name="ngay_hieu_luc" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Lý do</label><textarea name="ly_do" rows="3" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea></div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-dc\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Tạo quyết định</button></div>
</form>
'); ?>
<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
</script>
<?php renderFooter(); ?>

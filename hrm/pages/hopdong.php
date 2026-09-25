<?php
$role = getUserRole();
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Manager') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pdo->prepare("INSERT INTO HopDongLaoDong(MaHopDong,MaNV,LoaiHopDong,NgayBatDau,NgayKetThuc,LuongCoBan) VALUES(?,?,?,?,?,?)")->execute([
            $_POST['ma_hop_dong'], $_POST['ma_nv'], $_POST['loai_hop_dong'], $_POST['ngay_bat_dau'], $_POST['ngay_ket_thuc'] ?: null, $_POST['luong_co_ban']
        ]);
        header('Location: ?page=hopdong&msg=added'); exit;
    } elseif ($action === 'edit') {
        $pdo->prepare("UPDATE HopDongLaoDong SET LoaiHopDong=?,NgayBatDau=?,NgayKetThuc=?,LuongCoBan=?,TrangThaiHopDong=? WHERE MaHopDong=?")->execute([
            $_POST['loai_hop_dong'], $_POST['ngay_bat_dau'], $_POST['ngay_ket_thuc'] ?: null, $_POST['luong_co_ban'], $_POST['trang_thai'], $_POST['ma_hop_dong']
        ]);
        header('Location: ?page=hopdong&msg=updated'); exit;
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM HopDongLaoDong WHERE MaHopDong=?")->execute([$_POST['ma_hop_dong']]);
        header('Location: ?page=hopdong&msg=deleted'); exit;
    }
}

$nhanViens = $pdo->query("SELECT MaNV, HoVaTen FROM NhanVien ORDER BY HoVaTen")->fetchAll();
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT * FROM HopDongLaoDong WHERE MaNV=? ORDER BY NgayBatDau DESC");
    $stmt->execute([$maNV]);
    $hopDongs = $stmt->fetchAll();
} else {
    $hopDongs = $pdo->query("SELECT h.*, n.HoVaTen FROM HopDongLaoDong h JOIN NhanVien n ON h.MaNV=n.MaNV ORDER BY h.NgayBatDau DESC")->fetchAll();
}

renderHeader('Hợp đồng lao động');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added' => ['green','Thêm hợp đồng thành công!'], 'updated' => ['blue','Cập nhật thành công!'], 'deleted' => ['red','Đã xóa hợp đồng!']];
    [$cls, $txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<?php if ($role === 'Manager'): ?>
<button onclick="toggleModal('modal-add-hd', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Thêm hợp đồng</button>
<?php endif; ?>

<?php
$loaiHD = ['ThuViec' => 'Thử việc', 'XacDinhThoiHan' => 'Xác định thời hạn', 'KhongXacDinhThoiHan' => 'Không xác định'];
$trangThai = ['HieuLuc' => 'Hiệu lực', 'HetHan' => 'Hết hạn', 'HuyBo' => 'Hủy bỏ'];
$headers = $role === 'Employee' ? ['Mã HĐ', 'Loại', 'Ngày bắt đầu', 'Ngày kết thúc', 'Lương CB', 'Trạng thái'] : ['Nhân viên', 'Mã HĐ', 'Loại', 'Lương CB', 'Trạng thái', 'Hành động'];
$rows = [];
foreach ($hopDongs as $hd) {
    $badge = match($hd['TrangThaiHopDong']) { 'HieuLuc' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Hiệu lực</span>', 'HetHan' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Hết hạn</span>', default => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Hủy bỏ</span>' };
    $action = '';
    if ($role === 'Manager') {
        $action = '<button onclick=\'editHD('.json_encode($hd).')\' class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>'.
        '<form method="POST" class="inline" onsubmit="return confirm(\'Xóa?\')"><input type="hidden" name="action" value="delete"><input type="hidden" name="ma_hop_dong" value="'.$hd['MaHopDong'].'"><button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>';
    }
    if ($role === 'Employee') {
        $rows[] = [$hd['MaHopDong'], $loaiHD[$hd['LoaiHopDong']]??$hd['LoaiHopDong'], formatDate($hd['NgayBatDau']), formatDate($hd['NgayKetThuc']), formatCurrency($hd['LuongCoBan']), $badge];
    } else {
        $rows[] = [htmlspecialchars($hd['HoVaTen']??''), $hd['MaHopDong'], $loaiHD[$hd['LoaiHopDong']]??$hd['LoaiHopDong'], formatCurrency($hd['LuongCoBan']), $badge, $action];
    }
}
renderTable($headers, $rows);
?>

<?php $nvOpts = '<option value="">-- Chọn --</option>'; foreach($nhanViens as $nv) $nvOpts .= '<option value="'.$nv['MaNV'].'">'.htmlspecialchars($nv['HoVaTen']).'</option>'; ?>
<?php renderModal('modal-add-hd', 'Thêm Hợp đồng', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Mã HĐ *</label><input name="ma_hop_dong" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Nhân viên *</label><select name="ma_nv" class="w-full border rounded-lg px-3 py-2 text-sm">'.$nvOpts.'</select></div>
<div><label class="block text-sm font-medium mb-1">Loại HĐ *</label><select name="loai_hop_dong" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="ThuViec">Thử việc</option><option value="XacDinhThoiHan">Xác định thời hạn</option><option value="KhongXacDinhThoiHan">Không xác định</option></select></div>
<div><label class="block text-sm font-medium mb-1">Lương cơ bản *</label><input type="number" name="luong_co_ban" step="0.01" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Ngày bắt đầu *</label><input type="date" name="ngay_bat_dau" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Ngày kết thúc</label><input type="date" name="ngay_ket_thuc" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
</div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-hd\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Thêm</button></div>
</form>
'); ?>

<div id="modal-edit-hd" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-lg font-semibold">Sửa Hợp đồng</h3><button onclick="toggleModal('modal-edit-hd', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="edit"><input type="hidden" name="ma_hop_dong" id="ehd-ma">
            <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-medium mb-1">Loại HĐ</label><select name="loai_hop_dong" id="ehd-loai" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="ThuViec">Thử việc</option><option value="XacDinhThoiHan">Xác định thời hạn</option><option value="KhongXacDinhThoiHan">Không xác định</option></select></div>
            <div><label class="block text-sm font-medium mb-1">Lương CB</label><input type="number" name="luong_co_ban" id="ehd-luong" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">Ngày bắt đầu</label><input type="date" name="ngay_bat_dau" id="ehd-bd" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">Ngày kết thúc</label><input type="date" name="ngay_ket_thuc" id="ehd-kt" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">Trạng thái</label><select name="trang_thai" id="ehd-tt" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="HieuLuc">Hiệu lực</option><option value="HetHan">Hết hạn</option><option value="HuyBo">Hủy bỏ</option></select></div>
            </div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-edit-hd', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cập nhật</button></div>
        </form>
    </div>
</div>
<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
function editHD(hd) { document.getElementById('ehd-ma').value=hd.MaHopDong; document.getElementById('ehd-loai').value=hd.LoaiHopDong; document.getElementById('ehd-luong').value=hd.LuongCoBan; document.getElementById('ehd-bd').value=hd.NgayBatDau; document.getElementById('ehd-kt').value=hd.NgayKetThuc||''; document.getElementById('ehd-tt').value=hd.TrangThaiHopDong; toggleModal('modal-edit-hd', true); }
</script>
<?php renderFooter(); ?>

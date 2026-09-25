<?php
$role = getUserRole();
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_kh' && $role === 'Manager') {
        $pdo->prepare("INSERT INTO KhoaHoc(MaKhoaHoc,TenKhoaHoc,MoTa,HinhThuc,GiangVien,NgayBatDau,NgayKetThuc,NganSachDuKien) VALUES(?,?,?,?,?,?,?,?)")->execute([
            $_POST['ma_khoa_hoc'], $_POST['ten_khoa_hoc'], $_POST['mo_ta'], $_POST['hinh_thuc'], $_POST['giang_vien'], $_POST['ngay_bat_dau'], $_POST['ngay_ket_thuc'], $_POST['ngan_sach'] ?: 0
        ]);
        header('Location: ?page=daotao&msg=added_kh'); exit;
    } elseif ($action === 'register') {
        $pdo->prepare("INSERT INTO ChiTietDaoTao(MaKhoaHoc,MaNV) VALUES(?,?)")->execute([$_POST['ma_khoa_hoc'], $maNV]);
        header('Location: ?page=daotao&msg=registered'); exit;
    } elseif ($action === 'update_status' && $role === 'Manager') {
        $pdo->prepare("UPDATE ChiTietDaoTao SET TrangThaiHoc=?, DiemSo=? WHERE ID_ChiTiet=?")->execute([$_POST['trang_thai_hoc'], $_POST['diem_so'] ?: null, $_POST['id_chi_tiet']]);
        header('Location: ?page=daotao&msg=updated'); exit;
    }
}

$khoaHocs = $pdo->query("SELECT * FROM KhoaHoc ORDER BY NgayBatDau DESC")->fetchAll();
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT ct.*, kh.TenKhoaHoc, kh.HinhThuc, kh.GiangVien FROM ChiTietDaoTao ct JOIN KhoaHoc kh ON ct.MaKhoaHoc=kh.MaKhoaHoc WHERE ct.MaNV=? ORDER BY kh.NgayBatDau DESC");
    $stmt->execute([$maNV]);
    $daTaos = $stmt->fetchAll();
} else {
    $daTaos = $pdo->query("SELECT ct.*, kh.TenKhoaHoc, n.HoVaTen FROM ChiTietDaoTao ct JOIN KhoaHoc kh ON ct.MaKhoaHoc=kh.MaKhoaHoc JOIN NhanVien n ON ct.MaNV=n.MaNV ORDER BY kh.NgayBatDau DESC, n.HoVaTen")->fetchAll();
}

renderHeader('Đào tạo');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added_kh' => ['green','Thêm khóa học thành công!'], 'registered' => ['blue','Đăng ký thành công!'], 'updated' => ['blue','Cập nhật kết quả!']];
    [$cls, $txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<?php if ($role === 'Manager'): ?>
<button onclick="toggleModal('modal-add-kh', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Thêm khóa học</button>
<?php endif; ?>

<!-- Danh sách khóa học -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
<?php foreach ($khoaHocs as $kh): ?>
<div class="bg-white rounded-xl shadow-sm p-5 border-t-4 border-blue-500">
    <h4 class="font-semibold text-lg mb-2"><?= htmlspecialchars($kh['TenKhoaHoc']) ?></h4>
    <div class="text-sm text-gray-600 space-y-1">
        <p><i class="fas fa-chalkboard-teacher mr-2 text-blue-400"></i><?= htmlspecialchars($kh['GiangVien'] ?? 'N/A') ?></p>
        <p><i class="fas fa-laptop mr-2 text-purple-400"></i><?= $kh['HinhThuc'] ?></p>
        <p><i class="fas fa-calendar mr-2 text-green-400"></i><?= formatDate($kh['NgayBatDau']) ?> - <?= formatDate($kh['NgayKetThuc']) ?></p>
        <?php if ($kh['NganSachDuKien'] > 0): ?>
        <p><i class="fas fa-coins mr-2 text-yellow-400"></i><?= formatCurrency($kh['NganSachDuKien']) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($role === 'Employee'): ?>
    <?php
    $registered = $pdo->prepare("SELECT ID_ChiTiet FROM ChiTietDaoTao WHERE MaKhoaHoc=? AND MaNV=?");
    $registered->execute([$kh['MaKhoaHoc'], $maNV]);
    $isRegistered = $registered->fetch();
    ?>
    <?php if (!$isRegistered): ?>
    <form method="POST" class="mt-3"><input type="hidden" name="action" value="register"><input type="hidden" name="ma_khoa_hoc" value="<?= $kh['MaKhoaHoc'] ?>">
        <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm"><i class="fas fa-sign-in-alt mr-1"></i>Đăng ký</button>
    </form>
    <?php else: ?>
    <span class="inline-block mt-3 px-3 py-1 rounded-full text-xs bg-green-100 text-green-700">Đã đăng ký</span>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<!-- Kết quả đào tạo -->
<h3 class="text-lg font-semibold mb-4">Kết quả đào tạo</h3>
<?php
$headers = $role === 'Employee' ? ['Khóa học', 'Trạng thái', 'Điểm'] : ['Nhân viên', 'Khóa học', 'Trạng thái', 'Điểm', 'Hành động'];
$rows = [];
foreach ($daTaos as $dt) {
    $badge = match($dt['TrangThaiHoc']) { 'HoanThanh' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Hoàn thành</span>', 'HuyBo' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Hủy</span>', default => '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Đang học</span>' };
    $action = '';
    if ($role === 'Manager') {
        $action = '<button onclick=\'editDT('.json_encode($dt).')\' class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>';
    }
    if ($role === 'Employee') {
        $rows[] = [htmlspecialchars($dt['TenKhoaHoc']), $badge, $dt['DiemSo'] ?? 'N/A'];
    } else {
        $rows[] = [htmlspecialchars($dt['HoVaTen']??''), htmlspecialchars($dt['TenKhoaHoc']), $badge, $dt['DiemSo'] ?? 'N/A', $action];
    }
}
renderTable($headers, $rows);
?>

<?php renderModal('modal-add-kh', 'Thêm Khóa học', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add_kh">
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Mã khóa học *</label><input name="ma_khoa_hoc" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Tên khóa học *</label><input name="ten_khoa_hoc" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div class="col-span-2"><label class="block text-sm font-medium mb-1">Mô tả</label><textarea name="mo_ta" rows="2" class="w-full border rounded-lg px-3 py-2 text-sm"></textarea></div>
<div><label class="block text-sm font-medium mb-1">Hình thức</label><select name="hinh_thuc" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="Online">Online</option><option value="Offline">Offline</option><option value="Hybrid">Hybrid</option></select></div>
<div><label class="block text-sm font-medium mb-1">Giảng viên</label><input name="giang_vien" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Ngày bắt đầu *</label><input type="date" name="ngay_bat_dau" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Ngày kết thúc *</label><input type="date" name="ngay_ket_thuc" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Ngân sách dự kiến</label><input type="number" name="ngan_sach" step="0.01" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
</div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-kh\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Thêm</button></div>
</form>
'); ?>

<div id="modal-edit-dt" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-lg font-semibold">Cập nhật kết quả đào tạo</h3><button onclick="toggleModal('modal-edit-dt', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="update_status"><input type="hidden" name="id_chi_tiet" id="edt-id">
            <div><label class="block text-sm font-medium mb-1">Trạng thái</label><select name="trang_thai_hoc" id="edt-tt" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="DangHoc">Đang học</option><option value="HoanThanh">Hoàn thành</option><option value="HuyBo">Hủy</option></select></div>
            <div><label class="block text-sm font-medium mb-1">Điểm số</label><input type="number" name="diem_so" id="edt-diem" step="0.01" min="0" max="10" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-edit-dt', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cập nhật</button></div>
        </form>
    </div>
</div>
<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
function editDT(dt) { document.getElementById('edt-id').value=dt.ID_ChiTiet; document.getElementById('edt-tt').value=dt.TrangThaiHoc; document.getElementById('edt-diem').value=dt.DiemSo||''; toggleModal('modal-edit-dt', true); }
</script>
<?php renderFooter(); ?>

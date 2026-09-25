<?php
// UC07: Quan ly Ho so nhan su - Manager (dieu hanh). Admin KHONG can thiep.
// Tach biet ky thuat - dieu hanh: tai khoan do Admin cap, Manager chi tiep nhan ho so va lien ket tai khoan co san.
$role = getUserRole();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'Manager') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        // Lien ket tai khoan co san do Admin cap, khong tu tao tai khoan tai day
        $maTaiKhoan = $_POST['ma_tai_khoan'] ?: null;
        $pdo->prepare("INSERT INTO NhanVien(MaNV,HoVaTen,NgaySinh,GioiTinh,CCCD,SoDienThoai,Email,DiaChiThuongTru,MaPhongBan,MaChucVu,MaTaiKhoan,NgayVaoLam) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
            $_POST['ma_nv'], $_POST['ho_va_ten'], $_POST['ngay_sinh'], $_POST['gioi_tinh'],
            $_POST['cccd'], $_POST['so_dien_thoai'], $_POST['email'], $_POST['dia_chi'],
            $_POST['ma_phong_ban'] ?: null, $_POST['ma_chuc_vu'] ?: null, $maTaiKhoan, $_POST['ngay_vao_lam']
        ]);
        header('Location: ?page=nhanvien&msg=added');
        exit;
    } elseif ($action === 'edit') {
        // Manager quan ly toan bo ho so + trang thai cong tac (MaPhongBan/MaChucVu/TrangThai do Manager nam)
        $pdo->prepare("UPDATE NhanVien SET HoVaTen=?,NgaySinh=?,GioiTinh=?,CCCD=?,SoDienThoai=?,Email=?,DiaChiThuongTru=?,MaPhongBan=?,MaChucVu=?,TrangThaiLamViec=?,NgayVaoLam=? WHERE MaNV=?")->execute([
            $_POST['ho_va_ten'], $_POST['ngay_sinh'], $_POST['gioi_tinh'],
            $_POST['cccd'], $_POST['so_dien_thoai'], $_POST['email'], $_POST['dia_chi'],
            $_POST['ma_phong_ban'] ?: null, $_POST['ma_chuc_vu'] ?: null, $_POST['trang_thai'], $_POST['ngay_vao_lam'], $_POST['ma_nv']
        ]);
        header('Location: ?page=nhanvien&msg=updated');
        exit;
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM NhanVien WHERE MaNV=?")->execute([$_POST['ma_nv']]);
        header('Location: ?page=nhanvien&msg=deleted');
        exit;
    }
}

// Manager xem TOAN BO nhan su (UC07 + UC11 bao cao toan dien)
$phongBan = $pdo->query("SELECT * FROM PhongBan ORDER BY TenPhongBan")->fetchAll();
$chucVu = $pdo->query("SELECT * FROM ChucVu ORDER BY TenChucVu")->fetchAll();
// Muc 3.3: truy xuat tron ven thong tin lam viec kem trang thai tai khoan
$nhanViens = $pdo->query("SELECT nv.MaNV, nv.HoVaTen, pb.TenPhongBan, cv.TenChucVu, tk.TenDangNhap, tk.TrangThai AS TrangThaiTK, nv.* FROM NhanVien nv LEFT JOIN PhongBan pb ON nv.MaPhongBan = pb.MaPhongBan LEFT JOIN ChucVu cv ON nv.MaChucVu = cv.MaChucVu LEFT JOIN TaiKhoan tk ON nv.MaTaiKhoan = tk.MaTaiKhoan ORDER BY nv.MaNV")->fetchAll();
// Tai khoan chua lien ket ho so (do Admin cap san)
$freeAccounts = $pdo->query("SELECT t.MaTaiKhoan, t.TenDangNhap, t.VaiTro FROM TaiKhoan t LEFT JOIN NhanVien n ON t.MaTaiKhoan=n.MaTaiKhoan WHERE n.MaNV IS NULL AND t.TrangThai=1 ORDER BY t.TenDangNhap")->fetchAll();

$msg = $_GET['msg'] ?? '';
renderHeader('Quản lý Hồ sơ nhân sự');

if ($msg === 'added'): ?><div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4">Tiếp nhận nhân sự mới thành công!</div><?php endif;
if ($msg === 'updated'): ?><div class="bg-blue-50 text-blue-700 px-4 py-3 rounded-lg mb-4">Cập nhật hồ sơ thành công!</div><?php endif;
if ($msg === 'deleted'): ?><div class="bg-red-50 text-red-700 px-4 py-3 rounded-lg mb-4">Đã xóa hồ sơ nhân viên!</div><?php endif;
?>

<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-sm text-blue-800">
    <i class="fas fa-info-circle mr-2"></i>Manager quản lý hồ sơ, trạng thái công tác (MaPhongBan / MaChucVu / TrangThaiLamViec). Tài khoản đăng nhập do Admin cấp — Manager chỉ liên kết tài khoản có sẵn.
</div>

<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
    <p class="text-gray-600">Tổng: <b><?= count($nhanViens) ?></b> nhân viên</p>
    <button onclick="toggleModal('modal-add', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
        <i class="fas fa-plus mr-1"></i> Tiếp nhận nhân sự mới
    </button>
</div>

<?php
$headers = ['Mã NV', 'Họ tên', 'Phòng ban', 'Chức vụ', 'Tài khoản', 'Trạng thái', 'Hành động'];
$rows = [];
foreach ($nhanViens as $nv) {
    $actions = '<button type="button" onclick="editNV(' . htmlspecialchars(json_encode($nv)) . ')" class="text-blue-500 hover:text-blue-700 mr-2" title="Sửa"><i class="fas fa-edit"></i></button>';
    $actions .= '<form method="POST" class="inline" onsubmit="return confirm(\'Xóa hồ sơ này?\')"><input type="hidden" name="action" value="delete"><input type="hidden" name="ma_nv" value="'.$nv['MaNV'].'"><button class="text-red-500 hover:text-red-700" title="Xóa"><i class="fas fa-trash"></i></button></form>';
    $trangThaiBadge = match($nv['TrangThaiLamViec']) {
        'DangLamViec' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đang làm</span>',
        'NghiPhep' => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Nghỉ phép</span>',
        'DaNghiViec' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Nghỉ việc</span>',
        default => $nv['TrangThaiLamViec']
    };
    $tkInfo = isset($nv['TenDangNhap']) && $nv['TenDangNhap']
        ? htmlspecialchars($nv['TenDangNhap']) . ' ' . ($nv['TrangThaiTK'] ? '<span class="px-1.5 py-0.5 rounded text-xs bg-green-100 text-green-700">Hoạt động</span>' : '<span class="px-1.5 py-0.5 rounded text-xs bg-red-100 text-red-700">Khóa</span>')
        : '<span class="text-gray-400">Chưa LK</span>';
    $rows[] = [$nv['MaNV'], htmlspecialchars($nv['HoVaTen']), $nv['TenPhongBan'] ?? 'N/A', $nv['TenChucVu'] ?? 'N/A', $tkInfo, $trangThaiBadge, $actions];
}
renderTable($headers, $rows);
?>

<?php
$pbOptions = '<option value="">-- Chọn --</option>';
foreach ($phongBan as $pb) $pbOptions .= '<option value="'.$pb['MaPhongBan'].'">'.htmlspecialchars($pb['TenPhongBan']).'</option>';
$cvOptions = '<option value="">-- Chọn --</option>';
foreach ($chucVu as $cv) $cvOptions .= '<option value="'.$cv['MaChucVu'].'">'.htmlspecialchars($cv['TenChucVu']).'</option>';
$tkOptions = '<option value="">-- Chưa liên kết --</option>';
foreach ($freeAccounts as $tk) $tkOptions .= '<option value="'.$tk['MaTaiKhoan'].'">'.htmlspecialchars($tk['TenDangNhap']).' ('.$tk['VaiTro'].')</option>';
?>

<?php renderModal('modal-add', 'Tiếp nhận nhân sự mới', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div class="grid grid-cols-2 gap-4">
    <div><label class="block text-sm font-medium mb-1">Mã NV *</label><input name="ma_nv" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Họ và tên *</label><input name="ho_va_ten" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Ngày sinh *</label><input type="date" name="ngay_sinh" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Giới tính *</label><select name="gioi_tinh" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="Nam">Nam</option><option value="Nu">Nữ</option><option value="Khac">Khác</option></select></div>
    <div><label class="block text-sm font-medium mb-1">CCCD *</label><input name="cccd" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">SĐT *</label><input name="so_dien_thoai" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div class="col-span-2"><label class="block text-sm font-medium mb-1">Email *</label><input type="email" name="email" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div class="col-span-2"><label class="block text-sm font-medium mb-1">Địa chỉ</label><input name="dia_chi" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Phòng ban</label><select name="ma_phong_ban" class="w-full border rounded-lg px-3 py-2 text-sm">'.$pbOptions.'</select></div>
    <div><label class="block text-sm font-medium mb-1">Chức vụ</label><select name="ma_chuc_vu" class="w-full border rounded-lg px-3 py-2 text-sm">'.$cvOptions.'</select></div>
    <div><label class="block text-sm font-medium mb-1">Ngày vào làm *</label><input type="date" name="ngay_vao_lam" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Liên kết tài khoản (do Admin cấp)</label><select name="ma_tai_khoan" class="w-full border rounded-lg px-3 py-2 text-sm">'.$tkOptions.'</select></div>
</div>
<div class="flex justify-end gap-2 mt-4">
    <button type="button" onclick="toggleModal(\'modal-add\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Tiếp nhận</button>
</div>
</form>
'); ?>

<div id="modal-edit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Cập nhật hồ sơ nhân sự</h3>
            <button onclick="toggleModal('modal-edit', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="ma_nv" id="edit-ma_nv">
            <div><label class="block text-sm font-medium mb-1">Họ và tên</label><input name="ho_va_ten" id="edit-ho_va_ten" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Ngày sinh</label><input type="date" name="ngay_sinh" id="edit-ngay_sinh" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">Giới tính</label><select name="gioi_tinh" id="edit-gioi_tinh" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="Nam">Nam</option><option value="Nu">Nữ</option><option value="Khac">Khác</option></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">CCCD</label><input name="cccd" id="edit-cccd" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
                <div><label class="block text-sm font-medium mb-1">SĐT</label><input name="so_dien_thoai" id="edit-so_dien_thoai" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            </div>
            <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email" id="edit-email" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div><label class="block text-sm font-medium mb-1">Địa chỉ</label><input name="dia_chi" id="edit-dia_chi" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Phòng ban</label><select name="ma_phong_ban" id="edit-ma_phong_ban" class="w-full border rounded-lg px-3 py-2 text-sm"><?= $pbOptions ?></select></div>
                <div><label class="block text-sm font-medium mb-1">Chức vụ</label><select name="ma_chuc_vu" id="edit-ma_chuc_vu" class="w-full border rounded-lg px-3 py-2 text-sm"><?= $cvOptions ?></select></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium mb-1">Trạng thái công tác</label><select name="trang_thai" id="edit-trang_thai" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="DangLamViec">Đang làm việc</option><option value="NghiPhep">Nghỉ phép</option><option value="DaNghiViec">Đã nghỉ việc</option></select></div>
                <div><label class="block text-sm font-medium mb-1">Ngày vào làm</label><input type="date" name="ngay_vao_lam" id="edit-ngay_vao_lam" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" onclick="toggleModal('modal-edit', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleModal(id, show) {
    const m = document.getElementById(id);
    if (show) { m.classList.remove('hidden'); m.classList.add('flex'); }
    else { m.classList.add('hidden'); m.classList.remove('flex'); }
}
function editNV(nv) {
    document.getElementById('edit-ma_nv').value = nv.MaNV;
    document.getElementById('edit-ho_va_ten').value = nv.HoVaTen;
    document.getElementById('edit-ngay_sinh').value = nv.NgaySinh;
    document.getElementById('edit-gioi_tinh').value = nv.GioiTinh;
    document.getElementById('edit-cccd').value = nv.CCCD;
    document.getElementById('edit-so_dien_thoai').value = nv.SoDienThoai;
    document.getElementById('edit-email').value = nv.Email;
    document.getElementById('edit-dia_chi').value = nv.DiaChiThuongTru || '';
    document.getElementById('edit-ma_phong_ban').value = nv.MaPhongBan || '';
    document.getElementById('edit-ma_chuc_vu').value = nv.MaChucVu || '';
    document.getElementById('edit-trang_thai').value = nv.TrangThaiLamViec;
    document.getElementById('edit-ngay_vao_lam').value = nv.NgayVaoLam;
    toggleModal('modal-edit', true);
}
</script>
<?php renderFooter(); ?>

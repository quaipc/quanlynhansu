<?php
// UC13: Quan tri Tai khoan & Phan quyen (RBAC) - Admin only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $hash = password_hash($_POST['mat_khau'] ?? '123456', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO TaiKhoan(TenDangNhap, MatKhau, VaiTro, TrangThai) VALUES(?,?,?,?)")->execute([
            trim($_POST['ten_dang_nhap']), $hash, $_POST['vai_tro'], $_POST['trang_thai'] ?? 1
        ]);
        header('Location: ?page=taikhoan&msg=added'); exit;
    } elseif ($action === 'role') {
        // Gan vai tro truy cap (RBAC)
        $pdo->prepare("UPDATE TaiKhoan SET VaiTro=? WHERE MaTaiKhoan=?")->execute([$_POST['vai_tro'], $_POST['ma_tai_khoan']]);
        header('Location: ?page=taikhoan&msg=role'); exit;
    } elseif ($action === 'lock') {
        // Khoa / mo khoa tai khoan
        $pdo->prepare("UPDATE TaiKhoan SET TrangThai=? WHERE MaTaiKhoan=?")->execute([$_POST['trang_thai'], $_POST['ma_tai_khoan']]);
        header('Location: ?page=taikhoan&msg=lock'); exit;
    } elseif ($action === 'reset') {
        // Dat lai mat khau
        $hash = password_hash($_POST['mat_khau_moi'] ?? '123456', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE TaiKhoan SET MatKhau=? WHERE MaTaiKhoan=?")->execute([$hash, $_POST['ma_tai_khoan']]);
        header('Location: ?page=taikhoan&msg=reset'); exit;
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM TaiKhoan WHERE MaTaiKhoan=?")->execute([$_POST['ma_tai_khoan']]);
        header('Location: ?page=taikhoan&msg=deleted'); exit;
    } elseif ($action === 'link') {
        // TC04 + muc 3.3: Admin cap tai khoan ky thuat cho ho so do Manager tao (MaTaiKhoan dang NULL)
        // UPDATE NhanVien SET MaTaiKhoan = :maTaiKhoanMoi WHERE MaNV = :maNV
        $maTK = $_POST['ma_tai_khoan'];
        $maNV = $_POST['ma_nv'];
        $pdo->prepare("UPDATE NhanVien SET MaTaiKhoan=NULL WHERE MaTaiKhoan=?")->execute([$maTK]);
        $pdo->prepare("UPDATE NhanVien SET MaTaiKhoan=? WHERE MaNV=?")->execute([$maTK, $maNV]);
        header('Location: ?page=taikhoan&msg=linked'); exit;
    } elseif ($action === 'unlink') {
        // Go lien ket: tra ho so ve trang thai chua cap tai khoan
        $pdo->prepare("UPDATE NhanVien SET MaTaiKhoan=NULL WHERE MaTaiKhoan=?")->execute([$_POST['ma_tai_khoan']]);
        header('Location: ?page=taikhoan&msg=unlinked'); exit;
    }
}

// Ho so chua duoc cap tai khoan (Manager tao, MaTaiKhoan NULL) - phuc vu TC04
$unlinkedNV = $pdo->query("SELECT MaNV, HoVaTen FROM NhanVien WHERE MaTaiKhoan IS NULL ORDER BY HoVaTen")->fetchAll();

$taiKhoans = $pdo->query("SELECT t.*, n.MaNV, n.HoVaTen FROM TaiKhoan t LEFT JOIN NhanVien n ON t.MaTaiKhoan=n.MaTaiKhoan ORDER BY t.MaTaiKhoan")->fetchAll();

renderHeader('Quản trị Tài khoản & Phân quyền');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added'=>['green','Cấp tài khoản mới thành công!'],'role'=>['blue','Gán vai trò thành công!'],'lock'=>['yellow','Cập nhật trạng thái khóa/mở thành công!'],'reset'=>['blue','Đặt lại mật khẩu thành công!'],'deleted'=>['red','Đã xóa tài khoản!'],'linked'=>['green','Liên kết tài khoản với hồ sơ thành công!'],'unlinked'=>['yellow','Đã gỡ liên kết tài khoản!']];
    [$cls,$txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4 text-sm text-blue-800">
    <i class="fas fa-info-circle mr-2"></i>Admin quản trị người dùng: cấp tài khoản mới, đặt lại mật khẩu, khóa/mở khóa, gán vai trò truy cập (Admin / Manager / Employee). Việc tạo hồ sơ nhân sự do Manager thực hiện ở phân hệ điều hành.
</div>

<button onclick="toggleModal('modal-add-tk', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Cấp tài khoản mới</button>

<?php
$headers = ['ID', 'Tên đăng nhập', 'Vai trò (RBAC)', 'Nhân viên liên kết', 'Trạng thái', 'Ngày tạo', 'Hành động'];
$rows = [];
foreach ($taiKhoans as $tk) {
    $roleBadge = match($tk['VaiTro']) {
        'Admin' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Admin</span>',
        'Manager' => '<span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700">Manager</span>',
        default => '<span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Employee</span>'
    };
    $ttBadge = $tk['TrangThai'] ? '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Hoạt động</span>' : '<span class="px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-600">Đã khóa</span>';
    $nvInfo = $tk['HoVaTen'] ? htmlspecialchars($tk['HoVaTen']) . " ({$tk['MaNV']})" : '<span class="text-gray-400">Chưa liên kết</span>';
    $actions = '<div class="flex gap-1">'
        . '<button type="button" onclick="openLink('.$tk['MaTaiKhoan'].',\''.$tk['MaNV'].'\')" class="text-green-600 hover:text-green-800" title="Liên kết với hồ sơ nhân sự"><i class="fas fa-link"></i></button>'
        . '<button type="button" onclick="editRole('.$tk['MaTaiKhoan'].',\''.$tk['VaiTro'].'\')" class="text-purple-500 hover:text-purple-700" title="Gán vai trò"><i class="fas fa-user-tag"></i></button>'
        . '<form method="POST" class="inline"><input type="hidden" name="action" value="lock"><input type="hidden" name="ma_tai_khoan" value="'.$tk['MaTaiKhoan'].'"><input type="hidden" name="trang_thai" value="'.($tk['TrangThai']?0:1).'"><button class="text-yellow-600 hover:text-yellow-800" title="'.($tk['TrangThai']?'Khóa':'Mở khóa').'"><i class="fas fa-'.($tk['TrangThai']?'lock':'lock-open').'"></i></button></form>'
        . '<button onclick="resetPass('.$tk['MaTaiKhoan'].')" class="text-blue-500 hover:text-blue-700" title="Đặt lại mật khẩu"><i class="fas fa-key"></i></button>'
        . '<form method="POST" class="inline" onsubmit="return confirm(\'Xóa tài khoản này?\')"><input type="hidden" name="action" value="delete"><input type="hidden" name="ma_tai_khoan" value="'.$tk['MaTaiKhoan'].'"><button class="text-red-500 hover:text-red-700" title="Xóa"><i class="fas fa-trash"></i></button></form>'
        . '</div>';
    $rows[] = [$tk['MaTaiKhoan'], htmlspecialchars($tk['TenDangNhap']), $roleBadge, $nvInfo, $ttBadge, formatDateTime($tk['NgayTao']), $actions];
}
renderTable($headers, $rows);
?>

<?php renderModal('modal-add-tk', 'Cấp tài khoản mới', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div><label class="block text-sm font-medium mb-1">Tên đăng nhập *</label><input name="ten_dang_nhap" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div class="grid grid-cols-2 gap-4">
<div><label class="block text-sm font-medium mb-1">Mật khẩu</label><input name="mat_khau" value="123456" class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div><label class="block text-sm font-medium mb-1">Vai trò (RBAC) *</label><select name="vai_tro" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="Employee">Employee</option><option value="Manager">Manager</option><option value="Admin">Admin</option></select></div>
</div>
<div><label class="block text-sm font-medium mb-1">Trạng thái</label><select name="trang_thai" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="1">Hoạt động</option><option value="0">Khóa</option></select></div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-tk\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cấp tài khoản</button></div>
</form>
'); ?>

<div id="modal-role" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="font-semibold">Gán vai trò truy cập (RBAC)</h3><button onclick="toggleModal('modal-role', false)" class="text-gray-400"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="role"><input type="hidden" name="ma_tai_khoan" id="role-id">
            <div><label class="block text-sm font-medium mb-1">Vai trò</label><select name="vai_tro" id="role-val" class="w-full border rounded-lg px-3 py-2 text-sm"><option value="Employee">Employee - Cổng tự phục vụ</option><option value="Manager">Manager - Quản lý điều hành</option><option value="Admin">Admin - Quản trị hệ thống</option></select></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-role', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm">Gán quyền</button></div>
        </form>
    </div>
</div>

<div id="modal-reset" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="font-semibold">Đặt lại mật khẩu</h3><button onclick="toggleModal('modal-reset', false)" class="text-gray-400"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="reset"><input type="hidden" name="ma_tai_khoan" id="reset-id">
            <div><label class="block text-sm font-medium mb-1">Mật khẩu mới</label><input name="mat_khau_moi" value="123456" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-reset', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Đặt lại</button></div>
        </form>
    </div>
</div>

<div id="modal-link" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-md shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="font-semibold">Liên kết tài khoản ↔ hồ sơ</h3><button type="button" onclick="toggleModal('modal-link', false)" class="text-gray-400"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="link"><input type="hidden" name="ma_tai_khoan" id="link-tk">
            <p class="text-xs text-gray-500">Quy trình 2 bước (mục 3.3): Manager tạo hồ sơ trước (MaTaiKhoan NULL) → Admin cấp/liên kết tài khoản kỹ thuật tại đây.</p>
            <div><label class="block text-sm font-medium mb-1">Hồ sơ nhân sự chưa có tài khoản</label>
                <select name="ma_nv" id="link-nv" required class="w-full border rounded-lg px-3 py-2 text-sm">
                    <option value="">-- Chọn hồ sơ --</option>
                    <?php foreach ($unlinkedNV as $u): ?>
                    <option value="<?= $u['MaNV'] ?>"><?= $u['MaNV'] ?> - <?= htmlspecialchars($u['HoVaTen']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="toggleModal('modal-link', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">Liên kết</button>
            </div>
        </form>
        <form method="POST" class="px-6 pb-4">
            <input type="hidden" name="action" value="unlink"><input type="hidden" name="ma_tai_khoan" id="unlink-tk">
            <button class="text-xs text-red-500 hover:underline" onclick="return confirm('Gỡ liên kết tài khoản này khỏi hồ sơ?')">Gỡ liên kết hiện tại</button>
        </form>
    </div>
</div>

<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
function editRole(id, role) { document.getElementById('role-id').value=id; document.getElementById('role-val').value=role; toggleModal('modal-role', true); }
function resetPass(id) { document.getElementById('reset-id').value=id; toggleModal('modal-reset', true); }
function openLink(tkId, maNV) {
    document.getElementById('link-tk').value = tkId;
    document.getElementById('unlink-tk').value = tkId;
    const sel = document.getElementById('link-nv');
    // Dua ho so dang lien ket (neu co) vao danh sach chon
    let exists = false;
    for (const o of sel.options) { if (o.value === maNV && maNV) exists = true; }
    if (maNV && !exists) {
        const opt = document.createElement('option');
        opt.value = maNV; opt.textContent = maNV + ' (đang liên kết)';
        sel.appendChild(opt);
    }
    sel.value = maNV || '';
    toggleModal('modal-link', true);
}
</script>
<?php renderFooter(); ?>

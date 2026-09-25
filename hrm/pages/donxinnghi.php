<?php
$role = getUserRole();
$maNV = getMaNV();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' && $role === 'Employee') {
        $pdo->prepare("INSERT INTO DonXinNghi(MaNV,LoaiNghiPhep,TuNgay,DenNgay,LyDo) VALUES(?,?,?,?,?)")->execute([
            $maNV, $_POST['loai_nghi'], $_POST['tu_ngay'], $_POST['den_ngay'], $_POST['ly_do']
        ]);
        header('Location: ?page=donxinnghi&msg=added');
        exit;
    } elseif ($action === 'approve' && $role === 'Manager') {
        $trangThai = $_POST['trang_thai_duyet'];
        $pdo->prepare("UPDATE DonXinNghi SET TrangThaiDuyet=?, MaNguoiDuyet=? WHERE MaDon=? AND TrangThaiDuyet='ChoDuyet'")->execute([$trangThai, $maNV, $_POST['ma_don']]);
        header('Location: ?page=donxinnghi&msg=approved');
        exit;
    }
}

// Lấy danh sách đơn: Employee xem của mình; Manager xem TOAN BO de phe duyet
if ($role === 'Employee') {
    $stmt = $pdo->prepare("SELECT * FROM DonXinNghi WHERE MaNV=? ORDER BY NgayTao DESC");
    $stmt->execute([$maNV]);
    $donList = $stmt->fetchAll();
} else {
    // Manager: toan quyen phe duyet nghi phep toan doanh nghiep
    $donList = $pdo->query("SELECT d.*, n.HoVaTen FROM DonXinNghi d JOIN NhanVien n ON d.MaNV=n.MaNV ORDER BY d.NgayTao DESC")->fetchAll();
}

renderHeader('Phê duyệt đơn nghỉ phép');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added' => ['green', 'Gửi đơn thành công!'], 'approved' => ['blue', 'Xử lý đơn thành công!']];
    [$cls, $txt] = $texts[$msg] ?? ['gray', ''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<?php if ($role === 'Employee'): ?>
<button onclick="toggleModal('modal-add-don', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4">
    <i class="fas fa-plus mr-1"></i> Tạo đơn xin nghỉ
</button>
<?php endif; ?>

<?php
$loaiNghiLabels = ['NghiPhepNam' => 'Phép năm', 'NghiOm' => 'Nghỉ ốm', 'NghiViecRieng' => 'Việc riêng', 'NghiKhongLuong' => 'Nghỉ không lương'];
$headers = $role === 'Employee' ? ['Loại nghỉ', 'Từ ngày', 'Đến ngày', 'Lý do', 'Trạng thái'] : ['Nhân viên', 'Loại nghỉ', 'Từ ngày', 'Đến ngày', 'Trạng thái', 'Hành động'];
$rows = [];
foreach ($donList as $d) {
    $badge = match($d['TrangThaiDuyet']) { 'DaDuyet' => '<span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Đã duyệt</span>', 'TuChoi' => '<span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Từ chối</span>', default => '<span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">Chờ duyệt</span>' };
    $action = '';
    if ($d['TrangThaiDuyet'] === 'ChoDuyet' && $role !== 'Employee') {
        $action = '<form method="POST" class="inline"><input type="hidden" name="action" value="approve"><input type="hidden" name="ma_don" value="'.$d['MaDon'].'"><button name="trang_thai_duyet" value="DaDuyet" class="text-green-500 hover:text-green-700 mr-1" title="Duyệt"><i class="fas fa-check-circle"></i></button><button name="trang_thai_duyet" value="TuChoi" class="text-red-500 hover:text-red-700" title="Từ chối"><i class="fas fa-times-circle"></i></button></form>';
    }
    if ($role === 'Employee') {
        $rows[] = [$loaiNghiLabels[$d['LoaiNghiPhep']] ?? $d['LoaiNghiPhep'], formatDateTime($d['TuNgay']), formatDateTime($d['DenNgay']), htmlspecialchars(mb_substr($d['LyDo'], 0, 30)), $badge];
    } else {
        $rows[] = [htmlspecialchars($d['HoVaTen'] ?? ''), $loaiNghiLabels[$d['LoaiNghiPhep']] ?? $d['LoaiNghiPhep'], formatDateTime($d['TuNgay']), formatDateTime($d['DenNgay']), $badge, $action];
    }
}
renderTable($headers, $rows);
?>

<?php renderModal('modal-add-don', 'Tạo đơn xin nghỉ phép', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div><label class="block text-sm font-medium mb-1">Loại nghỉ phép</label>
<select name="loai_nghi" class="w-full border rounded-lg px-3 py-2 text-sm" required>
<option value="NghiPhepNam">Phép năm</option><option value="NghiOm">Nghỉ ốm</option><option value="NghiViecRieng">Việc riêng</option><option value="NghiKhongLuong">Nghỉ không lương</option>
</select></div>
<div class="grid grid-cols-2 gap-4">
    <div><label class="block text-sm font-medium mb-1">Từ ngày</label><input type="datetime-local" name="tu_ngay" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
    <div><label class="block text-sm font-medium mb-1">Đến ngày</label><input type="datetime-local" name="den_ngay" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
</div>
<div><label class="block text-sm font-medium mb-1">Lý do</label><textarea name="ly_do" rows="3" required class="w-full border rounded-lg px-3 py-2 text-sm"></textarea></div>
<div class="flex justify-end gap-2 mt-4">
    <button type="button" onclick="toggleModal(\'modal-add-don\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Gửi đơn</button>
</div>
</form>
'); ?>
<?php renderFooter(); ?>

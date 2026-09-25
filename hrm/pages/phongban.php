<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pdo->prepare("INSERT INTO PhongBan(MaPhongBan,TenPhongBan) VALUES(?,?)")->execute([$_POST['ma_phong_ban'], $_POST['ten_phong_ban']]);
        header('Location: ?page=phongban&msg=added'); exit;
    } elseif ($action === 'edit') {
        $pdo->prepare("UPDATE PhongBan SET TenPhongBan=? WHERE MaPhongBan=?")->execute([$_POST['ten_phong_ban'], $_POST['ma_phong_ban']]);
        header('Location: ?page=phongban&msg=updated'); exit;
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM PhongBan WHERE MaPhongBan=?")->execute([$_POST['ma_phong_ban']]);
        header('Location: ?page=phongban&msg=deleted'); exit;
    }
}

// Muc 2.1: PhongBan chi gom (MaPhongBan, TenPhongBan) - khong quan ly Truong phong tren giao dien
$phongBans = $pdo->query("SELECT pb.*, (SELECT COUNT(*) FROM NhanVien n WHERE n.MaPhongBan=pb.MaPhongBan) AS SoNV FROM PhongBan pb ORDER BY pb.MaPhongBan")->fetchAll();

renderHeader('Quản lý Phòng ban');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added' => ['green','Thêm phòng ban thành công!'], 'updated' => ['blue','Cập nhật thành công!'], 'deleted' => ['red','Đã xóa phòng ban!']];
    [$cls, $txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<button onclick="toggleModal('modal-add-pb', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Thêm phòng ban</button>

<?php
$headers = ['Mã PB', 'Tên phòng ban', 'Số nhân viên', 'Hành động'];
$rows = [];
foreach ($phongBans as $pb) {
    $rows[] = [
        $pb['MaPhongBan'],
        htmlspecialchars($pb['TenPhongBan']),
        '<span class="px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-700">'.$pb['SoNV'].' NV</span>',
        '<button type="button" onclick=\'editPB('.json_encode($pb).')\' class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>'.
        '<form method="POST" class="inline" onsubmit="return confirm(\'Xóa?\')"><input type="hidden" name="action" value="delete"><input type="hidden" name="ma_phong_ban" value="'.$pb['MaPhongBan'].'"><button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>'
    ];
}
renderTable($headers, $rows);
?>

<?php renderModal('modal-add-pb', 'Thêm Phòng ban', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div><label class="block text-sm font-medium mb-1">Mã phòng ban *</label><input name="ma_phong_ban" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="VD: PB01"></div>
<div><label class="block text-sm font-medium mb-1">Tên phòng ban *</label><input name="ten_phong_ban" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-pb\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Thêm</button></div>
</form>
'); ?>

<div id="modal-edit-pb" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-lg font-semibold">Sửa Phòng ban</h3><button onclick="toggleModal('modal-edit-pb', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="edit"><input type="hidden" name="ma_phong_ban" id="epb-ma">
            <div><label class="block text-sm font-medium mb-1">Tên phòng ban</label><input name="ten_phong_ban" id="epb-ten" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-edit-pb', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cập nhật</button></div>
        </form>
    </div>
</div>
<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
function editPB(pb) { document.getElementById('epb-ma').value=pb.MaPhongBan; document.getElementById('epb-ten').value=pb.TenPhongBan; toggleModal('modal-edit-pb', true); }
</script>
<?php renderFooter(); ?>

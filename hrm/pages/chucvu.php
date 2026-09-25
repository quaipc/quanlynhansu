<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $pdo->prepare("INSERT INTO ChucVu(MaChucVu,TenChucVu) VALUES(?,?)")->execute([$_POST['ma_chuc_vu'], $_POST['ten_chuc_vu']]);
        header('Location: ?page=chucvu&msg=added'); exit;
    } elseif ($action === 'edit') {
        $pdo->prepare("UPDATE ChucVu SET TenChucVu=? WHERE MaChucVu=?")->execute([$_POST['ten_chuc_vu'], $_POST['ma_chuc_vu']]);
        header('Location: ?page=chucvu&msg=updated'); exit;
    } elseif ($action === 'delete') {
        $pdo->prepare("DELETE FROM ChucVu WHERE MaChucVu=?")->execute([$_POST['ma_chuc_vu']]);
        header('Location: ?page=chucvu&msg=deleted'); exit;
    }
}

$chucVus = $pdo->query("SELECT * FROM ChucVu ORDER BY MaChucVu")->fetchAll();

renderHeader('Quản lý Chức vụ');

if ($msg = $_GET['msg'] ?? '') {
    $texts = ['added' => ['green','Thêm chức vụ thành công!'], 'updated' => ['blue','Cập nhật thành công!'], 'deleted' => ['red','Đã xóa chức vụ!']];
    [$cls, $txt] = $texts[$msg] ?? ['',''];
    if ($txt) echo "<div class=\"bg-{$cls}-50 text-{$cls}-700 px-4 py-3 rounded-lg mb-4\">$txt</div>";
}
?>

<button onclick="toggleModal('modal-add-cv', true)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm mb-4"><i class="fas fa-plus mr-1"></i> Thêm chức vụ</button>

<?php
$headers = ['Mã chức vụ', 'Tên chức vụ', 'Hành động'];
$rows = [];
foreach ($chucVus as $cv) {
    $rows[] = [
        $cv['MaChucVu'],
        htmlspecialchars($cv['TenChucVu']),
        '<button onclick=\'editCV('.json_encode($cv).')\' class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>'.
        '<form method="POST" class="inline" onsubmit="return confirm(\'Xóa?\')"><input type="hidden" name="action" value="delete"><input type="hidden" name="ma_chuc_vu" value="'.$cv['MaChucVu'].'"><button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button></form>'
    ];
}
renderTable($headers, $rows);
?>

<?php renderModal('modal-add-cv', 'Thêm Chức vụ', '
<form method="POST" class="space-y-4">
<input type="hidden" name="action" value="add">
<div><label class="block text-sm font-medium mb-1">Mã chức vụ *</label><input name="ma_chuc_vu" required class="w-full border rounded-lg px-3 py-2 text-sm" placeholder="VD: CV01"></div>
<div><label class="block text-sm font-medium mb-1">Tên chức vụ *</label><input name="ten_chuc_vu" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
<div class="flex justify-end gap-2 mt-4"><button type="button" onclick="toggleModal(\'modal-add-cv\', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Thêm</button></div>
</form>
'); ?>

<div id="modal-edit-cv" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b"><h3 class="text-lg font-semibold">Sửa Chức vụ</h3><button onclick="toggleModal('modal-edit-cv', false)" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button></div>
        <form method="POST" class="px-6 py-4 space-y-4">
            <input type="hidden" name="action" value="edit"><input type="hidden" name="ma_chuc_vu" id="ecv-ma">
            <div><label class="block text-sm font-medium mb-1">Tên chức vụ</label><input name="ten_chuc_vu" id="ecv-ten" required class="w-full border rounded-lg px-3 py-2 text-sm"></div>
            <div class="flex justify-end gap-2"><button type="button" onclick="toggleModal('modal-edit-cv', false)" class="px-4 py-2 border rounded-lg text-sm">Hủy</button><button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Cập nhật</button></div>
        </form>
    </div>
</div>
<script>
function toggleModal(id, show) { const m=document.getElementById(id); if(show){m.classList.remove('hidden');m.classList.add('flex')}else{m.classList.add('hidden');m.classList.remove('flex')} }
function editCV(cv) { document.getElementById('ecv-ma').value=cv.MaChucVu; document.getElementById('ecv-ten').value=cv.TenChucVu; toggleModal('modal-edit-cv', true); }
</script>
<?php renderFooter(); ?>

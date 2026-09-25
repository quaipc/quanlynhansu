<?php
function renderHeader($pageTitle = 'HRM System') {
    $currentPage = $_GET['page'] ?? 'dashboard';
    $role = getUserRole();
    $userName = getUserName();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - HRM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .sidebar-link.active { background: rgba(255,255,255,0.15); border-right: 3px solid #60a5fa; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .stat-card { transition: all 0.3s ease; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); position: fixed !important; z-index: 50; } .sidebar.open { transform: translateX(0); } }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
<div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar w-64 bg-gradient-to-b from-slate-800 to-slate-900 text-white flex-shrink-0 flex flex-col transition-transform duration-300 fixed md:sticky top-0 h-screen overflow-y-auto">
        <div class="p-4 border-b border-slate-700">
            <h1 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-building text-blue-400"></i>
                <span>HRM System</span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">Quản lý nhân sự</p>
        </div>
        <nav class="flex-1 p-3 space-y-1">
            <a href="?page=dashboard" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt w-5 text-center"></i> Dashboard
            </a>
            <?php if ($role === 'Admin'): ?>
            <!-- Phan he Quan tri he thong: UC12, UC13, UC14 -->
            <p class="text-xs text-slate-400 px-3 pt-2 uppercase">Quản trị hệ thống</p>
            <a href="?page=taikhoan" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'taikhoan' ? 'active' : '' ?>">
                <i class="fas fa-user-cog w-5 text-center"></i> Tài khoản & RBAC
            </a>
            <a href="?page=phongban" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'phongban' ? 'active' : '' ?>">
                <i class="fas fa-sitemap w-5 text-center"></i> Phòng ban
            </a>
            <a href="?page=chucvu" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'chucvu' ? 'active' : '' ?>">
                <i class="fas fa-briefcase w-5 text-center"></i> Chức vụ
            </a>
            <a href="?page=hethong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'hethong' ? 'active' : '' ?>">
                <i class="fas fa-server w-5 text-center"></i> Cấu hình & Giám sát
            </a>
            <?php endif; ?>
            <?php if ($role === 'Manager'): ?>
            <!-- Phan he Quan ly dieu hanh: UC06-UC11 -->
            <p class="text-xs text-slate-400 px-3 pt-2 uppercase">Quản lý điều hành</p>
            <a href="?page=nhanvien" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'nhanvien' ? 'active' : '' ?>">
                <i class="fas fa-users w-5 text-center"></i> Hồ sơ nhân sự
            </a>
            <a href="?page=hopdong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'hopdong' ? 'active' : '' ?>">
                <i class="fas fa-file-contract w-5 text-center"></i> Hợp đồng
            </a>
            <a href="?page=donxinnghi" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'donxinnghi' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check w-5 text-center"></i> Duyệt đơn nghỉ
            </a>
            <a href="?page=dieuchuyen" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'dieuchuyen' ? 'active' : '' ?>">
                <i class="fas fa-exchange-alt w-5 text-center"></i> Điều chuyển
            </a>
            <a href="?page=bangluong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'bangluong' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave w-5 text-center"></i> Bảng lương
            </a>
            <a href="?page=chamcong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'chamcong' ? 'active' : '' ?>">
                <i class="fas fa-clock w-5 text-center"></i> Giám sát chấm công
            </a>
            <a href="?page=daotao" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'daotao' ? 'active' : '' ?>">
                <i class="fas fa-graduation-cap w-5 text-center"></i> Đào tạo
            </a>
            <a href="?page=baocao" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'baocao' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar w-5 text-center"></i> Báo cáo
            </a>
            <?php endif; ?>
            <?php if ($role === 'Employee'): ?>
            <!-- Phan he Cong tu phuc vu: UC01-UC05 -->
            <p class="text-xs text-slate-400 px-3 pt-2 uppercase">Tự phục vụ</p>
            <a href="?page=hosocanhan" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'hosocanhan' ? 'active' : '' ?>">
                <i class="fas fa-id-card w-5 text-center"></i> Hồ sơ cá nhân
            </a>
            <a href="?page=chamcong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'chamcong' ? 'active' : '' ?>">
                <i class="fas fa-clock w-5 text-center"></i> Chấm công
            </a>
            <a href="?page=donxinnghi" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'donxinnghi' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check w-5 text-center"></i> Đơn xin nghỉ
            </a>
            <a href="?page=bangluong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'bangluong' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave w-5 text-center"></i> Phiếu lương
            </a>
            <a href="?page=daotao" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'daotao' ? 'active' : '' ?>">
                <i class="fas fa-graduation-cap w-5 text-center"></i> Đào tạo
            </a>
            <a href="?page=hopdong" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $currentPage === 'hopdong' ? 'active' : '' ?>">
                <i class="fas fa-file-contract w-5 text-center"></i> Hợp đồng của tôi
            </a>
            <?php endif; ?>
        </nav>
        <div class="p-3 border-t border-slate-700">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-sm font-bold"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate"><?= htmlspecialchars($userName) ?></p>
                    <p class="text-xs text-slate-400"><?= getVaiTroLabel($role) ?></p>
                </div>
            </div>
            <a href="?page=logout" class="sidebar-link flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-red-300 mt-1">
                <i class="fas fa-sign-out-alt w-5 text-center"></i> Đăng xuất
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0">
        <header class="bg-white shadow-sm sticky top-0 z-40">
            <div class="flex items-center justify-between px-4 md:px-6 py-3">
                <button onclick="document.getElementById('sidebar').classList.toggle('open')" class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-lg font-semibold text-gray-800"><?= $pageTitle ?></h2>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500"><?= date('d/m/Y') ?></span>
                </div>
            </div>
        </header>
        <div class="p-4 md:p-6">
<?php
}

function renderFooter() {
?>
        </div>
    </main>
</div>
<script>
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth < 768 && sidebar.classList.contains('open') && !sidebar.contains(e.target)) {
        sidebar.classList.remove('open');
    }
});
</script>
</body>
</html>
<?php
}

function renderStats($stats) {
?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <?php foreach ($stats as $s): ?>
    <div class="stat-card bg-white rounded-xl p-5 shadow-sm border-l-4 <?= $s['color'] ?>">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500"><?= $s['label'] ?></p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $s['value'] ?></p>
            </div>
            <div class="w-12 h-12 <?= $s['iconBg'] ?> rounded-full flex items-center justify-center">
                <i class="<?= $s['icon'] ?> <?= $s['iconColor'] ?> text-xl"></i>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php
}

function renderTable($headers, $rows, $emptyMsg = 'Không có dữ liệu') {
?>
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <?php foreach ($headers as $h): ?>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600"><?= $h ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($rows)): ?>
                <tr><td colspan="<?= count($headers) ?>" class="px-4 py-8 text-center text-gray-400"><?= $emptyMsg ?></td></tr>
                <?php else: ?>
                <?php foreach ($rows as $row): ?>
                <tr class="hover:bg-gray-50">
                    <?php foreach ($row as $cell): ?>
                    <td class="px-4 py-3"><?= $cell ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
}

function renderModal($id, $title, $body, $footer = '') {
?>
<div id="<?= $id ?>" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold"><?= $title ?></h3>
            <button onclick="toggleModal('<?= $id ?>', false)" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="px-6 py-4"><?= $body ?></div>
        <?php if ($footer): ?>
        <div class="px-6 py-4 border-t bg-gray-50 rounded-b-xl"><?= $footer ?></div>
        <?php endif; ?>
    </div>
</div>
<script>
function toggleModal(id, show) {
    const m = document.getElementById(id);
    if (show) { m.classList.remove('hidden'); m.classList.add('flex'); }
    else { m.classList.add('hidden'); m.classList.remove('flex'); }
}
</script>
<?php
}

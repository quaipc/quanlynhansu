<?php
/**
 * HRM System - Script cai dat du lieu mau
 * Chay trinh duyet: http://localhost/bai/hrm/setup.php
 * Sau khi setup xong, XOA file nay!
 */

require_once 'config/db.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup'])) {
    try {
        // Xoa du lieu cu
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        $tables = ['ChiTietDaoTao','KhoaHoc','BangLuong','DonXinNghi','ChamCong','HopDongLaoDong','DieuChuyenNhanVien','NhanVien','PhongBan','ChucVu','TaiKhoan'];
        foreach ($tables as $t) $pdo->exec("TRUNCATE TABLE $t");
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

        // Tao tai khoan voi mat khau hash
        $hash = password_hash('123456', PASSWORD_DEFAULT);

        // Tai khoan
        $stmt = $pdo->prepare("INSERT INTO TaiKhoan(TenDangNhap, MatKhau, VaiTro) VALUES(?,?,?)");
        $accounts = [
            ['admin','Admin'],['manager1','Manager'],['manager2','Manager'],
            ['employee1','Employee'],['employee2','Employee'],['employee3','Employee'],
            ['employee4','Employee'],['employee5','Employee']
        ];
        foreach ($accounts as [$user,$role]) $stmt->execute([$user, $hash, $role]);

        // Chuc vu
        $cvData = [['CV01','Giám đốc'],['CV02','Phó giám đốc'],['CV03','Trưởng phòng'],['CV04','Phó phòng'],['CV05','Nhân viên'],['CV06','Thực tập sinh']];
        $stmt = $pdo->prepare("INSERT INTO ChucVu VALUES(?,?)");
        foreach ($cvData as $cv) $stmt->execute($cv);

        // Phong ban
        $pbData = [['PB01','Phòng Nhân sự'],['PB02','Phòng Kế toán'],['PB03','Phòng Kỹ thuật'],['PB04','Phòng Marketing'],['PB05','Phòng Kinh doanh']];
        $stmt = $pdo->prepare("INSERT INTO PhongBan(MaPhongBan,TenPhongBan) VALUES(?,?)");
        foreach ($pbData as $pb) $stmt->execute($pb);

        // Nhan vien
        $nvData = [
            ['NV001','Nguyen Van Admin','1985-03-15','Nam','001234567890','0901234567','admin@hrm.com','Da Nang','PB01','CV01',1,'DangLamViec','2020-01-01'],
            ['NV002','Tran Thi Manager','1990-06-20','Nu','001234567891','0901234568','manager1@hrm.com','Da Nang','PB01','CV03',2,'DangLamViec','2021-03-15'],
            ['NV003','Le Van Manager2','1988-09-10','Nam','001234567892','0901234569','manager2@hrm.com','Da Nang','PB03','CV03',3,'DangLamViec','2021-06-01'],
            ['NV004','Pham Minh Employee','1995-01-25','Nam','001234567893','0901234570','emp1@hrm.com','Da Nang','PB01','CV05',4,'DangLamViec','2022-04-10'],
            ['NV005','Hoang Thi Emp2','1997-07-12','Nu','001234567894','0901234571','emp2@hrm.com','Da Nang','PB01','CV05',5,'DangLamViec','2022-05-20'],
            ['NV006','Nguyen Van Emp3','1993-11-08','Nam','001234567895','0901234572','emp3@hrm.com','Da Nang','PB03','CV05',6,'DangLamViec','2022-06-15'],
            ['NV007','Tran Thi Emp4','1996-04-18','Nu','001234567896','0901234573','emp4@hrm.com','Da Nang','PB03','CV06',7,'DangLamViec','2023-01-10'],
            ['NV008','Vo Van Emp5','1998-08-30','Nam','001234567897','0901234574','emp5@hrm.com','Da Nang','PB02','CV05',8,'DangLamViec','2023-03-01']
        ];
        $stmt = $pdo->prepare("INSERT INTO NhanVien(MaNV,HoVaTen,NgaySinh,GioiTinh,CCCD,SoDienThoai,Email,DiaChiThuongTru,MaPhongBan,MaChucVu,MaTaiKhoan,TrangThaiLamViec,NgayVaoLam) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
        foreach ($nvData as $nv) $stmt->execute($nv);

        // Hop dong
        $hdData = [
            ['HD001','NV001','KhongXacDinhThoiHan','2020-01-01',null,25000000,'HieuLuc'],
            ['HD002','NV002','KhongXacDinhThoiHan','2021-03-15',null,18000000,'HieuLuc'],
            ['HD003','NV003','KhongXacDinhThoiHan','2021-06-01',null,18000000,'HieuLuc'],
            ['HD004','NV004','XacDinhThoiHan','2022-04-10','2025-04-10',12000000,'HieuLuc'],
            ['HD005','NV005','XacDinhThoiHan','2022-05-20','2025-05-20',12000000,'HieuLuc'],
            ['HD006','NV006','XacDinhThoiHan','2022-06-15','2025-06-15',13000000,'HieuLuc'],
            ['HD007','NV007','ThuViec','2023-01-10','2023-04-10',8000000,'HetHan'],
            ['HD008','NV008','XacDinhThoiHan','2023-03-01','2026-03-01',14000000,'HieuLuc']
        ];
        $stmt = $pdo->prepare("INSERT INTO HopDongLaoDong(MaHopDong,MaNV,LoaiHopDong,NgayBatDau,NgayKetThuc,LuongCoBan,TrangThaiHopDong) VALUES(?,?,?,?,?,?,?)");
        foreach ($hdData as $hd) $stmt->execute($hd);

        // Cham cong
        $ccData = [
            ['NV001','2026-09-01','08:25:00','17:05:00','DungGio'],
            ['NV001','2026-09-02','08:20:00','17:10:00','DungGio'],
            ['NV001','2026-09-03','08:35:00','17:00:00','DiMuon'],
            ['NV002','2026-09-01','08:15:00','17:00:00','DungGio'],
            ['NV002','2026-09-02','08:20:00','16:30:00','VeSom'],
            ['NV003','2026-09-01','08:10:00','17:05:00','DungGio'],
            ['NV004','2026-09-01','08:28:00','17:00:00','DungGio'],
            ['NV004','2026-09-02','08:45:00','17:10:00','DiMuon'],
            ['NV005','2026-09-01','08:20:00','17:00:00','DungGio'],
            ['NV006','2026-09-01','08:22:00','17:05:00','DungGio'],
            ['NV007','2026-09-01','08:30:00','17:00:00','DungGio'],
            ['NV008','2026-09-01','08:25:00','17:15:00','DungGio']
        ];
        $stmt = $pdo->prepare("INSERT INTO ChamCong(MaNV,NgayChamCong,ThoiGianVao,ThoiGianRa,TrangThaiCong) VALUES(?,?,?,?,?)");
        foreach ($ccData as $cc) $stmt->execute($cc);

        // Don xin nghi
        $dnData = [
            ['NV004','NghiPhepNam','2026-09-10 08:00:00','2026-09-11 17:00:00','Nghỉ phép năm đi du lịch','ChoDuyet',null],
            ['NV005','NghiOm','2026-09-05 08:00:00','2026-09-05 17:00:00','Bị cảm sốt','DaDuyet','NV002'],
            ['NV006','NghiViecRieng','2026-09-15 08:00:00','2026-09-15 17:00:00','Giải quyết việc gia đình','ChoDuyet',null]
        ];
        $stmt = $pdo->prepare("INSERT INTO DonXinNghi(MaNV,LoaiNghiPhep,TuNgay,DenNgay,LyDo,TrangThaiDuyet,MaNguoiDuyet) VALUES(?,?,?,?,?,?,?)");
        foreach ($dnData as $dn) $stmt->execute($dn);

        // Bang luong
        $blData = [
            ['NV001','2026-08',26,24.0,25000000,2000000,1000000,1500000,25961538.46,'DaThanhToan'],
            ['NV002','2026-08',26,22.0,18000000,1500000,500000,1200000,16384615.38,'DaThanhToan'],
            ['NV003','2026-08',26,25.0,18000000,1500000,800000,1200000,18307692.31,'DaThanhToan'],
            ['NV004','2026-08',26,23.0,12000000,1000000,300000,900000,11215384.62,'DaThanhToan'],
            ['NV005','2026-08',26,20.0,12000000,1000000,200000,900000,9615384.62,'ChuaThanhToan']
        ];
        $stmt = $pdo->prepare("INSERT INTO BangLuong(MaNV,ThangNam,SoNgayCongChuan,SoNgayThucTe,LuongCoBan,TienPhuCap,TienThuong,CacKhoanKhauTru,ThucLinh,TrangThaiThanhToan) VALUES(?,?,?,?,?,?,?,?,?,?)");
        foreach ($blData as $bl) $stmt->execute($bl);

        // Khoa hoc
        $khData = [
            ['KH001','Lập trình PHP nâng cao','Khóa học về PHP 8, Laravel, MySQL','Online','TS. Nguyen Van A','2026-09-01','2026-10-30',50000000],
            ['KH002','Quản trị nhân sự hiện đại','Phương pháp quản lý nhân sự 4.0','Offline','PGS. Tran Thi B','2026-09-15','2026-09-20',30000000],
            ['KH003','An ninh mạng cơ bản','Nhập môn cybersecurity','Hybrid','Ths. Le Van C','2026-10-01','2026-11-30',40000000]
        ];
        $stmt = $pdo->prepare("INSERT INTO KhoaHoc(MaKhoaHoc,TenKhoaHoc,MoTa,HinhThuc,GiangVien,NgayBatDau,NgayKetThuc,NganSachDuKien) VALUES(?,?,?,?,?,?,?,?)");
        foreach ($khData as $kh) $stmt->execute($kh);

        // Chi tiet dao tao
        $ctData = [
            ['KH001','NV006','DangHoc',null],['KH001','NV007','DangHoc',null],
            ['KH002','NV002','HoanThanh',8.50],['KH002','NV004','DangHoc',null],
            ['KH003','NV003','DangHoc',null]
        ];
        $stmt = $pdo->prepare("INSERT INTO ChiTietDaoTao(MaKhoaHoc,MaNV,TrangThaiHoc,DiemSo) VALUES(?,?,?,?)");
        foreach ($ctData as $ct) $stmt->execute($ct);

        $message = 'Cai dat du lieu mau thanh cong! Tat ca tai khoan mat khau la: 123456';
    } catch (PDOException $e) {
        $error = 'Loi: ' . $e->getMessage();
    }
}

// Dem du lieu
$count = [];
try {
    $tables = ['TaiKhoan','ChucVu','PhongBan','NhanVien','HopDongLaoDong','ChamCong','DonXinNghi','BangLuong','KhoaHoc','ChiTietDaoTao','DieuChuyenNhanVien'];
    foreach ($tables as $t) {
        $count[$t] = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
    }
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - HRM System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
<div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg">
    <h1 class="text-2xl font-bold text-center mb-2">HRM System - Setup</h1>
    <p class="text-gray-500 text-center text-sm mb-6">Cai dat du lieu mau cho he thong quan ly nhan su</p>

    <?php if ($message): ?>
    <div class="bg-green-50 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm"><?= $message ?></div>
    <a href="?page=login" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold">Dang nhap he thong</a>
    <?php else: ?>

    <?php if ($error): ?>
    <div class="bg-red-50 text-red-600 px-4 py-3 rounded-lg mb-4 text-sm"><?= $error ?></div>
    <?php endif; ?>

    <?php if (!empty($count)): ?>
    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
        <p class="text-sm font-medium mb-2">Du lieu hien tai:</p>
        <div class="grid grid-cols-2 gap-2 text-sm">
            <?php foreach ($count as $tbl => $cnt): ?>
            <div class="flex justify-between"><span><?= $tbl ?></span><span class="font-mono"><?= $cnt ?></span></div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <form method="POST">
        <button name="setup" value="1" onclick="return confirm('Du lieu cu se bi xoa. Ban co chac chan?')" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold text-lg">
            Cai dat du lieu mau
        </button>
    </form>
    <?php endif; ?>

    <p class="text-xs text-gray-400 text-center mt-6">Sau khi setup xong, hay xoa file setup.php!</p>
</div>
</body>
</html>

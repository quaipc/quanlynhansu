CREATE DATABASE IF NOT EXISTS hrm_system
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hrm_system;

-- 1. Bảng Tài khoản
CREATE TABLE TaiKhoan (
    MaTaiKhoan INT AUTO_INCREMENT PRIMARY KEY,
    TenDangNhap VARCHAR(50) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NOT NULL,
    VaiTro ENUM('Admin', 'Manager', 'Employee') NOT NULL DEFAULT 'Employee',
    TrangThai TINYINT DEFAULT 1,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Bảng Chức vụ
CREATE TABLE ChucVu (
    MaChucVu VARCHAR(20) PRIMARY KEY,
    TenChucVu VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 3. Bảng Phòng ban (Đã xóa MaTruongPhong)
CREATE TABLE PhongBan (
    MaPhongBan VARCHAR(20) PRIMARY KEY,
    TenPhongBan VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- 4. Bảng Nhân viên cốt lõi
CREATE TABLE NhanVien (
    MaNV VARCHAR(20) PRIMARY KEY,
    HoVaTen VARCHAR(100) NOT NULL,
    NgaySinh DATE NOT NULL,
    GioiTinh ENUM('Nam', 'Nu', 'Khac') NOT NULL,
    CCCD VARCHAR(20) NOT NULL UNIQUE,
    SoDienThoai VARCHAR(15) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    DiaChiThuongTru TEXT,
    MaPhongBan VARCHAR(20),
    MaChucVu VARCHAR(20),
    MaTaiKhoan INT UNIQUE,
    TrangThaiLamViec ENUM('DangLamViec', 'NghiPhep', 'DaNghiViec') DEFAULT 'DangLamViec',
    NgayVaoLam DATE NOT NULL,
    FOREIGN KEY (MaPhongBan) REFERENCES PhongBan(MaPhongBan) ON DELETE SET NULL,
    FOREIGN KEY (MaChucVu) REFERENCES ChucVu(MaChucVu) ON DELETE SET NULL,
    FOREIGN KEY (MaTaiKhoan) REFERENCES TaiKhoan(MaTaiKhoan) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Bảng Lịch sử Điều chuyển Nhân viên
CREATE TABLE DieuChuyenNhanVien (
    MaDieuChuyen INT AUTO_INCREMENT PRIMARY KEY,
    MaNV VARCHAR(20) NOT NULL,
    PhongBanCu VARCHAR(20),
    PhongBanMoi VARCHAR(20),
    ChucVuCu VARCHAR(20),
    ChucVuMoi VARCHAR(20),
    NgayCoHieuLuc DATE NOT NULL,
    LyDoDieuChuyen TEXT,
    MaNguoiQuyetDinh VARCHAR(20),
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE,
    FOREIGN KEY (PhongBanCu) REFERENCES PhongBan(MaPhongBan) ON DELETE SET NULL,
    FOREIGN KEY (PhongBanMoi) REFERENCES PhongBan(MaPhongBan) ON DELETE SET NULL,
    FOREIGN KEY (ChucVuCu) REFERENCES ChucVu(MaChucVu) ON DELETE SET NULL,
    FOREIGN KEY (ChucVuMoi) REFERENCES ChucVu(MaChucVu) ON DELETE SET NULL,
    FOREIGN KEY (MaNguoiQuyetDinh) REFERENCES NhanVien(MaNV) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 6. Bảng Hợp đồng Lao động
CREATE TABLE HopDongLaoDong (
    MaHopDong VARCHAR(50) PRIMARY KEY,
    MaNV VARCHAR(20) NOT NULL,
    LoaiHopDong ENUM('ThuViec', 'XacDinhThoiHan', 'KhongXacDinhThoiHan') NOT NULL,
    NgayBatDau DATE NOT NULL,
    NgayKetThuc DATE NULL,
    LuongCoBan DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    TrangThaiHopDong ENUM('HieuLuc', 'HetHan', 'HuyBo') DEFAULT 'HieuLuc',
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Bảng Chấm công (có duyệt công của Manager)
CREATE TABLE ChamCong (
    ID_ChamCong BIGINT AUTO_INCREMENT PRIMARY KEY,
    MaNV VARCHAR(20) NOT NULL,
    NgayChamCong DATE NOT NULL,
    ThoiGianVao TIME NULL,
    ThoiGianRa TIME NULL,
    TrangThaiCong ENUM('DungGio', 'DiMuon', 'VeSom', 'NghiKhongPhep', 'NghiCoPhep') DEFAULT 'DungGio',
    TrangThaiDuyet ENUM('ChoDuyet', 'DaDuyet', 'TuChoi') NOT NULL DEFAULT 'ChoDuyet',
    MaNguoiDuyet VARCHAR(20) NULL,
    UNIQUE KEY UQ_ChamCong_Ngay (MaNV, NgayChamCong),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE,
    FOREIGN KEY (MaNguoiDuyet) REFERENCES NhanVien(MaNV) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. Bảng Đơn xin nghỉ phép
CREATE TABLE DonXinNghi (
    MaDon INT AUTO_INCREMENT PRIMARY KEY,
    MaNV VARCHAR(20) NOT NULL,
    LoaiNghiPhep ENUM('NghiPhepNam', 'NghiOm', 'NghiViecRieng', 'NghiKhongLuong') NOT NULL,
    TuNgay DATETIME NOT NULL,
    DenNgay DATETIME NOT NULL,
    LyDo TEXT NOT NULL,
    TrangThaiDuyet ENUM('ChoDuyet', 'DaDuyet', 'TuChoi') DEFAULT 'ChoDuyet',
    MaNguoiDuyet VARCHAR(20) NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE,
    FOREIGN KEY (MaNguoiDuyet) REFERENCES NhanVien(MaNV) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 9. Bảng Lương hàng tháng
CREATE TABLE BangLuong (
    MaBangLuong INT AUTO_INCREMENT PRIMARY KEY,
    MaNV VARCHAR(20) NOT NULL,
    ThangNam VARCHAR(7) NOT NULL COMMENT 'Định dạng YYYY-MM',
    SoNgayCongChuan INT DEFAULT 26,
    SoNgayThucTe DECIMAL(4, 1) NOT NULL DEFAULT 0.0,
    LuongCoBan DECIMAL(15, 2) NOT NULL,
    TienPhuCap DECIMAL(15, 2) DEFAULT 0.00,
    TienThuong DECIMAL(15, 2) DEFAULT 0.00,
    CacKhoanKhauTru DECIMAL(15, 2) DEFAULT 0.00,
    ThucLinh DECIMAL(15, 2) NOT NULL,
    TrangThaiThanhToan ENUM('ChuaThanhToan', 'DaThanhToan') DEFAULT 'ChuaThanhToan',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY UQ_Luong_Thang (MaNV, ThangNam),
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Bảng Khóa học Đào tạo
CREATE TABLE KhoaHoc (
    MaKhoaHoc VARCHAR(20) PRIMARY KEY,
    TenKhoaHoc VARCHAR(150) NOT NULL,
    MoTa TEXT,
    HinhThuc ENUM('Online', 'Offline', 'Hybrid') DEFAULT 'Offline',
    GiangVien VARCHAR(100),
    NgayBatDau DATE NOT NULL,
    NgayKetThuc DATE NOT NULL,
    NganSachDuKien DECIMAL(15, 2) DEFAULT 0.00
) ENGINE=InnoDB;

-- 11. Bảng Đăng ký Đào tạo
CREATE TABLE ChiTietDaoTao (
    ID_ChiTiet INT AUTO_INCREMENT PRIMARY KEY,
    MaKhoaHoc VARCHAR(20) NOT NULL,
    MaNV VARCHAR(20) NOT NULL,
    TrangThaiHoc ENUM('DangHoc', 'HoanThanh', 'HuyBo') DEFAULT 'DangHoc',
    DiemSo DECIMAL(4, 2) NULL,
    FOREIGN KEY (MaKhoaHoc) REFERENCES KhoaHoc(MaKhoaHoc) ON DELETE CASCADE,
    FOREIGN KEY (MaNV) REFERENCES NhanVien(MaNV) ON DELETE CASCADE
) ENGINE=InnoDB;

USE hrm_system;

-- Tai khoan (mat khau mac dinh: 123456)
INSERT INTO TaiKhoan (TenDangNhap, MatKhau, VaiTro) VALUES
('admin', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Admin'),
('manager1', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Manager'),
('manager2', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Manager'),
('employee1', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Employee'),
('employee2', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Employee'),
('employee3', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Employee'),
('employee4', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Employee'),
('employee5', '$2y$10$YourHashHere12345678901234567890123456789012345', 'Employee');

-- Chuc vu
INSERT INTO ChucVu (MaChucVu, TenChucVu) VALUES
('CV01', 'Giám đốc'),
('CV02', 'Phó giám đốc'),
('CV03', 'Trưởng phòng'),
('CV04', 'Phó phòng'),
('CV05', 'Nhân viên'),
('CV06', 'Thực tập sinh');

-- Phong ban
INSERT INTO PhongBan (MaPhongBan, TenPhongBan) VALUES
('PB01', 'Phòng Nhân sự'),
('PB02', 'Phòng Kế toán'),
('PB03', 'Phòng Kỹ thuật'),
('PB04', 'Phòng Marketing'),
('PB05', 'Phòng Kinh doanh');

-- Nhan vien
INSERT INTO NhanVien (MaNV, HoVaTen, NgaySinh, GioiTinh, CCCD, SoDienThoai, Email, DiaChiThuongTru, MaPhongBan, MaChucVu, MaTaiKhoan, TrangThaiLamViec, NgayVaoLam) VALUES
('NV001', 'Nguyen Van Admin', '1985-03-15', 'Nam', '001234567890', '0901234567', 'admin@hrm.com', 'Da Nang', 'PB01', 'CV01', 1, 'DangLamViec', '2020-01-01'),
('NV002', 'Tran Thi Manager', '1990-06-20', 'Nu', '001234567891', '0901234568', 'manager1@hrm.com', 'Da Nang', 'PB01', 'CV03', 2, 'DangLamViec', '2021-03-15'),
('NV003', 'Le Van Manager2', '1988-09-10', 'Nam', '001234567892', '0901234569', 'manager2@hrm.com', 'Da Nang', 'PB03', 'CV03', 3, 'DangLamViec', '2021-06-01'),
('NV004', 'Pham Minh Employee', '1995-01-25', 'Nam', '001234567893', '0901234570', 'emp1@hrm.com', 'Da Nang', 'PB01', 'CV05', 4, 'DangLamViec', '2022-04-10'),
('NV005', 'Hoang Thi Emp2', '1997-07-12', 'Nu', '001234567894', '0901234571', 'emp2@hrm.com', 'Da Nang', 'PB01', 'CV05', 5, 'DangLamViec', '2022-05-20'),
('NV006', 'Nguyen Van Emp3', '1993-11-08', 'Nam', '001234567895', '0901234572', 'emp3@hrm.com', 'Da Nang', 'PB03', 'CV05', 6, 'DangLamViec', '2022-06-15'),
('NV007', 'Tran Thi Emp4', '1996-04-18', 'Nu', '001234567896', '0901234573', 'emp4@hrm.com', 'Da Nang', 'PB03', 'CV06', 7, 'DangLamViec', '2023-01-10'),
('NV008', 'Vo Van Emp5', '1998-08-30', 'Nam', '001234567897', '0901234574', 'emp5@hrm.com', 'Da Nang', 'PB02', 'CV05', 8, 'DangLamViec', '2023-03-01');

-- Hop dong lao dong
INSERT INTO HopDongLaoDong (MaHopDong, MaNV, LoaiHopDong, NgayBatDau, NgayKetThuc, LuongCoBan, TrangThaiHopDong) VALUES
('HD001', 'NV001', 'KhongXacDinhThoiHan', '2020-01-01', NULL, 25000000, 'HieuLuc'),
('HD002', 'NV002', 'KhongXacDinhThoiHan', '2021-03-15', NULL, 18000000, 'HieuLuc'),
('HD003', 'NV003', 'KhongXacDinhThoiHan', '2021-06-01', NULL, 18000000, 'HieuLuc'),
('HD004', 'NV004', 'XacDinhThoiHan', '2022-04-10', '2025-04-10', 12000000, 'HieuLuc'),
('HD005', 'NV005', 'XacDinhThoiHan', '2022-05-20', '2025-05-20', 12000000, 'HieuLuc'),
('HD006', 'NV006', 'XacDinhThoiHan', '2022-06-15', '2025-06-15', 13000000, 'HieuLuc'),
('HD007', 'NV007', 'ThuViec', '2023-01-10', '2023-04-10', 8000000, 'HetHan'),
('HD008', 'NV008', 'XacDinhThoiHan', '2023-03-01', '2026-03-01', 14000000, 'HieuLuc');

-- Cham cong (thang 9/2026)
INSERT INTO ChamCong (MaNV, NgayChamCong, ThoiGianVao, ThoiGianRa, TrangThaiCong) VALUES
('NV001', '2026-09-01', '08:25:00', '17:05:00', 'DungGio'),
('NV001', '2026-09-02', '08:20:00', '17:10:00', 'DungGio'),
('NV001', '2026-09-03', '08:35:00', '17:00:00', 'DiMuon'),
('NV002', '2026-09-01', '08:15:00', '17:00:00', 'DungGio'),
('NV002', '2026-09-02', '08:20:00', '16:30:00', 'VeSom'),
('NV003', '2026-09-01', '08:10:00', '17:05:00', 'DungGio'),
('NV004', '2026-09-01', '08:28:00', '17:00:00', 'DungGio'),
('NV004', '2026-09-02', '08:45:00', '17:10:00', 'DiMuon'),
('NV005', '2026-09-01', '08:20:00', '17:00:00', 'DungGio'),
('NV006', '2026-09-01', '08:22:00', '17:05:00', 'DungGio'),
('NV007', '2026-09-01', '08:30:00', '17:00:00', 'DungGio'),
('NV008', '2026-09-01', '08:25:00', '17:15:00', 'DungGio');

-- Don xin nghi
INSERT INTO DonXinNghi (MaNV, LoaiNghiPhep, TuNgay, DenNgay, LyDo, TrangThaiDuyet, MaNguoiDuyet) VALUES
('NV004', 'NghiPhepNam', '2026-09-10 08:00:00', '2026-09-11 17:00:00', 'Nghỉ phép năm đi du lịch', 'ChoDuyet', NULL),
('NV005', 'NghiOm', '2026-09-05 08:00:00', '2026-09-05 17:00:00', 'Bị cảm sốt', 'DaDuyet', 'NV002'),
('NV006', 'NghiViecRieng', '2026-09-15 08:00:00', '2026-09-15 17:00:00', 'Giải quyết việc gia đình', 'ChoDuyet', NULL);

-- Bang luong
INSERT INTO BangLuong (MaNV, ThangNam, SoNgayCongChuan, SoNgayThucTe, LuongCoBan, TienPhuCap, TienThuong, CacKhoanKhauTru, ThucLinh, TrangThaiThanhToan) VALUES
('NV001', '2026-08', 26, 24.0, 25000000, 2000000, 1000000, 1500000, 25961538.46, 'DaThanhToan'),
('NV002', '2026-08', 26, 22.0, 18000000, 1500000, 500000, 1200000, 16384615.38, 'DaThanhToan'),
('NV003', '2026-08', 26, 25.0, 18000000, 1500000, 800000, 1200000, 18307692.31, 'DaThanhToan'),
('NV004', '2026-08', 26, 23.0, 12000000, 1000000, 300000, 900000, 11215384.62, 'DaThanhToan'),
('NV005', '2026-08', 26, 20.0, 12000000, 1000000, 200000, 900000, 9615384.62, 'ChuaThanhToan');

-- Khoa hoc dao tao
INSERT INTO KhoaHoc (MaKhoaHoc, TenKhoaHoc, MoTa, HinhThuc, GiangVien, NgayBatDau, NgayKetThuc, NganSachDuKien) VALUES
('KH001', 'Lập trình PHP nâng cao', 'Khóa học về PHP 8, Laravel, MySQL', 'Online', 'TS. Nguyen Van A', '2026-09-01', '2026-10-30', 50000000),
('KH002', 'Quản trị nhân sự hiện đại', 'Phương pháp quản lý nhân sự 4.0', 'Offline', 'PGS. Tran Thi B', '2026-09-15', '2026-09-20', 30000000),
('KH003', 'An ninh mạng cơ bản', 'Nhập môn cybersecurity', 'Hybrid', 'Ths. Le Van C', '2026-10-01', '2026-11-30', 40000000);

-- Chi tiet dao tao
INSERT INTO ChiTietDaoTao (MaKhoaHoc, MaNV, TrangThaiHoc, DiemSo) VALUES
('KH001', 'NV006', 'DangHoc', NULL),
('KH001', 'NV007', 'DangHoc', NULL),
('KH002', 'NV002', 'HoanThanh', 8.50),
('KH002', 'NV004', 'DangHoc', NULL),
('KH003', 'NV003', 'DangHoc', NULL);

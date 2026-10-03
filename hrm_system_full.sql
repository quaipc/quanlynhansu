-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: hrm_system
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bangluong`
--

DROP TABLE IF EXISTS `bangluong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bangluong` (
  `MaBangLuong` int(11) NOT NULL AUTO_INCREMENT,
  `MaNV` varchar(20) NOT NULL,
  `ThangNam` varchar(7) NOT NULL COMMENT 'Dinh dang YYYY-MM',
  `SoNgayCongChuan` int(11) DEFAULT 26,
  `SoNgayThucTe` decimal(4,1) NOT NULL DEFAULT 0.0,
  `LuongCoBan` decimal(15,2) NOT NULL,
  `TienPhuCap` decimal(15,2) DEFAULT 0.00,
  `TienThuong` decimal(15,2) DEFAULT 0.00,
  `CacKhoanKhauTru` decimal(15,2) DEFAULT 0.00,
  `ThucLinh` decimal(15,2) NOT NULL,
  `TrangThaiThanhToan` enum('ChuaThanhToan','DaThanhToan') DEFAULT 'ChuaThanhToan',
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaBangLuong`),
  UNIQUE KEY `UQ_Luong_Thang` (`MaNV`,`ThangNam`),
  CONSTRAINT `bangluong_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bangluong`
--

LOCK TABLES `bangluong` WRITE;
/*!40000 ALTER TABLE `bangluong` DISABLE KEYS */;
INSERT INTO `bangluong` VALUES (1,'NV001','2026-08',26,24.0,25000000.00,2000000.00,1000000.00,1500000.00,25961538.46,'DaThanhToan','2026-09-22 15:10:39'),(2,'NV002','2026-08',26,22.0,18000000.00,1500000.00,500000.00,1200000.00,16384615.38,'DaThanhToan','2026-09-22 15:10:39'),(3,'NV003','2026-08',26,25.0,18000000.00,1500000.00,800000.00,1200000.00,18307692.31,'DaThanhToan','2026-09-22 15:10:39'),(4,'NV004','2026-08',26,23.0,12000000.00,1000000.00,300000.00,900000.00,11215384.62,'DaThanhToan','2026-09-22 15:10:39'),(5,'NV005','2026-08',26,20.0,12000000.00,1000000.00,200000.00,900000.00,9615384.62,'DaThanhToan','2026-09-22 15:10:39'),(76,'NV001','2026-09',26,3.0,25000000.00,0.00,0.00,0.00,2884615.38,'ChuaThanhToan','2026-09-25 11:11:04'),(77,'NV002','2026-09',26,1.0,18000000.00,0.00,0.00,0.00,692307.69,'ChuaThanhToan','2026-09-25 11:11:04'),(78,'NV003','2026-09',26,1.0,18000000.00,0.00,0.00,0.00,692307.69,'ChuaThanhToan','2026-09-25 11:11:04'),(79,'NV004','2026-09',26,3.0,12000000.00,0.00,0.00,0.00,1384615.38,'ChuaThanhToan','2026-09-25 11:11:04'),(80,'NV005','2026-09',26,1.0,12000000.00,0.00,0.00,0.00,461538.46,'ChuaThanhToan','2026-09-25 11:11:04'),(81,'NV006','2026-09',26,1.0,13000000.00,0.00,0.00,0.00,500000.00,'ChuaThanhToan','2026-09-25 11:11:04'),(82,'NV008','2026-09',26,1.0,14000000.00,0.00,0.00,0.00,538461.54,'ChuaThanhToan','2026-09-25 11:11:04'),(160,'NV001','2026-10',26,0.0,25000000.00,0.00,0.00,0.00,0.00,'DaThanhToan','2026-10-03 10:01:35'),(161,'NV002','2026-10',26,0.0,18000000.00,0.00,0.00,0.00,0.00,'ChuaThanhToan','2026-10-03 10:01:35'),(162,'NV003','2026-10',26,0.0,18000000.00,0.00,0.00,0.00,0.00,'ChuaThanhToan','2026-10-03 10:01:35'),(163,'NV004','2026-10',26,1.0,12000000.00,0.00,0.00,0.00,461538.46,'DaThanhToan','2026-10-03 10:01:35'),(164,'NV005','2026-10',26,0.0,12000000.00,0.00,0.00,0.00,0.00,'ChuaThanhToan','2026-10-03 10:01:35'),(165,'NV006','2026-10',26,0.0,13000000.00,0.00,0.00,0.00,0.00,'ChuaThanhToan','2026-10-03 10:01:35'),(166,'NV008','2026-10',26,0.0,14000000.00,0.00,0.00,0.00,0.00,'DaThanhToan','2026-10-03 10:01:35');
/*!40000 ALTER TABLE `bangluong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chamcong`
--

DROP TABLE IF EXISTS `chamcong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chamcong` (
  `ID_ChamCong` bigint(20) NOT NULL AUTO_INCREMENT,
  `MaNV` varchar(20) NOT NULL,
  `NgayChamCong` date NOT NULL,
  `ThoiGianVao` time DEFAULT NULL,
  `ThoiGianRa` time DEFAULT NULL,
  `TrangThaiCong` enum('DungGio','DiMuon','VeSom','NghiKhongPhep','NghiCoPhep') DEFAULT 'DungGio',
  PRIMARY KEY (`ID_ChamCong`),
  UNIQUE KEY `UQ_ChamCong_Ngay` (`MaNV`,`NgayChamCong`),
  CONSTRAINT `chamcong_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chamcong`
--

LOCK TABLES `chamcong` WRITE;
/*!40000 ALTER TABLE `chamcong` DISABLE KEYS */;
INSERT INTO `chamcong` VALUES (1,'NV001','2026-09-01','08:25:00','17:05:00','DungGio'),(2,'NV001','2026-09-02','08:20:00','17:10:00','DungGio'),(3,'NV001','2026-09-03','08:35:00','17:00:00','DiMuon'),(4,'NV002','2026-09-01','08:15:00','17:00:00','DungGio'),(5,'NV002','2026-09-02','08:20:00','16:30:00','VeSom'),(6,'NV003','2026-09-01','08:10:00','17:05:00','DungGio'),(7,'NV004','2026-09-01','08:28:00','17:00:00','DungGio'),(8,'NV004','2026-09-02','08:45:00','17:10:00','DiMuon'),(9,'NV005','2026-09-01','08:20:00','17:00:00','DungGio'),(10,'NV006','2026-09-01','08:22:00','17:05:00','DungGio'),(11,'NV007','2026-09-01','08:30:00','17:00:00','DungGio'),(12,'NV008','2026-09-01','08:25:00','17:15:00','DungGio'),(13,'NV004','2026-09-22','10:16:37','10:41:04','DiMuon'),(14,'NV009','2026-09-25','04:16:44','04:16:53','DungGio'),(16,'NV004','2026-10-03','09:32:13','09:32:19','DiMuon');
/*!40000 ALTER TABLE `chamcong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chitietdaotao`
--

DROP TABLE IF EXISTS `chitietdaotao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chitietdaotao` (
  `ID_ChiTiet` int(11) NOT NULL AUTO_INCREMENT,
  `MaKhoaHoc` varchar(20) NOT NULL,
  `MaNV` varchar(20) NOT NULL,
  `TrangThaiHoc` enum('DangHoc','HoanThanh','HuyBo') DEFAULT 'DangHoc',
  `DiemSo` decimal(4,2) DEFAULT NULL,
  PRIMARY KEY (`ID_ChiTiet`),
  KEY `MaKhoaHoc` (`MaKhoaHoc`),
  KEY `MaNV` (`MaNV`),
  CONSTRAINT `chitietdaotao_ibfk_1` FOREIGN KEY (`MaKhoaHoc`) REFERENCES `khoahoc` (`MaKhoaHoc`) ON DELETE CASCADE,
  CONSTRAINT `chitietdaotao_ibfk_2` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chitietdaotao`
--

LOCK TABLES `chitietdaotao` WRITE;
/*!40000 ALTER TABLE `chitietdaotao` DISABLE KEYS */;
INSERT INTO `chitietdaotao` VALUES (1,'KH001','NV006','DangHoc',NULL),(2,'KH001','NV007','DangHoc',NULL),(3,'KH002','NV002','HoanThanh',8.50),(4,'KH002','NV004','DangHoc',NULL),(5,'KH003','NV003','DangHoc',NULL),(6,'KH003','NV009','DangHoc',NULL),(7,'KH002','NV009','DangHoc',NULL),(8,'KH001','NV009','DangHoc',NULL);
/*!40000 ALTER TABLE `chitietdaotao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chucvu`
--

DROP TABLE IF EXISTS `chucvu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chucvu` (
  `MaChucVu` varchar(20) NOT NULL,
  `TenChucVu` varchar(100) NOT NULL,
  PRIMARY KEY (`MaChucVu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chucvu`
--

LOCK TABLES `chucvu` WRITE;
/*!40000 ALTER TABLE `chucvu` DISABLE KEYS */;
INSERT INTO `chucvu` VALUES ('CV05','Nhân viên'),('CV06','Thực tập sinh');
/*!40000 ALTER TABLE `chucvu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dieuchuyennhanvien`
--

DROP TABLE IF EXISTS `dieuchuyennhanvien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dieuchuyennhanvien` (
  `MaDieuChuyen` int(11) NOT NULL AUTO_INCREMENT,
  `MaNV` varchar(20) NOT NULL,
  `PhongBanCu` varchar(20) DEFAULT NULL,
  `PhongBanMoi` varchar(20) DEFAULT NULL,
  `ChucVuCu` varchar(20) DEFAULT NULL,
  `ChucVuMoi` varchar(20) DEFAULT NULL,
  `NgayCoHieuLuc` date NOT NULL,
  `LyDoDieuChuyen` text DEFAULT NULL,
  `MaNguoiQuyetDinh` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaDieuChuyen`),
  KEY `MaNV` (`MaNV`),
  KEY `PhongBanCu` (`PhongBanCu`),
  KEY `PhongBanMoi` (`PhongBanMoi`),
  KEY `ChucVuCu` (`ChucVuCu`),
  KEY `ChucVuMoi` (`ChucVuMoi`),
  KEY `MaNguoiQuyetDinh` (`MaNguoiQuyetDinh`),
  CONSTRAINT `dieuchuyennhanvien_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE,
  CONSTRAINT `dieuchuyennhanvien_ibfk_2` FOREIGN KEY (`PhongBanCu`) REFERENCES `phongban` (`MaPhongBan`) ON DELETE SET NULL,
  CONSTRAINT `dieuchuyennhanvien_ibfk_3` FOREIGN KEY (`PhongBanMoi`) REFERENCES `phongban` (`MaPhongBan`) ON DELETE SET NULL,
  CONSTRAINT `dieuchuyennhanvien_ibfk_4` FOREIGN KEY (`ChucVuCu`) REFERENCES `chucvu` (`MaChucVu`) ON DELETE SET NULL,
  CONSTRAINT `dieuchuyennhanvien_ibfk_5` FOREIGN KEY (`ChucVuMoi`) REFERENCES `chucvu` (`MaChucVu`) ON DELETE SET NULL,
  CONSTRAINT `dieuchuyennhanvien_ibfk_6` FOREIGN KEY (`MaNguoiQuyetDinh`) REFERENCES `nhanvien` (`MaNV`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dieuchuyennhanvien`
--

LOCK TABLES `dieuchuyennhanvien` WRITE;
/*!40000 ALTER TABLE `dieuchuyennhanvien` DISABLE KEYS */;
INSERT INTO `dieuchuyennhanvien` VALUES (1,'NV006','PB03','PB05','CV05',NULL,'2026-09-24','abc','NV002','2026-09-25 10:57:58');
/*!40000 ALTER TABLE `dieuchuyennhanvien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donxinnghi`
--

DROP TABLE IF EXISTS `donxinnghi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donxinnghi` (
  `MaDon` int(11) NOT NULL AUTO_INCREMENT,
  `MaNV` varchar(20) NOT NULL,
  `LoaiNghiPhep` enum('NghiPhepNam','NghiOm','NghiViecRieng','NghiKhongLuong') NOT NULL,
  `TuNgay` datetime NOT NULL,
  `DenNgay` datetime NOT NULL,
  `LyDo` text NOT NULL,
  `TrangThaiDuyet` enum('ChoDuyet','DaDuyet','TuChoi') DEFAULT 'ChoDuyet',
  `MaNguoiDuyet` varchar(20) DEFAULT NULL,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaDon`),
  KEY `MaNV` (`MaNV`),
  KEY `MaNguoiDuyet` (`MaNguoiDuyet`),
  CONSTRAINT `donxinnghi_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE,
  CONSTRAINT `donxinnghi_ibfk_2` FOREIGN KEY (`MaNguoiDuyet`) REFERENCES `nhanvien` (`MaNV`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donxinnghi`
--

LOCK TABLES `donxinnghi` WRITE;
/*!40000 ALTER TABLE `donxinnghi` DISABLE KEYS */;
INSERT INTO `donxinnghi` VALUES (1,'NV004','NghiPhepNam','2026-09-10 08:00:00','2026-09-11 17:00:00','Nghỉ phép năm đi du lịch','TuChoi','NV002','2026-09-22 15:10:39'),(2,'NV005','NghiOm','2026-09-05 08:00:00','2026-09-05 17:00:00','Bị cảm sốt','DaDuyet','NV002','2026-09-22 15:10:39'),(3,'NV006','NghiViecRieng','2026-09-15 08:00:00','2026-09-15 17:00:00','Giải quyết việc gia đình','ChoDuyet',NULL,'2026-09-22 15:10:39');
/*!40000 ALTER TABLE `donxinnghi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hopdonglaodong`
--

DROP TABLE IF EXISTS `hopdonglaodong`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hopdonglaodong` (
  `MaHopDong` varchar(50) NOT NULL,
  `MaNV` varchar(20) NOT NULL,
  `LoaiHopDong` enum('ThuViec','XacDinhThoiHan','KhongXacDinhThoiHan') NOT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date DEFAULT NULL,
  `LuongCoBan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `TrangThaiHopDong` enum('HieuLuc','HetHan','HuyBo') DEFAULT 'HieuLuc',
  PRIMARY KEY (`MaHopDong`),
  KEY `MaNV` (`MaNV`),
  CONSTRAINT `hopdonglaodong_ibfk_1` FOREIGN KEY (`MaNV`) REFERENCES `nhanvien` (`MaNV`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hopdonglaodong`
--

LOCK TABLES `hopdonglaodong` WRITE;
/*!40000 ALTER TABLE `hopdonglaodong` DISABLE KEYS */;
INSERT INTO `hopdonglaodong` VALUES ('HD001','NV001','KhongXacDinhThoiHan','2020-01-01',NULL,25000000.00,'HieuLuc'),('HD002','NV002','KhongXacDinhThoiHan','2021-03-15',NULL,18000000.00,'HieuLuc'),('HD003','NV003','KhongXacDinhThoiHan','2021-06-01',NULL,18000000.00,'HieuLuc'),('HD004','NV004','XacDinhThoiHan','2022-04-10','2025-04-10',12000000.00,'HieuLuc'),('HD005','NV005','XacDinhThoiHan','2022-05-20','2025-05-20',12000000.00,'HieuLuc'),('HD006','NV006','XacDinhThoiHan','2022-06-15','2025-06-15',13000000.00,'HieuLuc'),('HD007','NV007','ThuViec','2023-01-10','2023-04-10',8000000.00,'HetHan'),('HD008','NV008','XacDinhThoiHan','2023-03-01','2026-03-01',14000000.00,'HieuLuc');
/*!40000 ALTER TABLE `hopdonglaodong` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khoahoc`
--

DROP TABLE IF EXISTS `khoahoc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `khoahoc` (
  `MaKhoaHoc` varchar(20) NOT NULL,
  `TenKhoaHoc` varchar(150) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `HinhThuc` enum('Online','Offline','Hybrid') DEFAULT 'Offline',
  `GiangVien` varchar(100) DEFAULT NULL,
  `NgayBatDau` date NOT NULL,
  `NgayKetThuc` date NOT NULL,
  `NganSachDuKien` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`MaKhoaHoc`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khoahoc`
--

LOCK TABLES `khoahoc` WRITE;
/*!40000 ALTER TABLE `khoahoc` DISABLE KEYS */;
INSERT INTO `khoahoc` VALUES ('KH001','Lập trình PHP nâng cao','Khóa học về PHP 8, Laravel, MySQL','Online','TS. Nguyen Van A','2026-09-01','2026-10-30',50000000.00),('KH002','Quản trị nhân sự hiện đại','Phương pháp quản lý nhân sự 4.0','Offline','PGS. Tran Thi B','2026-09-15','2026-09-20',30000000.00),('KH003','An ninh mạng cơ bản','Nhập môn cybersecurity','Hybrid','Ths. Le Van C','2026-10-01','2026-11-30',40000000.00);
/*!40000 ALTER TABLE `khoahoc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nhanvien` (
  `MaNV` varchar(20) NOT NULL,
  `HoVaTen` varchar(100) NOT NULL,
  `NgaySinh` date NOT NULL,
  `GioiTinh` enum('Nam','Nu','Khac') NOT NULL,
  `CCCD` varchar(20) NOT NULL,
  `SoDienThoai` varchar(15) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `DiaChiThuongTru` text DEFAULT NULL,
  `MaPhongBan` varchar(20) DEFAULT NULL,
  `MaChucVu` varchar(20) DEFAULT NULL,
  `MaTaiKhoan` int(11) DEFAULT NULL,
  `TrangThaiLamViec` enum('DangLamViec','NghiPhep','DaNghiViec') DEFAULT 'DangLamViec',
  `NgayVaoLam` date NOT NULL,
  PRIMARY KEY (`MaNV`),
  UNIQUE KEY `CCCD` (`CCCD`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `MaTaiKhoan` (`MaTaiKhoan`),
  KEY `MaPhongBan` (`MaPhongBan`),
  KEY `MaChucVu` (`MaChucVu`),
  CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`MaPhongBan`) REFERENCES `phongban` (`MaPhongBan`) ON DELETE SET NULL,
  CONSTRAINT `nhanvien_ibfk_2` FOREIGN KEY (`MaChucVu`) REFERENCES `chucvu` (`MaChucVu`) ON DELETE SET NULL,
  CONSTRAINT `nhanvien_ibfk_3` FOREIGN KEY (`MaTaiKhoan`) REFERENCES `taikhoan` (`MaTaiKhoan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhanvien`
--

LOCK TABLES `nhanvien` WRITE;
/*!40000 ALTER TABLE `nhanvien` DISABLE KEYS */;
INSERT INTO `nhanvien` VALUES ('NV001','Admin','1985-03-15','Nam','001234567890','0901234567','admin@hrm.com','Da Nang','PB01',NULL,1,'DangLamViec','2020-01-01'),('NV002','Manager 1','1990-06-20','Nu','001234567891','0901234568','manager1@hrm.com','Da Nang','PB01',NULL,2,'DangLamViec','2021-03-15'),('NV003','Manager2','1988-09-10','Nam','001234567892','0901234569','manager2@hrm.com','Da Nang','PB03',NULL,3,'DangLamViec','2021-06-01'),('NV004','Employee','1995-01-25','Nam','001234567893','0901234570','emp1@hrm.com','Da Nang','PB01','CV05',4,'DangLamViec','2022-04-10'),('NV005','Emp2','1997-07-12','Nu','001234567894','0901234571','emp2@hrm.com','Da Nang','PB01','CV05',5,'DangLamViec','2022-05-20'),('NV006','Emp3','1993-11-08','Nam','001234567895','0901234572','emp3@hrm.com','Da Nang','PB05',NULL,6,'DangLamViec','2022-06-15'),('NV007','Emp4','1996-04-18','Nu','001234567896','0901234573','emp4@hrm.com','Da Nang','PB03','CV06',7,'DangLamViec','2023-01-10'),('NV008','Emp5','1998-08-30','Nam','001234567897','0901234574','emp5@hrm.com','Da Nang','PB02','CV05',8,'DangLamViec','2023-03-01'),('NV009','Emp6','1999-12-20','Nu','9876543','2345678','sdfds@gmail.com','Da Nang','PB04','CV05',9,'DangLamViec','2022-12-30');
/*!40000 ALTER TABLE `nhanvien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phongban`
--

DROP TABLE IF EXISTS `phongban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `phongban` (
  `MaPhongBan` varchar(20) NOT NULL,
  `TenPhongBan` varchar(100) NOT NULL,
  PRIMARY KEY (`MaPhongBan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phongban`
--

LOCK TABLES `phongban` WRITE;
/*!40000 ALTER TABLE `phongban` DISABLE KEYS */;
INSERT INTO `phongban` VALUES ('PB01','Phòng Nhân sự'),('PB02','Phòng Kế toán'),('PB03','Phòng Kỹ thuật'),('PB04','Phòng Marketing'),('PB05','Phòng Kinh doanh');
/*!40000 ALTER TABLE `phongban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taikhoan`
--

DROP TABLE IF EXISTS `taikhoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taikhoan` (
  `MaTaiKhoan` int(11) NOT NULL AUTO_INCREMENT,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `VaiTro` enum('Admin','Manager','Employee') NOT NULL DEFAULT 'Employee',
  `TrangThai` tinyint(4) DEFAULT 1,
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`MaTaiKhoan`),
  UNIQUE KEY `TenDangNhap` (`TenDangNhap`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taikhoan`
--

LOCK TABLES `taikhoan` WRITE;
/*!40000 ALTER TABLE `taikhoan` DISABLE KEYS */;
INSERT INTO `taikhoan` VALUES (1,'admin','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Admin',1,'2026-09-22 15:10:39'),(2,'manager1','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Manager',1,'2026-09-22 15:10:39'),(3,'manager2','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Manager',1,'2026-09-22 15:10:39'),(4,'employee1','$2y$10$zJKFvOBSak.P5LnEbhAQUucJ1Bqp9yxBbJkxsFadFKpLs/DQ6SQZG','Employee',1,'2026-09-22 15:10:39'),(5,'employee2','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Employee',1,'2026-09-22 15:10:39'),(6,'employee3','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Employee',1,'2026-09-22 15:10:39'),(7,'employee4','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Employee',1,'2026-09-22 15:10:39'),(8,'employee5','$2y$10$ukcIkoDLOne0QXyHZ3jqL.qVM04dCssBTbCbb3x9Uo3yV3qENOkUK','Employee',1,'2026-09-22 15:10:39'),(9,'employee6','$2y$10$5lv0xFEr6HbK45IKO0sGqefwWOxObyqEsIAUkm7cIYWQ6wgPG.4Ba','Employee',1,'2026-09-24 12:14:01');
/*!40000 ALTER TABLE `taikhoan` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-03 10:13:11

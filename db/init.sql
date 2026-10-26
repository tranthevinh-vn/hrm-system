-- =============================================
-- HRM System - Database Init
-- Sinh vien: Tran The Vinh - DTC245240009
-- =============================================

CREATE DATABASE IF NOT EXISTS hrm_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hrm_db;

-- Bang phong ban
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bang nhan vien
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    employee_code VARCHAR(20) UNIQUE NOT NULL,
    department_id INT,
    position VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(20),
    hire_date DATE,
    salary DECIMAL(12,2),
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Du lieu mau phong ban
INSERT INTO departments (name, description) VALUES
('IT', 'Phong Cong nghe Thong tin'),
('HR', 'Phong Nhan su'),
('Ke toan', 'Phong Ke toan - Tai chinh'),
('Kinh doanh', 'Phong Kinh doanh');

-- Du lieu mau nhan vien
INSERT INTO employees (full_name, employee_code, department_id, position, email, phone, hire_date, salary) VALUES
('Nguyen Van An', 'NV001', 1, 'Lap trinh vien', 'an.nv@hrm.local', '0912345678', '2024-01-15', 12000000),
('Tran Thi Binh', 'NV002', 2, 'Chuyen vien nhan su', 'binh.tt@hrm.local', '0923456789', '2024-03-01', 10000000),
('Le Van Cuong', 'NV003', 3, 'Ke toan vien', 'cuong.lv@hrm.local', '0934567890', '2023-11-20', 11000000),
('Pham Thi Dung', 'NV004', 4, 'Nhan vien kinh doanh', 'dung.pt@hrm.local', '0945678901', '2024-06-01', 9000000),
('Hoang Van Em', 'NV005', 1, 'Ky su he thong', 'em.hv@hrm.local', '0956789012', '2023-08-15', 13000000);

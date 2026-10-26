<?php
// =============================================
// HRM System - Quan ly Nhan su
// Sinh vien: Tran The Vinh - DTC245240009
// Lop: K23.K1.MMT K23A
// =============================================

$host = getenv('DB_HOST') ?: 'hrm_mysql';
$db   = getenv('MYSQL_DATABASE') ?: 'hrm_db';
$user = getenv('MYSQL_USER') ?: 'hrm_user';
$pass = getenv('MYSQL_PASSWORD') ?: '';

$error_msg = '';
$employees = [];
$departments = [];
$stats = [];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Lay thong ke
    $stats['total']    = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    $stats['active']   = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='active'")->fetchColumn();
    $stats['depts']    = $pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();

    // Lay danh sach nhan vien
    $employees = $pdo->query("
        SELECT e.*, d.name AS dept_name
        FROM employees e
        LEFT JOIN departments d ON e.department_id = d.id
        ORDER BY e.id
    ")->fetchAll(PDO::FETCH_ASSOC);

    $departments = $pdo->query("SELECT * FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_msg = "Loi ket noi CSDL: " . $e->getMessage();
}

// Xu ly them nhan vien (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        try {
            $stmt = $pdo->prepare("INSERT INTO employees
                (full_name, employee_code, department_id, position, email, phone, hire_date, salary, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([
                htmlspecialchars($_POST['full_name']),
                htmlspecialchars($_POST['employee_code']),
                intval($_POST['department_id']),
                htmlspecialchars($_POST['position']),
                htmlspecialchars($_POST['email']),
                htmlspecialchars($_POST['phone']),
                $_POST['hire_date'],
                floatval($_POST['salary'])
            ]);
            header("Location: /");
            exit;
        } catch (PDOException $e) {
            $error_msg = "Loi them nhan vien: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRM - He thong Quan ly Nhan su</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; color: #333; }

        .header {
            background: linear-gradient(135deg, #1a237e, #283593);
            color: white; padding: 18px 30px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .header h1 { font-size: 1.5rem; }
        .header .sub { font-size: 0.85rem; opacity: 0.8; margin-top: 4px; }
        .badge { background: #ff6f00; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }

        .container { max-width: 1300px; margin: 0 auto; padding: 24px 16px; }

        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card {
            background: white; border-radius: 10px; padding: 20px;
            text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            border-top: 4px solid;
        }
        .stat-card:nth-child(1) { border-color: #1565c0; }
        .stat-card:nth-child(2) { border-color: #2e7d32; }
        .stat-card:nth-child(3) { border-color: #e65100; }
        .stat-card .num { font-size: 2.5rem; font-weight: bold; color: #1a237e; }
        .stat-card .label { color: #666; font-size: 0.9rem; margin-top: 4px; }

        .card { background: white; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.08); margin-bottom: 24px; overflow: hidden; }
        .card-header { background: #1a237e; color: white; padding: 14px 20px; font-weight: bold; font-size: 1rem; display: flex; align-items: center; gap: 8px; }

        .add-form { padding: 20px; display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px; }
        .add-form input, .add-form select {
            border: 1px solid #ddd; border-radius: 6px; padding: 8px 12px;
            font-size: 0.9rem; width: 100%;
        }
        .add-form input:focus, .add-form select:focus { outline: none; border-color: #1a237e; }
        .btn {
            background: #1a237e; color: white; border: none;
            padding: 9px 20px; border-radius: 6px; cursor: pointer; font-size: 0.9rem;
        }
        .btn:hover { background: #283593; }
        .btn-add { grid-column: 1 / -1; justify-self: start; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #e8eaf6; color: #1a237e; padding: 12px 14px; text-align: left; font-size: 0.88rem; }
        td { padding: 11px 14px; border-bottom: 1px solid #f0f0f0; font-size: 0.88rem; }
        tr:hover td { background: #f5f5f5; }

        .status-active { background: #e8f5e9; color: #2e7d32; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
        .status-inactive { background: #fce4ec; color: #c62828; padding: 3px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }

        .error { background: #ffebee; border: 1px solid #ef9a9a; color: #c62828; padding: 12px 20px; border-radius: 8px; margin-bottom: 16px; }

        .footer { text-align: center; color: #999; font-size: 0.8rem; padding: 20px; }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>🏢 He thong Quan ly Nhan su (HRM)</h1>
        <div class="sub">Moi truong: Docker Container | MySQL | Nginx Reverse Proxy</div>
    </div>
    <div>
        <span class="badge">DTC245240009</span>
    </div>
</div>

<div class="container">

    <?php if ($error_msg): ?>
    <div class="error">⚠️ <?= htmlspecialchars($error_msg) ?></div>
    <?php endif; ?>

    <!-- Thong ke -->
    <div class="stats">
        <div class="stat-card">
            <div class="num"><?= $stats['total'] ?? 0 ?></div>
            <div class="label">👥 Tong nhan vien</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $stats['active'] ?? 0 ?></div>
            <div class="label">✅ Dang lam viec</div>
        </div>
        <div class="stat-card">
            <div class="num"><?= $stats['depts'] ?? 0 ?></div>
            <div class="label">🏗️ Phong ban</div>
        </div>
    </div>

    <!-- Form them nhan vien -->
    <div class="card">
        <div class="card-header">➕ Them nhan vien moi</div>
        <form method="POST" class="add-form">
            <input type="hidden" name="action" value="add">
            <input type="text" name="full_name" placeholder="Ho va ten *" required>
            <input type="text" name="employee_code" placeholder="Ma NV (VD: NV006) *" required>
            <select name="department_id" required>
                <option value="">-- Chon phong ban --</option>
                <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="position" placeholder="Chuc vu" >
            <input type="email" name="email" placeholder="Email">
            <input type="text" name="phone" placeholder="So dien thoai">
            <input type="date" name="hire_date" title="Ngay vao lam">
            <input type="number" name="salary" placeholder="Luong (VND)" step="500000" min="0">
            <button type="submit" class="btn btn-add">💾 Luu nhan vien</button>
        </form>
    </div>

    <!-- Bang danh sach -->
    <div class="card">
        <div class="card-header">📋 Danh sach nhan vien (<?= count($employees) ?> nguoi)</div>
        <table>
            <tr>
                <th>#</th><th>Ma NV</th><th>Ho va ten</th><th>Phong ban</th>
                <th>Chuc vu</th><th>Email</th><th>Dien thoai</th>
                <th>Ngay vao</th><th>Luong</th><th>Trang thai</th>
            </tr>
            <?php if (empty($employees)): ?>
            <tr><td colspan="10" style="text-align:center;color:#999;padding:30px">Chua co nhan vien nao</td></tr>
            <?php else: ?>
            <?php foreach ($employees as $i => $e): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong><?= htmlspecialchars($e['employee_code']) ?></strong></td>
                <td><?= htmlspecialchars($e['full_name']) ?></td>
                <td><?= htmlspecialchars($e['dept_name'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['position']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['phone']) ?></td>
                <td><?= htmlspecialchars($e['hire_date']) ?></td>
                <td><?= number_format($e['salary'], 0, ',', '.') ?> d</td>
                <td>
                    <?php if ($e['status'] === 'active'): ?>
                        <span class="status-active">Dang lam</span>
                    <?php else: ?>
                        <span class="status-inactive">Da nghi</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>

</div>

<div class="footer">
    HRM System | Sinh vien: Tran The Vinh | MSSV: DTC245240009 | Lop: K23.K1.MMT K23A<br>
    Mon: Trien khai va Quan tri He thong Phan mem | <?= date('Y') ?>
</div>

</body>
</html>

<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include '../config.php';

// Ambil statistik
try {
    // Total villas
    $stmt = $pdo->query("SELECT COUNT(*) as total_villas FROM villas");
    $total_villas = $stmt->fetch()['total_villas'];
    
    // Total bookings
    $stmt = $pdo->query("SELECT COUNT(*) as total_bookings FROM bookings");
    $total_bookings = $stmt->fetch()['total_bookings'];
    
    // Total pendapatan
    $stmt = $pdo->query("
        SELECT SUM(DATEDIFF(b.check_out, b.check_in) * v.price_per_night) as total_income 
        FROM bookings b
        JOIN villas v ON b.villa_id = v.id
        WHERE b.status = 'confirmed'
    ");
    $total_income = $stmt->fetch()['total_income'] ?? 0;
    
    // Recent bookings
    $stmt = $pdo->query("
        SELECT b.*, v.name as villa_name 
        FROM bookings b
        JOIN villas v ON b.villa_id = v.id
        ORDER BY b.id DESC
        LIMIT 5
    ");
    $recent_bookings = $stmt->fetchAll();
    
    // Recent villas
    $stmt = $pdo->query("SELECT * FROM villas ORDER BY id DESC LIMIT 3");
    $recent_villas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Luxury Villas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #2a9d8f;
            --primary-dark: #21867a;
            --secondary: #264653;
            --accent: #e9c46a;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fa;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: var(--secondary);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background-color: rgba(0,0,0,0.1);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-brand {
            font-weight: 700;
            font-size: 1.3rem;
            color: white;
            text-decoration: none;
        }
        
        .sidebar-brand i {
            color: var(--primary);
            margin-right: 10px;
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .sidebar-menu li a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            color: white;
            background-color: rgba(0,0,0,0.2);
            padding-left: 25px;
        }
        
        .sidebar-menu li a i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
            color: var(--primary);
        }
        
        .sidebar-menu li.active {
            border-left: 4px solid var(--primary);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: all 0.3s;
        }
        
        .welcome-section {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .welcome-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        
        .welcome-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }
        
        /* Stats Cards */
        .stats-card {
            border: none;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }
        
        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }
        
        .stats-title {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .stats-value {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .stats-link {
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.8;
            display: inline-block;
            margin-top: 10px;
        }
        
        .stats-link:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        .card-villas {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }
        
        .card-bookings {
            background: linear-gradient(135deg, #e9c46a, #f4a261);
        }
        
        .card-income {
            background: linear-gradient(135deg, #264653, #1d3557);
        }
        
        /* Tables */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            font-weight: 600;
            color: var(--secondary);
            border-top: none;
            border-bottom: 1px solid #dee2e6;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-confirmed {
            background-color: #198754;
        }
        
        .badge-cancelled {
            background-color: #dc3545;
        }
        
        /* Villa List */
        .villa-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .villa-item:last-child {
            border-bottom: none;
        }
        
        .villa-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        
        .villa-name {
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 5px;
        }
        
        .villa-location {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="fas fa-umbrella-beach"></i>LuxuryVillas
            </a>
            <small class="d-block mt-1 text-muted">Admin Panel</small>
        </div>
        <ul class="sidebar-menu">
            <li class="active">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li>
                <a href="villas.php"><i class="fas fa-home"></i> Kelola Villa</a>
            </li>
            <li>
                <a href="bookings.php"><i class="fas fa-calendar-check"></i> Kelola Booking</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-users"></i> Kelola Admin</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-cog"></i> Pengaturan</a>
            </li>
            <li>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="welcome-section">
            <h3 class="welcome-title">Selamat datang, <?= $_SESSION['admin_user'] ?>!</h3>
            <p class="welcome-subtitle">Berikut adalah ringkasan aktivitas sistem Anda hari ini.</p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="stats-card card-villas">
                    <i class="fas fa-home stats-icon"></i>
                    <h5 class="stats-title">Total Villa</h5>
                    <h3 class="stats-value"><?= $total_villas ?></h3>
                    <a href="villas.php" class="stats-link">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card card-bookings">
                    <i class="fas fa-calendar-check stats-icon"></i>
                    <h5 class="stats-title">Total Booking</h5>
                    <h3 class="stats-value"><?= $total_bookings ?></h3>
                    <a href="bookings.php" class="stats-link">Lihat Semua <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card card-income">
                    <i class="fas fa-money-bill-wave stats-icon"></i>
                    <h5 class="stats-title">Total Pendapatan</h5>
                    <h3 class="stats-value">Rp<?= number_format($total_income, 0, ',', '.') ?></h3>
                    <a href="bookings.php" class="stats-link">Lihat Detail <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Booking Terbaru</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Villa</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_bookings as $booking): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($booking['full_name']) ?></td>
                                            <td><?= htmlspecialchars($booking['villa_name']) ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?= $booking['status'] == 'pending' ? 'badge-pending' : 
                                                       ($booking['status'] == 'confirmed' ? 'badge-confirmed' : 'badge-cancelled') ?>">
                                                    <?= $booking['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Villa Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($recent_villas as $villa): ?>
                            <div class="villa-item">
                                <img src="../uploads/<?= $villa['photo'] ?>" class="villa-img" alt="<?= htmlspecialchars($villa['name']) ?>">
                                <div>
                                    <h6 class="villa-name"><?= htmlspecialchars($villa['name']) ?></h6>
                                    <p class="villa-location">
                                        <i class="fas fa-map-marker-alt text-muted"></i> <?= htmlspecialchars($villa['location']) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar di mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.createElement('button');
            sidebarToggle.className = 'btn btn-primary d-lg-none';
            sidebarToggle.style.position = 'fixed';
            sidebarToggle.style.bottom = '20px';
            sidebarToggle.style.right = '20px';
            sidebarToggle.style.zIndex = '1000';
            sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
            
            document.body.appendChild(sidebarToggle);
            
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        });
    </script>
</body>
</html>
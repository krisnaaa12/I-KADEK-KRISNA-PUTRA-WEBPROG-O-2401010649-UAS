<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config.php';

// Proses update status booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$status, $booking_id]);

    header('Location: bookings.php');
    exit;
}

// Ambil semua booking dengan JOIN villa
$stmt = $pdo->query("
    SELECT bookings.*, villas.name AS villa_name 
    FROM bookings 
    JOIN villas ON bookings.villa_id = villas.id 
    ORDER BY bookings.id DESC
");
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Booking - Luxury Villas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 280px;
            --primary: #2a9d8f;
            --primary-dark: #21867a;
            --secondary: #264653;
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
        
        .sidebar-menu li a:hover {
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
            background-color: rgba(0,0,0,0.2);
            border-left: 4px solid var(--primary);
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
            transition: all 0.3s;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .page-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 0;
        }
        
        /* Table */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 20px;
            border-radius: 10px 10px 0 0 !important;
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
        
        .table td {
            vertical-align: middle;
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
        
        .btn-action {
            padding: 5px 12px;
            font-size: 0.85rem;
            border-radius: 6px;
            margin-right: 5px;
        }
        
        .btn-confirm {
            background-color: #198754;
            border: none;
            color: white;
        }
        
        .btn-confirm:hover {
            background-color: #157347;
            color: white;
        }
        
        .btn-cancel {
            background-color: #dc3545;
            border: none;
            color: white;
        }
        
        .btn-cancel:hover {
            background-color: #bb2d3b;
            color: white;
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
            <li>
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li>
                <a href="villas.php"><i class="fas fa-home"></i> Kelola Villa</a>
            </li>
            <li class="active">
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
        <div class="page-header">
            <h1 class="page-title">Daftar Booking</h1>
        </div>
        
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Villa</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?= htmlspecialchars($booking['full_name']) ?></td>
                                    <td><?= htmlspecialchars($booking['villa_name']) ?></td>
                                    <td><?= htmlspecialchars($booking['email']) ?></td>
                                    <td><?= htmlspecialchars($booking['phone']) ?></td>
                                    <td><?= date('d M Y', strtotime($booking['check_in'])) ?></td>
                                    <td><?= date('d M Y', strtotime($booking['check_out'])) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?= $booking['status'] == 'pending' ? 'badge-pending' : 
                                               ($booking['status'] == 'confirmed' ? 'badge-confirmed' : 'badge-cancelled') ?>">
                                            <?= $booking['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-action btn-confirm" 
                                                <?= $booking['status'] == 'confirmed' ? 'disabled' : '' ?>>
                                                <i class="fas fa-check"></i> Konfirmasi
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-action btn-cancel"
                                                <?= $booking['status'] == 'cancelled' ? 'disabled' : '' ?>>
                                                <i class="fas fa-times"></i> Batalkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
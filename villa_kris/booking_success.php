<?php
include 'config.php';

if (!isset($_GET['booking_id'])) {
    header('Location: index.php');
    exit;
}

$booking_id = $_GET['booking_id'];

try {
    $stmt = $pdo->prepare("
        SELECT bookings.*, villas.name AS villa_name, villas.price_per_night 
        FROM bookings 
        JOIN villas ON bookings.villa_id = villas.id 
        WHERE bookings.id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        header('Location: index.php');
        exit;
    }
    
    // Hitung total harga
    $check_in = new DateTime($booking['check_in']);
    $check_out = new DateTime($booking['check_out']);
    $nights = $check_out->diff($check_in)->days;
    $total_price = $nights * $booking['price_per_night'];
} catch (PDOException $e) {
    die("Gagal mengambil data booking: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil - Luxury Villas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2a9d8f;
            --primary-dark: #21867a;
            --secondary: #264653;
            --light: #f8f9fa;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .success-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin: 50px auto;
            max-width: 700px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .success-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        
        .success-message {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #555;
        }
        
        .booking-details {
            background-color: var(--light);
            border-radius: 10px;
            padding: 25px;
            margin: 30px 0;
            text-align: left;
        }
        
        .detail-item {
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 10px;
        }
        
        .detail-label {
            font-weight: 500;
            color: var(--secondary);
        }
        
        .detail-value {
            font-weight: 600;
        }
        
        .total-price {
            font-size: 1.2rem;
            color: var(--primary);
            font-weight: 700;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid var(--primary);
        }
        
        .btn-home {
            background-color: var(--primary);
            border: none;
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s;
            color: white;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        
        .btn-home:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="success-title">Booking Berhasil!</h2>
            <p class="success-message">
                Terima kasih telah memesan villa kami. Detail booking Anda telah dikirim ke email <?= htmlspecialchars($booking['email']) ?>.
                Silahkan cek email Anda untuk instruksi lebih lanjut.
            </p>
            
            <div class="booking-details">
                <h5 class="text-center mb-4" style="color: var(--secondary);">Detail Booking</h5>
                
                <div class="detail-item">
                    <span class="detail-label">Kode Booking:</span>
                    <span class="detail-value">#<?= str_pad($booking['id'], 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Nama Villa:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['villa_name']) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Nama Pemesan:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['full_name']) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['email']) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">No. Telepon:</span>
                    <span class="detail-value"><?= htmlspecialchars($booking['phone']) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Tanggal Check-in:</span>
                    <span class="detail-value"><?= date('d F Y', strtotime($booking['check_in'])) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Tanggal Check-out:</span>
                    <span class="detail-value"><?= date('d F Y', strtotime($booking['check_out'])) ?></span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Durasi Menginap:</span>
                    <span class="detail-value"><?= $nights ?> Malam</span>
                </div>
                
                <div class="detail-item">
                    <span class="detail-label">Harga per Malam:</span>
                    <span class="detail-value">Rp<?= number_format($booking['price_per_night'], 0, ',', '.') ?></span>
                </div>
                
                <div class="text-end total-price">
                    Total Harga: Rp<?= number_format($total_price, 0, ',', '.') ?>
                </div>
            </div>
            
            <p>Status booking Anda saat ini: 
                <span class="badge 
                    <?= $booking['status'] == 'pending' ? 'bg-warning' : 
                       ($booking['status'] == 'confirmed' ? 'bg-success' : 'bg-danger') ?>">
                    <?= $booking['status'] ?>
                </span>
            </p>
            
            <a href="index.php" class="btn btn-home">Kembali ke Beranda</a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
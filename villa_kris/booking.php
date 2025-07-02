<?php
include 'config.php';

// Pastikan villa_id ada di URL
if (!isset($_GET['villa_id'])) {
    header('Location: index.php');
    exit;
}

$villa_id = $_GET['villa_id'];

try {
    $stmt = $pdo->prepare("SELECT * FROM villas WHERE id = ?");
    $stmt->execute([$villa_id]);
    $villa = $stmt->fetch();
    
    if (!$villa) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die("Gagal mengambil data villa: " . $e->getMessage());
}

// Proses form booking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $villa_id = $_POST['villa_id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings (villa_id, full_name, email, phone, check_in, check_out, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$villa_id, $full_name, $email, $phone, $check_in, $check_out]);
        
        header('Location: booking_success.php?booking_id=' . $pdo->lastInsertId());
        exit;
    } catch (PDOException $e) {
        $error = "Gagal melakukan booking: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Villa - <?= htmlspecialchars($villa['name']) ?></title>
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
        
        .booking-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
            margin: 50px auto;
            max-width: 900px;
        }
        
        .booking-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            padding-bottom: 15px;
        }
        
        .booking-title:after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary);
            margin: 15px auto 0;
            border-radius: 2px;
        }
        
        .villa-summary {
            background-color: var(--light);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .villa-summary img {
            border-radius: 10px;
            width: 100%;
            height: 200px;
            object-fit: cover;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .villa-name {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 10px;
        }
        
        .villa-location {
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .villa-location i {
            color: var(--primary);
        }
        
        .villa-price {
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--primary);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(42, 157, 143, 0.25);
        }
        
        .section-title {
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light);
        }
        
        .btn-booking {
            background-color: var(--primary);
            border: none;
            padding: 12px 30px;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .btn-booking:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-cancel {
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: 500;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="booking-container">
            <h2 class="booking-title">Booking Villa: <?= htmlspecialchars($villa['name']) ?></h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <div class="villa-summary">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <img src="uploads/<?= $villa['photo'] ?>" alt="<?= htmlspecialchars($villa['name']) ?>">
                    </div>
                    <div class="col-md-8">
                        <h4 class="villa-name"><?= htmlspecialchars($villa['name']) ?></h4>
                        <p class="villa-location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($villa['location']) ?></p>
                        <p class="villa-price">Rp<?= number_format($villa['price_per_night'], 0, ',', '.') ?> / malam</p>
                    </div>
                </div>
            </div>
            
            <form method="POST" id="bookingForm">
                <input type="hidden" name="villa_id" value="<?= $villa['id'] ?>">
                
                <h5 class="section-title">Informasi Pribadi</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="full_name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" required>
                        <div id="fullNameError" class="error-message"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <div id="emailError" class="error-message"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Nomor Telepon</label>
                        <input type="tel" name="phone" id="phone" class="form-control" required>
                        <div id="phoneError" class="error-message"></div>
                    </div>
                </div>
                
                <h5 class="section-title">Tanggal Booking</h5>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <label for="check_in" class="form-label">Tanggal Check-in</label>
                        <input type="date" name="check_in" id="check_in" class="form-control" required>
                        <div id="checkInError" class="error-message"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="check_out" class="form-label">Tanggal Check-out</label>
                        <input type="date" name="check_out" id="check_out" class="form-control" required>
                        <div id="checkOutError" class="error-message"></div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="index.php" class="btn btn-outline-secondary btn-cancel">Batal</a>
                    <button type="submit" class="btn btn-primary btn-booking">Selesaikan Booking</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validasi form client-side
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validasi nama lengkap
            const fullName = document.getElementById('full_name').value.trim();
            if (fullName === '') {
                document.getElementById('fullNameError').textContent = 'Nama lengkap harus diisi';
                isValid = false;
            } else {
                document.getElementById('fullNameError').textContent = '';
            }
            
            // Validasi email
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                document.getElementById('emailError').textContent = 'Email harus diisi';
                isValid = false;
            } else if (!emailRegex.test(email)) {
                document.getElementById('emailError').textContent = 'Format email tidak valid';
                isValid = false;
            } else {
                document.getElementById('emailError').textContent = '';
            }
            
            // Validasi nomor telepon
            const phone = document.getElementById('phone').value.trim();
            const phoneRegex = /^[0-9]{10,15}$/;
            if (phone === '') {
                document.getElementById('phoneError').textContent = 'Nomor telepon harus diisi';
                isValid = false;
            } else if (!phoneRegex.test(phone)) {
                document.getElementById('phoneError').textContent = 'Format nomor telepon tidak valid';
                isValid = false;
            } else {
                document.getElementById('phoneError').textContent = '';
            }
            
            // Validasi tanggal check-in
            const checkIn = document.getElementById('check_in').value;
            if (checkIn === '') {
                document.getElementById('checkInError').textContent = 'Tanggal check-in harus diisi';
                isValid = false;
            } else {
                document.getElementById('checkInError').textContent = '';
            }
            
            // Validasi tanggal check-out
            const checkOut = document.getElementById('check_out').value;
            if (checkOut === '') {
                document.getElementById('checkOutError').textContent = 'Tanggal check-out harus diisi';
                isValid = false;
            } else if (checkIn && checkOut && new Date(checkOut) <= new Date(checkIn)) {
                document.getElementById('checkOutError').textContent = 'Tanggal check-out harus setelah check-in';
                isValid = false;
            } else {
                document.getElementById('checkOutError').textContent = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Set minimum date untuk input tanggal (hari ini)
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('check_in').min = today;
        document.getElementById('check_out').min = today;
        
        // Update minimum check-out saat check-in diubah
        document.getElementById('check_in').addEventListener('change', function() {
            document.getElementById('check_out').min = this.value;
        });
    </script>
</body>
</html>
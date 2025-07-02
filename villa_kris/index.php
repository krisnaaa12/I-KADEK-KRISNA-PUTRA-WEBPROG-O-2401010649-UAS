<?php
include 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM villas");
    $villas = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data villa: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Villas - Tempat Liburan Eksklusif</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2a9d8f;
            --primary-dark: #21867a;
            --secondary: #264653;
            --accent: #e9c46a;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }
        
        /* Navbar */
        .navbar {
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: white !important;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            margin: 0 10px;
            color: var(--dark) !important;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary) !important;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
            padding: 150px 0;
            text-align: center;
            margin-bottom: 50px;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-hero {
            background-color: var(--primary);
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 50px;
            transition: all 0.3s;
        }
        
        .btn-hero:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        /* Villa Cards */
        .section-title {
            position: relative;
            margin-bottom: 50px;
            text-align: center;
            font-weight: 600;
            color: var(--secondary);
        }
        
        .section-title:after {
            content: "";
            display: block;
            width: 80px;
            height: 4px;
            background: var(--primary);
            margin: 15px auto;
            border-radius: 2px;
        }
        
        .villa-card {
            transition: all 0.3s ease;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            background-color: white;
        }
        
        .villa-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .villa-img-container {
            position: relative;
            overflow: hidden;
            height: 250px;
        }
        
        .villa-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .villa-card:hover .villa-img {
            transform: scale(1.05);
        }
        
        .price-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--primary);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .villa-title {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--secondary);
        }
        
        .villa-location {
            color: #6c757d;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        
        .villa-location i {
            color: var(--primary);
            margin-right: 5px;
        }
        
        .btn-book {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-book:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }
        
        .rating {
            color: var(--accent);
            font-size: 0.9rem;
        }
        
        /* Features Section */
        .features-section {
            background-color: white;
            padding: 80px 0;
        }
        
        .feature-box {
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
            transition: all 0.3s;
            margin-bottom: 30px;
            background-color: var(--light);
        }
        
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }
        
        .feature-title {
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--secondary);
        }
        
        /* Testimonial Section */
        .testimonial-section {
            background-color: var(--light);
            padding: 80px 0;
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .testimonial-text {
            font-style: italic;
            margin-bottom: 20px;
            color: #555;
        }
        
        .testimonial-author {
            font-weight: 600;
            color: var(--secondary);
        }
        
        .testimonial-rating {
            color: var(--accent);
        }
        
        /* Footer */
        .footer {
            background-color: var(--secondary);
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-title {
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 10px;
        }
        
        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .footer-links a:hover {
            color: white;
            padding-left: 5px;
        }
        
        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background-color: rgba(255,255,255,0.1);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }
        
        .social-icons a:hover {
            background-color: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-umbrella-beach me-2"></i>LuxuryVillas
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#villas">Villa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang Kami</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Temukan Villa Impian Anda</h1>
            <p class="hero-subtitle">Nikmati liburan eksklusif di villa mewah kami dengan pemandangan menakjubkan dan fasilitas premium</p>
            <a href="#villas" class="btn btn-hero">Jelajahi Villa</a>
        </div>
    </section>

    <!-- Villas Section -->
    <section id="villas" class="py-5">
        <div class="container">
            <h2 class="section-title">Villa Mewah Kami</h2>
            <div class="row">
                <?php foreach ($villas as $villa): ?>
                    <div class="col-md-4">
                        <div class="villa-card">
                            <div class="villa-img-container">
                                <img src="uploads/<?= htmlspecialchars($villa['photo']) ?>" class="villa-img" alt="<?= htmlspecialchars($villa['name']) ?>">
                                <div class="price-tag">Rp<?= number_format($villa['price_per_night'], 0, ',', '.') ?>/malam</div>
                            </div>
                            <div class="card-body">
                                <h5 class="villa-title"><?= htmlspecialchars($villa['name']) ?></h5>
                                <p class="villa-location">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($villa['location']) ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="booking.php?villa_id=<?= $villa['id'] ?>" class="btn btn-book">Pesan Sekarang</a>
                                    <div class="rating">
                                        <i class="fas fa-star"></i> 4.8
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="about" class="features-section">
        <div class="container">
            <h2 class="section-title">Mengapa Memilih Kami</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <h4 class="feature-title">Villa Mewah</h4>
                        <p>Villa dengan kolam renang pribadi dan pemandangan menakjubkan yang akan membuat liburan Anda tak terlupakan.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-concierge-bell"></i>
                        </div>
                        <h4 class="feature-title">Pelayanan 24/7</h4>
                        <p>Staf profesional siap melayani kebutuhan Anda selama 24 jam untuk kenyamanan maksimal.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h4 class="feature-title">Lokasi Strategis</h4>
                        <p>Villa kami terletak di lokasi terbaik dengan akses mudah ke berbagai tempat wisata terkenal.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section id="testimonials" class="testimonial-section">
        <div class="container">
            <h2 class="section-title">Apa Kata Mereka</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"Villa yang sangat nyaman dengan pemandangan pantai yang menakjubkan. Pelayanan staff juga sangat baik."</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="testimonial-author">Budi Santoso</h6>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i> 4.9
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"Liburan keluarga kami menjadi sangat berkesan berkat villa yang luas dan fasilitas lengkap ini."</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="testimonial-author">Anita Wijaya</h6>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i> 5.0
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <p class="testimonial-text">"Sangat cocok untuk bulan madu, privasi terjaga dan suasana romantis. Pasti akan kembali lagi!"</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="testimonial-author">Rudi & Sinta</h6>
                            <div class="testimonial-rating">
                                <i class="fas fa-star"></i> 4.8
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="footer-title">
                        <i class="fas fa-umbrella-beach me-2"></i>LuxuryVillas
                    </h5>
                    <p>Menyediakan pengalaman menginap di villa mewah sejak 2010. Kami berkomitmen untuk memberikan pelayanan terbaik.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h5 class="footer-title">Tautan Cepat</h5>
                    <ul class="footer-links">
                        <li><a href="index.php">Beranda</a></li>
                        <li><a href="#villas">Villa</a></li>
                        <li><a href="#about">Tentang Kami</a></li>
                        <li><a href="#testimonials">Testimoni</a></li>
                        <li><a href="#contact">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">Kontak Kami</h5>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt me-2"></i> Jl. Villa Indah No. 123, Bali</li>
                        <li><i class="fas fa-phone me-2"></i> +62 812 3456 7890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@luxuryvillas.com</li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4">
                    <h5 class="footer-title">Ikuti Kami</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?= date('Y') ?> LuxuryVillas. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
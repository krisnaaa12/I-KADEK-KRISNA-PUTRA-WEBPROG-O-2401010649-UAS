<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../admin/login.php');
    exit;
}
include '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $price = $_POST['price_per_night'];
    $description = $_POST['description'];

    // Validasi input
    $errors = [];
    
    if (empty($name)) {
        $errors['name'] = 'Nama villa harus diisi';
    }
    
    if (empty($location)) {
        $errors['location'] = 'Lokasi villa harus diisi';
    }
    
    if (empty($price) || !is_numeric($price) || $price <= 0) {
        $errors['price_per_night'] = 'Harga per malam harus berupa angka dan lebih dari 0';
    }
    
    if (empty($description)) {
        $errors['description'] = 'Deskripsi villa harus diisi';
    }
    
    // Upload foto
    $photo_name = '';
    if ($_FILES['photo']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['photo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['photo'] = 'Format file tidak didukung. Hanya JPG, PNG, dan GIF yang diperbolehkan.';
        } else {
            $photo_name = time() . '_' . basename($_FILES['photo']['name']);
            $target_path = '../uploads/' . $photo_name;
            
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
                $errors['photo'] = 'Gagal mengupload foto. Silahkan coba lagi.';
            }
        }
    } else {
        $errors['photo'] = 'Foto villa harus diupload';
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO villas (name, location, price_per_night, description, photo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $location, $price, $description, $photo_name]);
            
            header('Location: ../admin/villas.php');
            exit;
        } catch (PDOException $e) {
            $error = "Gagal menambahkan villa: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Villa Baru - Luxury Villas</title>
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
        }
        
        .form-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 800px;
        }
        
        .form-title {
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light);
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(42, 157, 143, 0.25);
        }
        
        textarea.form-control {
            min-height: 120px;
        }
        
        .btn-submit {
            background-color: var(--primary);
            border: none;
            padding: 12px 25px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-cancel {
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 500;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 150px;
            margin-top: 10px;
            border-radius: 8px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Tambah Villa Baru</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="villaForm">
                <div class="mb-4">
                    <label for="name" class="form-label">Nama Villa</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" required>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-message"><?= $errors['name'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="location" class="form-label">Lokasi</label>
                    <input type="text" name="location" id="location" class="form-control" value="<?= isset($_POST['location']) ? htmlspecialchars($_POST['location']) : '' ?>" required>
                    <?php if (isset($errors['location'])): ?>
                        <div class="error-message"><?= $errors['location'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="price_per_night" class="form-label">Harga per Malam (Rp)</label>
                    <input type="number" name="price_per_night" id="price_per_night" class="form-control" value="<?= isset($_POST['price_per_night']) ? htmlspecialchars($_POST['price_per_night']) : '' ?>" required>
                    <?php if (isset($errors['price_per_night'])): ?>
                        <div class="error-message"><?= $errors['price_per_night'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" class="form-control" required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="error-message"><?= $errors['description'] ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="photo" class="form-label">Foto Villa</label>
                    <input type="file" name="photo" id="photo" class="form-control" accept="image/*" required>
                    <?php if (isset($errors['photo'])): ?>
                        <div class="error-message"><?= $errors['photo'] ?></div>
                    <?php endif; ?>
                    <img id="imagePreview" class="preview-image" src="#" alt="Preview Gambar">
                </div>
                
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="../admin/villas.php" class="btn btn-outline-secondary btn-cancel">Batal</a>
                    <button type="submit" class="btn btn-primary btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Preview image sebelum upload
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Validasi form client-side
        document.getElementById('villaForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validasi nama
            const name = document.getElementById('name').value.trim();
            if (name === '') {
                document.getElementById('name').nextElementSibling.textContent = 'Nama villa harus diisi';
                isValid = false;
            } else {
                document.getElementById('name').nextElementSibling.textContent = '';
            }
            
            // Validasi lokasi
            const location = document.getElementById('location').value.trim();
            if (location === '') {
                document.getElementById('location').nextElementSibling.textContent = 'Lokasi villa harus diisi';
                isValid = false;
            } else {
                document.getElementById('location').nextElementSibling.textContent = '';
            }
            
            // Validasi harga
            const price = document.getElementById('price_per_night').value.trim();
            if (price === '' || isNaN(price) || parseFloat(price) <= 0) {
                document.getElementById('price_per_night').nextElementSibling.textContent = 'Harga per malam harus berupa angka dan lebih dari 0';
                isValid = false;
            } else {
                document.getElementById('price_per_night').nextElementSibling.textContent = '';
            }
            
            // Validasi deskripsi
            const description = document.getElementById('description').value.trim();
            if (description === '') {
                document.getElementById('description').nextElementSibling.textContent = 'Deskripsi villa harus diisi';
                isValid = false;
            } else {
                document.getElementById('description').nextElementSibling.textContent = '';
            }
            
            // Validasi foto
            const photo = document.getElementById('photo').files[0];
            if (!photo) {
                document.getElementById('photo').nextElementSibling.textContent = 'Foto villa harus diupload';
                isValid = false;
            } else {
                document.getElementById('photo').nextElementSibling.textContent = '';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
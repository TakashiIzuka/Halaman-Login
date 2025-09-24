<?php
// Konfigurasi database (default XAMPP)
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "registrasi_db";

// Fungsi upload file aman
function uploadFile($file, $targetDir, $allowedTypes) {
    if ($file["error"] !== UPLOAD_ERR_OK) {
        return array('error' => 'Error upload file.');
    }
    
    $targetFile = $targetDir . basename($file["name"]);
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Validasi ukuran (max 5MB)
    if ($file["size"] > 5000000) {
        return array('error' => 'File terlalu besar (max 5MB).');
    }
    
    // Validasi tipe
    if (!in_array($fileType, $allowedTypes)) {
        return array('error' => 'Tipe file tidak diizinkan.');
    }
    
    // Rename jika file sudah ada (hindari overwrite)
    $i = 1;
    $originalName = basename($file["name"], "." . $fileType);
    while (file_exists($targetFile)) {
        $targetFile = $targetDir . $originalName . "_" . $i . "." . $fileType;
        $i++;
    }
    
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return array('success' => $targetFile);
    } else {
        return array('error' => 'Gagal upload file.');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Ambil dan sanitasi data
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $bday = $_POST['bday'] ?? '';
    $agama = $_POST['agama'] ?? '';
    $biografi = trim($_POST['biografi'] ?? '');
    $favcolor = $_POST['favcolor'] ?? '';

    // Validasi dasar
    $errors = [];
    if (empty($nama) || empty($username) || empty($email) || empty($password) || empty($jenis_kelamin) || empty($agama)) {
        $errors[] = "Semua field wajib harus diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }
    if (!in_array($jenis_kelamin, ['laki-laki', 'perempuan'])) {
        $errors[] = "Pilih jenis kelamin yang valid.";
    }

    // Upload file
    $uploadDir = "uploads/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $allowedCode = ['txt', 'js', 'py', 'html', 'css'];
    $allowedPhoto = ['jpg', 'jpeg', 'png', 'gif'];
    
    $filecodeResult = uploadFile($_FILES["filecode"], $uploadDir, $allowedCode);
    $filephotoResult = uploadFile($_FILES["filephoto"], $uploadDir, $allowedPhoto);
    
    if (isset($filecodeResult['error'])) {
        $errors[] = $filecodeResult['error'];
    }
    if (isset($filephotoResult['error'])) {
        $errors[] = $filephotoResult['error'];
    }

    // Jika tidak ada error, simpan ke DB
    if (empty($errors)) {
        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert dengan prepared statement
            $stmt = $pdo->prepare("INSERT INTO users (nama, username, email, password, jenis_kelamin, bday, agama, biografi, filecode_path, filephoto_path, favcolor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nama, $username, $email, $hashedPassword, $jenis_kelamin, $bday, $agama, $biografi,
                $filecodeResult['success'], $filephotoResult['success'], $favcolor
            ]);
            
            // Sukses: Redirect
            header("Location: index.php?status=success");
            exit();
            
        } catch(PDOException $e) {
            // Tangani duplikat atau error DB
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $errors[] = "Username atau email sudah terdaftar.";
            } else {
                $errors[] = "Error database: " . $e->getMessage();  // Di produksi, sembunyikan detail
            }
        }
    }

    // Jika error, redirect dengan pesan
    if (!empty($errors)) {
        $errorMsg = implode(' ', $errors);
        header("Location: index.php?status=error&msg=" . urlencode($errorMsg));
        exit();
    }
} else {
    // Bukan POST, redirect ke form
    header("Location: index.php");
    exit();
}
?>
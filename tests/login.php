<?php
// Memulai sesi jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Mengecek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once "config.php"; // Menghubungkan ke file konfigurasi database

    // Validasi input untuk mencegah input kosong
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo alert("Username dan Password tidak boleh kosong!", "login.php");
        exit;
    }

    // Menggunakan prepared statement untuk mencegah SQL Injection
    $sql = "SELECT * FROM pelanggan WHERE username = ? AND password = ?";
    if ($stmt = $connection->prepare($sql)) {
        // Mengenkripsi password dengan md5 (bisa diganti dengan hash lebih aman seperti bcrypt)
        $hashed_password = md5($password);

        // Mengikat parameter dan menjalankan query
        $stmt->bind_param("ss", $username, $hashed_password);
        $stmt->execute();

        // Mendapatkan hasil query
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();

            // Menyimpan data pengguna ke dalam sesi
            $_SESSION["pelanggan"] = [
                "is_logged" => true,
                "id" => $data["id_pelanggan"],
                "username" => $data["username"],
                "nama" => $data["nama"],
                "no_ktp" => $data["no_ktp"],
                "no_telp" => $data["no_telp"],
                "email" => $data["email"],
                "alamat" => $data["alamat"],
            ];

            // Redirect ke halaman index setelah login berhasil
            header('location: index.php');
        } else {
            echo alert("Username atau Password tidak sesuai!", "login.php");
        }

        $stmt->close(); // Menutup statement
    } else {
        echo "Query error!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Pelanggan - Akamse Rental Motor</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            margin-top: 40px;
            background-image: url(assets/img/bg.jpg);
            background-size: cover;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="text-center"><b>AKAMSE RENTAL MOTOR</b></h3>
                    </div>
                    <div class="panel-body">
                        <form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" class="form-control" id="username" placeholder="Masukkan username" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" class="btn btn-info btn-block">Login</button>
                        </form>
                    </div>
                    <div class="panel-footer">
                        Belum punya akun? <a href="index.php?page=daftar">Daftar sekarang.</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>
</html>

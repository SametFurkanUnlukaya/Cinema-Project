<?php
// Veritabanı bağlantısını dahil et
require_once '../includes/baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Şifre eşleşmiyorsa uyar
    if ($password !== $confirm) {
        echo "<script>alert('Şifreler uyuşmuyor!');</script>";
    } else {
        // Bu e-posta zaten kayıtlı mı kontrol et
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            echo "<script>alert('Bu e-posta adresi zaten kullanılıyor!');</script>";
        } else {
            // Şifreyi hashle
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Kayıt işlemi
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role, is_approved)
                                    VALUES (?, ?, ?, 'user', 0)");
            $stmt->bind_param("sss", $fullname, $email, $hashed);

            if ($stmt->execute()) {
                echo "<script>alert('Kayıt başarılı! Yönetici onayı bekleniyor.');</script>";
            } else {
                echo "<script>alert('Hata oluştu: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol | scene'SFrame</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h1 class="logo">scene'SFrame</h1>
</header>

<main class="main-kg">
    <section class="welcome-box-kg">
        <h2>Kayıt Ol</h2>
        <form method="post" action="#">
            <input type="text" name="fullname" placeholder="Ad Soyad" required><br><br>
            <input type="email" name="email" placeholder="E-posta" required><br><br>
            <input type="password" name="password" placeholder="Şifre" required><br><br>
            <input type="password" name="confirm_password" placeholder="Şifre Tekrar" required><br><br>
            <button type="submit" class="btn">Kaydol</button>
        </form>
    </section>
</main>

<footer>
    <p>&copy; 2025 SF Sinemaları - scene'SFrame</p>
</footer>

</body>
</html>

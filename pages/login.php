<?php
session_start(); // Oturum başlat
require_once '../includes/baglanti.php'; // Veritabanı bağlantısı

// Giriş formu gönderildiyse
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kullanıcıyı email'e göre çek
    $stmt = $conn->prepare("SELECT id, fullname, email, password, role, is_approved, must_change_password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kullanıcı varsa
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Şifre doğru mu?
        if (password_verify($password, $user['password'])) {
            // Onaylı kullanıcı mı?
            if ($user['is_approved'] == 1) {
                // Oturuma bilgileri kaydet
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_email']=$user['email'];
                //zorunlu şifre değişikliği
                if ($user['must_change_password'] == 1) {
                    header("Location: ../pages/change_password.php");
                    exit;
                }

                // Kullanıcı paneline yönlendir
                header("Location: ../pages/panel.php");
                exit;
            } elseif ($user['is_approved'] == 0) {
                echo "<script>alert('Hesabınız henüz yönetici tarafından onaylanmamış.');</script>";
            } else {
                echo "<script>alert('Hesabınız reddedilmiş.');</script>";
            }
        } else {
            echo "<script>alert('Şifre yanlış!');</script>";
        }
    } else {
        echo "<script>alert('Bu e-posta adresiyle kayıtlı kullanıcı bulunamadı!');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap | scene'SFrame</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <div class="stars"></div>

    <header>
        <h1 class="logo">scene'SFrame</h1>
    </header>

    <main class="main-kg">
        <section class="welcome-box-kg">
            <h2>Giriş Yap</h2>
            <form method="post" action="#">
                <input type="email" name="email" placeholder="E-posta" required><br><br>
                <input type="password" name="password" placeholder="Şifre" required><br><br>
                <button type="submit" class="btn">Giriş Yap</button>
            </form>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 SF Sinemaları - scene'SFrame</p>
    </footer>

</body>
</html>

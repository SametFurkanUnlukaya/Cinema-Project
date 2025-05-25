<?php
session_start();
require_once '../includes/baglanti.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $hata = "Şifreler uyuşmuyor!";
    } elseif (strlen($new_password) < 6) {
        $hata = "Şifre en az 6 karakter olmalı!";
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $user_id = $_SESSION['user_id'];

        $stmt = $conn->prepare("UPDATE users SET password=?, must_change_password=0 WHERE id=?");
        $stmt->bind_param("si", $hashed, $user_id);

        if ($stmt->execute()) {
            // Şifre değişti, panele yönlendir
            header("Location: panel.php");
            exit;
        } else {
            $hata = "Bir hata oluştu!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şifre Değiştir | scene'SFrame</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <main class="main-kg">
        <section class="welcome-box-kg">
            <h2>Yeni Şifre Belirle</h2>
            <?php if (isset($hata)): ?>
                <p style="color:red;"><?= htmlspecialchars($hata) ?></p>
            <?php endif; ?>
            <form method="post" action="">
                <input type="password" name="new_password" placeholder="Yeni Şifre" required><br><br>
                <input type="password" name="confirm_password" placeholder="Yeni Şifre (Tekrar)" required><br><br>
                <button type="submit" class="btn">Şifreyi Değiştir</button>
            </form>
        </section>
    </main>
</body>
</html>

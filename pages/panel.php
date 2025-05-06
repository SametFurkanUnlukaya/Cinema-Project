<?php
session_start();

// Giriş yapılmamışsa yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$kullanici = $_SESSION['fullname'] ?? 'Kullanıcı';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>scene'SFrame | Sinema Paneli</title>
    <link rel="stylesheet" href="../css/style.css">
    
</head>
<body>

    <nav class="navbar">
        <div class="menu">
            <a href="#">Etkinlikler</a>
            <a href="#">Biletlerim</a>
            <a href="#">Kuponlarım</a>
            <a href="#">Vizyona Girecekler</a>
            <a href="#">İletişim</a>
        </div>
        <div class="sepet">Sepetim</div>
    </nav>

    <div class="vizyonda">🎬 Vizyondakiler</div>

    <div class="kategoriler">
        <button>Tümü</button>
        <button>Macera</button>
        <button>Komedi</button>
        <button>Dram</button>
        <button>Korku</button>
        <button>Animasyon</button>
    </div>

    <div class="filmler">
        <!-- Örnek film kutuları -->
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
        <div class="film-karti"></div>
    </div>

    <footer>
        <p>&copy; 2025 SF Sinemaları - scene'SFrame</p>
    </footer>

</body>
</html>

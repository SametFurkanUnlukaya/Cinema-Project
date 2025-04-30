<?php
$servername = "localhost";  // Sunucu adı
$username = "root";         // Varsayılan kullanıcı adı (XAMPP için root)
$password = "";             // Varsayılan şifre (boş bırakılır)
$dbname = "scenesframe_db"; // Az önce oluşturduğumuz veritabanı adı

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Hata varsa durdur
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>

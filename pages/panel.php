<?php
session_start();
require_once '../includes/baglanti.php';

// Oturum yoksa girişe yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Admin kontrolü
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
$kullanici = $_SESSION['fullname'] ?? 'Kullanıcı';

// --- Etkinlik ekleme işlemi (sadece admin) ---
if ($is_admin && isset($_POST['etkinlik_ekle'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $event_date = trim($_POST['event_date']);

    // Görsel yükleme
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['tmp_name']) {
        $targetDir = "../img/";
        $fileName = time() . '_' . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = "img/" . $fileName;
        }
    }

    // Veritabanına ekle
    $stmt = $conn->prepare("INSERT INTO events (title, description, category, image, event_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $description, $category, $imagePath, $event_date);
    $stmt->execute();
    echo "<script>alert('Etkinlik başarıyla eklendi!');window.location='panel.php';</script>";
    exit;
}

// --- Etkinlik silme işlemi (sadece admin) ---
if ($is_admin && isset($_POST['delete_event'])) {
    $del_id = intval($_POST['delete_event']);
    // Resmi de silmek istersen dosyayı bul ve unlink kullanabilirsin!
    $conn->query("DELETE FROM events WHERE id = $del_id");
    echo "<script>alert('Etkinlik silindi!');window.location='panel.php';</script>";
    exit;
}

// --- Sepete ekle (örnek, geliştirmeye açık) ---
if (!$is_admin && isset($_POST['add_to_cart'])) {
    // Burada sepete ekleme işlemini yapabilirsin.
    // $_POST['add_to_cart'] ile event_id alınır, $_SESSION ile veya veritabanına eklenebilir.
    // Şimdilik uyarı bırakalım:
    echo "<script>alert('Etkinlik sepete eklendi (örnek)!');</script>";
}

// Etkinlikleri çek
$events = $conn->query("SELECT * FROM events ORDER BY event_date DESC");
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

    <?php if ($is_admin): ?>
        <div style="text-align:right; width:90%; margin: 20px auto;">
            <a href="#" class="btn" onclick="document.getElementById('etkinlikEkle').style.display='block'">+ Etkinlik Ekle</a>
        </div>
        <!-- Etkinlik ekleme formu (modal gibi) -->
        <div id="etkinlikEkle" style="display:none; width:90%; margin:0 auto; background:#161838; border-radius:12px; padding:20px;">
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Etkinlik Başlığı" required style="width:220px; margin-bottom:8px;"><br>
                <input type="text" name="category" placeholder="Kategori (ör. Komedi)" required style="width:220px; margin-bottom:8px;"><br>
                <textarea name="description" placeholder="Açıklama" required style="width:300px; height:70px; margin-bottom:8px;"></textarea><br>
                <input type="datetime-local" name="event_date" required style="margin-bottom:8px;"><br>
                <input type="file" name="image" accept="image/*" required style="margin-bottom:8px;"><br>
                <button type="submit" name="etkinlik_ekle" class="btn">Kaydet</button>
                <button type="button" class="btn btn-red" onclick="document.getElementById('etkinlikEkle').style.display='none'">İptal</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="vizyonda">🎬 Vizyondakiler</div>

    <!-- Kategoriler -->
    <div class="kategoriler">
        <button onclick="filterCategory('Tümü')">Tümü</button>
        <button onclick="filterCategory('Macera')">Macera</button>
        <button onclick="filterCategory('Komedi')">Komedi</button>
        <button onclick="filterCategory('Dram')">Dram</button>
        <button onclick="filterCategory('Korku')">Korku</button>
        <button onclick="filterCategory('Animasyon')">Animasyon</button>
    </div>

    <!-- Etkinlik kartları -->
    <div class="filmler" id="filmler">
        <?php while ($event = $events->fetch_assoc()): ?>
            <div class="film-karti" data-category="<?= htmlspecialchars($event['category']) ?>">
                <img src="../<?= htmlspecialchars($event['image']) ?>" alt="Etkinlik Görseli">
                <div class="film-karti-body">
                    <div class="film-karti-title"><?= htmlspecialchars($event['title']) ?></div>
                    <div class="film-karti-category"><?= htmlspecialchars($event['category']) ?></div>
                    <div class="film-karti-desc"><?= htmlspecialchars($event['description']) ?></div>
                    <div class="film-karti-date"><?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></div>
                    <?php if (!$is_admin): ?>
                        <form method="post">
                            <button type="submit" name="add_to_cart" value="<?= $event['id'] ?>" class="btn-sepet">Sepete Ekle</button>
                        </form>
                    <?php else: ?>
                        <form method="post">
                            <input type="hidden" name="delete_event" value="<?= $event['id'] ?>">
                            <button type="submit" class="btn btn-red">Sil</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <footer>
        <p>&copy; 2025 SF Sinemaları - scene'SFrame</p>
    </footer>

    <script>
        // Kategoriye göre filtreleme (sadece ön yüz, PHP ile de yapılabilir)
        function filterCategory(kategori) {
            const kartlar = document.querySelectorAll('.film-karti');
            kartlar.forEach(function(card) {
                if (kategori === 'Tümü' || card.getAttribute('data-category').toLowerCase() === kategori.toLowerCase()) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

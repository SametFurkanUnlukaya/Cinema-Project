<?php
// Veritabanına bağlan
require_once '../includes/baglanti.php';

// Eğer URL'de hem id hem de action (onay/reddet) varsa
if (isset($_GET['id']) && isset($_GET['action'])) {
    $user_id = intval($_GET['id']);
    $action = $_GET['action'];

    // Güncellenecek is_approved değeri belirleniyor
    if ($action === "approve") {
        $newStatus = 1; // Onaylandı
    } elseif ($action === "reject") {
        $newStatus = 2; // Reddedildi
    } else {
        // Geçersiz action verilirse işlem yapma
        header("Location: admin_onay.php");
        exit;
    }

    // Veritabanında güncelleme işlemi yapılır
    $update = $conn->prepare("UPDATE users SET is_approved = ? WHERE id = ?");
    $update->bind_param("ii", $newStatus, $user_id);
    $update->execute();

    // Sadece yenileme yap
    header("Location: admin_onay.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Onay Bekleyen Kullanıcılar</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <h1 class="logo">scene'SFrame Yönetici Paneli</h1>
</header>

<main>
    <section class="welcome-box-admin">
        <h2>Onay Bekleyen Kullanıcılar</h2>

        <div class="box-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ad Soyad</th>
                        <th>Email</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $query = $conn->query("SELECT * FROM users WHERE is_approved = 0");
                if ($query->num_rows > 0):
                    while ($user = $query->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['fullname']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <a href="admin_onay.php?id=<?= $user['id'] ?>&action=approve" class="btn btn-admin">Onayla</a>
                            <a href="admin_onay.php?id=<?= $user['id'] ?>&action=reject" class="btn btn-admin btn-red">Reddet</a>
                        </td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="4">Onay bekleyen kullanıcı bulunmamaktadır.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>

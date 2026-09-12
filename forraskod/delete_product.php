<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

require_once 'api/config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $db = Database::getInstance()->getConnection();

    // 1. Megkeressük a terméket, hogy megtudjuk a kép útvonalát
    $stmt = $db->prepare("SELECT kep_url FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Ha létezik a képfájl a szerveren, letöröljük azt is
        if (file_exists($product['kep_url'])) {
            unlink($product['kep_url']);
        }

        // 2. Töröljük a terméket az adatbázisból
        $deleteStmt = $db->prepare("DELETE FROM products WHERE id = :id");
        $deleteStmt->execute([':id' => $id]);
    }
}
 
// Visszairányítjuk az admint a termékkezelőbe egy sikeres üzenettel
header("Location: admin_products.php?msg=deleted");
exit;
?>
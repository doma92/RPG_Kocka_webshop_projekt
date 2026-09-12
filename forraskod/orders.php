<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: profile.php");
    exit;
}

require_once 'api/config/database.php';
$database = Database::getInstance();
$db = $database->getConnection();
$siker_uzenet = '';

// ÚJ: Ha az admin megnyomta a státusz Frissítés gombot
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['statusz'];
    
    $updateQuery = "UPDATE orders SET statusz = :statusz WHERE id = :id";
    $updateStmt = $db->prepare($updateQuery);
    if ($updateStmt->execute([':statusz' => $new_status, ':id' => $order_id])) {
        $siker_uzenet = "A #" . $order_id . " rendelés státusza sikeresen frissítve lett!";
    }
}

$query = "SELECT * FROM orders ORDER BY rendeles_ideje DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php'; 
?>

<main class="main-content orders-layout">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Beérkezett Rendelések</h1>
            <p style="color: #555;">Itt láthatod és kezelheted a rendeléseket.</p>
        </div>
    </div>

    <?php if($siker_uzenet): ?>
        <p style="color: green; font-weight: bold; padding: 10px; background: #e8f8f5; border-radius: 5px; margin-bottom: 20px;">
            <i class="fas fa-check-circle"></i> <?php echo $siker_uzenet; ?>
        </p>
    <?php endif; ?>

    <div class="orders-container">
        <?php if (count($orders) > 0): ?>
            
            <?php foreach ($orders as $order): ?>
                <article class="order-card">
                    
                    <div class="order-header" style="flex-wrap: wrap; gap: 15px;">
                        <div>
                            <div class="order-id">Rendelés #<?php echo $order['id']; ?></div>
                            <div class="order-date">
                                <i class="far fa-clock"></i> 
                                <?php echo date('Y. m. d. H:i', strtotime($order['rendeles_ideje'])); ?>
                            </div>
                        </div>
                        
                        <!-- ÚJ: Státusz módosító űrlap -->
                        <form action="orders.php" method="POST" style="display: flex; gap: 10px; align-items: center; background: rgba(255,255,255,0.1); padding: 10px; border-radius: 8px;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <label style="color: white; margin: 0;">Státusz:</label>
                            <select name="statusz" style="padding: 5px; border-radius: 4px; border: none;">
                                <option value="Új" <?php if($order['statusz'] == 'Új') echo 'selected'; ?>>Új</option>
                                <option value="Feldolgozás alatt" <?php if($order['statusz'] == 'Feldolgozás alatt') echo 'selected'; ?>>Feldolgozás alatt</option>
                                <option value="Postázva" <?php if($order['statusz'] == 'Postázva') echo 'selected'; ?>>Postázva</option>
                                <option value="Teljesítve" <?php if($order['statusz'] == 'Teljesítve') echo 'selected'; ?>>Teljesítve</option>
                            </select>
                            <button type="submit" name="update_status" class="btn-primary" style="padding: 5px 15px; font-size: 0.9rem; background-color: var(--accent-color);">Mentés</button>
                        </form>
                    </div>
                    
                    <div class="order-body">
                        <div class="order-customer-info">
                            <h3>Vásárló adatai</h3>
                            <p><strong>Név:</strong> <?php echo htmlspecialchars($order['vezeteknev'] . ' ' . $order['keresztnev']); ?></p>
                            <p><strong>E-mail:</strong> <a href="mailto:<?php echo htmlspecialchars($order['email']); ?>"><?php echo htmlspecialchars($order['email']); ?></a></p>
                            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($order['telefon']); ?></p>
                            <p><strong>Szállítási cím:</strong> <?php echo htmlspecialchars($order['iranyitoszam'] . ' ' . $order['telepules'] . ', ' . $order['cim']); ?></p>
                            
                            <?php if (!empty($order['megjegyzes'])): ?>
                                <p class="order-note"><strong>Megjegyzés:</strong> <em>"<?php echo htmlspecialchars($order['megjegyzes']); ?>"</em></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-items-list">
                            <h3>Rendelt tételek</h3>
                            <ul>
                                <?php
                                $itemQuery = "SELECT * FROM order_items WHERE order_id = :order_id";
                                $itemStmt = $db->prepare($itemQuery);
                                $itemStmt->execute([':order_id' => $order['id']]);
                                $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($items as $item):
                                ?>
                                    <li>
                                        <span class="item-name"><?php echo htmlspecialchars($item['termek_nev']); ?></span>
                                        <span class="item-price"><?php echo $item['darab']; ?> db &times; <?php echo number_format($item['egysegar'], 0, ',', ' '); ?> Ft</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="order-total">
                                Összesen: <?php echo number_format($order['vegosszeg'], 0, ',', ' '); ?> Ft
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

        <?php else: ?>
            <div class="empty-orders-msg">
                <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                <p>Még nem érkezett egyetlen rendelés sem a webshopba.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
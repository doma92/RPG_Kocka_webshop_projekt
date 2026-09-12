<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Védelem: Csak adminisztrátoroknak!
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

include 'header.php'; 
require_once 'api/config/database.php';

$database = Database::getInstance();
$db = $database->getConnection();

$query = "SELECT * FROM products ORDER BY id DESC";
$stmt = $db->query($query);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Egy kis extra stílus csak ehhez a táblázathoz -->
<style>
    .admin-table { width: 100%; border-collapse: collapse; margin-top: 20px; background: var(--header-bg); border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
    .admin-table th { background-color: var(--primary-color); color: white; }
    .admin-table img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
    .action-btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; color: white; display: inline-block; margin-right: 5px; font-size: 0.9rem; }
    .btn-edit { background-color: #f39c12; }
    .btn-edit:hover { background-color: #d68910; }
    .btn-delete { background-color: #e74c3c; }
    .btn-delete:hover { background-color: #c0392b; }
</style>

<main class="main-content orders-layout">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1>Termékek kezelése</h1>
            <p style="color: #555;">Itt módosíthatod vagy törölheted a bolt kínálatát.</p>
        </div>
        <a href="add_product.php" class="btn-primary" style="background-color: #27ae60;">
            <i class="fas fa-plus"></i> Új termék feltöltése
        </a>
    </div>

    <!-- Törlés/Szerkesztés visszajelző üzenet -->
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <p style="color: green; font-weight: bold; padding: 10px; background: #e8f8f5; border-radius: 5px;">A termék sikeresen törölve lett!</p>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Kép</th>
                    <th>Név</th>
                    <th>Típus</th>
                    <th>Szín</th>
                    <th>Ár</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><img src="<?php echo htmlspecialchars($product['kep_url']); ?>" alt="Kocka"></td>
                        <td><strong><?php echo htmlspecialchars($product['nev']); ?></strong></td>
                        <td><?php echo htmlspecialchars($product['tipus']); ?></td>
                        <td><?php echo htmlspecialchars($product['szin']); ?></td>
                        <td><?php echo number_format($product['ar'], 0, ',', ' '); ?> Ft</td>
                        <td>
                            <!-- Szerkesztés gomb -->
                            <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="action-btn btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Törlés gomb (JavaScript megerősítéssel) -->
                            <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Biztosan törölni akarod ezt a terméket?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                
                <?php if(count($products) == 0): ?>
                    <tr><td colspan="6" style="text-align: center;">Jelenleg nincs egyetlen termék sem a boltban.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
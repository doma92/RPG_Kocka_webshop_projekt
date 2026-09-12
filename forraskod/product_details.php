<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'api/config/database.php';
$db = Database::getInstance()->getConnection();

// Ellenőrizzük, hogy kaptunk-e ID-t az URL-ben (pl. product_details.php?id=5)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    header("Location: shop.php");
    exit;
}

// 1. Termék adatainak lekérése
$stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("A keresett varázstárgy nem található a kincstárban!");
}

// 2. Galéria képek lekérése
$galStmt = $db->prepare("SELECT kep_url FROM product_images WHERE product_id = :id");
$galStmt->execute([':id' => $id]);
$gallery = $galStmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Összegyűjtjük az összes képet egy tömbbe (Főkép + Galéria képek)
$osszes_kep = [$product['kep_url']];
foreach ($gallery as $kep) {
    $osszes_kep[] = $kep['kep_url'];
}

include 'header.php'; 
?>

<main class="main-content">
    <div style="display: flex; gap: 40px; max-width: 1200px; margin: 40px auto; padding: 20px; flex-wrap: wrap; background: var(--header-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <!-- BAL OLDAL: Képgaléria -->
        <div style="flex: 1; min-width: 300px;">
            <!-- Nagy főkép -->
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #eee; margin-bottom: 15px; background: #fff; text-align: center;">
                <img id="mainImageDisplay" src="<?php echo htmlspecialchars($osszes_kep[0]); ?>" alt="<?php echo htmlspecialchars($product['nev']); ?>" style="width: 100%; max-height: 500px; object-fit: contain; transition: 0.3s;">
            </div>
            
            <!-- Kis képek (Thumbnails) -->
            <?php if(count($osszes_kep) > 1): ?>
                <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                    <?php foreach($osszes_kep as $index => $img): ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" 
                             class="gallery-thumbnail" 
                             onclick="changeGalleryImage('<?php echo htmlspecialchars($img); ?>', this)"
                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 3px solid <?php echo $index === 0 ? 'var(--accent-color)' : 'transparent'; ?>; transition: 0.2s;">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- JOBB OLDAL: Termék információk és Vásárlás -->
        <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column;">
            
            <!-- Vissza gomb -->
            <a href="shop.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold; margin-bottom: 20px; display: inline-block;">
                <i class="fas fa-arrow-left"></i> Vissza a boltba
            </a>

            <h1 style="color: var(--primary-color); margin-bottom: 10px; font-size: 2.5rem;"><?php echo htmlspecialchars($product['nev']); ?></h1>
            <h2 style="color: var(--accent-color); font-size: 2rem; margin-bottom: 20px;"><?php echo number_format($product['ar'], 0, ',', ' '); ?> Ft</h2>

            <div style="background: var(--bg-color); padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <p style="margin-bottom: 10px; font-size: 1.1rem;"><strong><i class="fas fa-dice"></i> Típus:</strong> <?php echo htmlspecialchars($product['tipus']); ?></p>
                <p style="margin-bottom: 10px; font-size: 1.1rem;"><strong><i class="fas fa-palette"></i> Szín:</strong> <?php echo htmlspecialchars($product['szin']); ?></p>
                <p style="margin-bottom: 0; font-size: 1.1rem;">
                    <strong><i class="fas fa-box"></i> Készlet:</strong> 
                    <?php if($product['keszlet'] > 0): ?>
                        <span style="color: #27ae60; font-weight: bold;"><?php echo $product['keszlet']; ?> db raktáron</span>
                    <?php else: ?>
                        <span style="color: #e74c3c; font-weight: bold;">Jelenleg elfogyott</span>
                    <?php endif; ?>
                </p>
            </div>

            <div style="color: #555; line-height: 1.6; margin-bottom: 30px; flex-grow: 1;">
                <?php 
                if (!empty($product['leiras'])) {
                    echo nl2br(htmlspecialchars($product['leiras'])); 
                } else {
                    echo "<em>Ehhez a varázstárgyhoz még nem készült részletes leírás.</em>";
                }
                ?>
            </div>

            <!-- KOSÁRBA GOMB (Ugyanazokat a data attribútumokat kapja, amiket az app.js vár) -->
            <button class="btn-primary add-to-cart-btn" 
                    data-id="<?php echo $product['id']; ?>" 
                    data-name="<?php echo htmlspecialchars($product['nev']); ?>" 
                    data-price="<?php echo $product['ar']; ?>" 
                    data-image="<?php echo htmlspecialchars($product['kep_url']); ?>"
                    <?php if($product['keszlet'] <= 0) echo 'disabled style="background-color: #ccc; cursor: not-allowed;"'; ?>
                    style="width: 100%; padding: 15px; font-size: 1.2rem;">
                <i class="fas fa-cart-plus"></i> <?php echo $product['keszlet'] > 0 ? 'Kosárba rakom' : 'Elfogyott'; ?>
            </button>
            
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<!-- A galéria képcserélő logikája -->
<script>
    function changeGalleryImage(imgUrl, element) {
        // 1. Főkép cseréje
        document.getElementById('mainImageDisplay').src = imgUrl;
        
        // 2. Minden kis képről levesszük a keretet
        let thumbs = document.querySelectorAll('.gallery-thumbnail');
        thumbs.forEach(thumb => {
            thumb.style.borderColor = 'transparent';
        });
        
        // 3. Az aktív kis képre rátesszük a keretet
        element.style.borderColor = 'var(--accent-color)';
    }
</script>

<!-- Betöltjük a fő app.js-t a Kosár működéséhez -->
<script src="assets/js/app.js"></script>
</body>
</html>
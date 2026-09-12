<?php
session_start();
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

require_once 'api/config/database.php';
$db = Database::getInstance()->getConnection();

$hiba_uzenet = '';
$siker_uzenet = '';
$product = null;

// 1. Termék betöltése
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        die("A termék nem található!");
    }
} else {
    header("Location: admin_products.php");
    exit;
}

// --- ÚJ: GALÉRIA KÉP TÖRLÉSE ---
if (isset($_GET['delete_image_id'])) {
    $del_id = $_GET['delete_image_id'];
    
    // Lekérjük a kép útvonalát, hogy a szerverről is le tudjuk törölni a fájlt
    $delStmt = $db->prepare("SELECT kep_url FROM product_images WHERE id = :id AND product_id = :pid");
    $delStmt->execute([':id' => $del_id, ':pid' => $product['id']]);
    $del_img = $delStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($del_img) {
        if (file_exists($del_img['kep_url'])) {
            unlink($del_img['kep_url']); // Fájl fizikai törlése
        }
        $db->prepare("DELETE FROM product_images WHERE id = :id")->execute([':id' => $del_id]);
        $siker_uzenet = "A galéria kép sikeresen törölve lett!";
    }
}

// 2. Űrlap feldolgozása (Szerkesztés mentése)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nev = trim($_POST['nev']);
    $leiras = trim($_POST['leiras'] ?? '');
    $tipus = trim($_POST['tipus']);
    $szin = trim($_POST['szin']);
    $ar = intval($_POST['ar']);
    $keszlet = intval($_POST['keszlet']);
    
    $uj_kep_utvonal = $product['kep_url'];
    $engedelyezett_kiterjesztesek = ['jpg', 'jpeg', 'png', 'webp'];

    // Főkép cseréje (ha töltöttek fel újat)
    if (!empty($_FILES['kep']['name']) && $_FILES['kep']['error'] == 0) {
        $kiterjesztes = strtolower(pathinfo($_FILES['kep']['name'], PATHINFO_EXTENSION));
        if (in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {
            $uj_fajlnev = time() . '_main_' . basename($_FILES['kep']['name']);
            $cel_utvonal = 'assets/img/' . $uj_fajlnev;
            
            if (move_uploaded_file($_FILES['kep']['tmp_name'], $cel_utvonal)) {
                if (file_exists($product['kep_url'])) {
                    unlink($product['kep_url']); // Régi főkép törlése
                }
                $uj_kep_utvonal = $cel_utvonal;
            }
        } else {
            $hiba_uzenet = "Csak JPG, PNG vagy WEBP kép engedélyezett a főképnél!";
        }
    }

    if (empty($hiba_uzenet)) {
        // Alapadatok frissítése
      // ÚJ: Bekerült a 'leiras' oszlop frissítése
        $updateQuery = "UPDATE products SET nev = :nev, leiras = :leiras, tipus = :tipus, szin = :szin, ar = :ar, keszlet = :keszlet, kep_url = :kep_url WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        
        if ($updateStmt->execute([
            ':nev' => $nev, ':leiras' => $leiras, ':tipus' => $tipus, ':szin' => $szin, ':ar' => $ar, ':keszlet' => $keszlet, ':kep_url' => $uj_kep_utvonal, ':id' => $id
        ])) {
            $product['nev'] = $nev; $product['leiras'] = $leiras; $product['tipus'] = $tipus; $product['szin'] = $szin; $product['ar'] = $ar; $product['keszlet'] = $keszlet; $product['kep_url'] = $uj_kep_utvonal;
            $siker_uzenet = "A termék adatai sikeresen frissítve lettek!";
            
            // --- ÚJ: TOVÁBBI GALÉRIA KÉPEK FELTÖLTÉSE ---
            if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
                $galeria_kepek = $_FILES['galeria'];
                $kep_szam = count($galeria_kepek['name']);

                for ($i = 0; $i < $kep_szam; $i++) {
                    if ($galeria_kepek['error'][$i] === UPLOAD_ERR_OK) {
                        $gal_kiterjesztes = strtolower(pathinfo($galeria_kepek['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($gal_kiterjesztes, $engedelyezett_kiterjesztesek)) {
                            $galeria_fajlnev = time() . '_gal_' . $i . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($galeria_kepek['name'][$i]));
                            $galeria_utvonal = 'assets/img/' . $galeria_fajlnev;

                            if (move_uploaded_file($galeria_kepek['tmp_name'][$i], $galeria_utvonal)) {
                                $gal_query = "INSERT INTO product_images (product_id, kep_url) VALUES (:pid, :kurl)";
                                $gal_stmt = $db->prepare($gal_query);
                                $gal_stmt->execute([':pid' => $id, ':kurl' => $galeria_utvonal]);
                            }
                        }
                    }
                }
                $siker_uzenet = "A termék és az új galéria képek sikeresen frissítve lettek!";
            }
        }
    }
}

// --- ÚJ: Jelenlegi galéria képek lekérése a megjelenítéshez ---
$galeriaStmt = $db->prepare("SELECT * FROM product_images WHERE product_id = :id");
$galeriaStmt->execute([':id' => $product['id']]);
$jelenlegi_galeria = $galeriaStmt->fetchAll(PDO::FETCH_ASSOC);

include 'header.php'; 
?>

<main class="main-content contact-layout">
    <div class="contact-header">
        <h1>Termék szerkesztése</h1>
        <p>
            <a href="admin_products.php" style="color: var(--accent-color); font-weight:bold;">
                <i class="fas fa-arrow-left"></i> Vissza a termékekhez
            </a>
        </p>
    </div> 

    <div class="contact-container" style="justify-content: center;">
        <div class="contact-form-box" style="max-width: 800px; flex: none; width: 100%;">
            
            <?php if($hiba_uzenet): ?> <p style="color: red; text-align: center; font-weight: bold;"><i class="fas fa-exclamation-circle"></i> <?php echo $hiba_uzenet; ?></p> <?php endif; ?>
            <?php if($siker_uzenet): ?> <p style="color: green; text-align: center; font-weight: bold;"><i class="fas fa-check-circle"></i> <?php echo $siker_uzenet; ?></p> <?php endif; ?>

            <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="POST" enctype="multipart/form-data" class="contact-form">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                <div class="form-group">
                    <label>Termék neve *</label>
                    <input type="text" name="nev" value="<?php echo htmlspecialchars($product['nev']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Termék leírása</label>
                    <textarea name="leiras" rows="5" style="padding: 10px; width: 100%; border-radius: 4px; border: 1px solid #ccc; font-family: inherit;"><?php echo htmlspecialchars($product['leiras'] ?? ''); ?></textarea>
                </div>

                <div class="form-row" style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Anyag / Típus *</label>
                        <select name="tipus" required>
                            <option value="Akril" <?php if($product['tipus'] == 'Akril') echo 'selected'; ?>>Akril</option>
                            <option value="Fa" <?php if($product['tipus'] == 'Fa') echo 'selected'; ?>>Fa</option>
                            <option value="Fém" <?php if($product['tipus'] == 'Fém') echo 'selected'; ?>>Fém</option>
                            <option value="Borostyán" <?php if($product['tipus'] == 'Borostyán') echo 'selected'; ?>>Borostyán</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Színkategória *</label>
                        <select name="szin" required>
                            <option value="Piros" <?php if($product['szin'] == 'Piros') echo 'selected'; ?>>Piros</option>
                            <option value="Kék" <?php if($product['szin'] == 'Kék') echo 'selected'; ?>>Kék</option>
                            <option value="Zöld" <?php if($product['szin'] == 'Zöld') echo 'selected'; ?>>Zöld</option>
                            <option value="Fekete" <?php if($product['szin'] == 'Fekete') echo 'selected'; ?>>Fekete</option>
                            <option value="Lila" <?php if($product['szin'] == 'Lila') echo 'selected'; ?>>Lila</option>
                            <option value="Fehér" <?php if($product['szin'] == 'Fehér') echo 'selected'; ?>>Fehér</option>
                            <option value="Citromsárga" <?php if($product['szin'] == 'Citromsárga') echo 'selected'; ?>>Citromsárga</option>
                            <option value="Narancs" <?php if($product['szin'] == 'Narancs') echo 'selected'; ?>>Narancs</option>
                            <option value="Rózsaszín" <?php if($product['szin'] == 'Rózsaszín') echo 'selected'; ?>>Rózsaszín</option>
                            <option value="Arany" <?php if($product['szin'] == 'Arany') echo 'selected'; ?>>Arany</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Ár (Ft) *</label>
                        <input type="number" name="ar" value="<?php echo $product['ar']; ?>" min="1" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Készlet (db) *</label>
                        <input type="number" name="keszlet" value="<?php echo isset($product['keszlet']) ? $product['keszlet'] : 0; ?>" min="0" required>
                    </div>
                </div>

                <!-- KÉPEK KEZELÉSE SZEKCIÓ -->
                <div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #eee;">
                    
                    <h3 style="margin-top: 0; color: var(--primary-color);">1. Főkép kezelése</h3>
                    <div class="form-group">
                        <label>Jelenlegi főkép (A bolt kártyáján ez jelenik meg):</label><br>
                        <img src="<?php echo htmlspecialchars($product['kep_url']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; margin-bottom: 10px; border: 2px solid var(--accent-color);">
                        <label>Főkép lecserélése (Opcionális)</label>
                        <input type="file" name="kep" accept="image/png, image/jpeg, image/webp" style="background: #fff; padding: 10px; width: 100%;">
                    </div>

                    <hr style="margin: 30px 0; border: 0; border-top: 1px solid #ddd;">

                    <h3 style="color: var(--primary-color);">2. Galéria képek</h3>
                    
                    <!-- Jelenlegi galéria képek listázása törlés gombbal -->
                    <?php if (count($jelenlegi_galeria) > 0): ?>
                        <label>Jelenlegi további képek:</label>
                        <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
                            <?php foreach ($jelenlegi_galeria as $gkep): ?>
                                <div style="position: relative; width: 100px; height: 100px;">
                                    <img src="<?php echo htmlspecialchars($gkep['kep_url']); ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; border: 1px solid #ccc;">
                                    
                                    <!-- Törlés gomb (Piros X) -->
                                    <a href="edit_product.php?id=<?php echo $product['id']; ?>&delete_image_id=<?php echo $gkep['id']; ?>" 
                                       onclick="return confirm('Biztosan törlöd ezt a képet a galériából?');"
                                       style="position: absolute; top: -8px; right: -8px; background: #e74c3c; color: white; border-radius: 50%; width: 25px; height: 25px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        &times;
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #777; font-style: italic;">Még nincsenek feltöltve extra képek ehhez a termékhez.</p>
                    <?php endif; ?>

                    <div class="form-group">
                        <label>Új képek hozzáadása a galériához (Több is kijelölhető!)</label>
                        <input type="file" name="galeria[]" multiple accept="image/png, image/jpeg, image/webp" style="background: #fff; padding: 10px; width: 100%;">
                    </div>
                </div>

                <button type="submit" class="btn-primary btn-submit" style="background-color: #f39c12; width: 100%; font-size: 1.1rem; padding: 15px;">
                    <i class="fas fa-save"></i> Módosítások mentése
                </button>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
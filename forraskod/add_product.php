<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

require_once 'api/config/database.php';

$hiba_uzenet = '';
$siker_uzenet = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nev = trim($_POST['nev']);
    $leiras = trim($_POST['leiras'] ?? '');
    $tipus = trim($_POST['tipus']);
    $szin = trim($_POST['szin']);
    $ar = intval($_POST['ar']);
    $keszlet = intval($_POST['keszlet']);
    
    // 1. Főkép feltöltésének ellenőrzése
    if (isset($_FILES['kep']) && $_FILES['kep']['error'] == 0) {
        $engedelyezett_kiterjesztesek = ['jpg', 'jpeg', 'png', 'webp'];
        $fajl_nev = $_FILES['kep']['name'];
        $fajl_meret = $_FILES['kep']['size'];
        $fajl_tmp = $_FILES['kep']['tmp_name'];
        $kiterjesztes = strtolower(pathinfo($fajl_nev, PATHINFO_EXTENSION));
        
        if (in_array($kiterjesztes, $engedelyezett_kiterjesztesek)) {
            if ($fajl_meret < 5000000) {
                // Biztonságos fájlnév generálás a főképnek
                $uj_fajlnev = time() . '_main_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($fajl_nev));
                $cel_utvonal = 'assets/img/' . $uj_fajlnev;
                
                if (move_uploaded_file($fajl_tmp, $cel_utvonal)) {
                    try {
                        $database = Database::getInstance();
                        $db = $database->getConnection();
                        
                        // Fő termék mentése az adatbázisba                    
                        $query = "INSERT INTO products (nev, leiras, tipus, szin, ar, keszlet, kep_url) VALUES (:nev, :leiras, :tipus, :szin, :ar, :keszlet, :kep_url)";
                        $stmt = $db->prepare($query);
                        $stmt->execute([
                            ':nev' => $nev,
                            ':leiras' => $leiras, 
                            ':tipus' => $tipus,
                            ':szin' => $szin,
                            ':ar' => $ar,
                            ':keszlet' => $keszlet,
                            ':kep_url' => $cel_utvonal
                        ]);
                        
                        // ÚJ: Lekérjük az imént elmentett termék ID-ját
                        $termek_id = $db->lastInsertId();

                        // ÚJ: Galéria képek feldolgozása
                        if (isset($_FILES['galeria']) && !empty($_FILES['galeria']['name'][0])) {
                            $galeria_kepek = $_FILES['galeria'];
                            $kep_szam = count($galeria_kepek['name']);

                            // Végigmegyünk az összes feltöltött extra képen
                            for ($i = 0; $i < $kep_szam; $i++) {
                                if ($galeria_kepek['error'][$i] === UPLOAD_ERR_OK) {
                                    // Egyedi fájlnév a galéria képeknek
                                    $gal_kiterjesztes = strtolower(pathinfo($galeria_kepek['name'][$i], PATHINFO_EXTENSION));
                                    
                                    if (in_array($gal_kiterjesztes, $engedelyezett_kiterjesztesek)) {
                                        $galeria_fajlnev = time() . '_gal_' . $i . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($galeria_kepek['name'][$i]));
                                        $galeria_utvonal = 'assets/img/' . $galeria_fajlnev;

                                        if (move_uploaded_file($galeria_kepek['tmp_name'][$i], $galeria_utvonal)) {
                                            // Beszúrás a product_images táblába
                                            $gal_query = "INSERT INTO product_images (product_id, kep_url) VALUES (:pid, :kurl)";
                                            $gal_stmt = $db->prepare($gal_query);
                                            $gal_stmt->execute([
                                                ':pid' => $termek_id,
                                                ':kurl' => $galeria_utvonal
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        
                        $siker_uzenet = "A kocka és a galéria képek sikeresen bekerültek a kincstárba!";
                    } catch (PDOException $e) {
                        $hiba_uzenet = "Adatbázis hiba: " . $e->getMessage();
                    }
                } else {
                    $hiba_uzenet = "Hiba történt a főkép feltöltése közben.";
                }
            } else {
                $hiba_uzenet = "A főkép túl nagy! Maximum 5MB engedélyezett.";
            }
        } else {
            $hiba_uzenet = "Csak JPG, JPEG, PNG és WEBP formátumok engedélyezettek!";
        }
    } else {
        $hiba_uzenet = "Kérlek, válassz ki egy főképet a termékhez!";
    }
}

include 'header.php'; 
?>

<main class="main-content contact-layout">
    <div class="contact-header">
        <h1>Új termék feltöltése</h1>
        <p>
            <a href="admin_products.php" style="color: var(--accent-color); font-weight:bold;">
                <i class="fas fa-arrow-left"></i> Vissza a termékekhez
            </a>
        </p>
    </div>

    <div class="contact-container" style="justify-content: center;">
        <div class="contact-form-box" style="max-width: 600px; flex: none; width: 100%;">
            
            <?php if($hiba_uzenet): ?> <p style="color: red; text-align: center; font-weight: bold;"><i class="fas fa-exclamation-circle"></i> <?php echo $hiba_uzenet; ?></p> <?php endif; ?>
            <?php if($siker_uzenet): ?> <p style="color: green; text-align: center; font-weight: bold;"><i class="fas fa-check-circle"></i> <?php echo $siker_uzenet; ?></p> <?php endif; ?>

            <form action="add_product.php" method="POST" enctype="multipart/form-data" class="contact-form">
                
                <div class="form-group">
                    <label for="nev">Termék neve *</label>
                    <input type="text" id="nev" name="nev" placeholder="Pl. Sárkánytűz" required>
                </div>

                <div class="form-group">
                    <label for="leiras">Termék leírása</label>
                    <textarea id="leiras" name="leiras" rows="5" placeholder="Írd le a kocka történetét, anyagát, különlegességeit..." style="padding: 10px; width: 100%; border-radius: 4px; border: 1px solid #ccc; font-family: inherit;"></textarea>
                </div>

                <div class="form-row" style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="tipus">Anyag / Típus *</label>
                        <select id="tipus" name="tipus" required>
                            <option value="">Válassz anyagot...</option>
                            <option value="Akril">Akril</option>
                            <option value="Fa">Fa</option>
                            <option value="Fém">Fém</option>
                            <option value="Borostyán">Borostyán</option>
                        </select>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="szin">Színkategória *</label>
                        <select id="szin" name="szin" required>
                            <option value="">Válassz színt...</option>
                            <option value="Piros">Piros</option>
                            <option value="Kék">Kék</option>
                            <option value="Zöld">Zöld</option>
                            <option value="Fekete">Fekete</option>
                            <option value="Lila">Lila</option>
                            <option value="Fehér">Fehér</option>
                            <option value="Citromsárga">Citromsárga</option>
                            <option value="Narancs">Narancs</option>
                            <option value="Rózsaszín">Rózsaszín</option>
                            <option value="Arany">Arany</option>
                        </select>
                    </div>
                </div>

                <div class="form-row" style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="ar">Ár (Ft) *</label>
                        <input type="number" id="ar" name="ar" min="1" placeholder="Pl. 1500" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="keszlet">Készlet (db) *</label>
                        <input type="number" id="keszlet" name="keszlet" min="0" value="10" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="kep">Főkép feltöltése (Ez jelenik meg a Boltban) *</label>
                    <input type="file" id="kep" name="kep" accept="image/png, image/jpeg, image/webp" required style="padding: 10px; background-color: #fff;">
                </div>

                <div class="form-group">
                    <label for="galeria">További galéria képek (Opcionális, több kép is kijelölhető!)</label>
                    <input type="file" id="galeria" name="galeria[]" multiple accept="image/png, image/jpeg, image/webp" style="padding: 10px; background-color: #fff;">
                </div>

                <button type="submit" class="btn-primary btn-submit" style="margin-top: 15px;">
                    <i class="fas fa-upload"></i> Termék feltöltése
                </button>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
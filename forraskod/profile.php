<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Ha nincs bejelentkezve, kidobjuk a login oldalra
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'api/config/database.php';
$db = Database::getInstance()->getConnection();
$user_id = $_SESSION['user_id'];

$siker_uzenet = '';
$hiba_uzenet = '';

// --- 1. PROFIL ADATOK FRISSÍTÉSE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $felhasznalonev = trim($_POST['felhasznalonev']);
    $email = trim($_POST['email']);
    
    // ÚJ: Nevek beolvasása
    $vezeteknev = trim($_POST['vezeteknev']);
    $keresztnev = trim($_POST['keresztnev']);
    
    $telefon = trim($_POST['telefon']);
    $iranyitoszam = trim($_POST['iranyitoszam']);
    $telepules = trim($_POST['telepules']);
    $cim = trim($_POST['cim']);
    
    $uj_jelszo = $_POST['uj_jelszo'];
    
    if (empty($felhasznalonev) || empty($email)) {
        $hiba_uzenet = "A felhasználónév és az e-mail cím megadása kötelező!";
    } else {
        $checkStmt = $db->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
        $checkStmt->execute([':email' => $email, ':id' => $user_id]);
        
        if ($checkStmt->rowCount() > 0) {
            $hiba_uzenet = "Ez az e-mail cím már foglalt egy másik fiókhoz!";
        } else {
            if (!empty($uj_jelszo)) {
                $hashed_password = password_hash($uj_jelszo, PASSWORD_BCRYPT);
                $updateStmt = $db->prepare("UPDATE users SET felhasznalonev = :nev, vezeteknev = :vnev, keresztnev = :knev, email = :email, jelszo = :jelszo, telefon = :telefon, iranyitoszam = :iranyitoszam, telepules = :telepules, cim = :cim WHERE id = :id");
                $updateStmt->execute([':nev' => $felhasznalonev, ':vnev' => $vezeteknev, ':knev' => $keresztnev, ':email' => $email, ':jelszo' => $hashed_password, ':telefon' => $telefon, ':iranyitoszam' => $iranyitoszam, ':telepules' => $telepules, ':cim' => $cim, ':id' => $user_id]);
                $siker_uzenet = "Az adataidat és a jelszavadat sikeresen frissítettük!";
            } else {
                $updateStmt = $db->prepare("UPDATE users SET felhasznalonev = :nev, vezeteknev = :vnev, keresztnev = :knev, email = :email, telefon = :telefon, iranyitoszam = :iranyitoszam, telepules = :telepules, cim = :cim WHERE id = :id");
                $updateStmt->execute([':nev' => $felhasznalonev, ':vnev' => $vezeteknev, ':knev' => $keresztnev, ':email' => $email, ':telefon' => $telefon, ':iranyitoszam' => $iranyitoszam, ':telepules' => $telepules, ':cim' => $cim, ':id' => $user_id]);
                $siker_uzenet = "Az adataidat sikeresen frissítettük!";
            }
        }
    }
}

// --- 2. FELHASZNÁLÓ AKTUÁLIS ADATAINAK LEKÉRÉSE ---
$stmtUser = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmtUser->execute([':id' => $user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// --- 3. RENDELÉSEK LEKÉRÉSE ---
$stmtOrd = $db->prepare("SELECT * FROM orders WHERE user_id = :id ORDER BY rendeles_ideje DESC");
$stmtOrd->execute([':id' => $user_id]);
$orders = $stmtOrd->fetchAll(PDO::FETCH_ASSOC);

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'profil';
include 'header.php'; 
?>

<main class="main-content">
    <div style="display: flex; gap: 30px; max-width: 1200px; margin: 40px auto; padding: 0 20px; flex-wrap: wrap;">
        
        <aside style="flex: 1; min-width: 250px; background: var(--header-bg); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); align-self: flex-start;">
            <div style="text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #eee;">
                <i class="fas fa-user-circle" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 10px;"></i>
                <h3 style="margin: 0; color: var(--primary-color);"><?php echo htmlspecialchars($user['felhasznalonev']); ?></h3>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="margin-bottom: 10px;">
                    <a href="profile.php?tab=profil" style="display: block; padding: 12px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s; color: <?php echo $active_tab == 'profil' ? 'white' : '#555'; ?>; background: <?php echo $active_tab == 'profil' ? 'var(--primary-color)' : 'transparent'; ?>;">
                        <i class="fas fa-id-card" style="width: 25px;"></i> Adataim
                    </a>
                </li>
                <li style="margin-bottom: 10px;">
                    <a href="profile.php?tab=rendelesek" style="display: block; padding: 12px 15px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: 0.3s; color: <?php echo $active_tab == 'rendelesek' ? 'white' : '#555'; ?>; background: <?php echo $active_tab == 'rendelesek' ? 'var(--primary-color)' : 'transparent'; ?>;">
                        <i class="fas fa-box" style="width: 25px;"></i> Rendeléseim
                    </a>
                </li>
                <li style="margin-top: 30px;">
                    <a href="logout.php" style="display: block; padding: 12px 15px; border-radius: 8px; text-decoration: none; color: #e74c3c; font-weight: bold; border: 1px solid #e74c3c; text-align: center; transition: 0.3s;">
                        <i class="fas fa-sign-out-alt"></i> Kijelentkezés
                    </a>
                </li>
            </ul>
        </aside>

        <section style="flex: 3; min-width: 300px;">
            <?php if($siker_uzenet): ?> <p style="color: green; font-weight: bold; padding: 15px; background: #e8f8f5; border-radius: 8px; margin-bottom: 20px;"><i class="fas fa-check-circle"></i> <?php echo $siker_uzenet; ?></p> <?php endif; ?>
            <?php if($hiba_uzenet): ?> <p style="color: red; font-weight: bold; padding: 15px; background: #fdf2e9; border-radius: 8px; margin-bottom: 20px;"><i class="fas fa-exclamation-circle"></i> <?php echo $hiba_uzenet; ?></p> <?php endif; ?>

            <!-- ============================ -->
            <!-- 1. FÜL: PROFIL ADATOK ŰRLAP  -->
            <!-- ============================ -->
            <?php if($active_tab == 'profil'): ?>
                <div style="background: var(--header-bg); border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    
                    <form action="profile.php?tab=profil" method="POST" class="contact-form">
                        
                        <h2 style="color: var(--primary-color); margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">Fiók adatai</h2>
                        
                        <div class="form-row" style="display: flex; gap: 20px; margin-top: 15px;">
                            <div class="form-group" style="flex: 1;">
                                <label>Vezetéknév</label>
                                <input type="text" name="vezeteknev" value="<?php echo htmlspecialchars($user['vezeteknev'] ?? ''); ?>" style="padding: 12px;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>Keresztnév</label>
                                <input type="text" name="keresztnev" value="<?php echo htmlspecialchars($user['keresztnev'] ?? ''); ?>" style="padding: 12px;">
                            </div>
                        </div>

                        <div class="form-row" style="display: flex; gap: 20px;">
                            <div class="form-group" style="flex: 1;">
                                <label>Felhasználónév *</label>
                                <input type="text" name="felhasznalonev" value="<?php echo htmlspecialchars($user['felhasznalonev']); ?>" required style="padding: 12px;">
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label>E-mail cím *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="padding: 12px;">
                            </div>
                        </div>

                        <!-- ÚJ: Cím és elérhetőség -->
                        <h2 style="color: var(--primary-color); margin: 30px 0 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">Alapértelmezett Szállítási Cím</h2>
                        
                        <div class="form-group">
                            <label>Telefonszám</label>
                            <input type="text" name="telefon" value="<?php echo htmlspecialchars($user['telefon'] ?? ''); ?>" placeholder="Pl. +36301234567" style="padding: 12px;">
                        </div>

                        <div class="form-row" style="display: flex; gap: 20px;">
                            <div class="form-group" style="width: 30%;">
                                <label>Irányítószám</label>
                                <input type="text" name="iranyitoszam" value="<?php echo htmlspecialchars($user['iranyitoszam'] ?? ''); ?>" style="padding: 12px;">
                            </div>
                            <div class="form-group" style="width: 70%;">
                                <label>Település</label>
                                <input type="text" name="telepules" value="<?php echo htmlspecialchars($user['telepules'] ?? ''); ?>" style="padding: 12px;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Utca, házszám, emelet, ajtó</label>
                            <input type="text" name="cim" value="<?php echo htmlspecialchars($user['cim'] ?? ''); ?>" style="padding: 12px;">
                        </div>
                        
                        <h3 style="color: var(--primary-color); margin: 30px 0 10px; font-size: 1.1rem;">Jelszó módosítása</h3>
                        <p style="color: #777; font-size: 0.9rem; margin-bottom: 15px;">Csak akkor töltsd ki, ha meg szeretnéd változtatni a jelenlegi jelszavadat!</p>
                        
                        <div class="form-group">
                            <label>Új jelszó</label>
                            <input type="password" name="uj_jelszo" placeholder="Hagyd üresen, ha nem változtatod meg" style="padding: 12px;">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn-primary" style="margin-top: 15px; padding: 12px 25px; width: 100%; font-size: 1.1rem;">
                            <i class="fas fa-save"></i> Adatok mentése
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- ============================ -->
            <!-- 2. FÜL: EDDIGI RENDELÉSEK    -->
            <!-- ============================ -->
            <?php if($active_tab == 'rendelesek'): ?>
                <h2 style="color: var(--primary-color); margin-bottom: 20px;">Eddigi rendeléseid</h2>
                <div class="orders-container">
                    <?php if(count($orders) > 0): ?>
                        <?php foreach($orders as $order): ?>
                            <article class="order-card" style="background: var(--header-bg); border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px;">
                                <div class="order-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
                                    <div>
                                        <div class="order-id" style="font-weight: bold; font-size: 1.1rem; color: var(--primary-color);">Rendelés #<?php echo $order['id']; ?></div>
                                        <div class="order-date" style="color: #777; font-size: 0.9rem;">
                                            <i class="far fa-clock"></i> <?php echo date('Y. m. d. H:i', strtotime($order['rendeles_ideje'])); ?>
                                        </div>
                                    </div>
                                    <?php 
                                        $status_classes = ['Új' => 'status-uj', 'Feldolgozás alatt' => 'status-feldolgozas', 'Postázva' => 'status-postazva', 'Teljesítve' => 'status-teljesitve'];
                                        $status_class = isset($status_classes[$order['statusz']]) ? $status_classes[$order['statusz']] : 'status-uj';
                                    ?>
                                    <div><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($order['statusz']); ?></span></div>
                                </div>
                                <div class="order-items-list">
                                    <ul style="list-style: none; padding: 0; margin: 0 0 15px 0;">
                                        <?php
                                        $itemStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                                        $itemStmt->execute([':order_id' => $order['id']]);
                                        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($items as $item):
                                        ?>
                                            <li style="display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dashed #eee; padding-bottom: 5px;">
                                                <span class="item-name"><?php echo htmlspecialchars($item['termek_nev']); ?></span>
                                                <span class="item-price"><?php echo $item['darab']; ?> db &times; <?php echo number_format($item['egysegar'], 0, ',', ' '); ?> Ft</span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="order-total" style="text-align: right; font-weight: bold; font-size: 1.2rem; color: var(--accent-color);">
                                        Összesen: <?php echo number_format($order['vegosszeg'], 0, ',', ' '); ?> Ft
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-orders-msg" style="text-align: center; padding: 40px; background: var(--header-bg); border-radius: 12px;">
                            <i class="fas fa-box-open" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                            <p style="color: #777;">Még nem adtál le rendelést a webshopban.</p>
                            <a href="shop.php" class="btn-primary" style="margin-top: 15px; display: inline-block;">Körülnézek a boltban</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </section>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
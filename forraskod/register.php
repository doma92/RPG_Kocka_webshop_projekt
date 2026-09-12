<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ha már be van jelentkezve, felesleges regisztrálnia, átirányítjuk a főoldalra
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'api/config/database.php';

$hiba_uzenet = '';
$siker_uzenet = '';

// Ha elküldték az űrlapot
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $felhasznalonev = trim($_POST['username']);
    $email = trim($_POST['email']);
    $jelszo = $_POST['password'];
    $jelszo_ujra = $_POST['password_confirm'];

    // 1. Alapvető ellenőrzés
    if ($jelszo !== $jelszo_ujra) {
        $hiba_uzenet = 'A két jelszó nem egyezik!';
    } else {
        $database = Database::getInstance();
        $db = $database->getConnection();

        // 2. Ellenőrizzük, hogy foglalt-e már a név vagy az e-mail
        $checkQuery = "SELECT id FROM users WHERE email = :email OR felhasznalonev = :felhasznalonev";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([':email' => $email, ':felhasznalonev' => $felhasznalonev]);
        
        if ($checkStmt->rowCount() > 0) {
            $hiba_uzenet = 'Ez az e-mail cím vagy felhasználónév már foglalt!';
        } else {
            // 3. Jelszó titkosítása és mentés az adatbázisba
            $titkositott_jelszo = password_hash($jelszo, PASSWORD_BCRYPT);
            
            // Az is_admin alapértelmezetten 0 lesz (sima vásárló)
            $insertQuery = "INSERT INTO users (felhasznalonev, email, jelszo) VALUES (:felhasznalonev, :email, :jelszo)";
            $insertStmt = $db->prepare($insertQuery);
            
            if ($insertStmt->execute([
                ':felhasznalonev' => $felhasznalonev,
                ':email' => $email,
                ':jelszo' => $titkositott_jelszo
            ])) {
                $siker_uzenet = 'Sikeres regisztráció! Most már bejelentkezhetsz.';
            } else {
                $hiba_uzenet = 'Hiba történt a regisztráció során.';
            }
        }
    }
}

// Fejléc beemelése
include 'header.php'; 
?>

<main class="main-content contact-layout">
    <div class="contact-header">
        <h1>Regisztráció</h1>
        <p>Hozd létre saját fiókodat, hogy később visszanézhesd a rendeléseidet!</p>
    </div>

    <div class="contact-container" style="justify-content: center;">
        <div class="contact-form-box" style="max-width: 500px; flex: none; width: 100%;">
            
            <!-- Visszajelző üzenetek -->
            <?php if($hiba_uzenet): ?>
                <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 20px;">
                    <?php echo $hiba_uzenet; ?>
                </p>
            <?php endif; ?>
            
            <?php if($siker_uzenet): ?>
                <p style="color: green; text-align: center; font-weight: bold; margin-bottom: 20px;">
                    <?php echo $siker_uzenet; ?> <br><br>
                    <a href="login.php" class="btn-primary" style="display: inline-block;">Tovább a bejelentkezéshez</a>
                </p>
            <?php else: ?>
            
                <!-- Regisztrációs űrlap -->
                <form action="register.php" method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="username">Felhasználónév *</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail cím *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Jelszó *</label>
                        <input type="password" id="password" name="password" minlength="6" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirm">Jelszó újra *</label>
                        <input type="password" id="password_confirm" name="password_confirm" minlength="6" required>
                    </div>

                    <button type="submit" class="btn-primary btn-submit">
                        <i class="fas fa-user-plus"></i> Regisztrálok
                    </button>
                    
                    <p style="text-align: center; margin-top: 15px;">
                        Már van fiókod? <a href="login.php" style="color: var(--accent-color); font-weight: bold;">Jelentkezz be!</a>
                    </p>
                </form>

            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'api/config/database.php';

// 1. Alapértelmezett üres változók (Vendég vásárlás esetére)
$vezeteknev = '';
$keresztnev = '';
$email = '';
$telefon = '';
$iranyitoszam = '';
$telepules = '';
$cim = '';

// 2. Ha a felhasználó be van jelentkezve, kiolvassuk az adatait a users táblából
if (isset($_SESSION['user_id'])) {
    $db = Database::getInstance()->getConnection();
    
    $stmtUser = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmtUser->execute([':id' => $_SESSION['user_id']]);
    $userData = $stmtUser->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $vezeteknev = $userData['vezeteknev'] ?? '';
        $keresztnev = $userData['keresztnev'] ?? '';
        $email = $userData['email'] ?? '';
        $telefon = $userData['telefon'] ?? '';
        $iranyitoszam = $userData['iranyitoszam'] ?? '';
        $telepules = $userData['telepules'] ?? '';
        $cim = $userData['cim'] ?? '';
    }
}

// Fejléc beemelése
include 'header.php'; 
?>

<main class="main-content checkout-layout">
    <h1>Pénztár</h1>
    
    <div id="checkoutContent" class="checkout-container">
        
        <!-- Bal oldal: Űrlap az adatoknak -->
        <div class="checkout-form-box">
            <h3>Szállítási és Számlázási adatok</h3>
            
            <form id="checkoutForm" class="checkout-form">
                <div class="form-row">
                    <div class="form-group half">
                        <label for="lastName">Vezetéknév *</label>
                        <input type="text" id="lastName" value="<?php echo htmlspecialchars($vezeteknev); ?>" required>
                    </div>
                    <div class="form-group half">
                        <label for="firstName">Keresztnév *</label>
                        <input type="text" id="firstName" value="<?php echo htmlspecialchars($keresztnev); ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="email">E-mail cím *</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group half">
                        <label for="phone">Telefonszám *</label>
                        <input type="tel" id="phone" value="<?php echo htmlspecialchars($telefon); ?>" placeholder="+36 30 123 4567" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="city">Település *</label>
                    <input type="text" id="city" value="<?php echo htmlspecialchars($telepules); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="zip">Irányítószám *</label>
                        <input type="text" id="zip" value="<?php echo htmlspecialchars($iranyitoszam); ?>" required>
                    </div>
                    <div class="form-group half">
                        <label for="address">Utca, házszám *</label>
                        <input type="text" id="address" value="<?php echo htmlspecialchars($cim); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="note">Megjegyzés a futárnak (opcionális)</label>
                    <textarea id="note" rows="3"></textarea>
                </div>

                <button type="submit" class="btn-primary btn-submit-order">
                    <i class="fas fa-check-circle"></i> Rendelés véglegesítése
                </button>
            </form>
        </div>

        <!-- Jobb oldal: Rendelés összesítése -->
        <div class="checkout-summary-box">
            <h3>Rendelés összesítése</h3>
            
            <!-- A JS ide fogja generálni a megvásárolt tételeket -->
            <div id="checkoutItemsList" class="checkout-items">
                <!-- Tételek helye... -->
            </div>

            <div class="summary-row total">
                <span>Fizetendő:</span>
                <span id="checkoutTotalAmount">0 Ft</span>
            </div>
        </div>

    </div>
</main>

<?php 
// Lábléc beemelése
include 'footer.php'; 
?>

<script src="assets/js/app.js"></script>
</body>
</html>
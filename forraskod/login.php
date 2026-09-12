<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'api/config/database.php';

$hiba_uzenet = '';

// Ha elküldték az űrlapot (POST kérés)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $jelszo = $_POST['password'];

    $database = Database::getInstance();
    $db = $database->getConnection();

    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute([':email' => $email]);
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ellenőrizzük, hogy létezik-e a felhasználó, ÉS egyezik-e a titkosított jelszó
    if ($user && password_verify($jelszo, $user['jelszo'])) {
        // Sikeres belépés: Eltároljuk az adatait a Session-ben
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['felhasznalonev'] = $user['felhasznalonev'];
        $_SESSION['is_admin'] = $user['is_admin'];

        header("Location: index.php");
        exit;
    } else {
        $hiba_uzenet = 'Hibás e-mail cím vagy jelszó!';
    }
}

include 'header.php'; 
?>

<main class="main-content contact-layout">
    <div class="contact-header">
        <h1>Bejelentkezés</h1>
    </div>

    <div class="contact-container" style="justify-content: center;">
        <div class="contact-form-box" style="max-width: 500px; flex: none; width: 100%;">
            <?php if($hiba_uzenet): ?>
                <p style="color: red; text-align: center; font-weight: bold; margin-bottom: 20px;">
                    <?php echo $hiba_uzenet; ?>
                </p>
            <?php endif; ?>

            <form action="login.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="email">E-mail cím</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Jelszó</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary btn-submit">
                    <i class="fas fa-sign-in-alt"></i> Belépés
                </button>
                <p style="text-align: center; margin-top: 15px;">
                    Nincs még fiókod? <a href="register.php" style="color: var(--accent-color); font-weight: bold;">Regisztrálj itt!</a>
                </p>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
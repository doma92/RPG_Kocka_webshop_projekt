<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG kocka - Webshop</title>
    <!-- Külső CSS stíluslap -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- FontAwesome az ikonokhoz (kosár, profil, keresés) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <!-- A fejléc betöltése -->
    <?php include 'header.php'; ?>

<main class="main-content">
    
    <!-- Látványos üdvözlő (Hero) szekció -->
    <section class="hero-section">
        <div class="hero-text">
            <h1>Üdvözlünk az RPG Kocka Webshopban!</h1>
            <p>Készülj fel a következő kalandodra! Legyen szó D&D-ről, Pathfinderről vagy bármilyen más asztali szerepjátékról, nálunk megtalálod a tökéletes kockaszettet, amivel a kritikus sikerek garantáltak (vagy legalábbis nagyon jól fognak kinézni).</p>
            
            <!-- Gomb, ami egyből a boltba visz -->
            <a href="shop.php" class="btn-primary">
                <i class="fas fa-dice-d20"></i> Kockákat akarok!
            </a>
        </div>
        
        <div class="hero-image">
            <img src="assets/img/hero_image.jpg" alt="Gyönyörű RPG Kockák">
        </div>
    </section>

</main>

    <!-- A lábléc behívása -->
    <?php include 'footer.php'; ?>

    <!-- JavaScript a kliens oldali logikához -->
    <script src="assets/js/app.js"></script>
</body>
</html>
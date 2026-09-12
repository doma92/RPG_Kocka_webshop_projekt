<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RPG Kocka - Webshop</title>
    
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body> 
     
    <header class="main-header">
        <div class="header-container">
            
            <!-- Logó és Szöveg -->
            <div class="logo-container">
                <a href="index.php" class="header-logo-link">
                    <img src="assets/img/kockalogo.png" alt="RPG Kocka Webshop Logó" class="header-logo">
                    <span class="header-brand-name">RPG Kocka Webshop</span>
                </a>
            </div>

            <!-- Menü és Kereső EGYBE CSOPORTOSÍTVA (Közép) -->
            <div class="nav-and-search">
                <nav class="main-nav">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menü megnyitása">
                        <i class="fas fa-bars"></i>
                    </button>
                    <ul class="nav-links" id="navLinks">
                        <li><a href="index.php">Főoldal</a></li>
                        <li><a href="about.php">Rólunk</a></li>
                        <li><a href="shop.php">Bolt</a></li>
                        
                       <!-- DINAMIKUS MENÜPONTOK -->
                        <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                            <li><a href="orders.php" style="color: var(--accent-color); font-weight:bold;">Rendelések</a></li>
                            <li><a href="admin_products.php" style="color: var(--accent-color); font-weight:bold;">Termékek</a></li>
                        <?php endif; ?> 

                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="profile.php">Profilom</a></li>
                        <?php endif; ?>
                        
                        <li><a href="contact.php">Kapcsolat</a></li>
                    </ul>
                </nav>

                <form class="search-box" action="shop.php" method="GET">
                    <input type="text" name="q" placeholder="Keresés a kockák között..." aria-label="Keresés">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <!-- Ikonok (Jobb szél) -->
            <div class="header-actions">
                <div class="user-actions">
                    <a href="cart.php" class="icon-btn" aria-label="Kosár">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cartBadge">0</span>
                    </a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Ha be van jelentkezve: Kijelentkezés gomb -->
                        <a href="logout.php" class="icon-btn" aria-label="Kijelentkezés" title="Kijelentkezés">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    <?php else: ?>
                        <!-- Ha nincs bejelentkezve: Belépés gomb -->
                        <a href="login.php" class="icon-btn" aria-label="Bejelentkezés" title="Bejelentkezés">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </header>
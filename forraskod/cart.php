<?php 
// Fejléc beemelése
include 'header.php'; 
?>

<main class="main-content cart-layout">
    <h1>Kosaram</h1>
    
    <div class="cart-container">
        
        <!-- Bal oldal: A kosárba tett termékek listája -->
        <!-- Ezt a tartályt fogja a JavaScript feltölteni a LocalStorage adatai alapján -->
        <div class="cart-items" id="cartItemsContainer">
            <p class="loading-msg">Kosár betöltése...</p>
        </div>

        <!-- Jobb oldal: Összesítés és Pénztár gomb -->
        <!-- Alapból rejtve van (display: none), és csak akkor jelenik meg, ha van valami a kosárban -->
        <div class="cart-summary" id="cartSummary" style="display: none;">
            <h3>Összesítés</h3>
            <div class="summary-row">
                <span>Részösszeg:</span>
                <span id="cartSubtotal">0 Ft</span>
            </div>
            <div class="summary-row total">
                <span>Fizetendő:</span>
                <span id="cartTotal">0 Ft</span>
            </div>
            
            <!-- Ez majd a rendelés leadása oldalra fog vinni -->
            <a href="checkout.php" class="btn-primary btn-checkout">Tovább a pénztárhoz</a>
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
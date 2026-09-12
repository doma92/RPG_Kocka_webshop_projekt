<?php 
// 1. A fejléc beemelése (HTML fej, CSS linkek és navigáció)
include 'header.php'; 
?>

<main class="main-content shop-layout">
    
    <!-- Oldalsáv a szűrőknek -->
    <aside class="shop-sidebar">
        <h3>Szűrők</h3>
         
        <div class="filter-group"> <!-- A meglévő kódodban lévő div -->
            <h4>Anyag / Típus</h4>
            <label><input type="checkbox" data-category="tipus" value="Akril"> Akril</label>
            <label><input type="checkbox" data-category="tipus" value="Fa"> Fa</label>
            <label><input type="checkbox" data-category="tipus" value="Fém"> Fém</label>
            <label><input type="checkbox" data-category="tipus" value="Borostyán"> Borostyán</label>
        </div>
    
        <div class="filter-group">
            <h4>Szín</h4>
            <!-- Gomb, ami megnyitja a popupot -->
            <button type="button" id="btnOpenSzinPopup" class="btn-primary" style="padding: 8px; font-size: 0.9rem; margin-bottom: 10px;">
                <i class="fas fa-palette"></i> Szín választó
            </button>

            <!-- A data-category jelzi a JS-nek, hogy ez a színekre szűr -->
           <label><input type="checkbox" data-category="szin" value="Piros"> Piros</label>
            <label><input type="checkbox" data-category="szin" value="Kék"> Kék</label>
            <label><input type="checkbox" data-category="szin" value="Zöld"> Zöld</label>
            <label><input type="checkbox" data-category="szin" value="Fekete"> Fekete</label>
            <label><input type="checkbox" data-category="szin" value="Lila"> Lila</label>
            <label><input type="checkbox" data-category="szin" value="Fehér"> Fehér</label>
            <label><input type="checkbox" data-category="szin" value="Citromsárga"> Citromsárga</label>
            <label><input type="checkbox" data-category="szin" value="Narancs"> Narancs</label>
            <label><input type="checkbox" data-category="szin" value="Rózsaszín"> Rózsaszín</label>
            <label><input type="checkbox" data-category="szin" value="Arany"> Arany</label>
        </div>
    </aside>

    <!-- 3. Termékek rácsos (Grid) elrendezése -->
    <section class="shop-products">
        <h2>Összes kocka</h2>
        
        <!-- Ez az a konténer, amit a JavaScript aszinkron módon feltölt az API-ból -->
        <div class="product-grid" id="productGrid">
            <p class="loading-msg">Kockák megidézése folyamatban...</p>
        </div>
    </section>

    <!-- ÚJ: SZÍNVÁLASZTÓ POPUP -->
    <div id="szinPopup" class="popup">
        <div class="popup_tartalom">
            <span id="popupZar" class="zar">&times;</span>
            <h3 style="text-align: center; margin-bottom: 20px; color: var(--primary-color);">Válassz színt</h3>
            <div class="szinKor_wrap">
                <div id="szinKorPopup" class="szinKor"></div>
                <img src="assets/img/korkockalogo.png" class="szinKor_logo" id="szinKorLogoPopup" alt="logo">
            </div>
        </div>
    </div>

</main>

<?php 
// 4. A lábléc beemelése
include 'footer.php'; 
?>

<!-- 5. JavaScript betöltése a fájl legvégén, miután a HTML már felépült -->
<script src="assets/js/app.js"></script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php'; 
?>

<main class="main-content">
    <!-- Egy szép, középre igazított, dobozos elrendezés az olvasáshoz -->
    <div style="max-width: 800px; margin: 40px auto; padding: 40px; background: var(--header-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <h1 style="color: var(--primary-color); margin-bottom: 10px;">Általános Szerződési Feltételek (ÁSZF)</h1>
        <p style="color: #7f8c8d; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
            Utolsó frissítés: <?php echo date('Y. F d.'); ?>
        </p>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.3rem;">1. A Szolgáltató adatai</h2>
            <p><strong>Cégnév:</strong> RPG Kocka Webshop Kft.</p>
            <p><strong>Székhely:</strong> 2500 Esztergom, Kocka utca 1.</p>
            <p><strong>Adószám:</strong> 12345678-2-11</p>
            <p><strong>E-mail:</strong> info@rpgkocka.hu</p>
        </section>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.3rem;">2. Megrendelés menete</h2>
            <p style="line-height: 1.6; color: #444;">A megrendelések leadása a webshopon keresztül elektronikus úton lehetséges, regisztrált vásárlóként vagy vendégként. A megrendelés leadásával a vásárló kijelenti, hogy elfogadja a jelen ÁSZF-ben foglaltakat.</p>
        </section>

        <section style="margin-bottom: 30px;">
            <h2 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.3rem;">3. Elállási jog</h2>
            <p style="line-height: 1.6; color: #444;">A fogyasztót a 45/2014. (II. 26.) Korm. rendelet alapján 14 napos indokolás nélküli elállási jog illeti meg a termék átvételétől számítva. A visszaküldés költsége a vásárlót terheli.</p>
        </section>
        
        <section>
            <h2 style="color: var(--primary-color); margin-bottom: 15px; font-size: 1.3rem;">4. Panaszkezelés</h2>
            <p style="line-height: 1.6; color: #444;">Esetleges panaszával forduljon hozzánk bizalommal az <strong>info@rpgkocka.hu</strong> e-mail címen. Minden panaszt 30 napon belül kivizsgálunk és írásban megválaszolunk.</p>
        </section>

    </div>
</main>  

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html> 
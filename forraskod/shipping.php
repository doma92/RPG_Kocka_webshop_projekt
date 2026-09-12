<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php'; 
?>

<main class="main-content">
    <div style="max-width: 800px; margin: 40px auto; padding: 40px; background: var(--header-bg); border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
        
        <h1 style="color: var(--primary-color); margin-bottom: 30px; text-align: center;">Rendelés és Szállítás</h1>

        <div style="display: flex; gap: 30px; flex-wrap: wrap; margin-bottom: 40px;">
            
            <!-- Szállítási módok doboz -->
            <div style="flex: 1; min-width: 300px; background: var(--bg-color); padding: 25px; border-radius: 8px;">
                <h3 style="color: var(--primary-color); margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                    <i class="fas fa-truck"></i> Szállítási módok
                </h3>
                
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 15px;">
                        <strong><i class="fas fa-box" style="color: var(--accent-color);"></i> Házhozszállítás (GLS)</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">1-2 munkanap • 1 990 Ft</span>
                    </li>
                    <li style="margin-bottom: 15px;">
                        <strong><i class="fas fa-cube" style="color: var(--accent-color);"></i> Foxpost Csomagautomata</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">2-3 munkanap • 1 190 Ft</span>
                    </li>
                    <li>
                        <strong><i class="fas fa-store" style="color: var(--accent-color);"></i> Személyes átvétel</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">Esztergom, Kocka utca 1. • Ingyenes!</span>
                    </li>
                </ul>
            </div>

            <!-- Fizetési módok doboz -->
            <div style="flex: 1; min-width: 300px; background: var(--bg-color); padding: 25px; border-radius: 8px;">
                <h3 style="color: var(--primary-color); margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                    <i class="fas fa-credit-card"></i> Fizetési lehetőségek
                </h3>
                
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 15px;">
                        <strong><i class="fab fa-cc-visa" style="color: #2980b9;"></i> Bankkártyás fizetés (Stripe)</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">Azonnali, biztonságos online fizetés.</span>
                    </li>
                    <li style="margin-bottom: 15px;">
                        <strong><i class="fas fa-money-bill-wave" style="color: #27ae60;"></i> Utánvét</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">Fizetés a futárnál készpénzzel vagy kártyával (+390 Ft kezelési költség).</span>
                    </li>
                    <li>
                        <strong><i class="fas fa-university" style="color: #8e44ad;"></i> Előre utalás</strong><br>
                        <span style="color: #666; font-size: 0.9rem;">A csomagot az összeg beérkezése után indítjuk.</span>
                    </li>
                </ul>
            </div>

        </div>

        <div style="text-align: center; padding: 20px; background: #e8f8f5; border-radius: 8px;">
            <h4 style="color: #27ae60; margin-bottom: 10px;">Ingyenes szállítás!</h4>
            <p style="margin: 0; color: #444;">Minden <strong>20 000 Ft</strong> feletti rendelés esetén a házhozszállítás és a Foxpost is teljesen ingyenes!</p>
        </div>

    </div>
</main>

<?php include 'footer.php'; ?>
<script src="assets/js/app.js"></script>
</body>
</html>
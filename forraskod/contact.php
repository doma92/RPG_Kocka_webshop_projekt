<?php 
// Fejléc beemelése
include 'header.php'; 
?>

<main class="main-content contact-layout">
    <div class="contact-header">
        <h1>Lépj velünk kapcsolatba!</h1>
        <p>Kérdésed van egy kockaszettel kapcsolatban? Elakadt a rendelésed? Vagy csak mesélnél a legutóbbi kritikus sikeredről? Írj nekünk bátran!</p>
    </div>

    <div class="contact-container">
        
        <!-- Bal oldal: Kapcsolati információk -->
        <div class="contact-info-box">
            <h3>Elérhetőségeink</h3>
            
            <ul class="contact-details">
                <li>
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>Címünk:</strong><br>
                        2500 Esztergom, Kalandorok útja 20.<br>
                        <em>(Személyes átvétel előzetes egyeztetés alapján)</em>
                    </div>
                </li>
                <li>
                    <i class="fas fa-envelope"></i>
                    <div>
                        <strong>E-mail:</strong><br>
                        <a href="mailto:info@rpgkocka.hu">info@rpgkocka.hu</a>
                    </div>
                </li>
                <li>
                    <i class="fas fa-phone-alt"></i>
                    <div>
                        <strong>Telefon:</strong><br>
                        <a href="tel:+36301234567">+36 30 123 4567</a><br>
                        <em>(Hétköznap 10:00 - 18:00)</em>
                    </div>
                </li>
            </ul>

            <div class="contact-socials">
                <h4>Kövess minket!</h4>
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
                </div>
            </div>
        </div>

        <!-- Jobb oldal: Üzenetküldő űrlap -->
        <div class="contact-form-box">
            <h3>Írj üzenetet</h3>
            <form action="#" method="POST" class="contact-form">
                
                <div class="form-group">
                    <label for="name">Neved</label>
                    <input type="text" id="name" name="name" placeholder="Pl. Hősies Harcos" required>
                </div>

                <div class="form-group">
                    <label for="email">E-mail címed</label>
                    <input type="email" id="email" name="email" placeholder="kalandor@email.hu" required>
                </div>

                <div class="form-group">
                    <label for="subject">Tárgy</label>
                    <select id="subject" name="subject" required>
                        <option value="">Válassz egy témát...</option>
                        <option value="rendeles">Rendeléssel kapcsolatos kérdés</option>
                        <option value="termek">Termék információ</option>
                        <option value="egyeb">Egyéb / Csak beköszönök</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="message">Üzenet</label>
                    <textarea id="message" name="message" rows="5" placeholder="Miben segíthetünk?" required></textarea>
                </div>

                <!-- type="button" helyett type="submit", hogy később PHP-val feldolgozható legyen -->
                <button type="submit" class="btn-primary btn-submit">
                    <i class="fas fa-paper-plane"></i> Üzenet küldése
                </button>
            </form>
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
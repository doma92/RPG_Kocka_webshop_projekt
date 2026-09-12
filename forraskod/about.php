<?php 
// A fejléc beemelése
include 'header.php'; 
?>

<main class="main-content about-layout">
    
    <section class="about-intro">
        <h1>Kik vagyunk mi?</h1>
        <p class="lead-text">A kockák csörgése az asztalon, egy váratlan fordulat a kampányban, és az együtt töltött idő a barátokkal – ez az, ami minket hajt.</p>
        <p>A webshopunkat nem csak egy üzletnek álmodtuk meg, hanem egy olyan helynek, amit mi magunk is szívesen használnánk. Imádjuk a társasjátékokat, a D&D estéket és azt a varázslatot, amit egy-egy jól felépített asztali kaland tud nyújtani. Tudjuk, hogy egy jó kockaszett nem csak egy műanyag darab, hanem a karaktered lelke, a szerencséd záloga és az asztal dísze. Ezért válogatjuk össze a legjobb, legszebb és legkülönlegesebb kiegészítőket, hogy a ti kalandjaitok is felejthetetlenek legyenek.</p>
    </section>

    <!-- Üzemeltetők (Alapítók) csempéi -->
    <section class="about-founders">
        <h2>Az oldal üzemeltetői</h2>
        
        <div class="founders-grid">
            
            <!-- 1. Üzemeltető kártyája -->
            <article class="founder-card">
                <div class="founder-image">
                    <img src="assets/img/arc1.jpg" alt="1. Üzemeltető">
                </div>
                <div class="founder-info">
                    <h3>Teszt Elek</h3>
                    <span class="founder-role">Alapító & Fejlesztő</span>
                    <p>Nagy társasjáték rajongó vagyok, Óvodás korom óra játszom. A kedvenc társasjátékom a monopoly? Azért szeretek játszani mert kikapcsol.</p>
                    <a href="mailto:email@rpgkocka.hu" class="founder-contact">
                        <i class="fas fa-envelope"></i> tesztelek@rpgkocka.hu
                    </a>
                </div>
            </article>

            <!-- 2. Üzemeltető kártyája -->
            <article class="founder-card">
                <div class="founder-image">
                    <img src="assets/img/arc2.jpg" alt="2. Üzemeltető">
                </div>
                <div class="founder-info">
                    <h3>John Doe</h3>
                    <span class="founder-role">Társalapító & Dizájner</span>
                    <p>A társasjátékozás lett az életem. gyűjtöm a ritka és érdekes dobókockákat. Célom, hogy minél több emberrel megkedveltessem a társasjátékozást. </p>
                    <a href="mailto:masikemail@rpgkocka.hu" class="founder-contact">
                        <i class="fas fa-envelope"></i> johndoe@rpgkocka.hu
                    </a>
                </div>
            </article>

        </div>
    </section>

</main>

<?php 
// A lábléc beemelése
include 'footer.php'; 
?>
<script src="assets/js/app.js"></script>
</body>
</html>
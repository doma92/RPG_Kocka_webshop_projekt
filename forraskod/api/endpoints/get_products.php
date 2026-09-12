<?php
// api/endpoints/get_products.php

// 1. HTTP fejlécek (Headers) beállítása a REST API-hoz
header("Access-Control-Allow-Origin: *"); // Engedélyezi a hozzáférést más domainekről is (CORS)
header("Content-Type: application/json; charset=UTF-8"); // Jelzi, hogy JSON a válasz
header("Access-Control-Allow-Methods: GET"); // Csak GET kéréseket fogadunk el itt

// 2. Szükséges osztályok behúzása
require_once '../config/database.php';
require_once '../models/Product.php';

// 3. Adatbázis kapcsolat lekérése a Singleton osztálytól
$database = Database::getInstance();
$db = $database->getConnection();

// 4. Termék (Product) modell példányosítása az adatbázis kapcsolattal
$product = new Product($db);

// 5. Az összes termék lekérdezése 
$stmt = $product->readAll();
$num = $stmt->rowCount();

// 6. Ellenőrizzük, hogy van-e legalább egy termék az adatbázisban
if ($num > 0) {
    
    // A PDO fetchAll() metódusa egyből egy tiszta, asszociatív tömböt csinál a lekérdezésből
    $products_arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ÚJ LOGIKA: Minden termékhez lekérjük az extra galéria képeket is!
    foreach ($products_arr as &$p) {
        $imgStmt = $db->prepare("SELECT kep_url FROM product_images WHERE product_id = :id");
        $imgStmt->execute([':id' => $p['id']]);
        // A FETCH_COLUMN csak a fájl útvonalakat adja vissza egy egyszerű tömbben (pl. ["kep1.jpg", "kep2.jpg"])
        $p['galeria'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN); 
    }

    // HTTP 200 OK státuszkód beállítása
    http_response_code(200);

    // Adatok konvertálása JSON formátumba és kiírása
    // A JSON_UNESCAPED_UNICODE gondoskodik a magyar ékezetek helyes megjelenéséről
    // A JSON_PRETTY_PRINT pedig szépen formázza (olvashatóbbá teszi) a kimenetet
    echo json_encode($products_arr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} else {
    
    // Ha üres a tábla, HTTP 404 Not Found státuszkódot küldünk
    http_response_code(404);

    // Hibaüzenet JSON formátumban
    echo json_encode(["message" => "Jelenleg nincsenek elérhető termékek a boltban."], JSON_UNESCAPED_UNICODE);
}
?>
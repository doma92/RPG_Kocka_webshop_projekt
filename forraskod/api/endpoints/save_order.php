<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['cart'])) {
    http_response_code(400);
    echo json_encode(["message" => "Hiányzó adatok vagy üres kosár."]);
    exit;
}

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    $db->beginTransaction();
    
    $vegosszeg = 0;
    foreach($data['cart'] as $item) {
        $vegosszeg += ($item['price'] * $item['quantity']);
    }

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    
    $query = "INSERT INTO orders (user_id, vezeteknev, keresztnev, email, telefon, telepules, iranyitoszam, cim, megjegyzes, vegosszeg) 
              VALUES (:user_id, :vezeteknev, :keresztnev, :email, :telefon, :telepules, :iranyitoszam, :cim, :megjegyzes, :vegosszeg)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':user_id'     => $user_id,
        ':vezeteknev'  => $data['lastName'],
        ':keresztnev'  => $data['firstName'],
        ':email'       => $data['email'],
        ':telefon'     => $data['phone'],
        ':telepules'   => $data['city'],
        ':iranyitoszam'=> $data['zip'],
        ':cim'         => $data['address'],
        ':megjegyzes'  => $data['note'],
        ':vegosszeg'   => $vegosszeg
    ]);
    
    $orderId = $db->lastInsertId();
    
    //  Kérés a rendelési tételekhez
    $itemQuery = "INSERT INTO order_items (order_id, termek_nev, egysegar, darab) VALUES (:order_id, :termek_nev, :egysegar, :darab)";
    $itemStmt = $db->prepare($itemQuery);
    
    //  ÚJ: Kérés a készlet csökkentéséhez
    $stockQuery = "UPDATE products SET keszlet = keszlet - :darab WHERE id = :id";
    $stockStmt = $db->prepare($stockQuery);
    
    foreach($data['cart'] as $item) {
        // Tétel mentése
        $itemStmt->execute([
            ':order_id'   => $orderId,
            ':termek_nev' => $item['name'],
            ':egysegar'   => $item['price'],
            ':darab'      => $item['quantity']
        ]);
        
        // Készlet csökkentése (az id alapján vonja le a darabszámot)
        $stockStmt->execute([
            ':darab' => $item['quantity'],
            ':id'    => $item['id']
        ]);
    }
    
    $db->commit();
    http_response_code(201);
    echo json_encode(["message" => "Rendelés sikeresen mentve, készlet frissítve"]);
    
} catch (PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["message" => "Adatbázis hiba történt: " . $e->getMessage()]);
}


//

$db->beginTransaction();
try {
    //  Rendelés alapadatainak beszúrása az 'orders' táblába
    $stmt = $db->prepare("INSERT INTO orders (vezeteknev, email, vegosszeg) VALUES (:vnev, :email, :osszeg)");
    $stmt->execute([':vnev' => $data->lastName, ':email' => $data->email, ':osszeg' => $vegosszeg]);
    $order_id = $db->lastInsertId(); // Lekérjük a friss rendelés azonosítóját

    //  A kosár tételeinek végigiterálása és beszúrása az 'order_items' táblába
    $itemStmt = $db->prepare("INSERT INTO order_items (order_id, termek_id, darab, egysegar) VALUES (:oid, :tid, :db, :ar)");
    
    //  Készlet csökkentése a 'products' táblában
    $stockStmt = $db->prepare("UPDATE products SET keszlet = keszlet - :db WHERE id = :tid");

    foreach($data->cart as $item) {
        $itemStmt->execute([':oid' => $order_id, ':tid' => $item->id, ':db' => $item->quantity, ':ar' => $item->price]);
        $stockStmt->execute([':db' => $item->quantity, ':tid' => $item->id]);
    }

    // Ha idáig minden SQL parancs hiba nélkül lefutott, véglegesítjük az adatbázisban
    $db->commit();
    echo json_encode(["status" => "success"]);

} catch (Exception $e) {
    // Ha bármi hiba történik (pl. nincs elég készlet), visszavonjuk az ÖSSZES fenti SQL műveletet!
    $db->rollBack();
    http_response_code(500);
    echo json_encode(["error" => "Hiba a rendelés feldolgozásakor."]);
}

//


?>
<?php
// api/models/Product.php

class Product {
    // Adatbázis kapcsolat
    private $conn;
    private $table_name = "products"; // A tábla neve, így könnyen módosítható

    // Termék tulajdonságai (a tábla oszlopainak megfelelően)
    public $id;
    public $nev;
    public $leiras;
    public $tipus;
    public $szin;
    public $ar;
    public $keszlet_db;
    public $kep_url;

    /**
     * Konstruktor: A modell létrehozásakor megkapja az adatbázis kapcsolatot
     * (Dependency Injection)
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Az összes termék lekérdezése
     * 
     * @return PDOStatement A végrehajtott PDO lekérdezés eredménye
     */
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";

        // A lekérdezés előkészítése (Prepared Statement a biztonságért)
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
    

    /**
     * Egyetlen termék lekérdezése ID alapján (például egy termék adatlapjához)
     */
    public function readOne() {
        $query = "SELECT 
                    id, nev, leiras, tipus, szin, ar, keszlet_db, kep_url 
                  FROM 
                    " . $this->table_name . " 
                  WHERE 
                    id = ? 
                  LIMIT 0,1";

        $stmt = $this->conn->prepare($query);

        // Biztonsági szűrés: az ID biztosan szám legyen
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        // A paraméter bekötése a kérdőjel helyére
        $stmt->bindParam(1, $this->id);

        $stmt->execute();

        return $stmt;
    }
}
?>
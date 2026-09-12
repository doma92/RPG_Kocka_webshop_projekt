<?php
// api/config/database.php

class Database {
    // Adatbázis konfigurációs adatok
    private $host = "localhost";
    private $db_name = "rpg_webshop";
    private $username = "root";       // WAMP alapértelmezett felhasználó
    private $password = "";           // WAMP alapértelmezett jelszó (általában üres)
    private $charset = "utf8mb4";     // Ékezetes karakterek és emojik támogatása
    
    // A PDO példányt tároló statikus változó (Singleton minta)
    private static $instance = null;
    
    // A konkrét kapcsolat objektum
    private $conn;

    // A konstruktor privát, hogy ne lehessen kívülről a 'new' kulcsszóval példányosítani
    private function __construct() {
        $this->connect();
    }

    // A kapcsolat felépítése
    private function connect() {
        $this->conn = null;

        // DSN (Data Source Name) összeállítása
        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=" . $this->charset;
        
        // PDO beállítások tömbje a tiszta működésért és a megfelelő hibakezelésért
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hibák dobása Exception-ként
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Alapértelmezett: asszociatív tömbként adja vissza a rekordokat
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Valódi prepared statements használata
        ];

        try {
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $e) {
            // Éles környezetben (production) ne írjuk ki a konkrét hibaüzenetet a képernyőre!
            // Itt most fejlesztői környezetben vagyunk (WAMP), ezért kiírjuk a hibát.
            die("Adatbázis kapcsolódási hiba: " . $e->getMessage());
        }
    }

    /**
     * A Singleton példány lekérése.
     * Ha még nincs kapcsolat, létrehozza; ha van, visszaadja a meglévőt.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * A tényleges PDO kapcsolat (PDO objektum) visszaadása lekérdezésekhez
     */
    public function getConnection() {
        return $this->conn;
    }
}
?>
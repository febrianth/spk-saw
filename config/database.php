<?php
class Database {
    private static $host = "localhost";
    private static $db_name = "spk_saw"; 
    private static $username = "febrianth";
    private static $password = "febrianth123";
    private static $pdo;

    public static function getConnection() {
        if (self::$pdo == null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );

                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>

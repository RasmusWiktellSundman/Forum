<?php
namespace App\Lib;

use PDO;
use PDOException;

class Database {
    private static PDO $conn;

    /**
     * Returnerar en redan upprättad anslutning till databasen. Om ingen finns upprättas en anslutning.
     *
     * @return PDO
     */
    public static function getConnection(): PDO
    {
        // Kolla om en anslutning redan är gjord, innan en ny upprättas
        if(isset(self::$conn)) {
            return self::$conn;
        }

        try {
            $conn = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            
            // Sätter error läget till exception, så en exception skapas ifall något går fel
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Sätter default till PDO::FETCH_ASSOC vilken returnerar en associative array, standard är PDO::FETCH_BOTH
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        self::$conn = $conn;
        return self::$conn;
    }

    public static function hasConnection(): bool
    {
        return isset(self::$conn);
    }

    public static function closeConnection()
    {
        if(!isset(self::$conn)) return;
        // Stänger anslutningen (med pdo stängs anslutningen genom att sätta den till null, det finns ingen metod som ska kallas på)
        $conn = null;
    }
}
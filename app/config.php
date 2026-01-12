<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DB
{
    // CAMBIOS REALIZADOS PARA DOCKER:
    private static $host = "db";        // Antes "localhost", ahora "db" (nombre del servicio en docker-compose)
    private static $db = "aruma_db";    // Asegúrate de que coincida con MYSQL_DATABASE en docker-compose
    private static $user = "root";      
    private static $pass = "root";      // Antes vacío, ahora "root" como definimos en el docker-compose
    private static $charset = "utf8mb4";

    public static function get()
    {
        $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, self::$user, self::$pass, $options);
    }
}

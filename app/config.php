<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class DB
{
    private static $host = "localhost";
    private static $db = "aruma_spa";
    private static $user = "root";       // ← tu usuario real
    private static $pass = ""; // ← tu contraseña real
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

<?php
// app/config.php
declare(strict_types=1);

class DB {
    private static ?PDO $instance = null;

    public static function get(): PDO {
        if (self::$instance === null) {
            $host = '127.0.0.1';
            $db   = 'aruma_spa';
            $user = 'tu_usuario';
            $pass = 'tu_contraseña';
            $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$instance = new PDO($dsn, $user, $pass, $options);
        }
        return self::$instance;
    }
}


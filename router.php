<?php
// router.php para PHP built-in server

// Lista de extensiones que deben servirse directamente
$static_extensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'ttf', 'woff', 'woff2'];

// Obtener la ruta solicitada
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$extension = pathinfo($path, PATHINFO_EXTENSION);

// Si es un archivo estático y existe, servirlo directamente
if (in_array($extension, $static_extensions)) {
    $file = __DIR__ . $path;
    
    // Verificar que el archivo exista y esté dentro del directorio del proyecto
    if (file_exists($file) && strpos(realpath($file), realpath(__DIR__)) === 0) {
        // Determinar el tipo MIME correcto
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'ttf' => 'font/ttf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2'
        ];
        
        if (isset($mime_types[$extension])) {
            header('Content-Type: ' . $mime_types[$extension]);
        }
        
        readfile($file);
        exit;
    }
}

// Para todo lo demás, usar index.php como router
include __DIR__ . '/index.php';

<?php

// Mostrar todos los errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar variables de entorno desde el archivo .env
$envFile = __DIR__ . '/.env';

// Verificar si existe el archivo .env o si estamos en Vercel
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];
    
    foreach ($lines as $line) {
    // Ignorar comentarios
    if (strpos(trim($line), '#') === 0) {
        continue;
    }
    
    // Parsear variables de entorno
    list($name, $value) = explode('=', $line, 2);
    $env[trim($name)] = trim($value);
    }
} elseif (isset($_ENV['DB_HOST'])) {
    // Si estamos en Vercel, usar las variables de entorno de Vercel
    echo "<div style='background-color: #e8f5e9; padding: 10px; border: 1px solid #c8e6c9; margin: 15px 0;'>";
    echo "<p><strong>Información:</strong> Usando variables de entorno de Render.</p>";
    echo "</div>";
    
    // Asignar variables de entorno de Vercel a nuestro array $env
    $env = $_ENV;
} else {
    // Si no existe el archivo .env y no estamos en Vercel, mostrar error
    echo "<div style='background-color: #ffebee; padding: 10px; border: 1px solid #ffcdd2; margin: 15px 0;'>";
    echo "<p><strong>Error:</strong> No se encontró el archivo .env en la ruta {$envFile} y no se detectó entorno Vercel.</p>";
    echo "<p>Por favor, cree el archivo .env con la configuración necesaria.</p>";
    echo "</div>";
    
    // Asignar valores por defecto
    $env = [
        'DB_HOST' => 'localhost',
        'DB_PORT' => '5432',
        'DB_NAME' => 'library',
        'DB_USER' => 'postgres',
        'DB_PASSWORD' => '1'
    ];
}

// Configuración de la base de datos
$host = $env['DB_HOST'] ?? 'localhost';
$port = $env['DB_PORT'] ?? '5432';
$dbname = $env['DB_NAME'] ?? 'library';
$user = $env['DB_USER'] ?? 'postgres';
$password = $env['DB_PASSWORD'] ?? '1';

// Información de la conexión
echo "<h1>Verificación de conexión PHP-PostgreSQL</h1>";
echo "<h2>Información de conexión:</h2>";
echo "<ul>";
echo "<li>Host: {$host}</li>";
echo "<li>Puerto: {$port}</li>";
echo "<li>Base de datos: {$dbname}</li>";
echo "<li>Usuario: {$user}</li>";
echo "<li>Contraseña: ******</li>";
echo "</ul>";

// Verificar extensión PDO PostgreSQL
echo "<h2>Verificación de extensiones:</h2>";
echo "<ul>";
echo "<li>PDO instalado: " . (extension_loaded('pdo') ? 'Sí' : 'No') . "</li>";
echo "<li>PDO PostgreSQL instalado: " . (extension_loaded('pdo_pgsql') ? 'Sí' : 'No') . "</li>";
echo "</ul>";

// Mostrar los drivers PDO disponibles
$pdoDrivers = PDO::getAvailableDrivers();
echo "<h3>Drivers PDO disponibles:</h3>";
echo "<ul>";
foreach ($pdoDrivers as $driver) {
    echo "<li>{$driver}" . ($driver === 'pgsql' ? ' <strong>(PostgreSQL)</strong>' : '') . "</li>";
}
echo "</ul>";

echo "<div style='background-color: #e8f5e9; padding: 10px; border: 1px solid #c8e6c9; margin: 15px 0;'>";
echo "<p><strong>Nota:</strong> Este script está configurado para usar específicamente el driver PDO-PostgreSQL ('pgsql').</p>";
echo "<p>Configuración especial para Neon.tech aplicada automáticamente.</p>";
echo "</div>";

// Intentar conexión
echo "<h2>Intento de conexión:</h2>";
try {
    // DSN para PostgreSQL
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";

    // Configuración especial para Neon.tech
    if (strpos($host, 'neon.tech') !== false) {
        echo "<div style='background-color: #fff3cd; padding: 10px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
        echo "<p><strong>Detectado host de Neon.tech:</strong> Aplicando configuración SSL y endpoint.</p>";
        echo "</div>";
        
        // Forzar SSL
        $dsn .= ";sslmode=require";
        
        // Extraer el endpoint ID del host
        $endpoint_id = explode('.', $host)[0];
        echo "<p>Endpoint ID detectado: <strong>{$endpoint_id}</strong></p>";
        
        // CORRECCIÓN: Formato correcto para el parámetro endpoint
        //$dsn .= ";options='-c endpoint={$endpoint_id}'";
        $dsn .= ";options=endpoint={$endpoint_id}";
        
        echo "<p>DSN final: <code>{$dsn}</code></p>";
    }

    // Crear la conexión PDO
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 30, // Timeout de 30 segundos
        PDO::ATTR_PERSISTENT => false // No usar conexiones persistentes para Neon
    ]);
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background-color: #d4edda; border: 1px solid #c3e6cb;'>¡Conexión exitosa a PostgreSQL!</div>";
    
    // Mostrar información del servidor PostgreSQL
    $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    $clientVersion = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);
    $driverName = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    
    echo "<h3>Información del servidor PostgreSQL:</h3>";
    echo "<ul>";
    echo "<li>Driver PDO utilizado: {$driverName}</li>";
    echo "<li>Versión del servidor: {$serverVersion}</li>";
    echo "<li>Versión del cliente: {$clientVersion}</li>";
    echo "</ul>";
    
    // Verificar tablas existentes
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tablas disponibles:</h3>";
    echo "<ul>";
    if (count($tables) > 0) {
        foreach ($tables as $table) {
            echo "<li>{$table}</li>";
        }
    } else {
        echo "<li>No se encontraron tablas en el esquema 'public'</li>";
    }
    echo "</ul>";
    
    // Verificar si existe la tabla book
    if (in_array('book', $tables)) {
        echo "<h3>Demostración de uso de PDO-PostgreSQL:</h3>";
        echo "<div style='background-color: #f0f0f0; padding: 10px; border: 1px solid #ccc; margin-bottom: 15px;'>";
        echo "<p>Ejecutando consulta SQL con PDO-PostgreSQL:</p>";
        echo "<code>SELECT COUNT(*) FROM book</code>";
        echo "</div>";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM book");
        $count = $stmt->fetchColumn()-1;
        echo "<div>La tabla 'book' contiene {$count} registros.</div>";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT * FROM book LIMIT 5");
            $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<h3>Muestra de book:</h3>";
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr style='background-color: #f8f9fa;'>";
            foreach (array_keys($books[0]) as $column) {
                echo "<th style='padding: 8px; border: 1px solid #ddd;'>{$column}</th>";
            }
            echo "</tr>";
            
            foreach ($books as $book) {
                echo "<tr>";
                foreach ($book as $value) {
                    echo "<td style='padding: 8px; border: 1px solid #ddd;'>{$value}</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        // Crear tabla de prueba si no existe
        echo "<h3>Creando tabla de prueba:</h3>";
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (
                id SERIAL PRIMARY KEY,
                message VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            $pdo->exec("INSERT INTO test_connection (message) VALUES ('Conexión PHP-PostgreSQL exitosa con Neon.tech!')");
            
            $stmt = $pdo->query("SELECT * FROM test_connection ORDER BY created_at DESC LIMIT 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<div style='background-color: #d1ecf1; padding: 10px; border: 1px solid #bee5eb;'>";
            echo "<p><strong>Tabla de prueba creada exitosamente:</strong></p>";
            echo "<p>ID: {$result['id']}</p>";
            echo "<p>Mensaje: {$result['message']}</p>";
            echo "<p>Fecha: {$result['created_at']}</p>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div style='color: orange;'>Advertencia al crear tabla de prueba: " . $e->getMessage() . "</div>";
        }
    }
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb;'>";
    echo "Error de conexión: " . $e->getMessage();
    echo "</div>";
    
    echo "<h3>Posibles soluciones:</h3>";
    echo "<ul>";
    echo "<li>Verificar que la extensión pdo_pgsql esté instalada</li>";
    echo "<li>Comprobar que los datos de conexión sean correctos</li>";
    echo "<li>Verificar conectividad de red al servidor Neon.tech</li>";
    echo "<li>Revisar que el SSL esté configurado correctamente</li>";
    echo "</ul>";
    
    // Información adicional de debug
    echo "<h3>Información de debug:</h3>";
    echo "<p><strong>Host:</strong> {$host}</p>";
    echo "<p><strong>Es Neon.tech:</strong> " . (strpos($host, 'neon.tech') !== false ? 'Sí' : 'No') . "</p>";
    if (strpos($host, 'neon.tech') !== false) {
        $endpoint_id = explode('.', $host)[0];
        echo "<p><strong>Endpoint ID:</strong> {$endpoint_id}</p>";
    }
}
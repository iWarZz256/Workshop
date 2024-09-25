<?php
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception(".env file not found");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Ignorer les commentaires
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!isset($_ENV[$name])) {
            $_ENV[$name] = $value;
        }
    }
}

try {
    // Charger le fichier .env
    loadEnv(__DIR__ . '/.env');

    // Obtenir les informations de connexion
    $host = $_ENV['DB_HOST'];
    $dbname = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];

    // Tentative de connexion avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

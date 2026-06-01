<?php
try {
    // Check MySQL
    $db = new PDO('mysql:host=127.0.0.1;port=3306;dbname=absensi', 'root', '');
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema='absensi'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "MySQL tables: " . $row['cnt'] . PHP_EOL;
    
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . implode(', ', $tables) . PHP_EOL;
} catch (Exception $e) {
    echo "MySQL Error: " . $e->getMessage() . PHP_EOL;
}

// Check SQLite
try {
    $db = new PDO('sqlite:database/database.sqlite');
    $stmt = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "SQLite tables: " . implode(', ', $tables) . PHP_EOL;
} catch (Exception $e) {
    echo "SQLite Error: " . $e->getMessage() . PHP_EOL;
}

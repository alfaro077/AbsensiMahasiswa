<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('mahasiswa', 'dosen', 'admin') NOT NULL");
    echo "Successfully updated user roles.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

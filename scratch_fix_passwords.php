<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (User::all() as $user) {
    $user->password = 'password';
    $user->save();
    echo "Updated: " . $user->email . "\n";
}

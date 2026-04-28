<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::first();
if (!$user) {
    echo "No user found.\n";
    exit(1);
}

// Ensure the role exists
$role = Role::firstOrCreate(['name' => 'super_admin']);

// Assign role
$user->assignRole($role);

echo "Role super_admin assigned to {$user->email}\n";

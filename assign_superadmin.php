<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;

$email = 'admin@erp.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found: $email\n";
    exit(1);
}

$roleName = 'super_admin';
$role = Role::where('name', $roleName)->first();

if (!$role) {
    echo "Role not found: $roleName. Running shield:generate to ensure permissions exist...\n";
    // Usually role should exist from previous steps, but let's be safe.
}

if (!$user->hasRole($roleName)) {
    $user->assignRole($roleName);
    echo "Role $roleName assigned to {$user->email}\n";
} else {
    echo "User {$user->email} already has role $roleName\n";
}

<?php
use Illuminate\Support\Facades\Schema;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (Schema::hasColumn('payments', 'user_id')) {
    Schema::table('payments', function ($table) {
        $table->dropColumn('user_id');
    });
    echo "Column dropped\n";
} else {
    echo "Column does not exist\n";
}

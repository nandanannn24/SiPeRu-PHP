<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prodi;
use App\Models\User;

// Fix the codes in database for Fasilkom
$fasilkomProdis = [
    'Teknik Informatika' => '101',
    'Sistem Informasi' => '201',
    'Sains Data' => '301',
    'Bisnis Digital' => '401',
];

foreach ($fasilkomProdis as $name => $code) {
    $prodi = Prodi::where('name', $name)->first();
    if ($prodi) {
        $prodi->code = $code;
        $prodi->save();
        echo "Updated $name to code $code\n";
    }
}

// Fix the specific user account
$user = User::where('email', '24081010037@student.upnjatim.ac.id')->first();
if ($user) {
    $ifProdi = Prodi::where('name', 'Teknik Informatika')->first();
    if ($ifProdi) {
        $user->prodi_id = $ifProdi->id;
        // Since NPM is AAFFPPPXXXX
        // 24 08 101 0037 -> prodi = 101 -> Teknik Informatika
        $user->npm = '24081010037';
        $user->save();
        echo "Fixed user 24081010037 prodi_id to " . $ifProdi->id . "\n";
    }
} else {
    echo "User 24081010037 not found.\n";
}

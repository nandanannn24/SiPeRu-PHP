<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::firstOrCreate(
    ['email' => 'mahasiswa@upnjatim.ac.id'],
    [
        'name' => 'Mahasiswa Test',
        'npm' => '20081010001',
        'password' => \Hash::make('password'),
        'role_id' => \App\Models\Role::where('name', 'Mahasiswa')->first()->id,
        'fakultas_id' => \App\Models\Fakultas::where('code', '08')->first()->id,
        'prodi_id' => \App\Models\Prodi::where('name', 'Teknik Informatika')->first()->id,
    ]
);

$ruangan = \App\Models\Ruangan::where('name', 'Gedung Serbaguna Giri Loka')->first() ?? \App\Models\Ruangan::first();

$peminjaman = \App\Models\Peminjaman::create([
    'user_id' => $user->id,
    'ruangan_id' => $ruangan->id,
    'waktu_mulai' => now()->addDays(15),
    'waktu_selesai' => now()->addDays(15)->addHours(2),
    'keperluan' => 'Acara Seminar IT',
    'jenis_peminjaman' => 'luar_fakultas',
    'status' => 'menunggu_kaprodi',
    'file_sik' => 'dummy.pdf',
    'file_proposal' => 'dummy.pdf',
    'file_persetujuan_fasilitas' => 'dummy.pdf',
]);

echo 'Peminjaman ID: ' . $peminjaman->id . PHP_EOL;

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ruangan;
use App\Models\Fakultas;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $fakultasIds = [
            'FEB' => Fakultas::where('code', '01')->value('id'),
            'FP' => Fakultas::where('code', '02')->value('id'),
            'FT' => Fakultas::where('code', '03')->value('id'),
            'FAD' => Fakultas::where('code', '05')->value('id'),
            'Fasilkom' => Fakultas::where('code', '08')->value('id'),
        ];

        $ruangans = [
            // Aula
            ['name' => 'GSG Giri Loka', 'fakultas_id' => null, 'jenis' => 'aula', 'gedung' => 'Gedung Serbaguna'],
            ['name' => 'Aula GKB', 'fakultas_id' => null, 'jenis' => 'aula', 'gedung' => 'GKB'],
            ['name' => 'Auditorium FIK', 'fakultas_id' => $fakultasIds['Fasilkom'], 'jenis' => 'aula', 'gedung' => 'Gedung FIK'],
            ['name' => 'Aula FEB', 'fakultas_id' => $fakultasIds['FEB'], 'jenis' => 'aula', 'gedung' => 'Gedung FEB'],
            ['name' => 'Aula FAD', 'fakultas_id' => $fakultasIds['FAD'], 'jenis' => 'aula', 'gedung' => 'Gedung FAD'],

            // Kelas
            ['name' => 'Kelas GKB-101', 'fakultas_id' => null, 'jenis' => 'kelas', 'gedung' => 'GKB'],
            ['name' => 'Kelas GKB-102', 'fakultas_id' => null, 'jenis' => 'kelas', 'gedung' => 'GKB'],
            ['name' => 'Kelas GKB-201', 'fakultas_id' => null, 'jenis' => 'kelas', 'gedung' => 'GKB'],
            ['name' => 'Kelas FIK-201', 'fakultas_id' => $fakultasIds['Fasilkom'], 'jenis' => 'kelas', 'gedung' => 'Gedung FIK'],
            ['name' => 'Kelas FIK-202', 'fakultas_id' => $fakultasIds['Fasilkom'], 'jenis' => 'kelas', 'gedung' => 'Gedung FIK'],
            ['name' => 'Kelas FEB-101', 'fakultas_id' => $fakultasIds['FEB'], 'jenis' => 'kelas', 'gedung' => 'Gedung FEB'],
            ['name' => 'Kelas FEB-102', 'fakultas_id' => $fakultasIds['FEB'], 'jenis' => 'kelas', 'gedung' => 'Gedung FEB'],
            ['name' => 'Kelas FAD-301', 'fakultas_id' => $fakultasIds['FAD'], 'jenis' => 'kelas', 'gedung' => 'Gedung FAD'],
            ['name' => 'Kelas FT-101', 'fakultas_id' => $fakultasIds['FT'], 'jenis' => 'kelas', 'gedung' => 'Gedung FT'],
            ['name' => 'Kelas FP-101', 'fakultas_id' => $fakultasIds['FP'], 'jenis' => 'kelas', 'gedung' => 'Gedung FP'],
        ];

        foreach ($ruangans as $r) {
            Ruangan::create([
                'name' => $r['name'],
                'fakultas_id' => $r['fakultas_id'],
                'jenis' => $r['jenis'],
                'gedung' => $r['gedung'],
                'status' => 'tersedia'
            ]);
        }
    }
}

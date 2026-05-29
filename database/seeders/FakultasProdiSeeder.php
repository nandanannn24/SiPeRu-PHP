<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fakultas;
use App\Models\Prodi;

class FakultasProdiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            '01' => [
                'name' => 'FEB',
                'prodis' => [
                    '010' => 'Ekonomi Pembangunan',
                    '020' => 'Manajemen',
                    '030' => 'Akuntansi',
                    '040' => 'Kewirausahaan',
                ],
            ],
            '02' => [
                'name' => 'FP (Pertanian)',
                'prodis' => [
                    '010' => 'Agroteknologi',
                    '020' => 'Agribisnis',
                ],
            ],
            '03' => [
                'name' => 'FT (Teknik)',
                'prodis' => [
                    '010' => 'Teknik Kimia',
                    '020' => 'Teknik Industri',
                    '030' => 'Teknik Sipil',
                    '040' => 'Teknik Lingkungan',
                    '050' => 'Teknologi Pangan',
                    '060' => 'Teknik Mesin',
                    '070' => 'Fisika',
                ],
            ],
            '04' => [
                'name' => 'FISIP',
                'prodis' => [
                    '010' => 'Administrasi Negara',
                    '020' => 'Administrasi Bisnis',
                    '030' => 'Ilmu Komunikasi',
                    '040' => 'Hubungan Internasional',
                    '050' => 'Pariwisata',
                    '060' => 'Linguistik Indonesia',
                ],
            ],
            '05' => [
                'name' => 'FAD',
                'prodis' => [
                    '010' => 'Arsitektur',
                    '020' => 'Desain Komunikasi Visual',
                    '030' => 'Desain Interior',
                ],
            ],
            '06' => [
                'name' => 'FK',
                'prodis' => [
                    '010' => 'Pendidikan Dokter',
                    '020' => 'Profesi Dokter',
                ],
            ],
            '07' => [
                'name' => 'FH',
                'prodis' => [
                    '010' => 'Ilmu Hukum',
                ],
            ],
            '08' => [
                'name' => 'Fasilkom',
                'prodis' => [
                    '101' => 'Teknik Informatika',
                    '201' => 'Sistem Informasi',
                    '301' => 'Sains Data',
                    '401' => 'Bisnis Digital',
                ],
            ],
        ];

        foreach ($data as $code => $fakultasData) {
            $fakultas = Fakultas::create([
                'code' => $code,
                'name' => $fakultasData['name'],
            ]);

            foreach ($fakultasData['prodis'] as $prodiCode => $prodiName) {
                Prodi::create([
                    'fakultas_id' => $fakultas->id,
                    'code'        => $prodiCode,
                    'name'        => $prodiName,
                ]);
            }
        }
    }
}

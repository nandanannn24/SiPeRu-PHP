<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Setup Roles
        $rolesData = ['Mahasiswa', 'Kaprodi', 'Dekan 3', 'TU Fakultas', 'Admin'];
        $roles = [];
        foreach ($rolesData as $roleName) {
            $roles[$roleName] = Role::firstOrCreate(['name' => $roleName])->id;
        }

        $this->call([
            FakultasProdiSeeder::class,
            RuanganSeeder::class,
        ]);

        // Cache Fakultas and Prodi to avoid repetitive queries
        $fakultas = \App\Models\Fakultas::all()->keyBy('code');
        $prodis = \App\Models\Prodi::all()->groupBy('fakultas_id');

        $accounts = [
            // Admin Pusat
            ['role' => 'Admin', 'name' => 'TU Rektorat UPN Jatim', 'email' => 'tu.rektorat@upnjatim.ac.id', 'fakultas_code' => null, 'prodi_name' => null],

            // FAKULTAS 01: FEB
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FEB', 'email' => 'dekan3.feb@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FEB', 'email' => 'tu.feb@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Ekonomi Pembangunan', 'email' => 'kaprodi.ekbang@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => 'Ekonomi Pembangunan'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Manajemen', 'email' => 'kaprodi.manajemen@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => 'Manajemen'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Akuntansi', 'email' => 'kaprodi.akuntansi@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => 'Akuntansi'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Kewirausahaan', 'email' => 'kaprodi.kewirausahaan@upnjatim.ac.id', 'fakultas_code' => '01', 'prodi_name' => 'Kewirausahaan'],

            // FAKULTAS 02: FP - Pertanian
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FP', 'email' => 'dekan3.fp@upnjatim.ac.id', 'fakultas_code' => '02', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FP', 'email' => 'tu.fp@upnjatim.ac.id', 'fakultas_code' => '02', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Agroteknologi', 'email' => 'kaprodi.agroteknologi@upnjatim.ac.id', 'fakultas_code' => '02', 'prodi_name' => 'Agroteknologi'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Agribisnis', 'email' => 'kaprodi.agribisnis@upnjatim.ac.id', 'fakultas_code' => '02', 'prodi_name' => 'Agribisnis'],

            // FAKULTAS 03: FT - Teknik
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FT', 'email' => 'dekan3.ft@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FT', 'email' => 'tu.ft@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Kimia', 'email' => 'kaprodi.tkimia@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknik Kimia'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Industri', 'email' => 'kaprodi.tindustri@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknik Industri'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Sipil', 'email' => 'kaprodi.tsipil@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknik Sipil'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Lingkungan', 'email' => 'kaprodi.tlingkungan@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknik Lingkungan'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknologi Pangan', 'email' => 'kaprodi.tpangan@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknologi Pangan'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Mesin', 'email' => 'kaprodi.tmesin@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Teknik Mesin'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Fisika', 'email' => 'kaprodi.fisika@upnjatim.ac.id', 'fakultas_code' => '03', 'prodi_name' => 'Fisika'],

            // FAKULTAS 04: FISIP
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FISIP', 'email' => 'dekan3.fisip@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FISIP', 'email' => 'tu.fisip@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Administrasi Negara', 'email' => 'kaprodi.adneg@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Administrasi Negara'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Administrasi Bisnis', 'email' => 'kaprodi.adbis@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Administrasi Bisnis'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Ilmu Komunikasi', 'email' => 'kaprodi.ilkom@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Ilmu Komunikasi'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Hubungan Internasional', 'email' => 'kaprodi.hi@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Hubungan Internasional'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Pariwisata', 'email' => 'kaprodi.pariwisata@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Pariwisata'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Linguistik Indonesia', 'email' => 'kaprodi.linguistik@upnjatim.ac.id', 'fakultas_code' => '04', 'prodi_name' => 'Linguistik Indonesia'],

            // FAKULTAS 05: FAD
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FAD', 'email' => 'dekan3.fad@upnjatim.ac.id', 'fakultas_code' => '05', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FAD', 'email' => 'tu.fad@upnjatim.ac.id', 'fakultas_code' => '05', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Arsitektur', 'email' => 'kaprodi.arsitektur@upnjatim.ac.id', 'fakultas_code' => '05', 'prodi_name' => 'Arsitektur'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi DKV', 'email' => 'kaprodi.dkv@upnjatim.ac.id', 'fakultas_code' => '05', 'prodi_name' => 'Desain Komunikasi Visual'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Desain Interior', 'email' => 'kaprodi.interior@upnjatim.ac.id', 'fakultas_code' => '05', 'prodi_name' => 'Desain Interior'],

            // FAKULTAS 06: FK - Kedokteran
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FK', 'email' => 'dekan3.fk@upnjatim.ac.id', 'fakultas_code' => '06', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FK', 'email' => 'tu.fk@upnjatim.ac.id', 'fakultas_code' => '06', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Pendidikan Dokter', 'email' => 'kaprodi.pendokter@upnjatim.ac.id', 'fakultas_code' => '06', 'prodi_name' => 'Pendidikan Dokter'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Profesi Dokter', 'email' => 'kaprodi.profesidokter@upnjatim.ac.id', 'fakultas_code' => '06', 'prodi_name' => 'Profesi Dokter'],

            // FAKULTAS 07: FH - Hukum
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 FH', 'email' => 'dekan3.fh@upnjatim.ac.id', 'fakultas_code' => '07', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU FH', 'email' => 'tu.fh@upnjatim.ac.id', 'fakultas_code' => '07', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Ilmu Hukum', 'email' => 'kaprodi.hukum@upnjatim.ac.id', 'fakultas_code' => '07', 'prodi_name' => 'Ilmu Hukum'],

            // FAKULTAS 08: Fasilkom
            ['role' => 'Dekan 3', 'name' => 'Dekan 3 Fasilkom', 'email' => 'dekan3.fasilkom@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => null],
            ['role' => 'TU Fakultas', 'name' => 'TU Fasilkom', 'email' => 'tu.fasilkom@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => null],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Teknik Informatika', 'email' => 'kaprodi.if@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => 'Teknik Informatika'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Sistem Informasi', 'email' => 'kaprodi.si@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => 'Sistem Informasi'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Sains Data', 'email' => 'kaprodi.sd@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => 'Sains Data'],
            ['role' => 'Kaprodi', 'name' => 'Kaprodi Bisnis Digital', 'email' => 'kaprodi.bd@upnjatim.ac.id', 'fakultas_code' => '08', 'prodi_name' => 'Bisnis Digital'],
        ];

        $usersToInsert = array_map(function ($acc) use ($roles, $fakultas, $prodis) {
            $fakultasId = null;
            $prodiId = null;

            if ($acc['fakultas_code']) {
                $f = $fakultas->get($acc['fakultas_code']);
                if ($f) {
                    $fakultasId = $f->id;
                    if ($acc['prodi_name']) {
                        $pList = $prodis->get($f->id);
                        if ($pList) {
                            $prodiId = $pList->firstWhere('name', $acc['prodi_name'])?->id;
                        }
                    }
                }
            }

            return [
                'name' => $acc['name'],
                'email' => $acc['email'],
                'password' => Hash::make('password'),
                'role_id' => $roles[$acc['role']] ?? null,
                'fakultas_id' => $fakultasId,
                'prodi_id' => $prodiId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $accounts);

        User::insert($usersToInsert);
    }
}

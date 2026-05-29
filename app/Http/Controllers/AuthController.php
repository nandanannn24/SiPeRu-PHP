<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\Prodi;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $email       = $request->email;
        $npm         = null;
        $angkatan    = null;
        $fakultas_id = null;
        $prodi_id    = null;

        // ── Parse NPM dari email student UPN Jatim ────────
        // Format NPM: AAFFPPPXXXX (11 digit)
        //   AA   = 2 digit tahun angkatan (e.g. 24 = 2024)
        //   FF   = 2 digit kode fakultas  (e.g. 08 = Fasilkom)
        //   PPP  = 3 digit kode prodi     (e.g. 010 = Teknik Informatika)
        //   XXXX = 4 digit nomor urut     (e.g. 0037)
        if (str_ends_with($email, '@student.upnjatim.ac.id')) {
            $localPart = explode('@', $email)[0];

            // Validasi: harus tepat 11 digit angka
            if (preg_match('/^\d{11}$/', $localPart)) {
                $npm = $localPart;

                $angkatanCode = substr($npm, 0, 2);
                $fakultasCode = substr($npm, 2, 2);
                $prodiCode    = substr($npm, 4, 3);
                // nomor urut  = substr($npm, 7, 4) — tidak dipakai

                // Hanya terima angkatan yang masuk akal (2000-2099)
                $angkatanYear = (int) $angkatanCode;
                if ($angkatanYear >= 0 && $angkatanYear <= 99) {
                    $angkatan = '20' . str_pad($angkatanCode, 2, '0', STR_PAD_LEFT);
                }

                // Resolve fakultas
                $fakultas = Fakultas::where('code', $fakultasCode)->first();
                if ($fakultas) {
                    $fakultas_id = $fakultas->id;

                    // Resolve prodi berdasarkan code + fakultas_id
                    $prodi = Prodi::where('fakultas_id', $fakultas->id)
                        ->where('code', $prodiCode)
                        ->first();

                    if ($prodi) {
                        $prodi_id = $prodi->id;
                    }
                }
            }
            // Jika format NPM tidak valid, tetap lanjut register
            // tapi tanpa data NPM/angkatan/fakultas/prodi
        }

        // Default role: Mahasiswa
        $role = Role::where('name', 'Mahasiswa')->first();

        $user = User::create([
            'name'        => $request->name,
            'email'       => $email,
            'password'    => $request->password, // cast 'hashed' di model akan handle
            'npm'         => $npm,
            'angkatan'    => $angkatan,
            'fakultas_id' => $fakultas_id,
            'prodi_id'    => $prodi_id,
            'role_id'     => $role?->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Proses login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))
                ->with('success', 'Login berhasil!');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout.');
    }
}

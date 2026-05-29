<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('role', 'fakultas', 'prodi');
        return view('profile.edit', compact('user'));
    }

    public function updateSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            $filename = 'ttd_' . $user->id . '_' . time() . '.png';
            $path = storage_path('app/public/signatures/' . $filename);

            if (!file_exists(storage_path('app/public/signatures'))) {
                mkdir(storage_path('app/public/signatures'), 0755, true);
            }

            // Using Intervention Image V3
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            $image->scale(width: 300); // Scale down
            
            $image->save($path);

            if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                Storage::disk('public')->delete($user->signature_path);
            }

            $user->signature_path = 'signatures/' . $filename;
            $user->save();

            return back()->with('success', 'Tanda tangan berhasil diperbarui!');
        }

        return back()->with('error', 'Gagal mengunggah tanda tangan.');
    }
}

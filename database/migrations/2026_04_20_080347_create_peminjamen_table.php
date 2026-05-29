<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->constrained('ruangans')->cascadeOnDelete();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('keperluan');
            $table->string('jenis_peminjaman'); // 'dalam_fakultas' atau 'luar_fakultas'

            // Approval status berjenjang
            $table->string('approval_kaprodi')->default('pending'); // pending, approved, rejected
            $table->string('approval_dekan')->default('pending');   // pending, approved, rejected
            $table->string('approval_tu')->default('pending');      // pending, approved, rejected

            // Status utama transaksi
            $table->string('status')->default('menunggu_kaprodi'); // menunggu_kaprodi, revisi_kaprodi, menunggu_dekan, revisi_dekan, menunggu_tu, revisi_tu, disetujui, ditolak, dibatalkan

            // Batas waktu approval: H-3 sebelum waktu_mulai
            $table->date('batas_approval')->nullable();

            // Dokumen upload (Hanya 3 File)
            $table->string('file_sik')->nullable();
            $table->string('file_proposal')->nullable();
            $table->string('file_persetujuan_fasilitas')->nullable();

            $table->timestamps();

            // Index untuk query yang sering dilakukan
            $table->index(['ruangan_id', 'waktu_mulai', 'waktu_selesai']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};

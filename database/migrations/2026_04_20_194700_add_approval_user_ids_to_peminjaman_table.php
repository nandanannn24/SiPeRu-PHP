<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            // Track WHO approved at each stage (for signature retrieval)
            $table->foreignId('approved_by_kaprodi')->nullable()->after('approval_tu')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_dekan')->nullable()->after('approved_by_kaprodi')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by_tu')->nullable()->after('approved_by_dekan')->constrained('users')->nullOnDelete();

            // Path to the generated final PDF document
            $table->string('dokumen_terbit_path')->nullable()->after('file_persetujuan_fasilitas');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign(['approved_by_kaprodi']);
            $table->dropForeign(['approved_by_dekan']);
            $table->dropForeign(['approved_by_tu']);
            $table->dropColumn([
                'approved_by_kaprodi',
                'approved_by_dekan',
                'approved_by_tu',
                'dokumen_terbit_path',
            ]);
        });
    }
};

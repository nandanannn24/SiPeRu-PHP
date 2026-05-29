<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('signature_kaprodi')->nullable()->after('approved_by_tu');
            $table->string('signature_dekan')->nullable()->after('signature_kaprodi');
            $table->string('stempel_tu_fakultas')->nullable()->after('signature_dekan');
            $table->string('signature_tu_rektorat')->nullable()->after('stempel_tu_fakultas');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'signature_kaprodi',
                'signature_dekan',
                'stempel_tu_fakultas',
                'signature_tu_rektorat'
            ]);
        });
    }
};

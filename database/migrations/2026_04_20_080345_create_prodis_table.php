<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prodis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fakultas_id')->constrained('fakultas')->cascadeOnDelete();
            $table->string('code', 3)->comment('Kode 3 digit prodi dari NPM, e.g. 010');
            $table->string('name');
            $table->timestamps();

            $table->unique(['fakultas_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prodis');
    }
};

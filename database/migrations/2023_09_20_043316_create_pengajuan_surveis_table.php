<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan_survei', function (Blueprint $table) {
            $table->id();
            $table->string('id_penyewa');
            $table->string('nama_penyewa');
            $table->string('no_ktp');
            $table->date('tanggal_survei');
            $table->string('waktu');
            $table->string('keterangan');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_survei');
    }
};

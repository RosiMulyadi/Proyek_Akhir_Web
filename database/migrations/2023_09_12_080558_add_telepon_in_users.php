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
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_ktp')->after('name')->nullable();
            $table->string('alamat')->after('no_ktp')->nullable();
            $table->string('telepon')->after('email')->nullable();
            $table->string('jenkel')->after('telepon')->nullable();
            $table->date('tgl_lahir')->after('jenkel')->nullable();
            $table->string('tmpt_lahir')->after('tgl_lahir')->nullable();
            $table->string('created_by')->default('system');
            $table->string('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

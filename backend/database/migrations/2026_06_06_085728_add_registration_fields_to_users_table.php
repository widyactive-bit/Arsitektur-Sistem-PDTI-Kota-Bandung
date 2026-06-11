<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('ktp')->nullable();
            $table->string('kk')->nullable();
            $table->text('sertifikat')->nullable(); // JSON list of certificate paths
            $table->string('status')->default('Nonaktif');
            $table->string('role')->default('Atlet')->change(); // Change enum to string for flexibility
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'alamat', 'ktp', 'kk', 'sertifikat', 'status']);
        });
    }
};

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
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('sk_terbaru')->nullable()->after('jumlah_atlet');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('qr_code_data')->nullable()->after('longitude');
        });

        Schema::table('athlete_stats', function (Blueprint $table) {
            $table->text('catatan_pelatih')->nullable()->after('leadership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn('sk_terbaru');
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('qr_code_data');
        });

        Schema::table('athlete_stats', function (Blueprint $table) {
            $table->dropColumn('catatan_pelatih');
        });
    }
};

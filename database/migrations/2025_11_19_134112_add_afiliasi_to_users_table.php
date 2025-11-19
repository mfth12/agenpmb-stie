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
            // Hapus kolom lama jika perlu (hati-hati!)
            // $table->dropColumn('afiliasi'); // Jangan lakukan ini jika kolom sudah ada dan berisi data

            // Tambahkan kolom afiliasi baru dengan tipe yang benar
            $table->unsignedBigInteger('afiliasi')->nullable()->after('asal_sekolah'); // Sesuaikan posisi jika perlu

            // Tambahkan foreign key constraint
            $table->foreign('afiliasi')->references('afiliasi_id')->on('user_afiliasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['afiliasi']);
            $table->dropColumn('afiliasi');
        });
    }
};

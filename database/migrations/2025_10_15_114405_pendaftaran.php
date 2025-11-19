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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->bigIncrements('pendaftaran_id');
            $table->foreignId('user_id')->nullable()->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignId('mitra_id')->constrained('users', 'user_id')->onDelete('cascade');

            // Data dari PMB SIAKAD2
            $table->string('id_calon_mahasiswa')->unique();
            $table->string('username_siakad');
            $table->string('password_text')->nullable(); // Menyimpan password dalam plain text
            $table->string('no_transaksi');

            // Data Pendaftaran
            $table->string('prodi_id', 10);
            $table->string('prodi_nama');
            $table->integer('tahun');
            $table->integer('gelombang');
            $table->decimal('biaya', 15, 2);
            $table->string('kelas', 5);
            $table->string('nama_lengkap');
            $table->string('email');
            $table->string('nomor_hp');
            $table->string('nomor_hp2')->nullable();

            // Status
            $table->enum('status', ['pending', 'success', 'failed', 'synced', 'imported', 'removed'])->default('pending');
            $table->text('keterangan')->nullable();

            // Response dari PMB SIAKAD2
            $table->text('response_data')->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('mitra_id');
            $table->index('id_calon_mahasiswa');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};

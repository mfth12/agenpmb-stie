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
        Schema::create('user_afiliasis', function (Blueprint $table) {
            $table->id('afiliasi_id'); // Primary Key, integer auto-increment
            $table->unsignedBigInteger('parent_id')->nullable(); // Foreign Key ke diri sendiri
            $table->string('nama');
            $table->text('keterangan')->nullable();
            $table->timestamps(); // created_at, updated_at

            // Foreign Key Constraint
            $table->foreign('parent_id')->references('afiliasi_id')->on('user_afiliasis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_afiliasis');
    }
};

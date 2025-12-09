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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id')->from(3201);           //added
            $table->string('siakad_id', 64)->unique()->nullable();                        //synced
            $table->string('username');                             //added
            $table->string('password')->nullable();                                       //used for local user
            $table->string('name');                                                       //synced
            $table->string('asal_sekolah', 64)->nullable();         //added
            $table->string('email')->unique();                                            //synced
            $table->string('nomor_hp', 64)->unique();                                     //synced
            $table->string('nomor_hp2', 64)->unique()->nullable();                                  //synced
            $table->timestamp('email_verified_at')->nullable();                           //synced
            $table->text('about')->nullable();                                            //synced
            // $table->string('role', 64)->nullable();              //added
            $table->string('default_role', 15);                                           //synced
            $table->string('theme')->default('default');                                  //synced
            $table->string('avatar')->nullable();                                         //synced
            $table->string('status', 15)->default('active');                              //synced
            $table->string('status_login', 15)->default('offline');                       //synced
            $table->boolean('isdeleted')->default(false);                                  //synced
            $table->timestamp('last_logged_in')->nullable();                    //added
            $table->timestamp('last_synced_at')->nullable();                    //added
            $table->rememberToken();                                           //laravel
            $table->timestamps();                                              //laravel
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

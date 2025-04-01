<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activites', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('karu_id')->nullable(); // ID user yang melakukan aksi
            $table->string('action'); // Aksi (tambah, update, hapus)
            $table->string('table_name'); // Nama tabel yang diubah
            $table->json('old_data')->nullable(); // Data sebelum perubahan
            $table->json('new_data')->nullable(); // Data setelah perubahan
            $table->timestamp('created_at')->useCurrent(); // Waktu log dibuat
            $table->foreign('karu_id')->references('karu_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_activites');
    }
};

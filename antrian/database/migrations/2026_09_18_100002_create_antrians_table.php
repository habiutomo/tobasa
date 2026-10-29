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
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->unsignedInteger('nomor');
            $table->string('no_antrian', 10);
            $table->string('nama_pasien')->nullable();
            $table->enum('status', ['waiting', 'calling', 'done', 'skipped'])->default('waiting');
            $table->foreignId('loket_id')->nullable()->constrained('lokets')->nullOnDelete();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'poli_id', 'nomor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};
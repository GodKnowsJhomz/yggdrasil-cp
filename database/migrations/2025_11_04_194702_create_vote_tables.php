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
        // Tabela de sites de votação
        Schema::create('vote_sites', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('cover', 250)->nullable();
            $table->string('url', 250);
            $table->integer('points')->default(0);
            $table->integer('block_timer')->default(0)->comment('Tempo em segundos para bloquear novo voto');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Tabela de bloqueio de votos (por conta e IP)
        Schema::create('vote_blocks', function (Blueprint $table) {
            $table->unsignedBigInteger('vote_site_id');
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address', 100);
            $table->integer('last_timer')->comment('Timestamp Unix do último voto');
            $table->timestamps();
            
            $table->primary(['vote_site_id', 'user_id']);
            $table->foreign('vote_site_id')->references('id')->on('vote_sites')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Tabela de pontos dos usuários
        Schema::create('vote_points', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary();
            $table->integer('points')->default(0);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vote_blocks');
        Schema::dropIfExists('vote_points');
        Schema::dropIfExists('vote_sites');
    }
};

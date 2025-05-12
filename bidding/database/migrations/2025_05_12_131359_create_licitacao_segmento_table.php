<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('licitacao_segmento', function (Blueprint $table) {
            $table->id();

            // Correção: Especifica explicitamente a tabela 'licitacoes'
            $table->foreignId('licitacao_id')
                  ->constrained('licitacoes') // <-- Referência explícita adicionada
                  ->onDelete('cascade');

            // Correção: Especifica explicitamente a tabela 'segmentos'
            $table->foreignId('segmento_id')
                  ->constrained('segmentos') // <-- Referência explícita adicionada
                  ->onDelete('cascade');

            $table->integer('relevancia')->default(0);
            $table->timestamps();

            // Chave única para evitar duplicatas da mesma combinação
            $table->unique(['licitacao_id', 'segmento_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('licitacao_segmento');
    }
};

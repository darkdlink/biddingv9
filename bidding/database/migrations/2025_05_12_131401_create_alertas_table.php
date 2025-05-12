<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            // A referência para 'users' geralmente funciona bem por convenção
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Correção: Especifica explicitamente a tabela 'licitacoes'
            $table->foreignId('licitacao_id')
                  ->constrained('licitacoes') // <-- Referência explícita adicionada
                  ->onDelete('cascade');

            $table->string('titulo');
            $table->text('conteudo');
            $table->boolean('lido')->default(false);
            $table->dateTime('data_leitura')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas');
    }
};

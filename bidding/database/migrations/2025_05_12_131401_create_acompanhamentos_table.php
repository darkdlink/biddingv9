<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('acompanhamentos', function (Blueprint $table) {
            $table->id();

            // Correção: Especifica explicitamente a tabela 'licitacoes'
            $table->foreignId('licitacao_id')
                  ->constrained('licitacoes') // <-- Referência explícita adicionada
                  ->onDelete('cascade');

            // A referência para 'users' geralmente funciona bem por convenção
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('titulo');
            $table->text('descricao');
            $table->dateTime('data_evento')->nullable();
            $table->enum('tipo', ['anotacao', 'lembrete', 'alteracao', 'documento', 'outro'])->default('anotacao');
            $table->boolean('is_public')->default(false)->comment('Se verdadeiro, visível para toda a empresa/grupo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('acompanhamentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('propostas', function (Blueprint $table) {
            $table->id();
            // Correção: Especifica explicitamente a tabela 'licitacoes'
            $table->foreignId('licitacao_id')
                  ->constrained('licitacoes') // <-- Referência explícita garante o alvo correto
                  ->onDelete('cascade');

            // Esta referência para 'users' geralmente funciona bem pela convenção,
            // mas poderia ser explicitada como ->constrained('users') se necessário.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('titulo');
            $table->decimal('valor_proposta', 15, 2);
            $table->enum('status', ['rascunho', 'submetida', 'vencedora', 'perdedora', 'cancelada'])->default('rascunho');
            $table->dateTime('data_submissao')->nullable();
            $table->longText('conteudo')->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('propostas');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('licenca_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plano_id')->constrained('licenca_planos')->onDelete('cascade');
            $table->dateTime('data_inicio');
            $table->dateTime('data_expiracao')->nullable();
            $table->enum('ciclo_cobranca', ['mensal', 'anual'])->default('mensal');
            $table->enum('status', ['ativa', 'inativa', 'pendente', 'cancelada'])->default('pendente');
            $table->dateTime('ultimo_pagamento')->nullable();
            $table->dateTime('proximo_pagamento')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenca_usuarios');
    }
};

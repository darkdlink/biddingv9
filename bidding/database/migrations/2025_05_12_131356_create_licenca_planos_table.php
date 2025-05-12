<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('licenca_planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('tipo', ['pessoa_fisica', 'empresa', 'grupo']);
            $table->enum('tier', ['basico', 'intermediario', 'avancado']);
            $table->decimal('preco_mensal', 8, 2);
            $table->decimal('preco_anual', 8, 2);
            $table->integer('max_usuarios')->nullable();
            $table->integer('max_segmentos')->nullable();
            $table->integer('max_empresas')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('licenca_planos');
    }
};

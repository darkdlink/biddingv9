<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposta_versoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained()->onDelete('cascade');
            $table->integer('versao');
            $table->longText('conteudo');
            $table->decimal('valor_proposta', 15, 2);
            $table->foreignId('user_id')->constrained()->comment('Usuário que criou a versão');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposta_versoes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('proposta_arquivos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposta_id')->constrained()->onDelete('cascade');
            $table->string('nome');
            $table->string('caminho');
            $table->string('tipo_mime');
            $table->integer('tamanho');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('proposta_arquivos');
    }
};

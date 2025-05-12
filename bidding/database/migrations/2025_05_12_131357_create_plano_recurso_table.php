<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plano_recurso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_id')->constrained('licenca_planos')->onDelete('cascade');
            $table->foreignId('recurso_id')->constrained('licenca_recursos')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['plano_id', 'recurso_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('plano_recurso');
    }
};

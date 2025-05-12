<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Passo 1: Garantir que a coluna 'analisada' exista
        if (!Schema::hasColumn('licitacoes', 'analisada')) {
            Schema::table('licitacoes', function (Blueprint $table) {

                $table->boolean('analisada')->default(false)->after('cnpj')->index();
            });
        }


        Schema::table('licitacoes', function (Blueprint $table) {
            // Adicionar um índice pode ser útil.
            $table->boolean('interesse')->default(false)->after('analisada')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('licitacoes', function (Blueprint $table) {
            // Apenas desfaz o que o nome da migration implica diretamente
            if (Schema::hasColumn('licitacoes', 'interesse')) {
                $table->dropColumn('interesse');
            }
             // Se você *realmente* adicionou 'analisada' aqui e quer reverter:
             // if (Schema::hasColumn('licitacoes', 'analisada')) {
             //    $table->dropColumn('analisada');
             // }
        });
    }
};

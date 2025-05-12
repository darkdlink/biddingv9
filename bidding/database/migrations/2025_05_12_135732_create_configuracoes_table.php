<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();
            $table->text('valor')->nullable();
            $table->string('grupo')->nullable();
            $table->string('descricao')->nullable();
            $table->boolean('editavel')->default(true);
            $table->timestamps();
        });

        // Inserir configurações iniciais
        DB::table('configuracoes')->insert([
            [
                'chave' => 'app_name',
                'valor' => 'Sistema Bidding',
                'grupo' => 'geral',
                'descricao' => 'Nome da aplicação',
                'editavel' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'sincronizacao_automatica',
                'valor' => '1',
                'grupo' => 'sistema',
                'descricao' => 'Ativar sincronização automática com PNCP',
                'editavel' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'chave' => 'hora_sincronizacao',
                'valor' => '07:00',
                'grupo' => 'sistema',
                'descricao' => 'Horário da sincronização automática',
                'editavel' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configuracoes');
    }
};

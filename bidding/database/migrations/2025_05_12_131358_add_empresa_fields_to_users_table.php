<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('tipo_usuario', ['pessoa_fisica', 'usuario_master', 'admin_grupo', 'admin_sistema'])
                  ->default('pessoa_fisica');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn(['empresa_id', 'tipo_usuario', 'is_active']);
        });
    }
};

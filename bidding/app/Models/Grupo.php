<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'cnpj',
        'telefone',
        'email',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relação com empresas do grupo
    public function empresas()
    {
        return $this->hasMany(Empresa::class);
    }

    // Relação com administradores do grupo
    public function administradores()
    {
        return $this->hasManyThrough(
            User::class,
            Empresa::class,
        )->where('tipo_usuario', 'admin_grupo');
    }

    // Total de usuários no grupo
    public function totalUsuarios()
    {
        return $this->hasManyThrough(
            User::class,
            Empresa::class
        )->count();
    }

    // Empresas ativas do grupo
    public function empresasAtivas()
    {
        return $this->empresas()->where('is_active', true);
    }
}

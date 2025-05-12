<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
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
        'grupo_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relação com usuários da empresa
    public function usuarios()
    {
        return $this->hasMany(User::class);
    }

    // Relação com o usuário master da empresa
    public function usuarioMaster()
    {
        return $this->usuarios()->where('tipo_usuario', 'usuario_master')->first();
    }

    // Relação com o grupo empresarial (se pertencer a um)
    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    // Relação com propostas da empresa
    public function propostas()
    {
        return $this->hasManyThrough(Proposta::class, User::class);
    }

    // Relação com segmentos da empresa
    public function segmentos()
    {
        return $this->hasMany(Segmento::class);
    }

    // Verificar se pertence a um grupo
    public function pertenceAGrupo()
    {
        return !is_null($this->grupo_id);
    }
}

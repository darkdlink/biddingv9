<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaPlano extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'tipo', // 'pessoa_fisica', 'empresa', 'grupo'
        'tier', // 'basico', 'intermediario', 'avancado'
        'preco_mensal',
        'preco_anual',
        'max_usuarios',
        'max_segmentos',
        'max_empresas',
        'descricao',
    ];

    protected $casts = [
        'preco_mensal' => 'decimal:2',
        'preco_anual' => 'decimal:2',
        'max_usuarios' => 'integer',
        'max_segmentos' => 'integer',
        'max_empresas' => 'integer',
    ];

    // Relação com recursos disponíveis neste plano
    public function recursos()
    {
        return $this->belongsToMany(LicencaRecurso::class, 'plano_recurso', 'plano_id', 'recurso_id');
    }

    // Licenças de usuários com este plano
    public function licencasUsuarios()
    {
        return $this->hasMany(LicencaUsuario::class, 'plano_id');
    }

    // Verificar se plano tem recurso específico
    public function hasRecurso($nomeRecurso)
    {
        return $this->recursos()->where('nome', $nomeRecurso)->exists();
    }

    // Verificar se é plano para pessoa física
    public function isPessoaFisica()
    {
        return $this->tipo === 'pessoa_fisica';
    }

    // Verificar se é plano para empresa
    public function isEmpresa()
    {
        return $this->tipo === 'empresa';
    }

    // Verificar se é plano para grupo
    public function isGrupo()
    {
        return $this->tipo === 'grupo';
    }

    // Obter nome formatado com tier
    public function getNomeCompletoAttribute()
    {
        $tiersNomes = [
            'basico' => 'Básico',
            'intermediario' => 'Intermediário',
            'avancado' => 'Avançado'
        ];

        $tierNome = $tiersNomes[$this->tier] ?? ucfirst($this->tier);
        return $this->nome . ' - ' . $tierNome;
    }
}

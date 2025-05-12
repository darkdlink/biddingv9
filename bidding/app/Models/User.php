<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tipo_usuario', // 'pessoa_fisica', 'usuario_master', 'admin_grupo', 'admin_sistema'
        'empresa_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relacionamento com empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Relacionamento com licença
    public function licenca()
    {
        return $this->hasOne(LicencaUsuario::class);
    }

    // Verificar se é admin do sistema
    public function isAdmin()
    {
        return $this->tipo_usuario === 'admin_sistema';
    }

    // Verificar se é Usuário Master
    public function isUsuarioMaster()
    {
        return $this->tipo_usuario === 'usuario_master';
    }

    // Verificar se é Admin de Grupo
    public function isAdminGrupo()
    {
        return $this->tipo_usuario === 'admin_grupo';
    }

    // Verificar se é Pessoa Física
    public function isPessoaFisica()
    {
        return $this->tipo_usuario === 'pessoa_fisica';
    }

    // Verificar se tem acesso a um recurso específico
    public function hasRecurso($recursoNome)
    {
        if (!$this->licenca || !$this->licenca->plano) {
            return false;
        }

        return $this->licenca->plano->recursos()
            ->where('nome', $recursoNome)
            ->exists();
    }

    // Propostas do usuário
    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    // Segmentos que o usuário pode acessar
    public function segmentos()
    {
        return $this->belongsToMany(Segmento::class, 'usuario_segmento');
    }

    // Acompanhamentos criados pelo usuário
    public function acompanhamentos()
    {
        return $this->hasMany(Acompanhamento::class);
    }

    // Alertas do usuário
    public function alertas()
    {
        return $this->hasMany(Alerta::class);
    }

    // Alertas não lidos
    public function alertasNaoLidos()
    {
        return $this->alertas()->where('lido', false);
    }

    // Alertas recentes (últimos 30)
    public function alertasRecentes()
    {
        return $this->alertas()->orderBy('created_at', 'desc')->limit(30);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaRenovacao extends Model
{
    use HasFactory;

    protected $table = 'licenca_renovacoes';

    protected $fillable = [
        'licenca_id',
        'plano_id_anterior',
        'plano_id_novo',
        'ciclo_cobranca_anterior',
        'ciclo_cobranca_novo',
        'status_anterior',
        'status_novo',
        'data_expiracao_anterior',
        'data_expiracao_nova',
        'renovado_por',
    ];

    protected $casts = [
        'data_expiracao_anterior' => 'datetime',
        'data_expiracao_nova' => 'datetime',
    ];

    // Relação com licença
    public function licenca()
    {
        return $this->belongsTo(LicencaUsuario::class, 'licenca_id');
    }

    // Relação com plano anterior
    public function planoAnterior()
    {
        return $this->belongsTo(LicencaPlano::class, 'plano_id_anterior');
    }

    // Relação com plano novo
    public function planoNovo()
    {
        return $this->belongsTo(LicencaPlano::class, 'plano_id_novo');
    }

    // Relação com usuário que renovou
    public function renovadoPor()
    {
        return $this->belongsTo(User::class, 'renovado_por');
    }
}

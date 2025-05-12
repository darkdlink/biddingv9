<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LicencaUsuario extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plano_id',
        'data_inicio',
        'data_expiracao',
        'ciclo_cobranca', // 'mensal', 'anual'
        'status', // 'ativa', 'inativa', 'pendente', 'cancelada'
        'ultimo_pagamento',
        'proximo_pagamento',
    ];

    protected $casts = [
        'data_inicio' => 'datetime',
        'data_expiracao' => 'datetime',
        'ultimo_pagamento' => 'datetime',
        'proximo_pagamento' => 'datetime',
    ];

    // Relação com usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação com plano
    public function plano()
    {
        return $this->belongsTo(LicencaPlano::class, 'plano_id');
    }

    // Renovações da licença
    public function renovacoes()
    {
        return $this->hasMany(LicencaRenovacao::class, 'licenca_id');
    }

    // Verificar se licença está ativa
    public function isAtiva()
    {
        return $this->status === 'ativa' &&
               ($this->data_expiracao === null || $this->data_expiracao > Carbon::now());
    }

    // Verificar se licença está expirada
    public function isExpirada()
    {
        return $this->data_expiracao !== null && $this->data_expiracao < Carbon::now();
    }

    // Verificar se licença está próxima de expirar (30 dias)
    public function isProximaExpirar()
    {
        return $this->data_expiracao !== null &&
               $this->data_expiracao > Carbon::now() &&
               $this->data_expiracao < Carbon::now()->addDays(30);
    }

    // Verificar dias restantes
    public function diasRestantes()
    {
        if (!$this->data_expiracao) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($this->data_expiracao, false));
    }

    // Status formatado para exibição
    public function getStatusFormatadoAttribute()
    {
        $classes = [
            'ativa' => 'success',
            'inativa' => 'danger',
            'pendente' => 'warning',
            'cancelada' => 'secondary'
        ];

        $classe = $classes[$this->status] ?? 'info';

        if ($this->status === 'ativa' && $this->isProximaExpirar()) {
            $classe = 'warning';
        }

        if ($this->status === 'ativa' && $this->isExpirada()) {
            $classe = 'danger';
        }

        return '<span class="badge bg-' . $classe . '">' . ucfirst($this->status) . '</span>';
    }
}

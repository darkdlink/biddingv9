<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sincronizacao extends Model
{
    use HasFactory;

    protected $table = 'sincronizacoes';

    protected $fillable = [
        'tipo', // 'manual', 'agendada'
        'status', // 'em_andamento', 'concluido', 'erro'
        'parametros', // JSON com parâmetros usados na sincronização
        'resultado', // JSON com resultado da sincronização
        'iniciado_por', // ID do usuário que iniciou (se manual)
        'data_inicio',
        'data_fim',
    ];

    protected $casts = [
        'parametros' => 'json',
        'resultado' => 'json',
        'data_inicio' => 'datetime',
        'data_fim' => 'datetime',
    ];

    // Relação com usuário que iniciou a sincronização
    public function iniciadoPor()
    {
        return $this->belongsTo(User::class, 'iniciado_por');
    }

    // Verificar se a sincronização foi bem-sucedida
    public function isSuccess()
    {
        return $this->status === 'concluido';
    }

    // Verificar se a sincronização foi manual
    public function isManual()
    {
        return $this->tipo === 'manual';
    }

    // Verificar se a sincronização foi agendada
    public function isAgendada()
    {
        return $this->tipo === 'agendada';
    }

    // Verificar se a sincronização está em andamento
    public function isEmAndamento()
    {
        return $this->status === 'em_andamento';
    }

    // Verificar duração da sincronização
    public function getDuracaoAttribute()
    {
        if ($this->data_inicio && $this->data_fim) {
            return $this->data_fim->diffInSeconds($this->data_inicio);
        }

        return null;
    }

    // Formatar duração para exibição
    public function getDuracaoFormatadaAttribute()
    {
        $duracao = $this->duracao;

        if ($duracao === null) {
            return 'Em andamento';
        }

        $minutos = floor($duracao / 60);
        $segundos = $duracao % 60;

        return sprintf('%02d:%02d', $minutos, $segundos);
    }
}

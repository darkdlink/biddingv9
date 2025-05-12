<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acompanhamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'licitacao_id',
        'user_id',
        'titulo',
        'descricao',
        'data_evento',
        'tipo', // 'anotacao', 'lembrete', 'alteracao', 'documento', 'outro'
        'is_public',
    ];

    protected $casts = [
        'data_evento' => 'datetime',
        'is_public' => 'boolean',
    ];

    // Relação com licitação
    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    // Relação com usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Obter ícone baseado no tipo
    public function getIconeAttribute()
    {
        switch ($this->tipo) {
            case 'anotacao':
                return 'bi-journal-text';
            case 'lembrete':
                return 'bi-alarm';
            case 'alteracao':
                return 'bi-pencil-square';
            case 'documento':
                return 'bi-file-earmark';
            default:
                return 'bi-info-circle';
        }
    }

    // Tipo formatado para exibição
    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'anotacao' => 'Anotação',
            'lembrete' => 'Lembrete',
            'alteracao' => 'Alteração',
            'documento' => 'Documento',
            'outro' => 'Outro'
        ];

        return $tipos[$this->tipo] ?? ucfirst($this->tipo);
    }
}

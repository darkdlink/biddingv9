<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'licitacao_id',
        'titulo',
        'conteudo',
        'lido',
        'data_leitura',
    ];

    protected $casts = [
        'lido' => 'boolean',
        'data_leitura' => 'datetime',
    ];

    // Relação com usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação com licitação
    public function licitacao()
    {
        return $this->belongsTo(Licitacao::class);
    }

    // Verificar se é lido
    public function isLido()
    {
        return $this->lido;
    }

// Marcar como lido
    public function marcarComoLido()
    {
        $this->lido = true;
        $this->data_leitura = now();
        $this->save();

        return $this;
    }

    // Formatar data de criação para exibição
    public function getDataFormatadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i');
    }

    // Resumo do conteúdo
    public function getConteudoResumidoAttribute()
    {
        return strlen($this->conteudo) > 100 ?
            substr($this->conteudo, 0, 100) . '...' :
            $this->conteudo;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'licitacao_id',
        'user_id',
        'titulo',
        'valor_proposta',
        'status', // 'rascunho', 'submetida', 'vencedora', 'perdedora', 'cancelada'
        'data_submissao',
        'conteudo',
        'observacoes',
    ];

    protected $casts = [
        'valor_proposta' => 'decimal:2',
        'data_submissao' => 'datetime',
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

    // Arquivos anexados à proposta
    public function arquivos()
    {
        return $this->hasMany(PropostaArquivo::class);
    }

    // Histórico de versões da proposta
    public function versoes()
    {
        return $this->hasMany(PropostaVersao::class)->orderBy('versao', 'desc');
    }

    // Verificar se proposta está em rascunho
    public function isRascunho()
    {
        return $this->status === 'rascunho';
    }

    // Verificar se proposta já foi submetida
    public function isSubmetida()
    {
        return in_array($this->status, ['submetida', 'vencedora', 'perdedora']);
    }

    // Verificar se proposta foi vencedora
    public function isVencedora()
    {
        return $this->status === 'vencedora';
    }

    // Valor formatado para exibição
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_proposta, 2, ',', '.');
    }

    // Status formatado para exibição
    public function getStatusFormatadoAttribute()
    {
        $classes = [
            'rascunho' => 'secondary',
            'submetida' => 'primary',
            'vencedora' => 'success',
            'perdedora' => 'danger',
            'cancelada' => 'warning'
        ];

        $classe = $classes[$this->status] ?? 'info';

        return '<span class="badge bg-' . $classe . '">' . ucfirst($this->status) . '</span>';
    }
}

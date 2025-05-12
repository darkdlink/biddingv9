<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Licitacao extends Model
{
    use HasFactory;
    protected $table = 'licitacoes';

    protected $fillable = [
        'numero_controle_pncp',
        'orgao_entidade',
        'unidade_orgao',
        'ano_compra',
        'sequencial_compra',
        'numero_compra',
        'objeto_compra',
        'modalidade_nome',
        'modo_disputa_nome',
        'valor_total_estimado',
        'situacao_compra_nome',
        'data_inclusao',
        'data_publicacao_pncp',
        'data_abertura_proposta',
        'data_encerramento_proposta',
        'link_sistema_origem',
        'is_srp',
        'uf',
        'municipio',
        'cnpj',
        'analisada',
        'interesse',
    ];

    protected $casts = [
        'data_inclusao' => 'datetime',
        'data_publicacao_pncp' => 'datetime',
        'data_abertura_proposta' => 'datetime',
        'data_encerramento_proposta' => 'datetime',
        'valor_total_estimado' => 'decimal:2',
        'is_srp' => 'boolean',
        'analisada' => 'boolean',
        'interesse' => 'boolean',
    ];

    // Relação com propostas
    public function propostas()
    {
        return $this->hasMany(Proposta::class);
    }

    // Relação com segmentos relevantes
    public function segmentos()
    {
        return $this->belongsToMany(Segmento::class, 'licitacao_segmento')
                    ->withPivot('relevancia')
                    ->withTimestamps();
    }

    // Relação com acompanhamentos da licitação
    public function acompanhamentos()
    {
        return $this->hasMany(Acompanhamento::class);
    }

    // Verificar se licitação está aberta
    public function isAberta()
    {
        return $this->data_encerramento_proposta > Carbon::now();
    }

    // Verificar quantos dias restam para encerramento
    public function diasRestantes()
    {
        if (!$this->data_encerramento_proposta) {
            return null;
        }

        return max(0, Carbon::now()->diffInDays($this->data_encerramento_proposta, false));
    }

    // Obter status formatado
    public function getStatusFormatadoAttribute()
    {
        if (!$this->isAberta()) {
            return '<span class="badge bg-danger">Encerrada</span>';
        }

        $dias = $this->diasRestantes();

        if ($dias <= 3) {
            return '<span class="badge bg-warning">Urgente - ' . $dias . ' dias</span>';
        }

        return '<span class="badge bg-success">Aberta - ' . $dias . ' dias</span>';
    }

    // Analisar relevância para todos os segmentos
    public function analisarRelevancia($segmentos)
    {
        foreach ($segmentos as $segmento) {
            $relevancia = $segmento->verificarRelevancia($this);

            if ($relevancia > 0) {
                $this->segmentos()->attach($segmento->id, ['relevancia' => $relevancia]);
            }
        }
    }

    // Valor formatado para exibição
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total_estimado, 2, ',', '.');
    }

    // Objeto resumido para exibição em listas
    public function getObjetoResumidoAttribute()
    {
        return strlen($this->objeto_compra) > 100 ?
            substr($this->objeto_compra, 0, 100) . '...' :
            $this->objeto_compra;
    }
}

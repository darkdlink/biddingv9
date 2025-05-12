<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segmento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'palavras_chave',
        'user_id',
        'empresa_id',
    ];

    protected $casts = [
        'palavras_chave' => 'array',
    ];

    // Relação com usuário (se for segmento pessoal)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação com empresa (se for segmento da empresa)
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // Licitações relacionadas a este segmento
    public function licitacoes()
    {
        return $this->belongsToMany(Licitacao::class, 'licitacao_segmento')
                    ->withPivot('relevancia')
                    ->withTimestamps();
    }

    // Usuários que podem acessar este segmento
    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_segmento');
    }

    // Verificar relevância da licitação para este segmento
    public function verificarRelevancia($licitacao)
    {
        $pontuacao = 0;
        $texto = strtolower($licitacao->objeto_compra . ' ' . $licitacao->orgao_entidade);

        foreach ($this->palavras_chave as $palavra) {
            if (strpos($texto, strtolower($palavra)) !== false) {
                $pontuacao += 1;
            }
        }

        return $pontuacao;
    }

    // Palavras-chave formatadas para exibição
    public function getPalavrasChaveFormatadaAttribute()
    {
        return implode(', ', $this->palavras_chave);
    }
}

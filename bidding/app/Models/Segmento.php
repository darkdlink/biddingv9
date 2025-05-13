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

    /**
     * Relação com usuário (se for segmento pessoal)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relação com empresa (se for segmento da empresa)
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relação com licitações relacionadas a este segmento
     */
    public function licitacoes()
    {
        return $this->belongsToMany(Licitacao::class, 'licitacao_segmento')
            ->withPivot('relevancia')
            ->withTimestamps();
    }

    /**
     * Relação com usuários que têm acesso a este segmento
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_segmento')
            ->withTimestamps();
    }

    /**
     * Verificar relevância da licitação para este segmento
     */
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

    /**
     * Determina se o segmento pertence a um usuário específico ou à sua empresa
     */
    public function pertenceAoUsuario($user)
    {
        if ($this->user_id == $user->id) {
            return true;
        }

        if ($this->empresa_id && $user->empresa_id == $this->empresa_id) {
            return true;
        }

        return false;
    }

    /**
     * Retorna o nome do proprietário do segmento (usuário ou empresa)
     */
    public function getProprietarioAttribute()
    {
        if ($this->user_id) {
            return $this->user ? $this->user->name : 'Usuário desconhecido';
        }

        if ($this->empresa_id) {
            return $this->empresa ? $this->empresa->nome : 'Empresa desconhecida';
        }

        return 'Desconhecido';
    }

    /**
     * Retorna o tipo de segmento (pessoal ou empresa)
     */
    public function getTipoAttribute()
    {
        if ($this->user_id) {
            return 'pessoal';
        }

        if ($this->empresa_id) {
            return 'empresa';
        }

        return 'desconhecido';
    }
}

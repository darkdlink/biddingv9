<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropostaVersao extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposta_id',
        'versao',
        'conteudo',
        'valor_proposta',
        'user_id',
    ];

    protected $casts = [
        'valor_proposta' => 'decimal:2',
    ];

    // Relação com proposta
    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }

    // Relação com usuário que criou a versão
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Valor formatado para exibição
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_proposta, 2, ',', '.');
    }
}

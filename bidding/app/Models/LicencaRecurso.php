<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicencaRecurso extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
    ];

    // Relação com planos que possuem este recurso
    public function planos()
    {
        return $this->belongsToMany(LicencaPlano::class, 'plano_recurso', 'recurso_id', 'plano_id');
    }
}

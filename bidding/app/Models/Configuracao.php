<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;

    protected $table = 'configuracoes';

    protected $fillable = [
        'chave',
        'valor',
        'grupo',
        'descricao',
        'editavel',
    ];

    /**
     * Obter o valor de uma configuração pelo nome da chave
     *
     * @param string $chave
     * @param mixed $valorPadrao
     * @return mixed
     */
    public static function obterValor($chave, $valorPadrao = null)
    {
        $config = static::where('chave', $chave)->first();

        if (!$config) {
            return $valorPadrao;
        }

        return $config->valor;
    }

    /**
     * Alias para obterValor - para compatibilidade
     *
     * @param string $chave
     * @param mixed $valorPadrao
     * @return mixed
     */
    public static function obter($chave, $valorPadrao = null)
    {
        return static::obterValor($chave, $valorPadrao);
    }

    /**
     * Definir o valor de uma configuração
     *
     * @param string $chave
     * @param mixed $valor
     * @return bool
     */
    public static function definirValor($chave, $valor)
    {
        $config = static::where('chave', $chave)->first();

        if (!$config) {
            return static::create([
                'chave' => $chave,
                'valor' => $valor,
                'grupo' => 'sistema',
                'editavel' => true,
            ]);
        }

        $config->valor = $valor;
        return $config->save();
    }
}

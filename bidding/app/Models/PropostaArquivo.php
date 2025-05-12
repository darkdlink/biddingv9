<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PropostaArquivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'proposta_id',
        'nome',
        'caminho',
        'tipo_mime',
        'tamanho',
    ];

    // Relação com proposta
    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }

    // Obter URL para download
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->caminho);
    }

    // Obter tamanho formatado
    public function getTamanhoFormatadoAttribute()
    {
        $bytes = $this->tamanho;

        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < 1048576) {
            return round($bytes / 1024, 2) . ' KB';
        } elseif ($bytes < 1073741824) {
            return round($bytes / 1048576, 2) . ' MB';
        } else {
            return round($bytes / 1073741824, 2) . ' GB';
        }
    }

    // Obter ícone baseado no tipo MIME
    public function getIconeAttribute()
    {
        $tipo = explode('/', $this->tipo_mime)[0];
        $extensao = explode('/', $this->tipo_mime)[1] ?? '';

        switch ($tipo) {
            case 'image':
                return 'bi-file-image';
            case 'application':
                if (in_array($extensao, ['pdf'])) {
                    return 'bi-file-pdf';
                } elseif (in_array($extensao, ['msword', 'vnd.openxmlformats-officedocument.wordprocessingml.document', 'vnd.oasis.opendocument.text'])) {
                    return 'bi-file-word';
                } elseif (in_array($extensao, ['vnd.ms-excel', 'vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'vnd.oasis.opendocument.spreadsheet'])) {
                    return 'bi-file-excel';
                } elseif (in_array($extensao, ['zip', 'x-rar-compressed', 'x-7z-compressed'])) {
                    return 'bi-file-zip';
                }
                return 'bi-file-earmark';
            case 'text':
                return 'bi-file-text';
            case 'video':
                return 'bi-file-play';
            case 'audio':
                return 'bi-file-music';
            default:
                return 'bi-file';
        }
    }
}

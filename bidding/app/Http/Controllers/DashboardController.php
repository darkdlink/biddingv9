<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Licitacao;
use App\Models\Proposta;
use App\Models\Segmento;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Licitações de interesse
        $licitacoesInteresse = Licitacao::where('interesse', true)
            ->where('data_encerramento_proposta', '>=', now())
            ->orderBy('data_encerramento_proposta')
            ->take(5)
            ->get();

        // Propostas recentes
        $propostasRecentes = Proposta::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // Licitações relevantes para os segmentos do usuário
        $segmentosIds = $user->segmentos->pluck('id')->toArray();

        $licitacoesRelevantes = Licitacao::whereHas('segmentos', function($query) use ($segmentosIds) {
                $query->whereIn('segmento_id', $segmentosIds);
            })
            ->where('data_encerramento_proposta', '>=', now())
            ->orderBy('data_encerramento_proposta')
            ->take(5)
            ->get();

        // Contadores
        $licitacoesCount = Licitacao::where('data_encerramento_proposta', '>=', now())->count();
        $licitacoesInteresseCount = Licitacao::where('interesse', true)->count();
        $propostasCount = Proposta::where('user_id', $user->id)->count();
        $segmentosCount = $user->segmentos->count();

        // Alertas não lidos
        $alertasNaoLidos = $user->alertasNaoLidos()->count();

        return view('dashboard.index', [
            'licitacoesInteresse' => $licitacoesInteresse,
            'propostasRecentes' => $propostasRecentes,
            'licitacoesRelevantes' => $licitacoesRelevantes,
            'licitacoesCount' => $licitacoesCount,
            'licitacoesInteresseCount' => $licitacoesInteresseCount,
            'propostasCount' => $propostasCount,
            'segmentosCount' => $segmentosCount,
            'alertasNaoLidos' => $alertasNaoLidos
        ]);
    }
}

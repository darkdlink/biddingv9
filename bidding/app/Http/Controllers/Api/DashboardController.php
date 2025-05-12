<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Licitacao;
use App\Models\Proposta;
use App\Models\Alerta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Licitações de interesse ainda válidas
        $licitacoesInteresse = Licitacao::where('interesse', true)
            ->where('data_encerramento_proposta', '>=', now())
            ->orderBy('data_encerramento_proposta')
            ->limit(5)
            ->get();

        // Propostas recentes
        $propostasRecentes = Proposta::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        // Alertas não lidos
        $alertasNaoLidos = Alerta::where('user_id', $user->id)
            ->where('lido', false)
            ->count();

        // Próximas licitações a encerrar
        $proximasLicitacoes = Licitacao::where('data_encerramento_proposta', '>=', now())
            ->where('data_encerramento_proposta', '<=', now()->addDays(7))
            ->whereHas('segmentos', function($query) use ($user) {
                $query->whereIn('segmento_id', $user->segmentos->pluck('id'));
            })
            ->orderBy('data_encerramento_proposta')
            ->limit(5)
            ->get();

        // Estatísticas para gráficos
        $estatisticas = [
            'licitacoes_por_segmento' => $this->getLicitacoesPorSegmento($user),
            'propostas_por_status' => $this->getPropostasPorStatus($user),
            'licitacoes_por_mes' => $this->getLicitacoesPorMes(),
        ];

        // Estatísticas de cards
        $stats = [
            'total_licitacoes' => Licitacao::whereHas('segmentos', function($query) use ($user) {
                $query->whereIn('segmento_id', $user->segmentos->pluck('id'));
            })->count(),
            'total_propostas' => Proposta::where('user_id', $user->id)->count(),
            'propostas_vencedoras' => Proposta::where('user_id', $user->id)
                ->where('status', 'vencedora')
                ->count(),
            'licitacoes_interesse' => Licitacao::where('interesse', true)->count(),
            'valor_total_propostas' => Proposta::where('user_id', $user->id)
                ->where('status', 'vencedora')
                ->sum('valor_proposta'),
        ];

        return response()->json([
            'licitacoes_interesse' => $licitacoesInteresse,
            'propostas_recentes' => $propostasRecentes,
            'alertas_nao_lidos' => $alertasNaoLidos,
            'proximas_licitacoes' => $proximasLicitacoes,
            'estatisticas' => $estatisticas,
            'stats' => $stats,
        ]);
    }

    // Método auxiliar para obter licitações por segmento
    private function getLicitacoesPorSegmento($user)
    {
        $resultado = [];
        $segmentos = $user->segmentos;

        foreach ($segmentos as $segmento) {
            $count = $segmento->licitacoes()
                ->where('data_encerramento_proposta', '>=', now())
                ->count();

            $resultado[] = [
                'nome' => $segmento->nome,
                'total' => $count,
            ];
        }

        return $resultado;
    }

    // Método auxiliar para obter propostas por status
    private function getPropostasPorStatus($user)
    {
        $resultado = [];

        $statusList = ['rascunho', 'submetida', 'vencedora', 'perdedora', 'cancelada'];

        foreach ($statusList as $status) {
            $count = Proposta::where('user_id', $user->id)
                ->where('status', $status)
                ->count();

            $resultado[] = [
                'status' => ucfirst($status),
                'total' => $count,
            ];
        }

        return $resultado;
    }

    // Método auxiliar para obter licitações por mês (últimos 6 meses)
    private function getLicitacoesPorMes()
    {
        $resultado = [];

        // Obter últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $data = Carbon::now()->subMonths($i);
            $mesAno = $data->format('m/Y');
            $inicio = Carbon::now()->subMonths($i)->startOfMonth();
            $fim = Carbon::now()->subMonths($i)->endOfMonth();

            $count = Licitacao::whereBetween('data_publicacao_pncp', [$inicio, $fim])
                ->count();

            $resultado[] = [
                'mes' => $mesAno,
                'total' => $count,
            ];
        }

        return $resultado;
    }

    // Obter alertas do usuário
    public function alertas(Request $request)
    {
        $user = Auth::user();

        $query = Alerta::where('user_id', $user->id)
            ->with('licitacao');

        // Filtrar apenas não lidos se solicitado
        if ($request->has('nao_lidos') && $request->nao_lidos) {
            $query->where('lido', false);
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginação
        $perPage = $request->get('per_page', 10);
        $alertas = $query->paginate($perPage);

        return response()->json($alertas);
    }

    // Marcar alerta como lido
    public function marcarAlertaLido($id)
    {
        $alerta = Alerta::findOrFail($id);

        // Verificar se pertence ao usuário
        if ($alerta->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Você não tem permissão para acessar este alerta'
            ], 403);
        }

        $alerta->lido = true;
        $alerta->data_leitura = now();
        $alerta->save();

        return response()->json([
            'success' => true,
            'message' => 'Alerta marcado como lido',
            'alerta' => $alerta
        ]);
    }

    // Marcar todos os alertas como lidos
    public function marcarTodosAlertasLidos()
    {
        $user = Auth::user();

        Alerta::where('user_id', $user->id)
            ->where('lido', false)
            ->update([
                'lido' => true,
                'data_leitura' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Todos os alertas foram marcados como lidos'
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Licitacao;
use App\Models\Proposta;
use App\Models\LicencaUsuario;
use App\Models\Empresa;
use App\Models\Grupo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Métricas de Usuários
        $totalUsuarios = User::count();
        $usuariosAtivos = User::where('is_active', true)->count();
        $usuariosPorTipo = User::select('tipo_usuario', DB::raw('count(*) as total'))
            ->groupBy('tipo_usuario')
            ->pluck('total', 'tipo_usuario')
            ->toArray();

        // Métricas de Licenças
        $licencasAtivas = LicencaUsuario::where('status', 'ativa')
            ->where(function($query) {
                $query->whereNull('data_expiracao')
                      ->orWhere('data_expiracao', '>', now());
            })
            ->count();

        $licencasPorPlano = LicencaUsuario::join('licenca_planos', 'licenca_usuarios.plano_id', '=', 'licenca_planos.id')
            ->select('licenca_planos.nome', DB::raw('count(*) as total'))
            ->groupBy('licenca_planos.nome')
            ->pluck('total', 'licenca_planos.nome')
            ->toArray();

        $licencasAExpirar = LicencaUsuario::where('status', 'ativa')
            ->whereNotNull('data_expiracao')
            ->whereBetween('data_expiracao', [now(), now()->addDays(30)])
            ->count();

        // Métricas de Licitações
        $totalLicitacoes = Licitacao::count();
        $licitacoesAbertas = Licitacao::where('data_encerramento_proposta', '>', now())->count();
        $licitacoesInteresse = Licitacao::where('interesse', true)->count();

        $licitacoesPorUF = Licitacao::select('uf', DB::raw('count(*) as total'))
            ->whereNotNull('uf')
            ->groupBy('uf')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->pluck('total', 'uf')
            ->toArray();

        // Métricas de Empresas e Grupos
        $totalEmpresas = Empresa::count();
        $totalGrupos = Grupo::count();

        // Métricas de Faturamento
        $fatMensal = LicencaUsuario::where('ciclo_cobranca', 'mensal')
            ->join('licenca_planos', 'licenca_usuarios.plano_id', '=', 'licenca_planos.id')
            ->sum(DB::raw('licenca_planos.preco_mensal'));

        $fatAnual = LicencaUsuario::where('ciclo_cobranca', 'anual')
            ->join('licenca_planos', 'licenca_usuarios.plano_id', '=', 'licenca_planos.id')
            ->sum(DB::raw('licenca_planos.preco_anual / 12')); // Convertendo para mensal

        $faturamentoMensal = $fatMensal + $fatAnual;

        // Usuários mais recentes
        $usuariosRecentes = User::orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Licenças a expirar em breve
        $licenciasPrioridadeRenovacao = LicencaUsuario::with(['user', 'plano'])
            ->where('status', 'ativa')
            ->whereNotNull('data_expiracao')
            ->whereBetween('data_expiracao', [now(), now()->addDays(15)])
            ->orderBy('data_expiracao')
            ->take(10)
            ->get();

        return view('admin.dashboard', [
            'totalUsuarios' => $totalUsuarios,
            'usuariosAtivos' => $usuariosAtivos,
            'usuariosPorTipo' => $usuariosPorTipo,
            'licencasAtivas' => $licencasAtivas,
            'licencasPorPlano' => $licencasPorPlano,
            'licencasAExpirar' => $licencasAExpirar,
            'totalLicitacoes' => $totalLicitacoes,
            'licitacoesAbertas' => $licitacoesAbertas,
            'licitacoesInteresse' => $licitacoesInteresse,
            'licitacoesPorUF' => $licitacoesPorUF,
            'totalEmpresas' => $totalEmpresas,
            'totalGrupos' => $totalGrupos,
            'faturamentoMensal' => $faturamentoMensal,
            'usuariosRecentes' => $usuariosRecentes,
            'licenciasPrioridadeRenovacao' => $licenciasPrioridadeRenovacao
        ]);
    }

    public function configuracoes()
    {
        $configuracoes = [
            'sincronizacao_automatica' => config('app.sincronizacao_automatica', true),
            'hora_sincronizacao' => config('app.hora_sincronizacao', '07:00'),
            'maximo_licitacoes_sync' => config('app.maximo_licitacoes_sync', 200),
            'dias_manter_licitacoes' => config('app.dias_manter_licitacoes', 180),
        ];

        return view('admin.configuracoes', [
            'configuracoes' => $configuracoes
        ]);
    }

    public function salvarConfiguracoes(Request $request)
    {
        $request->validate([
            'sincronizacao_automatica' => 'required|boolean',
            'hora_sincronizacao' => 'required|date_format:H:i',
            'maximo_licitacoes_sync' => 'required|integer|min:10|max:1000',
            'dias_manter_licitacoes' => 'required|integer|min:30|max:365',
        ]);

        try {
            // Atualizar configurações no .env ou no banco de dados
            // (Aqui você precisaria implementar a lógica de salvar as configurações)

            return redirect()->route('admin.configuracoes')
                ->with('success', 'Configurações atualizadas com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao salvar configurações: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LicencaController extends Controller
{
    /**
     * Exibe a página de renovação de licença.
     *
     * @return \Illuminate\View\View
     */
    public function renovar()
    {
        $user = Auth::user();
        $licenca = $user->licenca()->with('plano')->first();

        if (!$licenca) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não possui uma licença.');
        }

        // Verificar se licença já está ativa
        if ($licenca->isAtiva() && !$licenca->isProximaExpirar()) {
            return redirect()->route('dashboard')
                ->with('info', 'Sua licença está ativa e válida até ' . $licenca->data_expiracao->format('d/m/Y') . '.');
        }

        // Buscar planos compatíveis para upgrade
        $planos = \App\Models\LicencaPlano::where('tipo', $licenca->plano->tipo)
                                         ->orderBy('preco_mensal')
                                         ->get();

        return view('auth.renovar-licenca', [
            'user' => $user,
            'licenca' => $licenca,
            'planos' => $planos
        ]);
    }

    /**
     * Processa a solicitação de renovação de licença.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processar(Request $request)
    {
        $request->validate([
            'plano_id' => 'required|exists:licenca_planos,id',
            'ciclo_cobranca' => 'required|in:mensal,anual',
        ]);

        $user = Auth::user();
        $licenca = $user->licenca;

        if (!$licenca) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não possui uma licença.');
        }

        // Verificar se o plano é compatível
        $novoPlano = \App\Models\LicencaPlano::findOrFail($request->plano_id);
        $planoAtual = $licenca->plano;

        if ($novoPlano->tipo !== $planoAtual->tipo) {
            return back()->withInput()->with('error', 'O plano selecionado não é compatível com seu tipo de usuário.');
        }

        // Salvar plano e ciclo escolhidos em sessão para uso no checkout
        session([
            'renovacao_plano_id' => $novoPlano->id,
            'renovacao_ciclo' => $request->ciclo_cobranca,
            'renovacao_licenca_id' => $licenca->id,
        ]);

        // Redirecionar para página de checkout
        return redirect()->route('checkout.renovacao');
    }

    /**
     * Simulação de finalização de pagamento e ativação da licença
     * Na implementação real, isso seria chamado após confirmação do gateway de pagamento
     */
    public function ativar($licencaId)
    {
        // Em produção, isso seria protegido por webhook ou outro mecanismo seguro
        $licenca = \App\Models\LicencaUsuario::findOrFail($licencaId);

        // Verificar se é o próprio usuário ou um admin
        if (Auth::id() !== $licenca->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Ação não autorizada.');
        }

        try {
            // Atualizar licença
            $licenca->status = 'ativa';
            $licenca->ultimo_pagamento = now();

            if ($licenca->ciclo_cobranca === 'mensal') {
                $licenca->data_expiracao = Carbon::now()->addMonth();
            } else {
                $licenca->data_expiracao = Carbon::now()->addYear();
            }

            $licenca->proximo_pagamento = $licenca->data_expiracao;
            $licenca->save();

            return redirect()->route('dashboard')
                ->with('success', 'Licença ativada com sucesso! Válida até ' . $licenca->data_expiracao->format('d/m/Y') . '.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao ativar licença: ' . $e->getMessage());
        }
    }
}

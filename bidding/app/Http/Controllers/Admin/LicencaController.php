<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LicencaUsuario;
use App\Models\LicencaPlano;
use App\Models\User;
use Carbon\Carbon;

class LicencaController extends Controller
{
    public function index(Request $request)
    {
        $query = LicencaUsuario::with(['user', 'plano']);

        // Filtros
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('plano_id') && !empty($request->plano_id)) {
            $query->where('plano_id', $request->plano_id);
        }

        if ($request->has('ciclo') && !empty($request->ciclo)) {
            $query->where('ciclo_cobranca', $request->ciclo);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'user') {
            $query->join('users', 'licenca_usuarios.user_id', '=', 'users.id')
                  ->orderBy('users.name', $sortDirection)
                  ->select('licenca_usuarios.*');
        } elseif ($sortField === 'plano') {
            $query->join('licenca_planos', 'licenca_usuarios.plano_id', '=', 'licenca_planos.id')
                  ->orderBy('licenca_planos.nome', $sortDirection)
                  ->select('licenca_usuarios.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $licencas = $query->paginate(15);
        $planos = LicencaPlano::all();

        return view('admin.licencas.index', [
            'licencas' => $licencas,
            'planos' => $planos,
            'filtros' => $request->all()
        ]);
    }

    public function create()
    {
        $planos = LicencaPlano::all();
        $usuarios = User::where('is_active', true)->get();

        return view('admin.licencas.create', [
            'planos' => $planos,
            'usuarios' => $usuarios
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plano_id' => 'required|exists:licenca_planos,id',
            'ciclo_cobranca' => 'required|in:mensal,anual',
            'status' => 'required|in:ativa,inativa,pendente,cancelada',
            'data_inicio' => 'required|date',
            'data_expiracao' => 'nullable|date|after:data_inicio',
        ]);

        try {
            // Verificar se usuário já possui licença
            $existingLicenca = LicencaUsuario::where('user_id', $request->user_id)->first();

            if ($existingLicenca) {
                return back()->withInput()
                    ->with('error', 'Este usuário já possui uma licença. Edite a licença existente ou remova-a primeiro.');
            }

            // Criar licença
            $licenca = LicencaUsuario::create([
                'user_id' => $request->user_id,
                'plano_id' => $request->plano_id,
                'ciclo_cobranca' => $request->ciclo_cobranca,
                'status' => $request->status,
                'data_inicio' => Carbon::parse($request->data_inicio),
                'data_expiracao' => $request->data_expiracao ? Carbon::parse($request->data_expiracao) : null,
                'ultimo_pagamento' => now(),
                'proximo_pagamento' => $request->data_expiracao ? Carbon::parse($request->data_expiracao) : null,
            ]);

            return redirect()->route('admin.licencas.index')
                ->with('success', 'Licença criada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar licença: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $licenca = LicencaUsuario::with(['user', 'plano'])->findOrFail($id);

        return view('admin.licencas.show', [
            'licenca' => $licenca
        ]);
    }

    public function edit($id)
    {
        $licenca = LicencaUsuario::with(['user', 'plano'])->findOrFail($id);
        $planos = LicencaPlano::all();

        return view('admin.licencas.edit', [
            'licenca' => $licenca,
            'planos' => $planos
        ]);
    }

    public function update(Request $request, $id)
    {
        $licenca = LicencaUsuario::findOrFail($id);

        $request->validate([
            'plano_id' => 'required|exists:licenca_planos,id',
            'ciclo_cobranca' => 'required|in:mensal,anual',
            'status' => 'required|in:ativa,inativa,pendente,cancelada',
            'data_expiracao' => 'nullable|date|after:data_inicio',
        ]);

        try {
            $licenca->update([
                'plano_id' => $request->plano_id,
                'ciclo_cobranca' => $request->ciclo_cobranca,
                'status' => $request->status,
                'data_expiracao' => $request->data_expiracao ? Carbon::parse($request->data_expiracao) : null,
                'proximo_pagamento' => $request->data_expiracao ? Carbon::parse($request->data_expiracao) : null,
            ]);

            return redirect()->route('admin.licencas.show', $licenca->id)
                ->with('success', 'Licença atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar licença: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $licenca = LicencaUsuario::findOrFail($id);

        try {
            $licenca->delete();

            return redirect()->route('admin.licencas.index')
                ->with('success', 'Licença removida com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao remover licença: ' . $e->getMessage());
        }
    }

    public function renovar($id)
    {
        $licenca = LicencaUsuario::findOrFail($id);

        try {
            // Calcular nova data de expiração
            $novaExpiracao = null;

            if ($licenca->ciclo_cobranca === 'mensal') {
                $novaExpiracao = $licenca->data_expiracao && $licenca->data_expiracao > now()
                               ? $licenca->data_expiracao->addMonth()
                               : now()->addMonth();
            } else {
                $novaExpiracao = $licenca->data_expiracao && $licenca->data_expiracao > now()
                               ? $licenca->data_expiracao->addYear()
                               : now()->addYear();
            }

            $licenca->update([
                'status' => 'ativa',
                'data_expiracao' => $novaExpiracao,
                'ultimo_pagamento' => now(),
                'proximo_pagamento' => $novaExpiracao,
]);

            return redirect()->route('admin.licencas.show', $licenca->id)
                ->with('success', 'Licença renovada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao renovar licença: ' . $e->getMessage());
        }
    }

    public function cancelar($id)
    {
        $licenca = LicencaUsuario::findOrFail($id);

        try {
            $licenca->update([
                'status' => 'cancelada',
            ]);

            return redirect()->route('admin.licencas.show', $licenca->id)
                ->with('success', 'Licença cancelada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao cancelar licença: ' . $e->getMessage());
        }
    }
}

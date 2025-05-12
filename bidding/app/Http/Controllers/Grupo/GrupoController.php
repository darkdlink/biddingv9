<?php

namespace App\Http\Controllers\Grupo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class GrupoController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-grupo');
    }

    public function show()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresa->grupo;

        // Carregar estatísticas do grupo
        $totalEmpresas = $grupo->empresas()->count();
        $totalUsuarios = $grupo->empresas()->withCount('usuarios')->get()->sum('usuarios_count');
        $totalSegmentos = $grupo->empresas()->withCount('segmentos')->get()->sum('segmentos_count');

        // Empresas do grupo
        $empresas = $grupo->empresas()->paginate(10);

        return view('grupo.show', [
            'grupo' => $grupo,
            'totalEmpresas' => $totalEmpresas,
            'totalUsuarios' => $totalUsuarios,
            'totalSegmentos' => $totalSegmentos,
            'empresas' => $empresas
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresa->grupo;

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18',
            'telefone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
        ]);

        try {
            $grupo->update($request->all());

            return redirect()->route('grupo.show')
                ->with('success', 'Informações do grupo atualizadas com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar informações do grupo: ' . $e->getMessage());
        }
    }

    public function empresas()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresa->grupo;
        $empresas = $grupo->empresas()->paginate(15);

        return view('grupo.empresas.index', [
            'grupo' => $grupo,
            'empresas' => $empresas
        ]);
    }

    public function adicionarEmpresa(Request $request)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresa->grupo;

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18',
            'telefone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
        ]);

        try {
            // Verificar limite de empresas pelo plano
            $plano = $user->licenca->plano;
            $empresasAtuais = $grupo->empresas()->count();

            if ($plano->max_empresas && $empresasAtuais >= $plano->max_empresas) {
                return back()->withInput()
                    ->with('error', 'Limite de empresas do seu plano atingido. Faça upgrade para adicionar mais empresas.');
            }

            // Criar empresa
            $novaEmpresa = new Empresa($request->all());
            $novaEmpresa->grupo_id = $grupo->id;
            $novaEmpresa->is_active = true;
            $novaEmpresa->save();

            return redirect()->route('grupo.empresas')
                ->with('success', 'Empresa adicionada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao adicionar empresa: ' . $e->getMessage());
        }
    }

    public function atualizarEmpresa(Request $request, $id)
    {
        $user = Auth::user();
        $empresaUser = $user->empresa;

        if (!$empresaUser || !$empresaUser->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresaUser->grupo;

        // Verificar se empresa pertence ao grupo
        $empresa = Empresa::where('id', $id)
            ->where('grupo_id', $grupo->id)
            ->firstOrFail();

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18',
            'telefone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        try {
            $empresa->fill($request->except('grupo_id'));
            $empresa->is_active = $request->has('is_active');
            $empresa->save();

            return redirect()->route('grupo.empresas')
                ->with('success', 'Empresa atualizada com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar empresa: ' . $e->getMessage());
        }
    }

    public function removerEmpresa($id)
    {
        $user = Auth::user();
        $empresaUser = $user->empresa;

        if (!$empresaUser || !$empresaUser->grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhum grupo empresarial.');
        }

        $grupo = $empresaUser->grupo;

        // Verificar se empresa pertence ao grupo
        $empresa = Empresa::where('id', $id)
            ->where('grupo_id', $grupo->id)
            ->firstOrFail();

        // Impedir remoção da própria empresa do admin de grupo
        if ($empresa->id === $empresaUser->id) {
            return back()->with('error', 'Você não pode remover sua própria empresa.');
        }

        try {
            // Usar transação para garantir integridade
            \DB::beginTransaction();

            // Remover usuários da empresa
            foreach ($empresa->usuarios as $usuario) {
                // Remover licenças
                if ($usuario->licenca) {
                    $usuario->licenca->delete();
                }

                // Remover segmentos pessoais
                $usuario->segmentosProprios()->delete();

                // Desvincular segmentos
                $usuario->segmentos()->detach();

                // Excluir usuário
                $usuario->delete();
            }

            // Remover segmentos da empresa
            $empresa->segmentos()->delete();

            // Excluir empresa
            $empresa->delete();

            \DB::commit();

            return redirect()->route('grupo.empresas')
                ->with('success', 'Empresa removida com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()
                ->with('error', 'Erro ao remover empresa: ' . $e->getMessage());
        }
    }
}

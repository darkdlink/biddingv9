<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class EmpresaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-empresa');
    }

    public function show()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

        // Carregar estatísticas da empresa
        $totalUsuarios = $empresa->usuarios()->count();
        $totalSegmentos = $empresa->segmentos()->count();
        $totalPropostas = $empresa->propostas()->count();

        // Usuários da empresa
        $usuarios = $empresa->usuarios()->paginate(10);

        return view('empresa.show', [
            'empresa' => $empresa,
            'totalUsuarios' => $totalUsuarios,
            'totalSegmentos' => $totalSegmentos,
            'totalPropostas' => $totalPropostas,
            'usuarios' => $usuarios
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

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
            $empresa->update($request->all());

            return redirect()->route('empresa.show')
                ->with('success', 'Informações da empresa atualizadas com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar informações da empresa: ' . $e->getMessage());
        }
    }

    public function usuarios()
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

        $usuarios = $empresa->usuarios()->paginate(15);

        return view('empresa.usuarios.index', [
            'empresa' => $empresa,
            'usuarios' => $usuarios
        ]);
    }

    public function adicionarUsuario(Request $request)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Verificar limite de usuários pelo plano
            $plano = $user->licenca->plano;
            $usuariosAtuais = $empresa->usuarios()->count();

            if ($plano->max_usuarios && $usuariosAtuais >= $plano->max_usuarios) {
                return back()->withInput()
                    ->with('error', 'Limite de usuários do seu plano atingido. Faça upgrade para adicionar mais usuários.');
            }

            // Criar usuário
            $novoUsuario = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'tipo_usuario' => 'pessoa_fisica',
                'empresa_id' => $empresa->id,
                'is_active' => true,
            ]);

            return redirect()->route('empresa.usuarios')
                ->with('success', 'Usuário adicionado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao adicionar usuário: ' . $e->getMessage());
        }
    }

    public function atualizarUsuario(Request $request, $id)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

        // Verificar se usuário pertence à empresa
        $usuario = User::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        try {
            $usuario->name = $request->name;
            $usuario->email = $request->email;

            if ($request->filled('password')) {
                $usuario->password = \Hash::make($request->password);
            }

            $usuario->is_active = $request->has('is_active');
            $usuario->save();

            return redirect()->route('empresa.usuarios')
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    public function removerUsuario($id)
    {
        $user = Auth::user();
        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não está associado a nenhuma empresa.');
        }

        // Verificar se usuário pertence à empresa
        $usuario = User::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        // Impedir remoção do próprio usuário master
        if ($usuario->id === $user->id) {
            return back()->with('error', 'Você não pode remover seu próprio usuário.');
        }

        try {
            // Usar transação para garantir integridade
            \DB::beginTransaction();

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

            \DB::commit();

            return redirect()->route('empresa.usuarios')
                ->with('success', 'Usuário removido com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();

            return back()
                ->with('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }
}

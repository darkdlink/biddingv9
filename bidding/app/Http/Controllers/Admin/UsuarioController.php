<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Grupo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('empresa');

        // Filtros
        if ($request->has('tipo') && !empty($request->tipo)) {
            $query->where('tipo_usuario', $request->tipo);
        }

        if ($request->has('empresa_id') && !empty($request->empresa_id)) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status == 1);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ordenação
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        if ($sortField === 'empresa') {
            $query->join('empresas', 'users.empresa_id', '=', 'empresas.id')
                  ->orderBy('empresas.nome', $sortDirection)
                  ->select('users.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $usuarios = $query->paginate(15);
        $empresas = Empresa::all();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'empresas' => $empresas,
            'filtros' => $request->all()
        ]);
    }

    public function create()
    {
        $empresas = Empresa::all();

        return view('admin.usuarios.create', [
            'empresas' => $empresas
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'tipo_usuario' => 'required|in:pessoa_fisica,usuario_master,admin_grupo,admin_sistema',
            'empresa_id' => 'nullable|required_if:tipo_usuario,usuario_master,admin_grupo|exists:empresas,id',
            'is_active' => 'boolean',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tipo_usuario' => $request->tipo_usuario,
                'empresa_id' => $request->empresa_id,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário criado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao criar usuário: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $usuario = User::with(['empresa', 'licenca.plano', 'propostas', 'segmentos'])
            ->findOrFail($id);

        return view('admin.usuarios.show', [
            'usuario' => $usuario
        ]);
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        $empresas = Empresa::all();

        return view('admin.usuarios.edit', [
            'usuario' => $usuario,
            'empresas' => $empresas
        ]);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'tipo_usuario' => 'required|in:pessoa_fisica,usuario_master,admin_grupo,admin_sistema',
            'empresa_id' => 'nullable|required_if:tipo_usuario,usuario_master,admin_grupo|exists:empresas,id',
            'is_active' => 'boolean',
        ]);

        try {
            $usuario->name = $request->name;
            $usuario->email = $request->email;

            if ($request->filled('password')) {
                $usuario->password = Hash::make($request->password);
            }

            $usuario->tipo_usuario = $request->tipo_usuario;
            $usuario->empresa_id = $request->empresa_id;
            $usuario->is_active = $request->has('is_active') ? $request->is_active : $usuario->is_active;

            $usuario->save();

            return redirect()->route('admin.usuarios.show', $usuario->id)
                ->with('success', 'Usuário atualizado com sucesso!');

        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        // Impedir exclusão do próprio usuário admin
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        try {
            // Usar transação para garantir integridade
            DB::beginTransaction();

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

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                ->with('success', 'Usuário excluído com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Erro ao excluir usuário: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $usuario = User::findOrFail($id);

        // Impedir desativação do próprio usuário admin
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'Você não pode desativar seu próprio usuário.');
        }

        try {
            $usuario->is_active = !$usuario->is_active;
            $usuario->save();

            $status = $usuario->is_active ? 'ativado' : 'desativado';

            return back()->with('success', "Usuário {$status} com sucesso!");

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Erro ao alterar status do usuário: ' . $e->getMessage());
        }
    }
}

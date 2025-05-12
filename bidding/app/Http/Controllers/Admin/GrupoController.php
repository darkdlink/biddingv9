<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grupo;
use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GrupoController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Empresas do grupo
        $empresas = Empresa::where('grupo_id', $grupo->id)
            ->orderBy('nome')
            ->get();

        // Total de usuários no grupo
        $totalUsuarios = User::whereIn('empresa_id', $empresas->pluck('id'))->count();

        // Verificar plano para saber quantas empresas podem ser adicionadas
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteEmpresas = $plano ? $plano->max_empresas : 0;
        $empresasAtuais = $empresas->count();
        $podemAdicionar = $limiteEmpresas > $empresasAtuais;

        return view('grupo.show', compact('grupo', 'empresas', 'totalUsuarios', 'limiteEmpresas', 'empresasAtuais', 'podemAdicionar'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => ['nullable', 'string', 'max:18', Rule::unique('grupos')->ignore($grupo->id)],
            'telefone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('grupos')->ignore($grupo->id)],
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
        ]);

        $grupo->update($request->all());

        return redirect()->route('grupo.show')
            ->with('success', 'Dados do grupo atualizados com sucesso!');
    }

    public function empresas()
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Empresas do grupo
        $empresas = Empresa::where('grupo_id', $grupo->id)
            ->orderBy('nome')
            ->paginate(10);

        // Verificar plano para saber quantas empresas podem ser adicionadas
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteEmpresas = $plano ? $plano->max_empresas : 0;
        $empresasAtuais = Empresa::where('grupo_id', $grupo->id)->count();
        $podemAdicionar = $limiteEmpresas > $empresasAtuais;

        return view('grupo.empresas.index', compact('empresas', 'grupo', 'limiteEmpresas', 'empresasAtuais', 'podemAdicionar'));
    }

    public function adicionarEmpresa()
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Verificar plano para saber se pode adicionar mais empresas
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteEmpresas = $plano ? $plano->max_empresas : 0;
        $empresasAtuais = Empresa::where('grupo_id', $grupo->id)->count();

        if ($limiteEmpresas <= $empresasAtuais) {
            return redirect()->route('grupo.empresas')
                ->with('error', 'Você atingiu o limite de empresas do seu plano. Considere fazer um upgrade para adicionar mais empresas.');
        }

        return view('grupo.empresas.create', compact('grupo'));
    }

    public function salvarEmpresa(Request $request)
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Verificar plano para saber se pode adicionar mais empresas
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteEmpresas = $plano ? $plano->max_empresas : 0;
        $empresasAtuais = Empresa::where('grupo_id', $grupo->id)->count();

        if ($limiteEmpresas <= $empresasAtuais) {
            return redirect()->route('grupo.empresas')
                ->with('error', 'Você atingiu o limite de empresas do seu plano. Considere fazer um upgrade para adicionar mais empresas.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:empresas',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:empresas',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'usuario_master_nome' => 'required|string|max:255',
            'usuario_master_email' => 'required|string|email|max:255|unique:users,email',
            'usuario_master_password' => 'required|string|min:8|confirmed',
        ]);

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Criar empresa
            $novaEmpresa = Empresa::create([
                'nome' => $request->nome,
                'cnpj' => $request->cnpj,
                'telefone' => $request->telefone,
                'email' => $request->email,
                'endereco' => $request->endereco,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'cep' => $request->cep,
                'grupo_id' => $grupo->id,
                'is_active' => true,
            ]);

            // Criar usuário master para a empresa
            $usuarioMaster = User::create([
                'name' => $request->usuario_master_nome,
                'email' => $request->usuario_master_email,
                'password' => bcrypt($request->usuario_master_password),
                'tipo_usuario' => 'usuario_master',
                'empresa_id' => $novaEmpresa->id,
                'is_active' => true,
            ]);

            \DB::commit();

            return redirect()->route('grupo.empresas')
                ->with('success', 'Empresa adicionada com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao adicionar empresa: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editarEmpresa($id)
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Buscar empresa a ser editada e verificar se pertence ao grupo
        $empresaEditar = Empresa::where('id', $id)
            ->where('grupo_id', $grupo->id)
            ->firstOrFail();

        // Buscar usuário master da empresa
        $usuarioMaster = User::where('empresa_id', $empresaEditar->id)
            ->where('tipo_usuario', 'usuario_master')
            ->first();

        return view('grupo.empresas.edit', compact('empresaEditar', 'usuarioMaster', 'grupo'));
    }

    public function atualizarEmpresa(Request $request, $id)
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Buscar empresa a ser editada e verificar se pertence ao grupo
        $empresaEditar = Empresa::where('id', $id)
            ->where('grupo_id', $grupo->id)
            ->firstOrFail();

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => ['nullable', 'string', 'max:18', Rule::unique('empresas')->ignore($empresaEditar->id)],
            'telefone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('empresas')->ignore($empresaEditar->id)],
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'is_active' => 'boolean',
        ]);

        // Atualizar empresa
        $empresaEditar->update([
            'nome' => $request->nome,
            'cnpj' => $request->cnpj,
            'telefone' => $request->telefone,
            'email' => $request->email,
            'endereco' => $request->endereco,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'cep' => $request->cep,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('grupo.empresas')
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function removerEmpresa($id)
    {
        $user = Auth::user();

        // Verificar se é admin de grupo
        if (!$user->isAdminGrupo()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa || !$empresa->grupo_id) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        $grupo = Grupo::find($empresa->grupo_id);

        if (!$grupo) {
            return redirect()->route('dashboard')
                ->with('error', 'Grupo não encontrado.');
        }

        // Buscar empresa a ser removida e verificar se pertence ao grupo
        $empresaRemover = Empresa::where('id', $id)
            ->where('grupo_id', $grupo->id)
            ->firstOrFail();

        // Não permitir remover a própria empresa do admin de grupo
        if ($empresaRemover->id === $empresa->id) {
            return redirect()->route('grupo.empresas')
                ->with('error', 'Não é possível remover sua própria empresa do grupo.');
        }

        // Verificar se há usuários na empresa
        $usuariosCount = User::where('empresa_id', $empresaRemover->id)->count();

        if ($usuariosCount > 0) {
            return redirect()->route('grupo.empresas')
                ->with('error', 'Não é possível remover uma empresa que possui usuários. Remova os usuários primeiro.');
        }

        // Remover empresa do grupo (apenas desassocia, não exclui)
        $empresaRemover->grupo_id = null;
        $empresaRemover->save();

        return redirect()->route('grupo.empresas')
            ->with('success', 'Empresa removida do grupo com sucesso!');
    }
}

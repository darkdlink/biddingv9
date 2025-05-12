<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\User;
use App\Models\LicencaPlano;
use App\Models\LicencaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EmpresaController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Obter usuários da empresa
        $usuarios = User::where('empresa_id', $empresa->id)
            ->with('licenca.plano')
            ->orderBy('name')
            ->get();

        // Verificar plano para saber quantos usuários podem ser adicionados
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteUsuarios = $plano ? $plano->max_usuarios : 0;
        $usuariosAtuais = $usuarios->count();
        $podemAdicionar = $limiteUsuarios > $usuariosAtuais;

        return view('empresa.show', compact('empresa', 'usuarios', 'limiteUsuarios', 'usuariosAtuais', 'podemAdicionar'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'cnpj' => ['nullable', 'string', 'max:18', Rule::unique('empresas')->ignore($empresa->id)],
            'telefone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('empresas')->ignore($empresa->id)],
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
        ]);

        $empresa->update($request->all());

        return redirect()->route('empresa.show')
            ->with('success', 'Dados da empresa atualizados com sucesso!');
    }

    public function usuarios()
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Obter usuários da empresa
        $usuarios = User::where('empresa_id', $empresa->id)
            ->with('licenca.plano')
            ->orderBy('name')
            ->paginate(10);

        // Verificar plano para saber quantos usuários podem ser adicionados
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteUsuarios = $plano ? $plano->max_usuarios : 0;
        $usuariosAtuais = User::where('empresa_id', $empresa->id)->count();
        $podemAdicionar = $limiteUsuarios > $usuariosAtuais;

        return view('empresa.usuarios.index', compact('usuarios', 'limiteUsuarios', 'usuariosAtuais', 'podemAdicionar'));
    }

    public function adicionarUsuario()
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Verificar plano para saber se pode adicionar mais usuários
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteUsuarios = $plano ? $plano->max_usuarios : 0;
        $usuariosAtuais = User::where('empresa_id', $empresa->id)->count();

        if ($limiteUsuarios <= $usuariosAtuais) {
            return redirect()->route('empresa.usuarios')
                ->with('error', 'Você atingiu o limite de usuários do seu plano. Considere fazer um upgrade para adicionar mais usuários.');
        }

        return view('empresa.usuarios.create');
    }

    public function salvarUsuario(Request $request)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Verificar plano para saber se pode adicionar mais usuários
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteUsuarios = $plano ? $plano->max_usuarios : 0;
        $usuariosAtuais = User::where('empresa_id', $empresa->id)->count();

        if ($limiteUsuarios <= $usuariosAtuais) {
            return redirect()->route('empresa.usuarios')
                ->with('error', 'Você atingiu o limite de usuários do seu plano. Considere fazer um upgrade para adicionar mais usuários.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criar usuário
        $novoUsuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_usuario' => 'pessoa_fisica', // Usuário comum da empresa
            'empresa_id' => $empresa->id,
            'is_active' => true,
        ]);

// Associar o usuário aos segmentos da empresa
        $segmentos = \App\Models\Segmento::where('empresa_id', $empresa->id)->get();

        foreach ($segmentos as $segmento) {
            $novoUsuario->segmentos()->attach($segmento->id);
        }

        return redirect()->route('empresa.usuarios')
            ->with('success', 'Usuário adicionado com sucesso!');
    }

    public function editarUsuario($id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar usuário e verificar se pertence à empresa
        $usuario = User::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        return view('empresa.usuarios.edit', compact('usuario'));
    }

    public function atualizarUsuario(Request $request, $id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar usuário e verificar se pertence à empresa
        $usuario = User::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($usuario->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        // Atualizar dados do usuário
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);

        return redirect()->route('empresa.usuarios')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function removerUsuario($id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar usuário e verificar se pertence à empresa
        $usuario = User::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        // Não permitir remover o próprio usuário master
        if ($usuario->id === $user->id) {
            return redirect()->route('empresa.usuarios')
                ->with('error', 'Não é possível remover seu próprio usuário.');
        }

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Excluir licença associada (se houver)
            if ($usuario->licenca) {
                $usuario->licenca->delete();
            }

            // Remover relações com segmentos
            $usuario->segmentos()->detach();

            // Excluir usuário
            $usuario->delete();

            \DB::commit();

            return redirect()->route('empresa.usuarios')
                ->with('success', 'Usuário removido com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao remover usuário: ' . $e->getMessage());
        }
    }

    public function segmentos()
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar segmentos da empresa
        $segmentos = \App\Models\Segmento::where('empresa_id', $empresa->id)
            ->orderBy('nome')
            ->paginate(10);

        // Verificar plano para saber quantos segmentos podem ser adicionados
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteSegmentos = $plano ? $plano->max_segmentos : 0;
        $segmentosAtuais = \App\Models\Segmento::where('empresa_id', $empresa->id)->count();
        $podemAdicionar = $limiteSegmentos > $segmentosAtuais;

        return view('empresa.segmentos.index', compact('segmentos', 'limiteSegmentos', 'segmentosAtuais', 'podemAdicionar'));
    }

    public function adicionarSegmento()
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Verificar plano para saber se pode adicionar mais segmentos
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteSegmentos = $plano ? $plano->max_segmentos : 0;
        $segmentosAtuais = \App\Models\Segmento::where('empresa_id', $empresa->id)->count();

        if ($limiteSegmentos <= $segmentosAtuais) {
            return redirect()->route('empresa.segmentos')
                ->with('error', 'Você atingiu o limite de segmentos do seu plano. Considere fazer um upgrade para adicionar mais segmentos.');
        }

        return view('empresa.segmentos.create');
    }

    public function salvarSegmento(Request $request)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Verificar plano para saber se pode adicionar mais segmentos
        $licenca = $user->licenca;
        $plano = $licenca ? $licenca->plano : null;

        $limiteSegmentos = $plano ? $plano->max_segmentos : 0;
        $segmentosAtuais = \App\Models\Segmento::where('empresa_id', $empresa->id)->count();

        if ($limiteSegmentos <= $segmentosAtuais) {
            return redirect()->route('empresa.segmentos')
                ->with('error', 'Você atingiu o limite de segmentos do seu plano. Considere fazer um upgrade para adicionar mais segmentos.');
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        // Converter string de palavras-chave para array
        $palavrasChave = array_map('trim', explode(',', $request->palavras_chave));
        $palavrasChave = array_filter($palavrasChave); // Remover itens vazios

        // Criar segmento
        $segmento = \App\Models\Segmento::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'palavras_chave' => $palavrasChave,
            'empresa_id' => $empresa->id,
        ]);

        // Associar o segmento a todos os usuários da empresa
        $usuarios = User::where('empresa_id', $empresa->id)->get();

        foreach ($usuarios as $usuario) {
            $usuario->segmentos()->attach($segmento->id);
        }

        return redirect()->route('empresa.segmentos')
            ->with('success', 'Segmento adicionado com sucesso!');
    }

    public function editarSegmento($id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar segmento e verificar se pertence à empresa
        $segmento = \App\Models\Segmento::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        return view('empresa.segmentos.edit', compact('segmento'));
    }

    public function atualizarSegmento(Request $request, $id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar segmento e verificar se pertence à empresa
        $segmento = \App\Models\Segmento::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'palavras_chave' => 'required|string',
        ]);

        // Converter string de palavras-chave para array
        $palavrasChave = array_map('trim', explode(',', $request->palavras_chave));
        $palavrasChave = array_filter($palavrasChave); // Remover itens vazios

        // Atualizar segmento
        $segmento->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'palavras_chave' => $palavrasChave,
        ]);

        return redirect()->route('empresa.segmentos')
            ->with('success', 'Segmento atualizado com sucesso!');
    }

    public function removerSegmento($id)
    {
        $user = Auth::user();

        // Verificar se é usuário master
        if (!$user->isUsuarioMaster()) {
            return redirect()->route('dashboard')
                ->with('error', 'Você não tem permissão para acessar esta página.');
        }

        $empresa = $user->empresa;

        if (!$empresa) {
            return redirect()->route('dashboard')
                ->with('error', 'Empresa não encontrada.');
        }

        // Buscar segmento e verificar se pertence à empresa
        $segmento = \App\Models\Segmento::where('id', $id)
            ->where('empresa_id', $empresa->id)
            ->firstOrFail();

        // Iniciar transação
        \DB::beginTransaction();

        try {
            // Desassociar o segmento de todas as licitações
            $segmento->licitacoes()->detach();

            // Desassociar o segmento de todos os usuários
            $segmento->users()->detach();

            // Excluir segmento
            $segmento->delete();

            \DB::commit();

            return redirect()->route('empresa.segmentos')
                ->with('success', 'Segmento removido com sucesso!');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao remover segmento: ' . $e->getMessage());
        }
    }
}

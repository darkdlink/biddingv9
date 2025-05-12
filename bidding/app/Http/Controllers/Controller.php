<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\View;
use App\Models\Configuracao;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Carregar variáveis globais para todas as views
        $this->shareGlobalVariables();
    }

    /**
     * Compartilhar variáveis globais com todas as views
     *
     * @return void
     */
    protected function shareGlobalVariables()
    {
        // Carregar configurações do sistema
        $appName = Configuracao::obter('app_name', 'Sistema Bidding');

        // Compartilhar variáveis com todas as views
        View::share('appName', $appName);

        // Compartilhar se estamos no modo de impersonar
        View::share('isImpersonating', session()->has('admin_id'));
    }

    /**
     * Formatar mensagem de erro para respostas da API
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Formatar resposta de sucesso para a API
     *
     * @param mixed $data
     * @param string|null $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = null, $message = null, $statusCode = 200)
    {
        $response = ['success' => true];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Verificar se o usuário tem acesso a um recurso específico de licença
     *
     * @param string $recurso
     * @return bool
     */
    protected function verificarRecursoLicenca($recurso)
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Admins têm acesso a tudo
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasRecurso($recurso);
    }

    /**
     * Redirecionar se o usuário não tiver acesso a um recurso
     *
     * @param string $recurso
     * @param string $mensagem
     * @return \Illuminate\Http\RedirectResponse|null
     */
    protected function redirecionarSemRecurso($recurso, $mensagem = 'Seu plano não permite acessar este recurso.')
    {
        if (!$this->verificarRecursoLicenca($recurso)) {
            return redirect()->route('dashboard')
                ->with('error', $mensagem);
        }

        return null;
    }
}

@extends('layouts.guest')

@section('title', 'Verificar Email')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Verificar Email</h4>
                </div>
                <div class="card-body">
                    <p class="mb-4">Obrigado por se registrar! Antes de começar, você precisa verificar seu endereço de e-mail clicando no link que acabamos de enviar para você. Se você não recebeu o e-mail, teremos prazer em enviar outro.</p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success" role="alert">
                            Um novo link de verificação foi enviado para o endereço de e-mail fornecido durante o registro.
                        </div>
                    @endif

                    <div class="d-flex justify-content-between">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                Reenviar Email de Verificação
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-link text-decoration-none">
                                Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.guest')

@section('title', 'Autenticação de Dois Fatores')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Autenticação de Dois Fatores</h4>
                </div>
                <div class="card-body">
                    <div x-data="{ recovery: false }">
                        <div class="mb-4 text-sm text-gray-600" x-show="! recovery">
                            Para continuar, insira o código de autenticação fornecido pelo seu aplicativo autenticador.
                        </div>

                        <div class="mb-4 text-sm text-gray-600" x-show="recovery">
                            Por favor, confirme o acesso à sua conta inserindo um dos seus códigos de recuperação de emergência.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('two-factor.login') }}">
                            @csrf

                            <div class="mb-3" x-show="! recovery">
                                <label for="code" class="form-label">Código</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" inputmode="numeric" autofocus x-ref="code" autocomplete="one-time-code">
                            </div>

                            <div class="mb-3" x-show="recovery">
                                <label for="recovery_code" class="form-label">Código de Recuperação</label>
                                <input type="text" class="form-control @error('recovery_code') is-invalid @enderror" id="recovery_code" name="recovery_code" autocomplete="one-time-code">
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-link text-decoration-none p-0" x-show="! recovery" x-on:click="
                                    recovery = true;
                                    $nextTick(() => { $refs.recovery_code.focus() })
                                ">
                                    Usar código de recuperação
                                </button>

                                <button type="button" class="btn btn-link text-decoration-none p-0" x-show="recovery" x-on:click="
                                    recovery = false;
                                    $nextTick(() => { $refs.code.focus() })
                                ">
                                    Usar código de autenticação
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BrisaGamer</title>
    {{-- Ícone da BrisaGamer na aba do navegador --}}
    <link rel="icon"
        type="image/png"
        href="{{ asset('img/EmblemaOnda.png') }}">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
    <!-- Identidade visual Neon Sunset (somente estilos) -->
    <link rel="stylesheet" href="{{ asset('css/neon-sunset.css') }}">
</head>

<body class="bg-light">


    <!-- Cabeçalho BrisaGamer -->
    <nav class="navbar navbar-brisa">

        <div class="container brisa-navbar-container">

            <!-- Logo com onda roxa -->
            <a href="{{ route('loja.index') }}"
            class="navbar-brand brisa-logo">

                <img src="{{ asset('img/EmblemaOnda.png') }}"
                    alt="Logo BrisaGamer"
                    class="brisa-onda">

                <span class="brisa-nome">
                    <span class="brisa-texto">Brisa</span><span class="gamer-texto">Gamer</span>
                </span>

            </a>

            <!-- Botão para voltar à loja sem cadastro -->
            <div class="brisa-navbar-acoes">
                <a href="{{ route('loja.index') }}"
                class="btn btn-outline-light">
                    Continuar sem cadastro
                </a>
            </div>

        </div>

    </nav>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-4 p-md-5">

                    <h3 class="fw-bold text-center">
                        Criar minha conta
                    </h3>

                    <p class="text-muted text-center mb-4">
                        Crie sua conta na BrisaGamer e aproveite o universo gamer!
                    </p>

                    <!-- Exibe erros de validação. -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


    <form action="{{ route('cliente.cadastrar') }}" method="POST">
        @csrf

            {{-- ==========================================
                DADOS PESSOAIS
            ========================================== --}}

            <h5 class="fw-bold mb-3">Dados pessoais</h5>

            <div class="row g-3 mb-4">

                {{-- Nome completo --}}
                <div class="col-md-12">
                    <label for="name" class="form-label">Nome completo</label>
                    <input type="text"
                        id="name"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        maxlength="255"
                        autocomplete="name"
                        required>
                </div>

                {{-- E-mail --}}
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email"
                        id="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        required>
                </div>

                {{-- Telefone --}}
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone / WhatsApp</label>
                    <input type="tel"
                        id="telefone"
                        name="telefone"
                        class="form-control"
                        value="{{ old('telefone') }}"
                        placeholder="(41) 99999-9999"
                        maxlength="20"
                        autocomplete="tel"
                        required>
                </div>

                {{-- Senha --}}
                <div class="col-md-6">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                        required>
                </div>

                {{-- Confirmar senha --}}
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">
                        Confirmar senha
                    </label>
                    <input type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                        required>
                </div>

            </div>

            <hr class="my-4">

            {{-- ==========================================
                ENDEREÇO DE ENTREGA
            ========================================== --}}

            <h5 class="fw-bold mb-3">Endereço de entrega</h5>

            <div class="row g-3 mb-4">

                {{-- CEP --}}
                <div class="col-md-4">
                    <label for="cep" class="form-label">CEP</label>
                    <input type="text"
                        id="cep"
                        name="cep"
                        class="form-control"
                        value="{{ old('cep') }}"
                        placeholder="00000-000"
                        maxlength="9"
                        autocomplete="postal-code"
                        required>
                </div>

                {{-- Rua / Avenida --}}
                <div class="col-md-8">
                    <label for="logradouro" class="form-label">Rua / Avenida</label>
                    <input type="text"
                        id="logradouro"
                        name="logradouro"
                        class="form-control"
                        value="{{ old('logradouro') }}"
                        maxlength="255"
                        autocomplete="address-line1"
                        required>
                </div>

                {{-- Número --}}
                <div class="col-md-3">
                    <label for="numero" class="form-label">Número</label>
                    <input type="text"
                        id="numero"
                        name="numero"
                        class="form-control"
                        value="{{ old('numero') }}"
                        maxlength="20"
                        required>
                </div>

                {{-- Complemento --}}
                <div class="col-md-9">
                    <label for="complemento" class="form-label">
                        Complemento (opcional)
                    </label>
                    <input type="text"
                        id="complemento"
                        name="complemento"
                        class="form-control"
                        value="{{ old('complemento') }}"
                        maxlength="255"
                        placeholder="Apartamento, bloco, casa..."
                        autocomplete="address-line2">
                </div>

                {{-- Bairro --}}
                <div class="col-md-5">
                    <label for="bairro" class="form-label">Bairro</label>
                    <input type="text"
                        id="bairro"
                        name="bairro"
                        class="form-control"
                        value="{{ old('bairro') }}"
                        maxlength="255"
                        required>
                </div>

                {{-- Cidade --}}
                <div class="col-md-5">
                    <label for="cidade" class="form-label">Cidade</label>
                    <input type="text"
                        id="cidade"
                        name="cidade"
                        class="form-control"
                        value="{{ old('cidade') }}"
                        maxlength="255"
                        autocomplete="address-level2"
                        required>
                </div>

                {{-- Estado --}}
                <div class="col-md-2">
                    <label for="estado" class="form-label">UF</label>
                    <input type="text"
                        id="estado"
                        name="estado"
                        class="form-control text-uppercase"
                        value="{{ old('estado') }}"
                        maxlength="2"
                        minlength="2"
                        placeholder="PR"
                        autocomplete="address-level1"
                        required>
                </div>

            </div>

            {{-- Botão de cadastro --}}
            <button type="submit"
                    class="btn btn-primary w-100 py-2">
                Criar conta
            </button>

        </form>


                    {{-- Retorna à loja e solicita a abertura do modal de login. --}}
                    <p class="text-center mt-4 mb-0">
                        Já possui conta?

                        <a href="{{ route('loja.index') }}?abrirLogin=1">
                            Entrar
                        </a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</main>

</body>
</html>

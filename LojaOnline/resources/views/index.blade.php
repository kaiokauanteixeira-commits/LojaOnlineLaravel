<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BrisaGamer</title>
    <link rel="icon"
        type="image/png"
        href="{{ asset('img/EmblemaOnda.png') }}">

    <!-- Importa os estilos do Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Estilos personalizados do catálogo -->
    <style>
        /* Configuração visual geral da página */
        body {
            background-color: #f4f6fa;
            font-family: Arial, sans-serif;
            color: #263238;
        }

        /* Cabeçalho superior do sistema */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e7eaf0;
            padding: 20px 0;
        }

        /* Cor de destaque da identidade visual */
        .brand-name {
            color: #2563eb;
            font-weight: 800;
            letter-spacing: -1px;
        }

        /* Cartões de indicadores */
        .stat-card {
            background: white;
            border: 1px solid #edf0f5;
            border-radius: 14px;
            padding: 20px;
            height: 100%;
        }

        .stat-card h3 {
            font-weight: 700;
            margin-bottom: 0;
        }

        /* Card individual do produto */
        .produto-card {
            background: white;
            border: 1px solid #e8edf3;
            border-radius: 16px;
            overflow: hidden;
            height: 100%;
            transition: transform .2s, box-shadow .2s;
        }

        /* Efeito visual ao passar o mouse */
        .produto-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgba(0,0,0,.09);
        }

        /* Área destinada à imagem do produto */
        .produto-imagem {
            height: 230px;
            background: #f8fafc;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Ajusta a imagem sem distorcer */
        .produto-imagem img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
        }

        /* Exibição quando não há imagem */
        .sem-imagem {
            color: #9ca3af;
            font-size: 14px;
        }

        /* Nome do produto */
        .produto-nome {
            font-size: 17px;
            font-weight: 700;
            min-height: 42px;
        }

        /* Descrição resumida */
        .produto-descricao {
            color: #6b7280;
            font-size: 13px;
            min-height: 40px;
        }

        /* Preço em destaque */
        .produto-preco {
            color: #15803d;
            font-size: 23px;
            font-weight: 800;
        }

        /* Estilo do campo de pesquisa */
        .campo-pesquisa {
            border-radius: 10px;
            padding: 12px 15px;
        }

        /* Estilo dos botões dos cards */
        .produto-acoes .btn {
            flex: 1;
            border-radius: 9px;
        }

        /* Aparência dos modais */
        .modal-content {
            border: none;
            border-radius: 16px;
        }

        .modal-header {
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
        }
    </style>
    <!-- Identidade visual Neon Sunset (somente estilos) -->
    <link rel="stylesheet" href="{{ asset('css/neon-sunset.css') }}">
</head>

<body>

    <!-- ==================================================
        CABEÇALHO PRINCIPAL DA LOJA
    ================================================== -->

    <!-- ==================================================
        CABEÇALHO ADMINISTRATIVO - BRISAGAMER
    ================================================== -->
    <header>
        <nav class="navbar navbar-brisa">

            <div class="container brisa-navbar-container">

                <!-- Logo da BrisaGamer -->
                <a href="{{ route('loja.index') }}"
                class="navbar-brand brisa-logo">

                    <!-- Onda original com fundo transparente -->
                    <img src="{{ asset('img/EmblemaOnda.png') }}"
                        alt="Logo BrisaGamer"
                        class="brisa-onda">

                    <!-- Nome estilizado da loja -->
                    <span class="brisa-nome">
                        <span class="brisa-texto">Brisa</span><span class="gamer-texto">Gamer</span>
                    </span>

                </a>

                <!-- Botões do painel administrativo -->
                <div class="brisa-navbar-acoes">

                    <!-- Retorna à loja sem encerrar a sessão -->
                    <a href="{{ route('loja.index') }}"
                    class="btn btn-outline-light">
                        Voltar à loja
                    </a>

                    <!-- Encerra a sessão administrativa -->
                    <form action="{{ route('admin.logout') }}"
                        method="POST"
                        class="m-0">

                        @csrf

                        <button type="submit"
                                class="btn btn-outline-light">
                            Sair
                        </button>

                    </form>

                </div>

            </div>

        </nav>
    </header>

<main class="container py-5">

    <!-- ==================================================
         TÍTULO E BOTÃO DE CADASTRO
    ================================================== -->
    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">Catálogo de Produtos</h2>
            <p class="text-muted mb-0">
                Gerencie os produtos da sua loja.
            </p>
        </div>

        <!-- Abre o modal de cadastro sem mudar de página -->
        <button class="btn btn-primary px-4 py-2"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#modalCadastrar">
            + Novo Produto
        </button>
    </div>

    <!-- ==================================================
         MENSAGENS DE SUCESSO E ERROS
    ================================================== -->

    <!-- Mostra erros de validação enviados pelo Laravel -->
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Verifique os dados informados:</strong>

            <ul class="mb-0">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ==================================================
         INDICADORES DO ESTOQUE
    ================================================== -->

    @php
            // Conta o total de produtos diferentes cadastrados.
            $totalProdutos = $produtos->count();

            // Soma todas as unidades disponíveis em estoque.
            $totalEstoque = $produtos->sum('estoque');

            // Conta os produtos com estoque de 1 até 5 unidades.
            $estoqueBaixo = $produtos->filter(
                fn($produto) => $produto->estoque > 0
                    && $produto->estoque <= 5
            )->count();

            // Conta os produtos que estão sem unidades.
            $semEstoque = $produtos->where('estoque', 0)->count();
        @endphp

        <div class="row g-3 mb-4">

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="text-muted small mb-2">Produtos cadastrados</p>
                    <h3>{{ $totalProdutos }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="text-muted small mb-2">Unidades em estoque</p>
                    <h3>{{ $totalEstoque }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="text-muted small mb-2">Estoque baixo</p>
                    <h3 class="text-warning">{{ $estoqueBaixo }}</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <p class="text-muted small mb-2">Sem estoque</p>
                    <h3 class="text-danger">{{ $semEstoque }}</h3>
                </div>
            </div>
        </div>

    <!-- ==================================================
         CAMPO DE PESQUISA
    ================================================== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <label for="pesquisaProduto"
                   class="form-label fw-semibold">
                Pesquisar produto
            </label>

            <!-- A pesquisa será feita em tempo real pelo JS -->
            <input type="search"
                   id="pesquisaProduto"
                   class="form-control campo-pesquisa"
                   placeholder="Digite o nome ou a descrição do produto...">
        </div>
    </div>

    <!-- ==================================================
         CATÁLOGO VISUAL DE PRODUTOS
    ================================================== -->
    <div class="row g-4" id="catalogoProdutos">

        <!-- Percorre todos os produtos enviados pelo Controller -->
        @forelse($produtos as $produto)

            <div class="col-12 col-sm-6 col-lg-4 col-xl-3 item-produto"
                 data-busca="{{ $produto->nome }} {{ $produto->descricao }}">

                <div class="produto-card d-flex flex-column">

                    <!-- Imagem do produto -->
                    <div class="produto-imagem">

                        @if($produto->imagem)

                            <!-- Exibe o arquivo salvo no storage público -->
                            <img
                                src="{{ asset('storage/' . $produto->imagem) }}"
                                alt="{{ $produto->nome }}"
                                loading="lazy">

                        @else

                            <!-- Mensagem quando o produto não tem foto -->
                            <span class="sem-imagem">
                                Sem imagem disponível
                            </span>

                        @endif

                    </div>

                    <!-- Informações do produto -->
                    <div class="p-3 d-flex flex-column flex-grow-1">

                        <!-- ID do produto -->
                        <small class="text-muted mb-2">
                            Produto #{{ $produto->id }}
                        </small>

                        <!-- Nome do produto -->
                        <div class="produto-nome mb-2">
                            {{ $produto->nome }}
                        </div>

                        <!-- Descrição resumida -->
                        <p class="produto-descricao mb-3">
                            {{ \Illuminate\Support\Str::limit(
                                $produto->descricao, 85
                            ) }}
                        </p>

                        <!-- Preço formatado em reais -->
                        <div class="produto-preco mb-3">
                            R$ {{ number_format(
                                $produto->preco, 2, ',', '.'
                            ) }}
                        </div>

                        <!-- Quantidade e situação do estoque -->
                        <div class="mb-3">

                            @if($produto->estoque == 0)

                                <span class="badge bg-danger">
                                    Sem estoque
                                </span>

                            @elseif($produto->estoque <= 5)

                                <span class="badge bg-warning text-dark">
                                    Estoque baixo: {{ $produto->estoque }}
                                </span>

                            @else

                                <span class="badge bg-success">
                                    {{ $produto->estoque }} disponíveis
                                </span>

                            @endif
                        </div>

                        <!-- ==================================================
                             BOTÕES DE EDIÇÃO E EXCLUSÃO
                        ================================================== -->
                        <div class="d-flex gap-2 produto-acoes mt-auto">

                            <!-- Abre o modal de edição com os dados do produto -->
                            <button type="button"
                                    class="btn btn-outline-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditar"
                                    data-url="{{ route('produtos.update', $produto) }}"
                                    data-nome="{{ $produto->nome }}"
                                    data-descricao="{{ $produto->descricao }}"
                                    data-preco="{{ $produto->preco }}"
                                    data-estoque="{{ $produto->estoque }}"
                                    data-imagem="{{ $produto->imagem ? asset('storage/' . $produto->imagem) : '' }}">
                                Editar
                            </button>

                            <!-- Abre o modal de confirmação de exclusão -->
                            <button type="button"
                                    class="btn btn-outline-danger btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalExcluir"
                                    data-url="{{ route('produtos.destroy', $produto) }}"
                                    data-nome="{{ $produto->nome }}">
                                Excluir
                            </button>

                        </div>
                    </div>
                </div>
            </div>

        @empty

            <!-- Exibido quando não há nenhum produto no banco -->
            <div class="col-12 text-center py-5">
                <h5>Nenhum produto cadastrado.</h5>
                <p class="text-muted">
                    Clique em Novo Produto para começar.
                </p>
            </div>

        @endforelse
    </div>

    <!-- Mensagem exibida quando a pesquisa não encontra produtos -->
    <div id="mensagemSemResultados"
         class="text-center text-muted py-5 d-none">
        Nenhum produto encontrado para esta pesquisa.
    </div>

</main>
    {{-- ==================================================
        NOTIFICAÇÃO TOAST DE SUCESSO
        Aparece após cadastrar, editar ou excluir.
        Desaparece automaticamente após 3 segundos.
    ================================================== --}}

    @if(session('success'))

        <!-- Container fixo no canto inferior direito -->
        <div class="toast-container position-fixed bottom-0 end-0 p-4"
            style="z-index: 1100;">

            <!-- Toast do Bootstrap com aparência de sucesso -->
            <div id="toastSucesso"
                class="toast border-0 shadow"
                role="status"
                aria-live="polite"
                aria-atomic="true"
                data-bs-autohide="true"
                data-bs-delay="3000">

                <!-- Cabeçalho da notificação -->
                <div class="toast-header">

                    <!-- Indicador visual verde -->
                    <span class="bg-success rounded-circle me-2"
                        style="width: 12px; height: 12px;"></span>

                    <!-- Título da notificação -->
                    <strong class="me-auto text-success">
                        Sucesso!
                    </strong>

                    <!-- Texto informativo -->
                    <small class="text-muted">LojaOnline</small>

                    <!-- Botão que permite fechar manualmente -->
                    <button type="button"
                            class="btn-close ms-2"
                            data-bs-dismiss="toast"
                            aria-label="Fechar notificação">
                    </button>
                </div>

                <!-- Mensagem recebida do Controller Laravel -->
                <div class="toast-body">
                    {{ session('success') }}
                </div>

            </div>
        </div>
    @endif

<!-- ==================================================
     MODAL DE CADASTRO DE PRODUTOS
================================================== -->
<div class="modal fade" id="modalCadastrar"
     tabindex="-1"
     aria-labelledby="tituloCadastrar"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- Envia os dados e a imagem ao método store -->
            <form action="{{ route('produtos.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                <!-- Proteção CSRF obrigatória no Laravel -->
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloCadastrar">
                        Cadastrar Produto
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Fechar"></button>
                </div>

                <div class="modal-body">

                    <!-- Nome do produto -->
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text"
                               name="nome"
                               class="form-control"
                               value="{{ old('nome') }}"
                               required>
                    </div>

                <!-- Categoria do produto -->
                <div class="mb-3">
                    <label for="categoria" class="form-label">
                        Categoria
                    </label>

                    <select name="categoria"
                            id="categoria"
                            class="form-select"
                            required>

                        <option value="" selected disabled>
                            Selecione uma categoria
                        </option>

                        <option value="pc_gamer" {{ old('categoria') == 'pc_gamer' ? 'selected' : '' }}>
                            PC Gamer Montado
                        </option>

                        <option value="consoles" {{ old('categoria') == 'consoles' ? 'selected' : '' }}>
                            Consoles
                        </option>

                        <option value="perifericos" {{ old('categoria') == 'perifericos' ? 'selected' : '' }}>
                            Periféricos
                        </option>

                        <option value="monitores" {{ old('categoria') == 'monitores' ? 'selected' : '' }}>
                            Monitores
                        </option>

                        <option value="jogos" {{ old('categoria') == 'jogos' ? 'selected' : '' }}>
                            Jogos
                        </option>

                    </select>
                </div>


                    <!-- Descrição do produto -->
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao"
                                  class="form-control"
                                  rows="3">{{ old('descricao') }}</textarea>
                    </div>

                    <div class="row">

                        <!-- Preço do produto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number"
                                   name="preco"
                                   class="form-control"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('preco') }}"
                                   required>
                        </div>

                        <!-- Quantidade inicial em estoque -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estoque</label>
                            <input type="number"
                                   name="estoque"
                                   class="form-control"
                                   min="0"
                                   value="{{ old('estoque', 0) }}"
                                   required>
                        </div>

                    </div>

                    <!-- Upload da imagem -->
                    <div class="mb-3">
                        <label class="form-label">
                            Imagem do Produto
                        </label>

                        <input type="file"
                               name="imagem"
                               class="form-control"
                               accept="image/*">

                        <small class="text-muted">
                            Imagem opcional. Tamanho máximo: 2 MB.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">

                    <!-- Fecha o modal sem enviar o formulário -->
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <!-- Envia o formulário de cadastro -->
                    <button type="submit" class="btn btn-primary">
                        Salvar Produto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================================
     MODAL DE EDIÇÃO DE PRODUTOS
================================================== -->
<div class="modal fade" id="modalEditar"
     tabindex="-1"
     aria-labelledby="tituloEditar"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- O JavaScript definirá a rota do produto selecionado -->
            <form id="formEditar"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <!-- Laravel interpreta a requisição como PUT -->
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloEditar" data-categoria="{{ $produto->categoria }}">
                        Editar Produto
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Fechar"></button>
                </div>

                <div class="modal-body">

                    <!-- Nome preenchido automaticamente -->
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text"
                               name="nome"
                               id="editarNome"
                               class="form-control"
                               required>
                    </div>

                    <!-- Categoria atual do produto -->
                    <div class="mb-3">
                        <label for="editarCategoria" class="form-label">
                            Categoria
                        </label>

                        <select name="categoria"
                                id="editarCategoria"
                                class="form-select"
                                required>

                            <option value="pc_gamer">PC Gamer Montado</option>
                            <option value="consoles">Consoles</option>
                            <option value="perifericos">Periféricos</option>
                            <option value="monitores">Monitores</option>
                            <option value="jogos">Jogos</option>

                        </select>
                    </div>


                    <!-- Descrição preenchida automaticamente -->
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea name="descricao"
                                  id="editarDescricao"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                    <div class="row">

                        <!-- Preço atual do produto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number"
                                   name="preco"
                                   id="editarPreco"
                                   class="form-control"
                                   step="0.01"
                                   min="0"
                                   required>
                        </div>

                        <!-- Estoque atual do produto -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estoque</label>
                            <input type="number"
                                   name="estoque"
                                   id="editarEstoque"
                                   class="form-control"
                                   min="0"
                                   required>
                        </div>
                    </div>

                    <!-- Visualização da imagem já cadastrada -->
                    <div class="mb-3">
                        <label class="form-label">Imagem atual</label>

                        <div>
                            <img id="editarImagemAtual"
                                 alt="Imagem atual do produto"
                                 width="110"
                                 height="110"
                                 class="rounded border mb-2 d-none"
                                 style="object-fit: contain;">

                            <p id="editarSemImagem"
                               class="text-muted small">
                                Produto sem imagem.
                            </p>
                        </div>

                        <!-- Permite substituir a imagem existente -->
                        <label class="form-label">
                            Substituir imagem
                        </label>

                        <input type="file"
                               name="imagem"
                               class="form-control"
                               accept="image/*">

                        <small class="text-muted">
                            Deixe vazio para manter a imagem atual.
                        </small>
                    </div>
                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==================================================
     MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
================================================== -->
<div class="modal fade" id="modalExcluir"
     tabindex="-1"
     aria-labelledby="tituloExcluir"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- O JavaScript define qual produto será excluído -->
            <form id="formExcluir" method="POST">

                @csrf

                    <!-- Laravel interpreta a requisição como DELETE -->
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger" id="tituloExcluir">
                            Confirmar Exclusão
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Fechar"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Alerta sobre a exclusão permanente -->
                        <div class="alert alert-warning">
                            Atenção! Esta ação não poderá ser desfeita.
                        </div>

                        <p>
                            Deseja realmente excluir o produto
                            <strong id="nomeProdutoExcluir"></strong>?
                        </p>
                    </div>

                    <div class="modal-footer">

                        <!-- Fecha o modal e mantém o produto -->
                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Não, cancelar
                        </button>

                        <!-- Confirma a exclusão no Laravel -->
                        <button type="submit" class="btn btn-danger">
                            Sim, excluir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

        <!-- Importa o JavaScript do Bootstrap, incluindo os modais -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        /* ==================================================
        PESQUISA INSTANTÂNEA DE PRODUTOS
        ================================================== */

        // Localiza o campo de pesquisa.
        const pesquisa = document.getElementById('pesquisaProduto');

        // Recupera todos os cards de produtos.
        const cards = document.querySelectorAll('.item-produto');

        // Mensagem apresentada quando não há resultados.
        const semResultados = document.getElementById('mensagemSemResultados');

        // Executa a pesquisa sempre que o usuário digita.
        pesquisa.addEventListener('input', function () {

            // Normaliza o texto para facilitar a comparação.
            const termo = pesquisa.value.toLocaleLowerCase('pt-BR').trim();

            // Conta quantos produtos correspondem à pesquisa.
            let encontrados = 0;

            // Percorre todos os cards.
            cards.forEach(function (card) {

                // Recupera o nome e a descrição do produto.
                const texto = card.dataset.busca.toLocaleLowerCase('pt-BR');

                // Verifica se o produto corresponde à pesquisa.
                const corresponde = texto.includes(termo);

                // Oculta ou exibe o card.
                card.classList.toggle('d-none', !corresponde);

                if (corresponde) {
                    encontrados++;
                }
            });

            // Mostra a mensagem somente se não houver resultados.
            semResultados.classList.toggle('d-none', encontrados > 0);
        });

        /* ==================================================
        PREENCHIMENTO AUTOMÁTICO DO MODAL DE EDIÇÃO
        ================================================== */

        // Recupera o modal de edição.
        const modalEditar = document.getElementById('modalEditar');

        // Executa quando o Bootstrap abre o modal.
        modalEditar.addEventListener('show.bs.modal', function (event) {

            // Identifica o botão Editar que foi clicado.
            const botao = event.relatedTarget;

            if (!botao) return;

            // Define a URL de atualização do produto selecionado.
            document.getElementById('formEditar').action = botao.dataset.url;

            // Preenche os campos com os dados do produto.
            document.getElementById('editarNome').value =
                botao.dataset.nome;

            document.getElementById('editarDescricao').value =
                botao.dataset.descricao || '';

            document.getElementById('editarPreco').value =
                botao.dataset.preco;

            document.getElementById('editarEstoque').value =
                botao.dataset.estoque;

            // Localiza os elementos de visualização da imagem.
            const imagem = document.getElementById('editarImagemAtual');
            const semImagem = document.getElementById('editarSemImagem');

            // Verifica se existe uma imagem cadastrada.
            if (botao.dataset.imagem) {

                // Exibe a imagem atual.
                imagem.src = botao.dataset.imagem;
                imagem.classList.remove('d-none');
                semImagem.classList.add('d-none');

            } else {

                // Oculta a imagem quando o produto não tem foto.
                imagem.removeAttribute('src');
                imagem.classList.add('d-none');
                semImagem.classList.remove('d-none');
            }

            // Limpa o campo de upload de edições anteriores.
            document.querySelector('#formEditar input[type="file"]').value = '';
        });

        /* ==================================================
        PREENCHIMENTO DO MODAL DE EXCLUSÃO
        ================================================== */

        // Recupera o modal de confirmação.
        const modalExcluir = document.getElementById('modalExcluir');

        // Executa quando o modal de exclusão é aberto.
        modalExcluir.addEventListener('show.bs.modal', function (event) {

            // Identifica o botão Excluir selecionado.
            const botao = event.relatedTarget;

            if (!botao) return;

            // Define a URL responsável pela exclusão.
            document.getElementById('formExcluir').action =
                botao.dataset.url;

            // Mostra o nome do produto na mensagem de confirmação.
            document.getElementById('nomeProdutoExcluir').textContent =
                botao.dataset.nome;
        });

        /* ==================================================
        EXIBE O TOAST DE SUCESSO AUTOMATICAMENTE
        ================================================== */

        /* ==================================================
        CONTROLE DA NOTIFICAÇÃO TOAST

        - Exibe a notificação automaticamente.
        - Mantém a mensagem visível por 3 segundos.
        - Permite fechar manualmente.
        ================================================== */

            // Procura o Toast de sucesso no HTML.
            const toastElemento = document.getElementById('toastSucesso');

            // Verifica se existe uma mensagem de sucesso na página.
            if (toastElemento) {

            // Cria a instância do Toast utilizando o Bootstrap 5.
            const toast = new bootstrap.Toast(toastElemento, {

                // Permite que a notificação desapareça automaticamente.
                autohide: true,

                // Define o tempo de exibição em milissegundos.
                // 3000 milissegundos equivalem a 3 segundos.
                delay: 3000
            });

            // Exibe a notificação ao carregar a página.
            toast.show();
        }

        // Preenche automaticamente a categoria do produto.
        document.getElementById('editarCategoria').value =
        botao.dataset.categoria || '';


</script>

</body>
</html>

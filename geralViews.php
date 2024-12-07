<?php
include 'db_connection.php';

// Função para contar o número de registros em uma tabela
function contarRegistros($conn, $tabela) {
    $sql = "SELECT COUNT(*) AS total FROM $tabela";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}

// Função para buscar dados de uma tabela
// Função para buscar dados de uma tabela
function buscarDados($conn, $tabela) {
    $sql = "SELECT * FROM $tabela";
    $result = $conn->query($sql);
    $dados = array(); // Alteração para usar array()
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row; // Adicionando dados ao array
    }
    return $dados;
}


// Buscar dados das entidades
$usuarios = buscarDados($conn, 'USUARIO');
$clientes = buscarDados($conn, 'CLIENTE');
$fornecedores = buscarDados($conn, 'FORNECEDOR');
$categorias = buscarDados($conn, 'CATEGORIA');
$produtos = buscarDados($conn, 'PRODUTO');
$vendas = buscarDados($conn, 'VENDA');

// Contar registros
$usuariosCount = contarRegistros($conn, 'USUARIO');
$clientesCount = contarRegistros($conn, 'CLIENTE');
$fornecedoresCount = contarRegistros($conn, 'FORNECEDOR');
$categoriasCount = contarRegistros($conn, 'CATEGORIA');
$produtosCount = contarRegistros($conn, 'PRODUTO');
$vendasCount = contarRegistros($conn, 'VENDA');

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visão Geral</title>
    <link rel="stylesheet" href="../CRUD/assets/css/styles.css">
    <style>
        .tabela-dados {
            display: none;
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .tabela-dados th, .tabela-dados td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        /* Estilo para a div que contém o título */
.section-header {
    border-radius: 8px;
    background-color: #f0f0f0;  /* Cor de fundo suave */
    padding: 10px 15px;
    margin: 10px 0;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.90); /* Sombra suave */
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

/* Efeito de hover para a seção */
.section-header:hover {
    background-color: #e0e0e0; /* Cor de fundo mais escura ao passar o mouse */
    box-shadow: 0 6px 8px rgba(0, 0, 0, 0.90); /* Sombra mais intensa ao passar o mouse */
}

/* Estilo para o título h3 */
.section-header h3 {
    margin: 0; /* Remove o espaçamento padrão */
    font-size: 18px;
    color: #333;
    font-weight: bold;
}

/* Adiciona espaçamento entre o título e o número de itens */
.section-header span {
    margin-left: 10px;
    font-size: 16px;
    color: #777;
}
/* Estilo para o container do campo de pesquisa */
.pesquisa-container {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

/* Estilo para o input de pesquisa */
.pesquisa-container input {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    flex: 1;
}

/* Estilo para o botão de pesquisa */
.pesquisa-container button {
    padding: 8px 12px;
    border: none;
    background-color: rgb(6, 59, 90); /* Cor azul */
    color: white;
    font-size: 14px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

/* Efeito de hover no botão */
.pesquisa-container button:hover {
    background-color: #0056b3; /* Azul mais escuro */
}


        header,table th,footer{
            background-color: rgb(6, 59, 90);
        }
      
    </style>
</head>
<body>
    <header>
        <h1>Visão Geral do Sistema</h1>
        <nav>
            <a href="index.php">Voltar para Home</a>
        </nav>
    </header>
    <main>
        <h2>Estatísticas do Sistema</h2>

        <!-----Secção Usuários------->
        <section>
    <div class="section-header" onclick="mostrarTabela('usuarios')">
        <h3>Usuários Cadastrados <span>(<?php echo $usuariosCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-usuarios" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('usuarios', 'pesquisa-usuarios')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="usuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo $usuario['IDUSUARIO']; ?></td>
                    <td><?php echo $usuario['nome']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

                <!-----Secção Clientes------->
<section>
    <div class="section-header" onclick="mostrarTabela('clientes')">
        <h3>Clientes Cadastrados <span>(<?php echo $clientesCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-clientes" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('clientes', 'pesquisa-clientes')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="clientes">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['IDCLIENTE']; ?></td>
                    <td><?php echo $cliente['nome']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

                <!-----Secção Fornecedores------->
<section>
    <div class="section-header" onclick="mostrarTabela('fornecedores')">
        <h3>Fornecedores Cadastrados <span>(<?php echo $fornecedoresCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-fornecedores" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('fornecedores', 'pesquisa-fornecedores')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="fornecedores">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>ID Usuário</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <tr>
                    <td><?php echo $fornecedor['IDFORNECEDOR']; ?></td>
                    <td><?php echo $fornecedor['nome']; ?></td>
                    <td><?php echo $fornecedor['IDUSUARIO']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

                <!-----Secção Categorias------->
<section>
    <div class="section-header" onclick="mostrarTabela('categorias')">
        <h3>Categorias Cadastradas <span>(<?php echo $categoriasCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-categorias" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('categorias', 'pesquisa-categorias')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="categorias">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?php echo $categoria['IDCATEGORIA']; ?></td>
                    <td><?php echo $categoria['nome']; ?></td>
                    <td><?php echo $categoria['descricao']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

                <!-----Secção Produtos------->
<section>
    <div class="section-header" onclick="mostrarTabela('produtos')">
        <h3>Produtos Cadastrados <span>(<?php echo $produtosCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-produtos" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('produtos', 'pesquisa-produtos')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="produtos">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Categoria</th>
                <th>Designação</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['IDPRODUTO']; ?></td>
                    <td><?php echo $produto['IDCATEGORIA']; ?></td>
                    <td><?php echo $produto['designacao']; ?></td>
                    <td><?php echo $produto['data']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

                <!-----Secção Vendas------->
<section>
    <div class="section-header" onclick="mostrarTabela('vendas')">
        <h3>Vendas Cadastradas <span>(<?php echo $vendasCount; ?>)</span></h3>
    </div>

    <!-- Campo de pesquisa -->
    <div class="pesquisa-container">
        <input type="text" id="pesquisa-vendas" placeholder="Pesquisar por ID...">
        <button onclick="pesquisarTabela('vendas', 'pesquisa-vendas')">Pesquisar</button>
    </div>

    <table class="tabela-dados" id="vendas">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Cliente</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vendas as $venda): ?>
                <tr>
                    <td><?php echo $venda['IDVENDA']; ?></td>
                    <td><?php echo $venda['IDCLIENTE']; ?></td>
                    <td><?php echo $venda['data']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

    </main>
    <footer>
        <p>&copy; 2024 - Gestão da Loja</p>
    </footer>

    <script>
        // Função para mostrar/ocultar a tabela de dados
        function mostrarTabela(id) {
            const tabela = document.getElementById(id);
            tabela.style.display = (tabela.style.display === "none" || tabela.style.display === "") ? "table" : "none";
        }

        function pesquisarTabela(tabelaId, inputId) {
    const tabela = document.getElementById(tabelaId);
    const input = document.getElementById(inputId);
    const filtro = input.value.toLowerCase();
    const linhas = tabela.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < linhas.length; i++) {
        const colunaId = linhas[i].getElementsByTagName('td')[0]; // Assume que o ID está na primeira coluna
        if (colunaId) {
            const textoId = colunaId.textContent || colunaId.innerText;
            linhas[i].style.display = textoId.toLowerCase().indexOf(filtro) > -1 ? "" : "none";
        }
    }
}

    </script>
</body>
</html>

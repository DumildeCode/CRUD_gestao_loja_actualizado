<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE PRODUTO SET IDPRODUTO = (@count := @count + 1);";
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    } else {
        echo "Erro ao reordenar IDs: " . $conn->error;
    }
}

// Verifica se a conexão foi bem-sucedida
if (!$conn) {
    die("Conexão falhou: " . mysqli_connect_error());
}

// Editar produto (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $produto_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM PRODUTO WHERE IDPRODUTO = $produto_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $produto = $result->fetch_assoc();
    } else {
        echo "Produto não encontrado ou erro na consulta.";
        $produto = null;
    }
}

// Atualizar ou adicionar um produto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['designacao'])) {
    $id_categoria = (int)$_POST['id_categoria'];
    $designacao = $conn->real_escape_string($_POST['designacao']);
    $data = $conn->real_escape_string($_POST['data']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE PRODUTO SET IDCATEGORIA = '$id_categoria', designacao = '$designacao', data = '$data' WHERE IDPRODUTO = $id";
    } else {
        $sql = "INSERT INTO PRODUTO (IDCATEGORIA, designacao, data) VALUES ('$id_categoria', '$designacao', '$data')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: produtos.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir produto e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM PRODUTO WHERE IDPRODUTO = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: produtos.php");
        exit;
    } else {
        echo "Erro ao excluir o registro: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Produtos</title>
    <link rel="stylesheet" href="../CRUD/assets/css/styles.css">
    <style>
        header,form button, table th,footer{
            background-color: rgb(6, 59, 90);
        }
    </style>
</head>
<body>
<header>
    <h1>Gestão de Produtos</h1>
    <nav>
        <a href="index.php">Voltar para Home</a>
    </nav>
</header>
<main>
    <h2><?php echo isset($produto) ? 'Editar Produto' : 'Adicionar Novo Produto'; ?></h2>
    <form action="produtos.php" method="POST">
        <?php if (isset($produto)): ?>
            <input type="hidden" name="id" value="<?php echo $produto['IDPRODUTO']; ?>">
        <?php endif; ?>
        <label for="id_categoria">ID Categoria:</label>
        <input type="number" id="id_categoria" name="id_categoria" value="<?php echo isset($produto['IDCATEGORIA']) ? $produto['IDCATEGORIA'] : ''; ?>" required>

        <label for="designacao">Designação:</label>
        <input type="text" id="designacao" name="designacao" value="<?php echo isset($produto['designacao']) ? $produto['designacao'] : ''; ?>" required>

        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="<?php echo isset($produto['data']) ? $produto['data'] : ''; ?>" required>

        <button type="submit"><?php echo isset($produto) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Produtos Cadastrados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Categoria</th>
                <th>Designação</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM PRODUTO ORDER BY IDPRODUTO ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDPRODUTO']; ?></td>
                        <td><?php echo $row['IDCATEGORIA']; ?></td>
                        <td><?php echo $row['designacao']; ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td>
                            <a href="produtos.php?edit=<?php echo $row['IDPRODUTO']; ?>">
                                <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
                            </a>
                            <a href="produtos.php?delete=<?php echo $row['IDPRODUTO']; ?>" onclick="return confirm('Tem certeza?')">
                                <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="5">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
<footer>
    <p>&copy; 2024 - Gestão da Loja</p>
</footer>
</body>
</html>

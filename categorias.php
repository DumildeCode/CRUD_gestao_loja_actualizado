<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE CATEGORIA SET IDCATEGORIA = (@count := @count + 1);";
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

// Editar categoria (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $categoria_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM CATEGORIA WHERE IDCATEGORIA = $categoria_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $categoria = $result->fetch_assoc();
    } else {
        echo "Categoria não encontrada ou erro na consulta.";
        $categoria = null;
    }
}

// Atualizar ou adicionar uma categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE CATEGORIA SET nome = '$nome', descricao = '$descricao' WHERE IDCATEGORIA = $id";
    } else {
        $sql = "INSERT INTO CATEGORIA (nome, descricao) VALUES ('$nome', '$descricao')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: categorias.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir categoria e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM CATEGORIA WHERE IDCATEGORIA = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: categorias.php");
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
    <title>Gestão de Categorias</title>
    <link rel="stylesheet" href="../CRUD01/assets/css/geral.css">
  
</head>
<body>
<header>
       
       <img src="assets/img/logo.png" alt="logo" class="picture">     
     
     <nav>
         <ul>
             
             <li><a href="index.php">Voltar à Página Inicial</a></li>
         </ul>
     </nav>
 </header>
<main>
<h3>Categorias</h3>
    <h2><?php echo isset($categoria) ? 'Editar Categoria' : 'Adicionar Nova Categoria'; ?></h2>
    <form action="categorias.php" method="POST">
        <?php if (isset($categoria)): ?>
            <input type="hidden" name="id" value="<?php echo $categoria['IDCATEGORIA']; ?>">
        <?php endif; ?>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo isset($categoria['nome']) ? $categoria['nome'] : ''; ?>" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao" required><?php echo isset($categoria['descricao']) ? $categoria['descricao'] : ''; ?></textarea>

        <button type="submit"><?php echo isset($categoria) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Categorias Cadastradas</h2>
    <div class="rolagem">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM CATEGORIA ORDER BY IDCATEGORIA ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDCATEGORIA']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td><?php echo $row['descricao']; ?></td>
                        <td>
                            <a href="categorias.php?edit=<?php echo $row['IDCATEGORIA']; ?>">
                                <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
                            </a>
                            <a href="categorias.php?delete=<?php echo $row['IDCATEGORIA']; ?>" onclick="return confirm('Tem certeza?')">
                                <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4">Nenhuma categoria encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</main>
</body>
</html>

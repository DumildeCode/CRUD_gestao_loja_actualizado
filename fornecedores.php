<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE FORNECEDOR SET IDFORNECEDOR = (@count := @count + 1);";
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

// Editar fornecedor (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $fornecedor_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM FORNECEDOR WHERE IDFORNECEDOR = $fornecedor_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $fornecedor = $result->fetch_assoc();
    } else {
        echo "Fornecedor não encontrado ou erro na consulta.";
        $fornecedor = null;
    }
}

// Atualizar ou adicionar um fornecedor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $id_usuario = $conn->real_escape_string($_POST['id_usuario']);
    $nome = $conn->real_escape_string($_POST['nome']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE FORNECEDOR SET IDUSUARIO = '$id_usuario', nome = '$nome' WHERE IDFORNECEDOR = $id";
    } else {
        $sql = "INSERT INTO FORNECEDOR (IDUSUARIO, nome) VALUES ('$id_usuario', '$nome')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: fornecedores.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir fornecedor e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM FORNECEDOR WHERE IDFORNECEDOR = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: fornecedores.php");
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
    <title>Gestão de Fornecedores</title>
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
<h3>Fornecedores</h3>
    <h2><?php echo isset($fornecedor) ? 'Editar Fornecedor' : 'Adicionar Novo Fornecedor'; ?></h2>
    <form action="fornecedores.php" method="POST">
        <?php if (isset($fornecedor)): ?>
            <input type="hidden" name="id" value="<?php echo $fornecedor['IDFORNECEDOR']; ?>">
        <?php endif; ?>
        <label for="id_usuario">ID Usuário:</label>
        <input type="number" id="id_usuario" name="id_usuario" value="<?php echo isset($fornecedor['IDUSUARIO']) ? $fornecedor['IDUSUARIO'] : ''; ?>" required>

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo isset($fornecedor['nome']) ? $fornecedor['nome'] : ''; ?>" required>

        <button type="submit"><?php echo isset($fornecedor) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Fornecedores Cadastrados</h2>
    <div class="rolagem">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Usuário</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM FORNECEDOR ORDER BY IDFORNECEDOR ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDFORNECEDOR']; ?></td>
                        <td><?php echo $row['IDUSUARIO']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td>
                            <a href="fornecedores.php?edit=<?php echo $row['IDFORNECEDOR']; ?>">
                                <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
                            </a>
                            <a href="fornecedores.php?delete=<?php echo $row['IDFORNECEDOR']; ?>" onclick="return confirm('Tem certeza?')">
                                <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4">Nenhum fornecedor encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</main>

</body>
</html>

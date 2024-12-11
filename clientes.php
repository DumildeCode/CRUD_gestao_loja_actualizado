<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE CLIENTE SET IDCLIENTE = (@count := @count + 1);";
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

// Editar cliente (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $client_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM CLIENTE WHERE IDCLIENTE = $client_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $client = $result->fetch_assoc();
    } else {
        echo "Cliente não encontrado ou erro na consulta.";
        $client = null;
    }
}

// Atualizar ou adicionar um cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_cliente'])) {
    $id_usuario = $conn->real_escape_string($_POST['id_usuario']);
    $nome_cliente = $conn->real_escape_string($_POST['nome_cliente']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE CLIENTE SET IDUSUARIO = '$id_usuario', nome_cliente = '$nome_cliente' WHERE IDCLIENTE = $id";
    } else {
        $sql = "INSERT INTO CLIENTE (IDUSUARIO, nome_cliente) VALUES ('$id_usuario', '$nome_cliente')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: clientes.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir cliente e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM CLIENTE WHERE IDCLIENTE = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: clientes.php");
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
    <title>Gestão de Clientes</title>
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
<h3>Clientes</h3>
    <h2><?php echo isset($client) ? 'Editar Cliente' : 'Adicionar Novo Cliente'; ?></h2>
    <form action="clientes.php" method="POST">
        <?php if (isset($client)): ?>
            <input type="hidden" name="id" value="<?php echo $client['IDCLIENTE']; ?>">
        <?php endif; ?>
        <label for="id_usuario">ID Usuário:</label>
        <input type="number" id="id_usuario" name="id_usuario" value="<?php echo isset($client['IDUSUARIO']) ? $client['IDUSUARIO'] : ''; ?>" required>

        <label for="nome_cliente">Nome do Cliente:</label>
        <input type="text" id="nome_cliente" name="nome_cliente" value="<?php echo isset($client['nome_cliente']) ? $client['nome_cliente'] : ''; ?>" required>

        <button type="submit"><?php echo isset($client) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Clientes Cadastrados</h2>
    <div class="rolagem">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Usuário</th>
                <th>Nome do Cliente</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM CLIENTE ORDER BY IDCLIENTE ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDCLIENTE']; ?></td>
                        <td><?php echo $row['IDUSUARIO']; ?></td>
                        <td><?php echo $row['nome_cliente']; ?></td>
                        <td>
                            <a href="clientes.php?edit=<?php echo $row['IDCLIENTE']; ?>">
                                <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
                            </a>
                            <a href="clientes.php?delete=<?php echo $row['IDCLIENTE']; ?>" onclick="return confirm('Tem certeza?')">
                                <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4">Nenhum cliente encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</main>

</body>
</html>

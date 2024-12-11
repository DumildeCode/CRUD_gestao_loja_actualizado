<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE VENDA SET IDVENDA = (@count := @count + 1);";
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

// Editar venda (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $venda_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM VENDA WHERE IDVENDA = $venda_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $venda = $result->fetch_assoc();
    } else {
        echo "Venda não encontrada ou erro na consulta.";
        $venda = null;
    }
}

// Atualizar ou adicionar uma venda
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_cliente'])) {
    $id_cliente = (int)$_POST['id_cliente'];
    $data = $conn->real_escape_string($_POST['data']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE VENDA SET IDCLIENTE = '$id_cliente', data = '$data' WHERE IDVENDA = $id";
    } else {
        $sql = "INSERT INTO VENDA (IDCLIENTE, data) VALUES ('$id_cliente', '$data')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: vendas.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir venda e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM VENDA WHERE IDVENDA = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: vendas.php");
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
    <title>Gestão de Vendas</title>
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
<h3>Vendas</h3>
    <h2><?php echo isset($venda) ? 'Editar Venda' : 'Adicionar Nova Venda'; ?></h2>
    <form action="vendas.php" method="POST">
        <?php if (isset($venda)): ?>
            <input type="hidden" name="id" value="<?php echo $venda['IDVENDA']; ?>">
        <?php endif; ?>
        <label for="id_cliente">ID Cliente:</label>
        <input type="number" id="id_cliente" name="id_cliente" value="<?php echo isset($venda['IDCLIENTE']) ? $venda['IDCLIENTE'] : ''; ?>" required>

        <label for="data">Data:</label>
        <input type="date" id="data" name="data" value="<?php echo isset($venda['data']) ? $venda['data'] : ''; ?>" required>

        <button type="submit"><?php echo isset($venda) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Vendas Cadastradas</h2>
    <div class="rolagem">
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>ID Cliente</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM VENDA ORDER BY IDVENDA ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDVENDA']; ?></td>
                        <td><?php echo $row['IDCLIENTE']; ?></td>
                        <td><?php echo $row['data']; ?></td>
                        <td>
                            <a href="vendas.php?edit=<?php echo $row['IDVENDA']; ?>">
                                <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
                            </a>
                            <a href="vendas.php?delete=<?php echo $row['IDVENDA']; ?>" onclick="return confirm('Tem certeza?')">
                                <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
                            </a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="4">Nenhuma venda encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
</main>
</body>
</html>

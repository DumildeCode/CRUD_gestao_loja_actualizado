<?php
include 'db_connection.php';

// Função para reordenar IDs após exclusão
function reordenarIDs($conn) {
    $sql = "SET @count = 0; UPDATE USUARIO SET IDUSUARIO = (@count := @count + 1);";
    if ($conn->multi_query($sql)) {
        // Necessário para processar múltiplas consultas
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

// Editar usuário (se o ID for passado via GET)
if (isset($_GET['edit'])) {
    $user_id = (int)$_GET['edit'];
    $sql = "SELECT * FROM USUARIO WHERE IDUSUARIO = $user_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "Usuário não encontrado ou erro na consulta.";
        $user = null;
    }
}

// Atualizar ou adicionar um usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    if (isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $sql = "UPDATE USUARIO SET nome = '$nome' WHERE IDUSUARIO = $id";
    } else {
        $sql = "INSERT INTO USUARIO (nome) VALUES ('$nome')";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: usuarios.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}

// Excluir usuário e reordenar IDs
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $sql = "DELETE FROM USUARIO WHERE IDUSUARIO = $delete_id";
    if ($conn->query($sql) === TRUE) {
        reordenarIDs($conn);
        header("Location: usuarios.php");
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
    <title>Gestão de Usuários</title>
    <link rel="stylesheet" href="../CRUD/assets/css/styles.css">
    <style>
        header,form button, table th,footer{
            background-color: rgb(6, 59, 90);
        }
    </style>
</head>
<body>
<header>
    <h1>Gestão de Usuários</h1>
    <nav>
        <a href="index.php">Voltar para Home</a>
    </nav>
</header>
<main>
    <h2><?php echo isset($user) ? 'Editar Usuário' : 'Adicionar Novo Usuário'; ?></h2>
    <form action="usuarios.php" method="POST">
        <?php if (isset($user)): ?>
            <input type="hidden" name="id" value="<?php echo $user['IDUSUARIO']; ?>">
        <?php endif; ?>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo isset($user['nome']) ? $user['nome'] : ''; ?>" required>
        <button type="submit"><?php echo isset($user) ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
    </form>

    <h2>Usuários Cadastrados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM USUARIO ORDER BY IDUSUARIO ASC");
            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['IDUSUARIO']; ?></td>
                        <td><?php echo $row['nome']; ?></td>
                        <td>
                            <a href="usuarios.php?edit=<?php echo $row['IDUSUARIO']; ?>">
    <img style="width: 30px;" src="assets/img/icons8_edit_property_50px_1.png" alt="Editar" title="Editar">
</a>
                            <a href="usuarios.php?delete=<?php echo $row['IDUSUARIO']; ?>" onclick="return confirm('Tem certeza?')">
    <img style="width: 30px;" src="assets/img/icons8_waste_50px.png" alt="Deletar" title="Deletar">
</a>
                        </td>
                    </tr>
                <?php endwhile;
            else: ?>
                <tr>
                    <td colspan="3">Nenhum usuário encontrado.</td>
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

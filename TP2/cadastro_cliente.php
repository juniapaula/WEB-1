<?php
session_start();
include 'dbConexao.php';

//verifica se o usuário está logado, se não, redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Faça login primeiro!'); window.location.href = 'login.php';</script>";
}

//verifica se há solicitação para editar cliente
if (isset($_GET['edit_client'])) {
    $id = $_GET['edit_client'];
    //busca os dados do cliente no banco
    $sql = "SELECT * FROM clientes WHERE id = $id";
    $result = $conn->query($sql);
    $cliente = $result->fetch_assoc(); //obtém os dados do cliente
}

//verifica se há solicitação para excluir cliente
if (isset($_GET['delete_client'])) {
    $id = $_GET['delete_client'];
    //deleta o cliente pelo ID
    $deleteQuery = "DELETE FROM clientes WHERE id = $id";
    $conn->query($deleteQuery);
    echo "<script>alert('Cliente excluído!'); window.location.href = 'cadastro_cliente.php';</script>";
}

// verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $modelo_carro = $_POST['modelo_carro'];
    $ano = $_POST['ano'];

    if (isset($_POST['id'])) {
        //atualiza os dados do cliente existente
        $id = $_POST['id'];
        $updateQuery = "UPDATE clientes SET nome = '$nome', telefone = '$telefone', endereco = '$endereco', modelo_carro = '$modelo_carro', ano = '$ano' WHERE id = $id";
        $conn->query($updateQuery);
        echo "<script>alert('Cliente atualizado com sucesso!');</script>";
    } else {
        $insertQuery = "INSERT INTO clientes (nome, telefone, endereco, modelo_carro, ano) 
                        VALUES ('$nome', '$telefone', '$endereco', '$modelo_carro', '$ano')";
        $conn->query($insertQuery);
        echo "<script>alert('Cliente cadastrado com sucesso!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cliente</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div class="menu-icon" onclick="toggleMenu()" aria-label="Abrir menu">&#9776;</div>
        <nav id="menu">
            <a href="index.php">Início</a>
            <a href="cadastro_cliente.php">Cadastro de Cliente</a>
            <a href="ordem_servico.php">Ordem de Serviço</a>
            <a href="cadastro_produto.php">Cadastro de Produtos</a>
            <?php if (isset($_SESSION['usuario'])): ?>
                <a href="logout.php">Sair</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
        <h1>Cadastro Cliente</h1>
    </header>
    <main>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo isset($cliente) ? $cliente['nome'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone">Telefone:</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo isset($cliente) ? $cliente['telefone'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" id="endereco" name="endereco" value="<?php echo isset($cliente) ? $cliente['endereco'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="modelo_carro">Modelo do Carro:</label>
                <input type="text" id="modelo_carro" name="modelo_carro" value="<?php echo isset($cliente) ? $cliente['modelo_carro'] : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="ano">Ano:</label>
                <input type="number" id="ano" name="ano" value="<?php echo isset($cliente) ? $cliente['ano'] : ''; ?>" required>
            </div>
            <?php if (isset($cliente)): ?>
                <!-- envia o ID do cliente em edição -->
                <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">
                <button type="submit">Atualizar Cliente</button>
            <?php else: ?>
                <button type="submit">Cadastrar Cliente</button>
            <?php endif; ?>
        </form>
        <h2>Clientes Cadastrados</h2>
        <table>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Endereço</th>
                <th>Modelo</th>
                <th>Ano</th>
                <th>Ações</th>
            </tr>
            <?php
            //busca todos os clientes cadastrados no banco
            $sql = "SELECT * FROM clientes";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                //mostra cada cliente em uma linha da tabela
                echo "<tr>
                        <td>{$row['nome']}</td>
                        <td>{$row['telefone']}</td>
                        <td>{$row['endereco']}</td>
                        <td>{$row['modelo_carro']}</td>
                        <td>{$row['ano']}</td>
                        <td>
                        <a href='ordem_servico.php?edit_order={$row['id']}' class='action-button'>Editar</a> | 
                        <a href='ordem_servico.php?delete_order={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir esta ordem de serviço?\");' class='action-button delete'>Excluir</a>
                        </td>
                    </tr>";
            }
            ?>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
    </footer>
    <script>
        //aterna a exibição do menu
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }
    </script>
</body>

</html>
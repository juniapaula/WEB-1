<?php
session_start();
include 'dbConexao.php';

//verifica se o usuário está logado; se não, redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Faça login primeiro!'); window.location.href = 'login.php';</script>";
}

//verifica se a URL contém o parâmetro 'delete_product' e realiza a exclusão do produto
if (isset($_GET['delete_product'])) {
    $id = $_GET['delete_product']; //obtém o ID do produto a ser excluído
    $deleteQuery = "DELETE FROM produtos WHERE id = $id"; //query para deletar o produto
    $conn->query($deleteQuery); //executa a query
    echo "<script>alert('Produto excluído com sucesso!'); window.location.href = 'cadastro_produto.php';</script>";
}

//verifica se a URL contém o parâmetro 'edit_product' e obtém os dados do produto para edição
if (isset($_GET['edit_product'])) {
    $id = $_GET['edit_product']; //obtém o ID do produto a ser editado
    $sql = "SELECT * FROM produtos WHERE id = $id"; //query para buscar o produto
    $result = $conn->query($sql); //executa a query
    $produto = $result->fetch_assoc(); //armazena os dados do produto
}

//processa a edição de produto enviada via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produto_id'])) {
    $produto_id = $_POST['produto_id']; //ID do produto a ser atualizado
    $produto_nome = $_POST['produto_nome']; //novo nome do produto
    $produto_preco = $_POST['produto_preco']; //novo preço do produto
    $produto_quantidade = $_POST['produto_quantidade']; //nova quantidade do produto

    //query para atualizar o produto
    $updateProdutoQuery = "UPDATE produtos SET nome = '$produto_nome', preco = '$produto_preco', quantidade = '$produto_quantidade' WHERE id = $produto_id";
    $conn->query($updateProdutoQuery); //executa a query
    echo "<script>alert('Produto atualizado com sucesso!'); window.location.href = 'cadastro_produto.php';</script>";
}

//processa o cadastro de um novo produto enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['produto_id'])) {
    $produto_nome = $_POST['produto_nome']; //nome do novo produto
    $produto_preco = $_POST['produto_preco']; //preço do novo produto
    $produto_quantidade = $_POST['produto_quantidade']; //quantidade do novo produto

    //query para inserir o novo produto no banco de dados
    $insertProdutoQuery = "INSERT INTO produtos (nome, preco, quantidade) VALUES ('$produto_nome', '$produto_preco', '$produto_quantidade')";
    $conn->query($insertProdutoQuery); //executa a query
    echo "<script>alert('Produto cadastrado com sucesso!');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos</title>
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
        <h1>Cadastro de Produtos</h1>
    </header>

    <main>
        <h2><?php echo isset($produto) ? 'Editar Produto' : 'Cadastrar Produto'; ?></h2>
        <form method="POST">
            <div class="form-group">
                <label for="produto_nome">Nome do Produto:</label>
                <input type="text" id="produto_nome" name="produto_nome" value="<?php echo isset($produto) ? $produto['nome'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="produto_preco">Preço:</label>
                <input type="number" step="0.01" id="produto_preco" name="produto_preco" value="<?php echo isset($produto) ? $produto['preco'] : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="produto_quantidade">Quantidade:</label>
                <input type="number" id="produto_quantidade" name="produto_quantidade" value="<?php echo isset($produto) ? $produto['quantidade'] : ''; ?>" required>
            </div>

            <!-- Exibe botão de atualizar ou cadastrar -->
            <?php if (isset($produto)): ?>
                <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                <button type="submit">Atualizar Produto</button>
            <?php else: ?>
                <button type="submit">Cadastrar Produto</button>
            <?php endif; ?>
        </form>
        <h2>Produtos Cadastrados</h2>
        <table>
            <tr>
                <th>Nome</th>
                <th>Preço</th>
                <th>Quantidade</th>
                <th>Ações</th>
            </tr>
            <?php
            //consulta os produtos cadastrados no banco de dados
            $produtosQuery = "SELECT * FROM produtos";
            $produtosResult = $conn->query($produtosQuery);
            while ($row = $produtosResult->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['nome']}</td>
                    <td>R$ {$row['preco']}</td>
                    <td>{$row['quantidade']}</td>
                    <td>
                        <a href='cadastro_produto.php?edit_product={$row['id']}' class='action-button'>Editar</a> | 
                        <a href='cadastro_produto.php?delete_product={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir este produto?\");' class='action-button delete'>Excluir</a>
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
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }
    </script>
</body>

</html>
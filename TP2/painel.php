<?php
session_start();
include 'dbConexao.php';

//verifica se o usuário está logado, se não redireciona para a página de login
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Faça login primeiro!'); window.location.href = 'login.php';</script>";
    exit(); //interrompe a execução do código caso o usuário não esteja logado
}

//exclui ordem de serviço com prepared statements
if (isset($_GET['delete_order'])) {
    $id = intval($_GET['delete_order']); //obtém o ID da ordem de serviço a ser excluída
    //prepara a query para excluir a ordem de serviço com segurança
    $deleteQuery = $conn->prepare("DELETE FROM ordens_servico WHERE id = ?");
    $deleteQuery->bind_param("i", $id); //bind do parâmetro de ID como inteiro
    if ($deleteQuery->execute()) { //executa a query
        echo "<script>alert('Ordem de serviço excluída com sucesso!'); window.location.href = 'painel.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir ordem de serviço.');</script>";
    }
    $deleteQuery->close(); //fecha a query após execução
}

//exclui cliente com prepared statements
if (isset($_GET['delete_client'])) {
    $id = intval($_GET['delete_client']); //obtém o ID do cliente a ser excluído
    //prepara a query para excluir o cliente com segurança
    $deleteQuery = $conn->prepare("DELETE FROM clientes WHERE id = ?");
    $deleteQuery->bind_param("i", $id); //bind do parâmetro de ID como inteiro
    if ($deleteQuery->execute()) { //executa a query
        echo "<script>alert('Cliente excluído com sucesso!'); window.location.href = 'painel.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir cliente.');</script>";
    }
    $deleteQuery->close(); //fecha a query após execução
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle</title>
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
            <a href="logout.php">Sair</a>
        </nav>
        <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</h1>
    </header>

    <main>
        <h2>Ordens de Serviço:</h2>
        <table>
            <tr>
                <th>Modelo</th>
                <th>Placa</th>
                <th>Serviços</th>
                <th>Mecânico</th>
                <th>Ações</th>
            </tr>
            <?php
            //consulta as ordens de serviço no banco de dados
            $sql = "SELECT * FROM ordens_servico";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) { //exibe cada ordem de serviço em uma linha da tabela
                echo "<tr>
                        <td>" . htmlspecialchars($row['modelo']) . "</td>
                        <td>" . htmlspecialchars($row['placa']) . "</td>
                        <td>" . htmlspecialchars($row['servicos']) . "</td>
                        <td>" . htmlspecialchars($row['mecanico']) . "</td>
                        <td>
                            <a href='javascript:void(0);' onclick='openOrderDetails({$row['id']})' class='action-button'>Ver Detalhes</a> | 
                            <a href='ordem_servico.php?edit_order={$row['id']}' class='action-button'>Editar</a> | 
                            <a href='painel.php?delete_order={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir esta ordem de serviço?\");' class='action-button delete'>Excluir</a>
                        </td>
                    </tr>";
            }
            ?>
        </table>

        <h2>Clientes Cadastrados:</h2>
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
            //consulta os clientes cadastrados no banco de dados
            $sql = "SELECT * FROM clientes";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) { //exibe cada cliente em uma linha da tabela
                echo "<tr>
                        <td>" . htmlspecialchars($row['nome']) . "</td>
                        <td>" . htmlspecialchars($row['telefone']) . "</td>
                        <td>" . htmlspecialchars($row['endereco']) . "</td>
                        <td>" . htmlspecialchars($row['modelo_carro']) . "</td>
                        <td>" . htmlspecialchars($row['ano']) . "</td>
                        <td>
                            <a href='cadastro_cliente.php?edit_client={$row['id']}' class='action-button'>Editar</a> | 
                            <a href='painel.php?delete_client={$row['id']}' onclick='return confirm(\"Tem certeza que deseja excluir este cliente?\");' class='action-button delete'>Excluir</a>
                        </td>
                    </tr>";
            }
            ?>
        </table>
    </main>

    <footer>
        <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
    </footer>

    <!-- Modal para exibir os detalhes da ordem de serviço -->
    <div id="modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Detalhes da Ordem de Serviço</h2>
            <div id="order-details">
                <!-- Os detalhes carregados pelo PHP serão inseridos aqui -->
            </div>
        </div>
    </div>

    <script>
        //função para abrir o modal com os detalhes da ordem de serviço
        function openOrderDetails(orderId) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'detalheOrdem.php?id=' + orderId, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('order-details').innerHTML = xhr.responseText;
                    document.getElementById('modal').style.display = 'block'; //exibe o modal
                } else {
                    alert('Erro ao carregar detalhes da ordem de serviço.');
                }
            };
            xhr.send();
        }

        //fecha o modal quando o botão de fechar é clicado
        document.querySelector('.close-btn').onclick = function() {
            document.getElementById('modal').style.display = 'none';
        };

        //fecha o modal quando clicar fora do conteúdo
        window.onclick = function(event) {
            if (event.target === document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
            }
        };

        function toggleMenu() {
            document.getElementById("menu").classList.toggle("show");
        }
    </script>
</body>

</html>
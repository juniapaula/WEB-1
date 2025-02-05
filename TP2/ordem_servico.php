<?php
session_start();
include 'dbConexao.php';

//verifica se o usuário está autenticado
if (!isset($_SESSION['usuario'])) {
    echo "<script>alert('Faça login primeiro!'); window.location.href = 'login.php';</script>"; // Redireciona para o login caso não esteja autenticado
}

if (isset($_GET['edit_order'])) { //edição de ordem de serviço
    $id = $_GET['edit_order']; //obtém o ID da ordem de serviço a ser editada
    $sql = "SELECT * FROM ordens_servico WHERE id = $id"; //consulta o banco para obter os dados da ordem de serviço
    $result = $conn->query($sql); //executa a consulta SQL
    $order = $result->fetch_assoc(); //obtém os dados da ordem de serviço

    //consulta os produtos associados à ordem de serviço
    $order_id = $order['id'];
    $productsQuery = "SELECT p.nome, op.quantidade FROM produtos_ordem_servico op JOIN produtos p ON op.produto_id = p.id WHERE op.ordem_servico_id = $order_id";
    $productsResult = $conn->query($productsQuery);
    $orderProducts = [];
    while ($row = $productsResult->fetch_assoc()) {
        $orderProducts[] = $row; //armazena os produtos da ordem de serviço em um array
    }
}

if (isset($_GET['delete_order'])) { //exclui ordem de serviço
    $id = $_GET['delete_order']; //obtém o ID da ordem de serviço a ser excluída
    //remove todos os produtos associados à ordem de serviço
    $deleteQuery = "DELETE FROM produtos_ordem_servico WHERE ordem_servico_id = $id";
    $conn->query($deleteQuery); //executa a exclusão
    $deleteQuery = "DELETE FROM ordens_servico WHERE id = $id"; //eclui a ordem de serviço
    $conn->query($deleteQuery); //executa a exclusão
    echo "<script>alert('Ordem de serviço excluída!'); window.location.href = 'ordem_servico.php';</script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { //processa o cadastro ou atualização da ordem de serviço
    //obtém os dados do formulário
    $hora_chegada = $_POST['hora_chegada'];
    $hora_saida = $_POST['hora_saida'];
    $modelo = $_POST['modelo'];
    $placa = $_POST['placa'];
    $ano = $_POST['ano'];
    $servicos = $_POST['servicos'];
    $mecanico = $_POST['mecanico'];
    $produtos = $_POST['produtos'];
    $cliente_id = $_POST['cliente_id'];

    if (isset($_POST['id'])) {
        //atualiza ordem de serviço
        $id = $_POST['id'];
        $updateQuery = "UPDATE ordens_servico SET hora_chegada = '$hora_chegada', hora_saida = '$hora_saida', modelo = '$modelo', placa = '$placa', ano = '$ano', servicos = '$servicos', mecanico = '$mecanico', cliente_id = $cliente_id WHERE id = $id";
        $conn->query($updateQuery); //executa a atualização da ordem de serviço
        //remove os produtos antigos associados à ordem de serviço
        $deleteQuery = "DELETE FROM produtos_ordem_servico WHERE ordem_servico_id = $id";
        $conn->query($deleteQuery);
    } else {
        //cadastra nova ordem de serviço
        $insertQuery = "INSERT INTO ordens_servico (hora_chegada, hora_saida, modelo, placa, ano, servicos, mecanico, cliente_id)
                        VALUES ('$hora_chegada', '$hora_saida', '$modelo', '$placa', '$ano', '$servicos', '$mecanico', $cliente_id)";
        $conn->query($insertQuery); //executa o cadastro da nova ordem de serviço
        $id = $conn->insert_id; //obtém o ID da nova ordem de serviço
    }

    //adiciona os produtos à ordem de serviço, se houver
    if ($produtos) {
        foreach ($produtos as $produto_id => $quantidade) {
            //adiciona o produto à ordem de serviço
            $insertProdutoQuery = "INSERT INTO produtos_ordem_servico (ordem_servico_id, produto_id, quantidade) VALUES ($id, $produto_id, $quantidade)";
            $conn->query($insertProdutoQuery); //executa a inserção do produto na ordem de serviço

            //atualiza o estoque, subtraindo a quantidade do produto
            $updateProdutoQuery = "UPDATE produtos SET quantidade = quantidade - $quantidade WHERE id = $produto_id";
            $conn->query($updateProdutoQuery); //executa a atualização do estoque
        }
    }
    echo "<script>alert('Ordem de serviço cadastrada/atualizada com sucesso!'); window.location.href = 'ordem_servico.php';</script>"; // Exibe uma mensagem de sucesso e redireciona
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Serviço</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleMenu() {
            const menu = document.getElementById("menu");
            menu.classList.toggle("show");
        }
    </script>
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
        <h1>Ordem de Serviço</h1>
    </header>

    <main>
        <form method="POST">
            <fieldset>
                <legend>Ordem de Serviço</legend>
                <div class="form-group">
                    <label for="cliente_id">Cliente:</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <option value="">Selecione um Cliente</option>
                        <?php
                        //consulta os clientes no banco de dados
                        $clientesQuery = "SELECT id, nome FROM clientes";
                        $clientesResult = $conn->query($clientesQuery);

                        //verifica se a consulta foi bem-sucedida
                        if ($clientesResult === false) {
                            die('Erro na consulta de clientes: ' . $conn->error);
                        }

                        //preenche o select com as opções de clientes
                        while ($row = $clientesResult->fetch_assoc()) {
                            echo "<option value='{$row['id']}'" . (isset($order) && $order['cliente_id'] == $row['id'] ? ' selected' : '') . ">{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hora_chegada">Hora de Chegada:</label>
                    <input type="time" id="hora_chegada" name="hora_chegada" value="<?php echo isset($order) ? $order['hora_chegada'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="hora_saida">Hora de Saída:</label>
                    <input type="time" id="hora_saida" name="hora_saida" value="<?php echo isset($order) ? $order['hora_saida'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo do Carro:</label>
                    <input type="text" id="modelo" name="modelo" value="<?php echo isset($order) ? $order['modelo'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="placa">Placa:</label>
                    <input type="text" id="placa" name="placa" value="<?php echo isset($order) ? $order['placa'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ano">Ano:</label>
                    <input type="number" id="ano" name="ano" value="<?php echo isset($order) ? $order['ano'] : ''; ?>" required>
                </div>
                <div class="form-group">
                    <label for="servicos">Serviços Realizados:</label>
                    <textarea id="servicos" name="servicos" required><?php echo isset($order) ? $order['servicos'] : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="mecanico">Mecânico:</label>
                    <select id="mecanico" name="mecanico" required>
                        <option value="">Selecione um Mecânico</option>
                        <?php
                        //consulta os mecânicos no banco de dados
                        $mecanicosQuery = "SELECT id, nome FROM mecanicos";
                        $mecanicosResult = $conn->query($mecanicosQuery);

                        //verifica se a consulta foi bem-sucedida
                        if ($mecanicosResult === false) {
                            die('Erro na consulta de mecânicos: ' . $conn->error);
                        }

                        //preenche o select com as opções de mecânicos
                        while ($row = $mecanicosResult->fetch_assoc()) {
                            echo "<option value='{$row['id']}'" . (isset($order) && $order['mecanico'] == $row['id'] ? ' selected' : '') . ">{$row['nome']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="produtos">Produtos:</label>
                    <div id="produtos-list">
                        <?php
                        //consulta os produtos disponíveis no banco de dados
                        $produtosQuery = "SELECT * FROM produtos WHERE quantidade > 0";
                        $produtosResult = $conn->query($produtosQuery);

                        //verifica se a consulta foi bem-sucedida
                        if ($produtosResult === false) {
                            die('Erro na consulta de produtos: ' . $conn->error);
                        }

                        //preenche o formulário com os produtos e suas quantidades
                        while ($row = $produtosResult->fetch_assoc()) {
                            echo "
                            <div class='produto-item'>
                                <label for='produto_{$row['id']}'>
                                    <input type='checkbox' name='produtos[{$row['id']}]' value='{$row['id']}' id='produto_{$row['id']}'>
                                    {$row['nome']} - R$ {$row['preco']}
                                </label>
                                <label for='quantidade_{$row['id']}'>Quantidade:</label>
                                <input type='number' name='quantidade[{$row['id']}]' id='quantidade_{$row['id']}' min='1' max='{$row['quantidade']}' value='1' class='quantidade-input' disabled>
                            </div>
                            <br>";
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn">Salvar</button>
                </div>
                <?php if (isset($order)) { ?>
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                <?php } ?>
            </fieldset>
        </form>

        <!-- Exibir ordens de serviço cadastradas -->
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
        </main>

        <footer>
            <p>&copy; 2024 Oficina Mecânica - Qualidade que você pode confiar.</p>
        </footer>

        <!-- Modal para exibir os detalhes da ordem de serviço -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Detalhes da Ordem de Serviço</h2>
                <div id="order-details"></div>
            </div>
        </div>

        <script>
            //habilita o campo de quantidade quando o checkbox for selecionado
            document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const quantidadeInput = document.getElementById('quantidade_' + this.value);
                    quantidadeInput.disabled = !this.checked; //habilita ou desabilita o campo de quantidade
                });
            });

            //função para abrir o modal com os detalhes da ordem de serviço
            function openOrderDetails(orderId) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'detalheOrdem.php?id=' + orderId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('order-details').innerHTML = xhr.responseText;
                        document.getElementById('modal').style.display = 'block'; //mostra modal
                    }
                };
                xhr.send();
            }

            //fecha o modal ao clicar no botão de fechar
            document.querySelector('.close-btn').onclick = function() {
                document.getElementById('modal').style.display = 'none'; //oculta modal
            }

            //fecha o modal clicando fora da caixa modal
            window.onclick = function(event) {
                if (event.target === document.getElementById('modal')) {
                    document.getElementById('modal').style.display = 'none'; //oculta modal
                }
            }
        </script>
</body>

</html>
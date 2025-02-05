<?php
include 'dbConexao.php';

if (isset($_GET['id'])) { //verifica se o parâmetro 'id' foi passado na URL
    $id = intval($_GET['id']); //converte o valor do ID para um inteiro para evitar injeção de SQL

    //consulta para buscar detalhes da ordem de serviço com base no ID fornecido
    $sql = "SELECT * FROM ordens_servico WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) { //verifica se a consulta retornou algum resultado
        $order = $result->fetch_assoc(); //obtém os detalhes da ordem de serviço

        //exibe os detalhes da ordem de serviço
        echo "<p><strong>Modelo:</strong> {$order['modelo']}</p>";
        echo "<p><strong>Placa:</strong> {$order['placa']}</p>";
        echo "<p><strong>Ano:</strong> {$order['ano']}</p>";
        echo "<p><strong>Serviços Realizados:</strong> {$order['servicos']}</p>";
        echo "<p><strong>Mecânico:</strong> {$order['mecanico']}</p>";

        //consulta para buscar os produtos associados à ordem de serviço
        echo "<h3>Produtos Usados:</h3>";
        $productsQuery = "
            SELECT p.nome, op.quantidade 
            FROM produtos_ordem_servico op
            LEFT JOIN produtos p ON op.produto_id = p.id
            WHERE op.ordem_servico_id = $id";
        $productsResult = $conn->query($productsQuery);

        //verifica se há produtos associados e exibe-os
        if ($productsResult->num_rows > 0) {
            echo "<ul>";
            while ($product = $productsResult->fetch_assoc()) {
                echo "<li>{$product['nome']} - Quantidade: {$product['quantidade']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nenhum produto usado nesta ordem.</p>";
        }
    } else {
        echo "<p>Ordem de serviço não encontrada.</p>";
    }
} else {
    echo "<p>ID inválido.</p>";
}

<?php
require_once 'Conexao.php';

try {
    $pdo = Conexao::getConexao();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        if ($nome && $email) {
            $stmt = $pdo->prepare('INSERT INTO registros (nome, email) VALUES (:nome, :email)');
            $stmt->execute(['nome' => $nome, 'email' => $email]);
            echo json_encode(['sucesso' => true]);
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Campos obrigatórios ausentes.']);
        }
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $dados = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('UPDATE registros SET nome = :nome, email = :email WHERE id = :id');
        $stmt->execute(['nome' => $dados['nome'], 'email' => $dados['email'], 'id' => $dados['id']]);
        echo json_encode(['sucesso' => true]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $dados = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare('DELETE FROM registros WHERE id = :id');
        $stmt->execute(['id' => $dados['id']]);
        echo json_encode(['sucesso' => true]);
        exit;
    }

    $stmt = $pdo->query('SELECT * FROM registros');
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($registros ?: []);
} catch (PDOException $e) {
    echo json_encode(['erro' => $e->getMessage()]);
}

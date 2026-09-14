<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {

    $tipo = $_POST['tipo'];
    $valor = (float) $_POST['valor'];
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];

    $sql = "INSERT INTO transacoes (tipo, valor, descricao, data)
            VALUES (:tipo, :valor, :descricao, :data)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':tipo' => $tipo,
        ':valor' => $valor,
        ':descricao' => $descricao,
        ':data' => $data
    ]);

    $id = $pdo->lastInsertId();

    $_SESSION['mensagem'] = 'Transação cadastrada com sucesso!';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit;
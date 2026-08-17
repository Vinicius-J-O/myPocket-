<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {

    $id = (int) $_POST['id'];
    $tipo = $_POST['tipo'];
    $valor = (float) $_POST['valor'];
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];

    $sql = "UPDATE transacoes
            SET tipo = :tipo,
                valor = :valor,
                descricao = :descricao,
                data = :data
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id,
        ':tipo' => $tipo,
        ':valor' => $valor,
        ':descricao' => $descricao,
        ':data' => $data
    ]);

    $_SESSION['mensagem'] = 'Transação atualizada com sucesso!';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit;
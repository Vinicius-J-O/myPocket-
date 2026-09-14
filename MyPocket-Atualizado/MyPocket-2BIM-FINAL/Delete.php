<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {

    $id = (int) $_POST['id'];

    $sql = "DELETE FROM transacoes WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':id' => $id
    ]);

    $_SESSION['mensagem'] = 'Transação excluída com sucesso!';

} catch (Exception $e) {

    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit; 
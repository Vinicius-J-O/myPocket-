<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {
    $valorDiario = (float) $_POST['valor_diario'];

    if ($valorDiario < 0) {
        throw new Exception('O valor diário não pode ser negativo.');
    }

    $pdo->exec("CREATE TABLE IF NOT EXISTS diario (id INT NOT NULL PRIMARY KEY, valor_diario DECIMAL(12,2) NOT NULL DEFAULT 0.00)");

    $stmt = $pdo->prepare("INSERT INTO diario (id, valor_diario) VALUES (1, :valor) ON DUPLICATE KEY UPDATE valor_diario = :valor2");
    $stmt->execute([
        ':valor' => $valorDiario,
        ':valor2' => $valorDiario
    ]);

    $_SESSION['mensagem'] = 'Valor diário atualizado com sucesso!';
} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit;

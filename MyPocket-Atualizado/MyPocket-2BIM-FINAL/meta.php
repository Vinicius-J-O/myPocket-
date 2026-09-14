<?php

declare(strict_types=1);

session_start();

require_once 'conexao.php';

try {
    $nome = trim($_POST['nome']);
    $valorObjetivo = (float) $_POST['valor_objetivo'];
    $valorAtual = (float) $_POST['valor_atual'];
    $dataLimite = $_POST['data_limite'];

    if ($nome === '') {
        throw new Exception('Informe o nome da meta.');
    }

    if ($valorObjetivo <= 0) {
        throw new Exception('O valor objetivo deve ser maior que zero.');
    }

    if ($valorAtual < 0) {
        throw new Exception('O valor atual não pode ser negativo.');
    }

    $sql = "INSERT INTO metas (nome, valor_objetivo, valor_atual, data_limite)
            VALUES (:nome, :valor_objetivo, :valor_atual, :data_limite)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':nome' => $nome,
        ':valor_objetivo' => $valorObjetivo,
        ':valor_atual' => $valorAtual,
        ':data_limite' => $dataLimite
    ]);

    $_SESSION['mensagem'] = 'Meta cadastrada com sucesso!';
} catch (Exception $e) {
    $_SESSION['erro'] = $e->getMessage();
}

header('Location: index.php');
exit;

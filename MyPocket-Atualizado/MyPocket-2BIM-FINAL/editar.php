<?php

declare(strict_types=1);

require_once 'conexao.php';

$id = (int) $_GET['id'];

$sql = "SELECT * FROM transacoes WHERE id = :id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$transacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$transacao) {
    die('Transação não encontrada.');
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Editar Transação</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="card">

            <div class="card-body">

                <h2 class="mb-4">
                    Editar Transação
                </h2>

                <form action="update.php" method="POST">

                    <input
                        type="hidden"
                        name="id"
                        value="<?= $transacao['id']; ?>">

                    <div class="mb-3">

                        <label class="form-label">
                            Tipo
                        </label>

                        <select
                            name="tipo"
                            class="form-select">

                            <option
                                value="receita"
                                <?= $transacao['tipo'] === 'receita' ? 'selected' : ''; ?>>

                                Receita

                            </option>

                            <option
                                value="despesa"
                                <?= $transacao['tipo'] === 'despesa' ? 'selected' : ''; ?>>

                                Despesa

                            </option>

                        </select>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Valor
                        </label>

                        <input
                            type="number"
                            step="0.01"
                            name="valor"
                            class="form-control"
                            value="<?= $transacao['valor']; ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Descrição
                        </label>

                        <input
                            type="text"
                            name="descricao"
                            class="form-control"
                            value="<?= htmlspecialchars($transacao['descricao']); ?>"
                            required>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">
                            Data
                        </label>

                        <input
                            type="date"
                            name="data"
                            class="form-control"
                            value="<?= $transacao['data']; ?>"
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        Salvar alterações

                    </button>

                    <a
                        href="index.php"
                        class="btn btn-secondary">

                        Cancelar

                    </a>

                </form>

            </div>

        </div>

    </div>

</body>

</html>
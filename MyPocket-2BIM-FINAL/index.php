<?php

declare(strict_types=1);

session_start();

require_once 'classes/Transacao.php';
require_once 'classes/Receita.php';
require_once 'classes/Despesa.php';
require_once 'classes/Carteira.php';
require_once 'conexao.php';

$carteira = new Carteira($pdo);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>MyPocket</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        .dark-mode {
            background-color: #121212 !important;
            color: #ffffff !important;
        }

        .dark-mode .card {
            background-color: #1e1e1e;
            color: #ffffff;
            border-color: #333;
        }

        .dark-mode .table {
            color: #ffffff;
        }

        .dark-mode .table th,
        .dark-mode .table td {
            color: #ffffff;
        }

        .dark-mode .form-control,
        .dark-mode .form-select {
            background-color: #2c2c2c;
            color: #ffffff;
            border-color: #444;
        }

        .dark-mode .form-control:focus,
        .dark-mode .form-select:focus {
            background-color: #2c2c2c;
            color: #ffffff;
        }

        .dark-mode .form-control::placeholder {
            color: #bbbbbb;
        }

        .dark-mode .table {
            --bs-table-bg: #1e1e1e;
        }
    </style>

</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h1 class="mb-0">
                MyPocket
            </h1>

            <button
                id="temaBtn"
                class="btn btn-dark">
                Modo Escuro
            </button>

        </div>

        <?php if (isset($_SESSION['mensagem'])): ?>

            <div class="alert alert-success">

                <?= $_SESSION['mensagem']; ?>

            </div>

            <?php unset($_SESSION['mensagem']); ?>

        <?php endif; ?>

        <?php if (isset($_SESSION['erro'])): ?>

            <div class="alert alert-danger">

                <?= $_SESSION['erro']; ?>

            </div>

            <?php unset($_SESSION['erro']); ?>

        <?php endif; ?>

        <div class="card mb-4">

            <div class="card-body">

                <h3>Saldo Atual</h3>

                <h2 class="text-primary">

                    R$
                    <?= number_format(
                        $carteira->getSaldo(),
                        2,
                        ',',
                        '.'
                    ); ?>

                </h2>

            </div>

        </div>

        <div class="card mb-4">

            <div class="card-body">

                <h3>Nova Transação</h3>

               
                </form>

                <form action="processa.php" method="POST">

                    
                    <div class="mb-3">

                        <label class="form-label">
                            Tipo
                        </label>

                        <select
                            name="tipo"
                            class="form-select"
                            required>

                            <option value="receita">
                                Receita
                            </option>

                            <option value="despesa">
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
                            required>

                    </div>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Cadastrar

                    </button>
                  

                    
                    

                </form>

            </div>

        </div>

        <div class="card">

            <div class="card-body">

                <h3 class="mb-3">
                    Extrato
                </h3>

                <table class="table">

                    <thead>

                        <tr>

                            <th>Data</th>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>ID</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($carteira->getHistorico() as $transacao): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($transacao->getData()); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($transacao->getDescricao()); ?>
                                </td>

                                <td>

                                    <?php if ($transacao->getTipo() === 'Entrada'): ?>

                                        <span class="badge bg-success">
                                            Entrada
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Saída
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    R$
                                    <?= number_format(
                                        $transacao->getValor(),
                                        2,
                                        ',',
                                        '.'
                                    ); ?>

                                </td>

                                <td>
                                   ID:
                                    <?= $transacao->getId(); ?>
                                </td>

                                    </tr>
                                        
                                        
                         <td>

                        <a
                            href="editar.php?id=<?= $transacao->getId(); ?>"
                            class="btn btn-warning btn-sm">

                            Editar

                        </a>

                        <form
                            action="delete.php"
                            method="POST"
                            style="display:inline;">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= $transacao->getId(); ?>">

                            <button
                                type="submit"
                                class="btn btn-danger btn-sm">

                                Excluir

                            </button>

                        </form>

                    </td>

                </td>

                        <?php endforeach; ?>

                        

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script>

        const temaBtn = document.getElementById('temaBtn');

        if (localStorage.getItem('tema') === 'dark') {

            document.body.classList.remove('bg-light');
            document.body.classList.add('dark-mode');

            temaBtn.innerHTML = 'Modo Claro';

        }

        temaBtn.addEventListener('click', () => {

            document.body.classList.toggle('dark-mode');

            if (document.body.classList.contains('dark-mode')) {

                document.body.classList.remove('bg-light');

                localStorage.setItem(
                    'tema',
                    'dark'
                );

                temaBtn.innerHTML =
                    'Modo Claro';

            } else {

                document.body.classList.add('bg-light');

                localStorage.setItem(
                    'tema',
                    'light'
                );

                temaBtn.innerHTML =
                    'Modo Escuro';

            }

        });

    </script>

</body>

</html>
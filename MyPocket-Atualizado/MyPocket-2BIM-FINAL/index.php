<?php

declare(strict_types=1);

session_start();

require_once 'classes/Transacao.php';
require_once 'classes/Receita.php';
require_once 'classes/Despesa.php';
require_once 'classes/Carteira.php';
require_once 'classes/Meta.php';
require_once 'conexao.php';

$carteira = new Carteira($pdo);

$pdo->exec("CREATE TABLE IF NOT EXISTS diario (id INT NOT NULL PRIMARY KEY, valor_diario DECIMAL(12,2) NOT NULL DEFAULT 0.00)");
$pdo->exec("INSERT INTO diario (id, valor_diario) VALUES (1, 0.00) ON DUPLICATE KEY UPDATE id = id");

$valorDiario = (float) $pdo->query("SELECT valor_diario FROM diario WHERE id = 1")->fetchColumn();
$diasDoMes = (int) date('t');
$totalDiario = $valorDiario * $diasDoMes;

$inicioMes = date('Y-m-01');
$fimMes = date('Y-m-t');

$stmtResumo = $pdo->prepare("SELECT
    COALESCE(SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END), 0) AS entradas,
    COALESCE(SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END), 0) AS saidas
    FROM transacoes
    WHERE data BETWEEN :inicio AND :fim");
$stmtResumo->execute([
    ':inicio' => $inicioMes,
    ':fim' => $fimMes
]);
$resumo = $stmtResumo->fetch(PDO::FETCH_ASSOC);
$entradasMes = (float) $resumo['entradas'];
$saidasMes = (float) $resumo['saidas'];
$performance = $entradasMes - $saidasMes - $totalDiario;

$stmtMetas = $pdo->query("SELECT id, nome, valor_objetivo, valor_atual, data_limite FROM metas ORDER BY data_limite ASC, id DESC");
$metas = $stmtMetas->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyPocket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .dark-mode { background-color: #121212 !important; color: #ffffff !important; }
        .dark-mode .card { background-color: #1e1e1e; color: #ffffff; border-color: #333; }
        .dark-mode .table { color: #ffffff; --bs-table-bg: #1e1e1e; }
        .dark-mode .table th, .dark-mode .table td { color: #ffffff; }
        .dark-mode .form-control, .dark-mode .form-select { background-color: #2c2c2c; color: #ffffff; border-color: #444; }
        .dark-mode .form-control:focus, .dark-mode .form-select:focus { background-color: #2c2c2c; color: #ffffff; }
        .dark-mode .form-control::placeholder { color: #bbbbbb; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">MyPocket</h1>
        <button id="temaBtn" class="btn btn-dark">Modo Escuro</button>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensagem']); ?></div>
        <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erro'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['erro']); ?></div>
        <?php unset($_SESSION['erro']); ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <h3>Saldo Atual</h3>
            <h2 class="text-primary">R$ <?= number_format($carteira->getSaldo(), 2, ',', '.'); ?></h2>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h3>Diário</h3>
            <form action="diario.php" method="POST" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Valor diário</label>
                    <input type="number" step="0.01" min="0" name="valor_diario" class="form-control" value="<?= number_format($valorDiario, 2, '.', ''); ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dias do mês</label>
                    <input type="text" class="form-control" value="<?= $diasDoMes; ?>" readonly>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">Salvar Diário</button>
                </div>
            </form>
            <div class="row mt-4">
                <div class="col-md-3"><strong>Entrada</strong><br>R$ <?= number_format($entradasMes, 2, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Saída</strong><br>R$ <?= number_format($saidasMes, 2, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Diário</strong><br>R$ <?= number_format($totalDiario, 2, ',', '.'); ?></div>
                <div class="col-md-3"><strong>Performance</strong><br>R$ <?= number_format($performance, 2, ',', '.'); ?></div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h3>Nova Meta</h3>
            <form action="meta.php" method="POST">
                <div class="mb-3"><label class="form-label">Nome da Meta</label><input type="text" name="nome" class="form-control" placeholder="Ex.: Comprar um PC" required></div>
                <div class="mb-3"><label class="form-label">Valor Objetivo</label><input type="number" step="0.01" min="0.01" name="valor_objetivo" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Valor Atual</label><input type="number" step="0.01" min="0" name="valor_atual" class="form-control" value="0" required></div>
                <div class="mb-3"><label class="form-label">Data Limite</label><input type="date" name="data_limite" class="form-control" required></div>
                <button type="submit" class="btn btn-primary">Cadastrar Meta</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h3 class="mb-3">Metas</h3>
            <?php if (count($metas) === 0): ?>
                <p class="mb-0">Nenhuma meta cadastrada.</p>
            <?php else: ?>
                <?php foreach ($metas as $dadosMeta): ?>
                    <?php
                    $meta = new Meta((int) $dadosMeta['id'], $dadosMeta['nome'], (float) $dadosMeta['valor_objetivo'], (float) $dadosMeta['valor_atual'], $dadosMeta['data_limite']);
                    $progresso = $meta->getProgresso();
                    ?>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between"><strong><?= htmlspecialchars($meta->getNome()); ?></strong><span><?= number_format($progresso, 1, ',', '.'); ?>%</span></div>
                        <div class="progress mt-2" style="height:20px"><div class="progress-bar" role="progressbar" style="width:<?= $progresso; ?>%"><?= number_format($progresso, 1, ',', '.'); ?>%</div></div>
                        <div class="mt-2">R$ <?= number_format($meta->getValorAtual(), 2, ',', '.'); ?> de R$ <?= number_format($meta->getValorObjetivo(), 2, ',', '.'); ?> — Limite: <?= htmlspecialchars($meta->getDataLimite()); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-body">
            <h3>Nova Transação</h3>
            <form action="processa.php" method="POST">
                <div class="mb-3"><label class="form-label">Tipo</label><select name="tipo" class="form-select" required><option value="receita">Receita</option><option value="despesa">Despesa</option></select></div>
                <div class="mb-3"><label class="form-label">Valor</label><input type="number" step="0.01" name="valor" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Descrição</label><input type="text" name="descricao" class="form-control" required></div>
                <div class="mb-3"><label class="form-label">Data</label><input type="date" name="data" class="form-control" value="<?= date('Y-m-d'); ?>" required></div>
                <button type="submit" class="btn btn-success">Cadastrar</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
    <div class="card-body">
        <h3>Cadastrar Usuário</h3>

        <form action="usuario.php" method="POST">

            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Sobrenome</label>
                <input type="text" name="sobrenome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nome de usuário</label>
                <input type="text" name="login" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Cadastrar Usuário
            </button>

        </form>
    </div>
</div>

    <div class="card mb-5">
        <div class="card-body">
            <h3 class="mb-3">Extrato</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Data</th><th>Descrição</th><th>Tipo</th><th>Valor</th><th>ID</th><th>Ações</th></tr></thead>
                    <tbody>
                    <?php foreach ($carteira->getHistorico() as $transacao): ?>
                        <tr>
                            <td><?= htmlspecialchars($transacao->getData()); ?></td>
                            <td><?= htmlspecialchars($transacao->getDescricao()); ?></td>
                            <td><?php if ($transacao->getTipo() === 'Entrada'): ?><span class="badge bg-success">Entrada</span><?php else: ?><span class="badge bg-danger">Saída</span><?php endif; ?></td>
                            <td>R$ <?= number_format($transacao->getValor(), 2, ',', '.'); ?></td>
                            <td><?= $transacao->getId(); ?></td>
                            <td>
                                <a href="editar.php?id=<?= $transacao->getId(); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <form action="delete.php" method="POST" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $transacao->getId(); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
const temaBtn = document.getElementById('temaBtn');
if (localStorage.getItem('tema') === 'dark') { document.body.classList.remove('bg-light'); document.body.classList.add('dark-mode'); temaBtn.innerHTML = 'Modo Claro'; }
temaBtn.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    if (document.body.classList.contains('dark-mode')) { document.body.classList.remove('bg-light'); localStorage.setItem('tema','dark'); temaBtn.innerHTML='Modo Claro'; }
    else { document.body.classList.add('bg-light'); localStorage.setItem('tema','light'); temaBtn.innerHTML='Modo Escuro'; }
});
</script>
</body>
</html>

<?php
session_start();
require_once __DIR__ . '/auth.php';
exigir_login();
require_once __DIR__ . '/conexao.php';

$sucesso = "";
$erro = "";

if (isset($_GET["excluir"])) {
    exigir_admin();
    try {
        $st = $pdo->prepare("DELETE FROM produto WHERE id_produto = ?");
        $st->execute([$_GET["excluir"]]);

        if ($st->rowCount() > 0) {
            $sucesso = "Produto removido com sucesso!";
        } else {
            $erro = "Não foi possível encontrar o produto!";
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1451) {       // código MySQL: registro possui dependentes (FK)
            $erro = "Este produto possui movimentações e não pode ser removido.";
        } else {
            throw $e;
        }
    }
}
if (isset($_GET["salvo"])) {
    $sucesso = "Produto salvo com sucesso!";
}

require_once __DIR__ . '/includes/menu.php';

$nomeBusca = trim($_GET["nome"] ?? "");
$dataBusca = trim($_GET["data"] ?? "");
$sql = "SELECT * FROM produto WHERE 1=1";
$parametros = [];
if ($nomeBusca !== "") { $sql .= " AND nome LIKE ?"; $parametros[] = "%" . $nomeBusca . "%"; }
if ($dataBusca !== "") { $sql .= " AND data_cadastro = ?"; $parametros[] = $dataBusca; }
$sql .= " ORDER BY nome";
$st = $pdo->prepare($sql);
$st->execute($parametros);
$produtos = $st->fetchAll();
?>
    <main class="flex-1 flex flex-col">

        <div class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-extrabold text-gray-800">Gerenciar Produtos</h1>
                <p class="text-sm text-gray-500">Cadastre, consulte e remova os produtos do estoque</p>
            </div>
            <a href="produto_form.php" class="bg-senai-green text-white font-bold px-4 py-2.5 rounded-lg text-sm hover:bg-green-600 transition flex items-center gap-2">
                + Novo Produto
            </a>
        </div>

        <div class="p-6 flex-1">

            <?php if (!empty($sucesso)): ?>
                <div class="bg-green-50 border border-green-300 text-green-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">✓</span>
                    <span><?= $sucesso ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="bg-red-50 border border-red-300 text-red-700 rounded-lg p-3 mb-5 flex items-center gap-2 text-sm">
                    <span class="font-bold text-base">!</span>
                    <span><?= $erro ?></span>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-700 text-lg leading-none">×</button>
                </div>
            <?php endif; ?>

            <form method="get" class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
                <div><label class="form-label">Nome</label><input class="form-input" name="nome" value="<?= htmlspecialchars($nomeBusca) ?>" placeholder="Buscar produto"></div>
                <div><label class="form-label">Data de cadastro</label><input class="form-input" type="date" name="data" value="<?= htmlspecialchars($dataBusca) ?>"></div>
                <button class="bg-senai-blue text-white px-4 py-2 rounded-lg text-sm">Filtrar</button>
                <a href="produtos.php" class="text-sm text-gray-500 px-2 py-2">Limpar</a>
            </form>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-senai-blue text-white">
                        <tr>
                            <th class="px-4 py-3 text-left">Produto</th>
                            <th class="px-4 py-3 text-center">Valor unitário</th>
                            <th class="px-4 py-3 text-center">Quantidade</th>
                            <th class="px-4 py-3 text-center">Valor total</th>
                            <th class="px-4 py-3 text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <?php foreach ($produtos as $p): ?>
                        <?php $valorTotal = round($p["valor_unitario"] * $p["quantidade_estoque"], 2); ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 text-gray-700 font-semibold"><?= htmlspecialchars($p["nome"]) ?></td>
                            <td class="px-4 py-3 text-center text-gray-600">R$ <?= number_format($p["valor_unitario"], 2, ",", ".") ?></td>
                            <td class="px-4 py-3 text-center text-gray-600"><?= $p["quantidade_estoque"] ?></td>
                            <td class="px-4 py-3 text-center text-gray-600 font-semibold">R$ <?= number_format($valorTotal, 2, ",", ".") ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="produto_form.php?id=<?= $p["id_produto"] ?>" class="bg-yellow-600 text-white text-xs px-2.5 py-1.5 rounded-md">Editar</a>
                                    <a href="produto.php?id=<?= $p["id_produto"] ?>" class="bg-senai-blue text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-senai-blue-dark transition" title="Ver produto">🔍 Ver</a>
                                    <a href="movimentacao_form.php?id_produto=<?= $p["id_produto"] ?>" class="bg-yellow-500 text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-yellow-600 transition" title="Movimentar estoque">🔄 Movimentar</a>
                                    <?php if (eh_admin()): ?>
                                    <a onclick="return confirm('Tem certeza disso?')" class="bg-senai-red text-white text-xs px-2.5 py-1.5 rounded-md hover:bg-red-700 transition" href="?excluir=<?= $p["id_produto"] ?>">Excluir</a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>

                <div class="border-t border-gray-100 px-4 py-3 bg-gray-50">
                    <p class="text-xs text-gray-400">Exibindo <?= count($produtos) ?> produtos</p>
                </div>
            </div>

        </div>
    </main>

</body>
</html>

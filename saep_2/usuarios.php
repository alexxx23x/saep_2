<?php
session_start();
require_once __DIR__ . '/auth.php';
exigir_admin();
require_once __DIR__ . '/conexao.php';
$erro = '';
$sucesso = '';
if (isset($_GET['excluir'])) {
    $id = (int) $_GET['excluir'];
    if ($id === (int) $_SESSION['id_user']) {
        $erro = 'Não é possível excluir o próprio usuário.';
    } else {
        $st = $pdo->prepare("DELETE FROM `user` WHERE id_user = ?");
        try { $st->execute([$id]); $sucesso = 'Usuário excluído.'; }
        catch (PDOException $e) { $erro = 'Não foi possível excluir este usuário.'; }
    }
}
$usuarios = $pdo->query("SELECT id_user, nome, login, perfil, ativo FROM `user` ORDER BY nome")->fetchAll();
require_once __DIR__ . '/includes/menu.php';
?>
<main class="flex-1">
<div class="bg-white border-b px-6 py-4 flex justify-between items-center"><div><h1 class="text-xl font-extrabold">Usuários</h1><p class="text-sm text-gray-500">Gerenciamento de acesso ao sistema</p></div><a href="usuario_form.php" class="bg-senai-green text-white px-4 py-2 rounded-lg text-sm font-bold">+ Novo usuário</a></div>
<div class="p-6">
<?php foreach (['erro'=>'red','sucesso'=>'green'] as $tipo=>$cor): if ($$tipo): ?><div class="bg-<?= $cor ?>-100 text-<?= $cor ?>-700 p-3 rounded mb-4"><?= htmlspecialchars($$tipo) ?></div><?php endif; endforeach; ?>
<div class="bg-white rounded-xl shadow-sm overflow-hidden"><table class="w-full text-sm"><thead class="bg-senai-blue text-white"><tr><th class="p-3 text-left">Nome</th><th class="p-3">Login</th><th class="p-3">Perfil</th><th class="p-3">Situação</th><th class="p-3">Ações</th></tr></thead><tbody>
<?php foreach ($usuarios as $u): ?><tr class="border-b"><td class="p-3"><?= htmlspecialchars($u['nome']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($u['login']) ?></td><td class="p-3 text-center"><?= htmlspecialchars($u['perfil']) ?></td><td class="p-3 text-center"><?= $u['ativo'] ? 'Ativo' : 'Inativo' ?></td><td class="p-3 text-center"><a class="text-blue-700" href="usuario_form.php?id=<?= $u['id_user'] ?>">Editar</a> <?php if ($u['id_user'] != $_SESSION['id_user']): ?><a class="text-red-700 ml-3" onclick="return confirm('Excluir usuário?')" href="?excluir=<?= $u['id_user'] ?>">Excluir</a><?php endif; ?></td></tr><?php endforeach; ?>
</tbody></table></div></div></main></body></html>

<?php
session_start();
require_once __DIR__ . '/auth.php';
exigir_admin();
require_once __DIR__ . '/conexao.php';
$id = (int) ($_GET['id'] ?? 0);
$nome = $login = '';
$perfil = 'OPERADOR';
$ativo = 1;
$erro = '';
if ($id) {
    $st = $pdo->prepare("SELECT * FROM `user` WHERE id_user = ?"); $st->execute([$id]); $u = $st->fetch();
    if (!$u) exit('Usuário não encontrado.');
    $nome=$u['nome']; $login=$u['login']; $perfil=$u['perfil']; $ativo=$u['ativo'];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome=trim($_POST['nome'] ?? ''); $login=trim($_POST['login'] ?? ''); $perfil=$_POST['perfil'] ?? 'OPERADOR'; $ativo=(int)($_POST['ativo'] ?? 1); $senha=$_POST['senha'] ?? '';
    if ($nome === '' || $login === '' || !in_array($perfil, ['ADMINISTRADOR','OPERADOR'], true)) $erro='Preencha os campos corretamente.';
    if (!$id && $senha === '') $erro='Informe uma senha para o novo usuário.';
    $st=$pdo->prepare("SELECT id_user FROM `user` WHERE login = ? AND id_user <> ?"); $st->execute([$login,$id]);
    if ($st->fetch()) $erro='Este login já está em uso.';
    if (!$erro) {
        if ($id && $senha === '') {
            $st=$pdo->prepare("UPDATE `user` SET nome=?, login=?, perfil=?, ativo=? WHERE id_user=?"); $st->execute([$nome,$login,$perfil,$ativo,$id]);
        } elseif ($id) {
            $st=$pdo->prepare("UPDATE `user` SET nome=?, login=?, senha=?, perfil=?, ativo=? WHERE id_user=?"); $st->execute([$nome,$login,password_hash($senha,PASSWORD_DEFAULT),$perfil,$ativo,$id]);
        } else {
            $st=$pdo->prepare("INSERT INTO `user` (nome,login,senha,perfil,ativo) VALUES (?,?,?,?,?)"); $st->execute([$nome,$login,password_hash($senha,PASSWORD_DEFAULT),$perfil,$ativo]);
        }
        header('Location: usuarios.php'); exit;
    }
}
require_once __DIR__ . '/includes/menu.php';
?>
<main class="flex-1"><div class="bg-white border-b px-6 py-4"><h1 class="text-xl font-extrabold"><?= $id ? 'Editar usuário' : 'Novo usuário' ?></h1></div><div class="p-6 max-w-xl">
<?php if ($erro): ?><div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
<form method="post" class="bg-white p-6 rounded-xl shadow-sm"><label class="form-label">Nome</label><input class="form-input mb-4" name="nome" maxlength="100" required value="<?= htmlspecialchars($nome) ?>"><label class="form-label">Login</label><input class="form-input mb-4" name="login" maxlength="50" required value="<?= htmlspecialchars($login) ?>"><label class="form-label">Senha <?= $id ? '(deixe em branco para manter)' : '' ?></label><input class="form-input mb-4" type="password" name="senha" <?= $id ? '' : 'required' ?>><label class="form-label">Perfil</label><select class="form-input mb-4" name="perfil"><option value="OPERADOR" <?= $perfil==='OPERADOR'?'selected':'' ?>>Operador de Almoxarifado</option><option value="ADMINISTRADOR" <?= $perfil==='ADMINISTRADOR'?'selected':'' ?>>Administrador do Sistema</option></select><label class="form-label">Situação</label><select class="form-input mb-6" name="ativo"><option value="1" <?= $ativo?'selected':'' ?>>Ativo</option><option value="0" <?= !$ativo?'selected':'' ?>>Inativo</option></select><button class="bg-senai-blue text-white px-5 py-2 rounded-lg">Salvar</button> <a href="usuarios.php" class="text-gray-600">Cancelar</a></form></div></main></body></html>

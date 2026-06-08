<?php
require_once __DIR__ . '/inc/auth.php';
require_manager();

$admin = is_admin();

$orders = db()->query(
    "SELECT z.*, s.fio AS klient, st.name AS status, pv.adres AS punkt
     FROM zakaz z
     JOIN sotrudnik s     ON s.id = z.id_client
     JOIN status_zakaza st ON st.id = z.id_status
     JOIN punkt_vidachi pv ON pv.id = z.id_punkt_vidachi
     ORDER BY z.id"
)->fetchAll();

$itemsByOrder = [];
$rows = db()->query(
    "SELECT zt.id_zakaz, t.artikul, zt.kol_vo
     FROM zakaz_tovar zt JOIN tovar t ON t.id = zt.id_tovar
     ORDER BY zt.id_zakaz, zt.id"
)->fetchAll();
foreach ($rows as $r) {
    $itemsByOrder[$r['id_zakaz']][] = $r['artikul'] . ' × ' . $r['kol_vo'];
}

$pageTitle = 'Заказы';
require __DIR__ . '/inc/header.php';
?>
<h2 class="page-title">Заказы</h2>

<?php if ($admin): ?>
    <p><a class="btn accent" href="order_form.php">➕ Добавить заказ</a></p>
<?php endif; ?>

<table class="grid">
    <thead>
    <tr>
        <th>№</th>
        <th>Состав (артикул × кол-во)</th>
        <th>Клиент</th>
        <th>Статус</th>
        <th>Пункт выдачи</th>
        <th>Дата заказа</th>
        <th>Дата выдачи</th>
        <th>Код</th>
        <?php if ($admin): ?><th>Действия</th><?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($orders as $o):
        $editUrl = $admin ? 'order_form.php?id=' . (int)$o['id'] : null; ?>
        <tr class="<?= $editUrl ? 'clickable' : '' ?>"
            <?= $editUrl ? 'onclick="if(!event.target.closest(\'a,button,form\'))location.href=\'' . $editUrl . '\'"' : '' ?>>
            <td><?= (int)$o['id'] ?></td>
            <td><?= e(implode(', ', $itemsByOrder[$o['id']] ?? [])) ?></td>
            <td><?= e($o['klient']) ?></td>
            <td><?= e($o['status']) ?></td>
            <td><?= e($o['punkt']) ?></td>
            <td><?= e($o['start_date']) ?></td>
            <td><?= e($o['finish_date']) ?></td>
            <td><?= e((string)$o['kod_polucheniya']) ?></td>
            <?php if ($admin): ?>
            <td>
                <a class="btn" href="order_form.php?id=<?= (int)$o['id'] ?>">✏️</a>
                <form method="post" action="order_delete.php" style="display:inline"
                      onsubmit="return confirm('Удалить заказ №<?= (int)$o['id'] ?>?')">
                    <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                    <button class="btn danger" type="submit">🗑</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    <?php if (!$orders): ?>
        <tr><td colspan="<?= $admin ? 9 : 8 ?>">Заказов нет.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/inc/footer.php'; ?>

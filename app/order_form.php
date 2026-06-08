<?php
require_once __DIR__ . '/inc/auth.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

if ($isEdit) {
    $st = db()->prepare('SELECT * FROM zakaz WHERE id = :id');
    $st->execute([':id' => $id]);
    $z = $st->fetch();
    if (!$z) {
        flash('error', 'Заказ не найден.');
        header('Location: orders.php');
        exit;
    }
    $st = db()->prepare('SELECT id_tovar, kol_vo FROM zakaz_tovar WHERE id_zakaz = :id ORDER BY id');
    $st->execute([':id' => $id]);
    $items = $st->fetchAll();
} else {
    $z = [
        'id' => null, 'start_date' => date('Y-m-d'), 'finish_date' => '',
        'id_punkt_vidachi' => '', 'id_client' => '', 'id_status' => '', 'kod_polucheniya' => '',
    ];
    $items = [];
}

$clients  = db()->query("SELECT id, fio FROM sotrudnik WHERE id_role = " . ROLE_CLIENT . " ORDER BY fio")->fetchAll();
$statuses = db()->query('SELECT id, name FROM status_zakaza ORDER BY id')->fetchAll();
$punkts   = db()->query('SELECT id, adres FROM punkt_vidachi ORDER BY id')->fetchAll();
$tovars   = db()->query('SELECT id, artikul, name FROM tovar ORDER BY artikul')->fetchAll();

function osel(array $rows, $sel, string $key): string
{
    $h = '';
    foreach ($rows as $r) {
        $s = ((string)$r['id'] === (string)$sel) ? ' selected' : '';
        $h .= '<option value="' . (int)$r['id'] . "\"$s>" . e((string)$r[$key]) . '</option>';
    }
    return $h;
}

$pageTitle = $isEdit ? 'Редактирование заказа' : 'Добавление заказа';
require __DIR__ . '/inc/header.php';
?>
<h2 class="page-title"><?= e($pageTitle) ?></h2>
<p><a class="btn" href="orders.php">← Назад к заказам</a></p>

<form class="card" method="post" action="order_save.php">
    <input type="hidden" name="id" value="<?= (int)($z['id'] ?? 0) ?>">

    <?php if ($isEdit): ?>
        <div class="field"><label>№ заказа</label>
            <input type="text" value="<?= (int)$z['id'] ?>" readonly></div>
    <?php endif; ?>

    <div class="field"><label>Клиент *</label>
        <select name="id_client" required><option value="">— выберите —</option><?= osel($clients, $z['id_client'], 'fio') ?></select></div>

    <div class="field"><label>Статус заказа *</label>
        <select name="id_status" required><?= osel($statuses, $z['id_status'], 'name') ?></select></div>

    <div class="field"><label>Адрес пункта выдачи *</label>
        <select name="id_punkt_vidachi" required><option value="">— выберите —</option><?= osel($punkts, $z['id_punkt_vidachi'], 'adres') ?></select></div>

    <div class="field"><label>Дата заказа *</label>
        <input type="date" name="start_date" required value="<?= e($z['start_date']) ?>"></div>

    <div class="field"><label>Дата выдачи</label>
        <input type="date" name="finish_date" value="<?= e($z['finish_date']) ?>"></div>

    <div class="field"><label>Код для получения *</label>
        <input type="number" name="kod_polucheniya" min="0" required value="<?= e((string)$z['kod_polucheniya']) ?>"></div>

    <div class="field">
        <label>Состав заказа (товар и количество)</label>
        <div id="items">
            <?php
            $render = function ($id_tovar = '', $kol = 1) use ($tovars) {
                $opts = '<option value="">— товар —</option>';
                foreach ($tovars as $t) {
                    $s = ((string)$t['id'] === (string)$id_tovar) ? ' selected' : '';
                    $opts .= '<option value="' . (int)$t['id'] . "\"$s>" . e($t['artikul'] . ' — ' . $t['name']) . '</option>';
                }
                return '<div class="item-row" style="display:flex;gap:8px;margin-bottom:6px">'
                    . '<select name="tovar_id[]" style="flex:1">' . $opts . '</select>'
                    . '<input type="number" name="tovar_qty[]" min="1" value="' . (int)$kol . '" style="width:90px" placeholder="кол-во">'
                    . '<button type="button" class="btn danger" onclick="this.parentNode.remove()">✕</button></div>';
            };
            if ($items) {
                foreach ($items as $it) echo $render($it['id_tovar'], $it['kol_vo']);
            } else {
                echo $render();
            }
            ?>
        </div>
        <button type="button" class="btn" onclick="addItem()">+ товар</button>
    </div>

    <div class="actions">
        <input class="btn accent" type="submit" value="<?= $isEdit ? 'Сохранить' : 'Добавить' ?>">
        <a class="btn" href="orders.php">Отмена</a>
    </div>
</form>

<script>
function addItem() {
    const tpl = document.querySelector('#items .item-row');
    const clone = tpl.cloneNode(true);
    clone.querySelectorAll('select,input').forEach(el => { if (el.tagName === 'INPUT') el.value = 1; else el.selectedIndex = 0; });
    document.getElementById('items').appendChild(clone);
}
</script>
<?php require __DIR__ . '/inc/footer.php'; ?>

<?php
require_once __DIR__ . '/inc/auth.php';

$manage = can_manage();
$admin  = is_admin();

$search   = $manage ? trim($_GET['q'] ?? '') : '';
$supplier = $manage ? trim($_GET['supplier'] ?? '') : '';
$sort     = $manage ? ($_GET['sort'] ?? '') : '';

$allowedSort = [
    'price_asc'  => 't.price ASC',
    'price_desc' => 't.price DESC',
    'qty_asc'    => 't.kol_vo ASC',
    'qty_desc'   => 't.kol_vo DESC',
];
$orderBy = $allowedSort[$sort] ?? 't.id ASC';

$where = [];
$params = [];
if ($search !== '') {

    $where[] = '(' . implode(' OR ', array_map(fn($c) => "$c ILIKE :q", [
        't.name', 't.artikul', 't.opisanie',
        'pr.name', 'ps.name', 'k.name', 'ei.name',
    ])) . ')';
    $params[':q'] = '%' . $search . '%';
}
if ($supplier !== '') {
    $where[] = 't.id_postavshik = :sup';
    $params[':sup'] = (int)$supplier;
}
$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$sql = "SELECT t.*, ei.name AS ed_izm, ps.name AS postavshik,
               pr.name AS proizvoditel, k.name AS kategoria
        FROM tovar t
        JOIN ed_izm ei       ON ei.id = t.id_ed_izm
        JOIN postavshik ps   ON ps.id = t.id_postavshik
        JOIN proizvoditel pr ON pr.id = t.id_proizvoditel
        JOIN kategoria k     ON k.id = t.id_kategoria
        $whereSql
        ORDER BY $orderBy";
$st = db()->prepare($sql);
$st->execute($params);
$products = $st->fetchAll();

$suppliers = db()->query('SELECT id, name FROM postavshik ORDER BY name')->fetchAll();

$pageTitle = 'Список товаров';
require __DIR__ . '/inc/header.php';
?>
<h2 class="page-title">Список товаров</h2>

<?php if ($manage): ?>
<form class="toolbar" method="get" id="filters">
    <input type="text" name="q" placeholder="Поиск по названию, артикулу, описанию…"
           value="<?= e($search) ?>" oninput="document.getElementById('filters').requestSubmit()">

    <label>Поставщик:
        <select name="supplier" onchange="document.getElementById('filters').requestSubmit()">
            <option value="">Все поставщики</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= (int)$s['id'] ?>" <?= $supplier == $s['id'] ? 'selected' : '' ?>>
                    <?= e($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>Сортировка:
        <select name="sort" onchange="document.getElementById('filters').requestSubmit()">
            <?php
            $opts = [
                ''           => 'без сортировки',
                'price_asc'  => 'цена ↑',
                'price_desc' => 'цена ↓',
                'qty_asc'    => 'кол-во на складе ↑',
                'qty_desc'   => 'кол-во на складе ↓',
            ];
            foreach ($opts as $v => $label):
            ?>
                <option value="<?= $v ?>" <?= $sort === $v ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <a class="btn" href="index.php">Сбросить</a>
</form>
<?php endif; ?>

<?php if ($admin): ?>
    <p><a class="btn accent" href="product_form.php">➕ Добавить товар</a></p>
<?php endif; ?>

<table class="grid">
    <thead>
    <tr>
        <th>Фото</th>
        <th>Наименование</th>
        <th>Категория</th>
        <th>Производитель</th>
        <th>Поставщик</th>
        <th>Цена</th>
        <th>Ед.</th>
        <th>На складе</th>
        <th>Скидка</th>
        <?php if ($admin): ?><th>Действия</th><?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php if (!$products): ?>
        <tr><td colspan="<?= $admin ? 10 : 9 ?>">Ничего не найдено.</td></tr>
    <?php endif; ?>
    <?php foreach ($products as $p):
        $skidka = (int)$p['skidka'];
        $price  = (float)$p['price'];
        $final  = round($price * (1 - $skidka / 100), 2);
        $noStock = (int)$p['kol_vo'] === 0;

        $rowClass = $noStock ? 'row-nostock' : ($skidka > 17 ? 'row-discount' : '');

        $img = 'assets/placeholder.png';
        if (!empty($p['foto']) && is_file(__DIR__ . '/uploads/' . $p['foto'])) {
            $img = 'uploads/' . rawurlencode($p['foto']);
        }
        $editUrl = $admin ? 'product_form.php?id=' . (int)$p['id'] : null;
    ?>
        <tr class="<?= $rowClass ?> <?= $editUrl ? 'clickable' : '' ?>"
            <?= $editUrl ? 'onclick="if(!event.target.closest(\'a,button,form\'))location.href=\'' . $editUrl . '\'"' : '' ?>>
            <td><img class="thumb" src="<?= $img ?>" alt=""></td>
            <td><?= e($p['name']) ?><br><small style="color:#777"><?= e($p['artikul']) ?></small></td>
            <td><?= e($p['kategoria']) ?></td>
            <td><?= e($p['proizvoditel']) ?></td>
            <td><?= e($p['postavshik']) ?></td>
            <td>
                <?php if ($skidka > 0): ?>
                    <span class="price-old"><?= number_format($price, 2, ',', ' ') ?></span>
                    <span class="price-new"><?= number_format($final, 2, ',', ' ') ?> ₽</span>
                <?php else: ?>
                    <span class="price-new"><?= number_format($price, 2, ',', ' ') ?> ₽</span>
                <?php endif; ?>
            </td>
            <td><?= e($p['ed_izm']) ?></td>
            <td><?= $noStock ? '<b>нет</b>' : (int)$p['kol_vo'] ?></td>
            <td><?= $skidka ?>%</td>
            <?php if ($admin): ?>
            <td>
                <a class="btn" href="product_form.php?id=<?= (int)$p['id'] ?>">✏️</a>
                <form method="post" action="product_delete.php" style="display:inline"
                      onsubmit="return confirm('Удалить товар «<?= e($p['name']) ?>»? Это действие необратимо.')">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <button class="btn danger" type="submit">🗑</button>
                </form>
            </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php require __DIR__ . '/inc/footer.php'; ?>

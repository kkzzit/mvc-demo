<div style="float: left">You have: <?= $money ?>$</div>

<h1>inventory</h1>

<table style="">
    <?php foreach ($itemlist as $item) { ?>
        <tr><td style="padding-bottom: 15px; vertical-align: top">

            <b><?= $item['itemName'] ?></b><?= $item['itemNum'] > 1 ? ' (' . $item['itemNum'] . ')' : '' ?>
            <?= $item['itemDescr'] ? '<div style="font-size: 12px">' . $item['itemDescr'] . '</div>' : '' ?>
            <?= (DEV === true) ? '<div style="font-size: 10px">IDX: ' . $item['idx'] . '</div>' : '' ?>

        </td></tr>
    <?php } ?>
</table>

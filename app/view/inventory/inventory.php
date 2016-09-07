<div class="pull-left">You have: <?= $money ?>$</div>

<?= $this->showTitle('INVENTORY') ?>

<table class="table table-condensed table-hover">
    <?php foreach ($itemlist as $item) { ?>
    <tr><td>

     <strong><?= $item['itemName'] ?></strong><?= $item['itemNum'] > 1 ? ' (' . $item['itemNum'] . ')' : '' ?>
     
     <?= $item['itemDescr'] ? '<div class="text-medium">' . $item['itemDescr'] . '</div>' : '' ?>

    </td></tr>
    <?php } ?>
</table>
<div class="pull-left">You have: <?= $money ?>$</div>

<?= $this->showTitle('INVENTORY') ?>

<div class="col-xs-4 col-xs-offset-1" style="margin-bottom: 30px">
    <table class="table table-condensed table-hover">
        <?php foreach (Inventory::$EQUIPMENT as $equip_key => $equip) { ?>
        <tr><td>

            <?php
            print $equip['name'] . ': ';

            if (array_key_exists($equip_key, $equipped) === true)
            {
                $i = $equipped[$equip_key];

                print '<strong>' . $this->linkCreate('inventory/useitem?item=' . $i['idx'], $i['itemName']) . '</strong>';
            }
            else
            {
                print '<i>none</i>';
            }
            ?>

        </td></tr>
        <?php } ?>
    </table>
</div>


<table class="table table-condensed table-hover">

    <?php foreach ($itemlist as $item) { ?>
    <tr><td class="col-xs-4">

        <strong><?= $item['itemType'] & 2 ? $this->linkCreate('inventory/useitem?item=' . $item['idx'], $item['itemName']) : $item['itemName'] ?></strong><?= $item['itemNum'] > 1 ? ' (' . $item['itemNum'] . ')' : '' ?>

    </td><td class="col-xs-8">

        <?= $item['itemDescr'] ? '<div class="text-medium">' . $item['itemDescr'] . '</div>' : '' ?>

    </td></tr>
    <?php } ?>
</table>

<div class="col-xs-4 col-xs-offset-1 pull-left" style="margin-bottom: 20px">
    <table class="table table-condensed table-hover">
        <?php foreach (Inventory::$EQUIPMENT as $equip_key => $equip) { ?>
        <tr><td>

            <?php
            print $equip['name'] . ': ';

            if (array_key_exists($equip_key, $equipped) === true)
            {
                $i = $equipped[$equip_key];

                print '<strong>' . $this->linkCreate('inventory/useitem/' . $i['idx'], $i['itemName']) . '</strong>';
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

<?= $this->showTitle('INVENTORY') ?>

<table class="table table-condensed table-hover">

    <tr><td colspan="2" style="padding: 8px 5px">

      You have: <?= $money ?>$

    </td></tr>

    <?php foreach ($itemlist as $item) { ?>
    <tr><td class="col-xs-4">

        <strong><?= $item['itemType'] & 2 ? $this->linkCreate('inventory/useitem/' . $item['idx'], $item['itemName']) : $item['itemName'] ?></strong><?= $item['itemNum'] > 1 ? ' (' . $item['itemNum'] . ')' : '' ?>

    </td><td class="col-xs-8">

        <?= $item['itemDescr'] ? '<div class="text-medium">' . $item['itemDescr'] . '</div>' : '' ?>

    </td></tr>
    <?php } ?>
</table>

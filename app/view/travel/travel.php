<div class="pull-left" style="margin: 0 0 20px 0">
    <?php if (isset($travel_info)) {?>
    Currently traveling to: <?= $travel_info['town_to_full'] ?> (<?= $travel_info['town_to'] ?>)<br>
    Time of arrival: <?= $travel_info['date_end'] ?><br>
    ETA: <?= $travel_info['eta'] ?> minute(s)<br>
    <?php } else { ?>
    Current location: <?= $curTownName ?>
    <?php } ?>
</div>


<?= $this->showTitle('TRAVEL') ?>


<table class="table table-hover travel<?= isset($travel_info) ? ' travel-disabled' : '' ?>">
    <?php foreach (TOWN as $townid => $town) { ?>
    <?php if ($this->Player->Location === $townid) continue ?>
    <tr><td class="col-xs-1 text-center goto">

      <?= $this->linkCreate('travel/travel/' . $townid, 'Travel', 'Travel', 'btn travel-btn') ?>

    </td><td class="col-xs-11 town">

      <strong><?= $town['name'] ?></strong><br>
      <small>Distance: <?= GameCore::TownTravelDistance($this->Player->Location, $townid) ?>km / Time: <?= GameCore::TownTravelTimeFormatted($this->Player->Location, $townid) ?></small>

    </td></tr>
    <?php } ?>
</table>

<?= $this->showTitle('RANKINGS') ?>

<table class="table table-hover ranking">
  <tr>
    <th class="col-xs-1"></th>
    <th class="col-xs-1"></th>
    <th class="col-xs-6"><?= Lang::Text('RANKING_NAME') ?></th>
    <th class="col-xs-1 col-sm-2"><?= Lang::Text('RANKING_LEVEL') ?></th>
    <th class="col-xs-3 col-sm-2"><?= Lang::Text('RANKING_EXP') ?></th>
  </tr>
  
  <?php foreach ($ranking as $player) { ?>
    <tr><td class="r">
      <?= $player['num'] ?>
    </td>
    
    <td>
      <?= $this->imgCreate('profiles/' . $player['pic'], false, 'icon') ?>
    </td>

    <td class="r">
      <?= $player['name'] ?>
    </td>

    <td class="r">
      <?= $player['level'] ?>
    </td>

    <td class="r">
      <div class="progress" title="<?= ($player['exp'] * 100) ?>%">
        <div class="progress-bar" role="progressbar" aria-valuenow="<?= ($player['exp'] * 100) ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= ($player['exp'] * 100) ?>%;">
          <span class="sr-only"><?= ($player['exp'] * 100) ?>%</span>
        </div>
      </div>
    </td></tr>
  <?php } ?>
    
</table>

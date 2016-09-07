<div class="pull-left">Available Skill Points: <?= $this->Player->SPoints ?></div>

<?= $this->showTitle('SKILLS') ?>

<table class="table table-hover skills">
    <?php foreach ($user_skills_list as $id => $skill) { ?>
        <tr><td class="col-xs-1 text-center skillup">

            <?= $this->linkCreate('skills/upgrade?skill=' . $id, '+', 'Skill Up', 'btn skillup-btn', false, false, ($skill['skillable'] === false ? 'disabled="disabled"' : false)); ?>

        </td><td class="col-xs-11">

          <strong><?= $skill['name'] ?></strong><br>
          Grade: <?= $skill['grade'] ?><br>
          <?php if ($skill['grade_next'] > 0) { ?>
          <div class="text-tiny<?= $skill['skillable'] === false ? ' red' : '' ?>">Next grade: level <?= $skill['grade_next'] ?></div>
          <?php } ?>

        </td></tr>
    <?php } ?>
</table>
<div class="pull-left">Available Skill Points: <?= $this->Player->SPoints ?></div>

<?= $this->showTitle('SKILLS') ?>

<table class="table table-hover skills">
    <?php foreach ($user_skills_list as $id => $skill) { ?>
        <tr><td class="col-xs-1 text-center skillup">

            <?= $this->linkCreate('skills/upgrade/' . $id, '+', 'Skill Up', 'btn skillup-btn', false, false, ($skill['skillable'] === false ? 'disabled="disabled"' : false)); ?>

        </td><td class="col-xs-3 text-center">

            <strong><?= $skill['name'] ?></strong><br>
            <div class="text-tiny">[ Grade <?= $skill['grade'] ?> ]</div>

        </td><td class="col-xs-4 small" style="vertical-align: middle">

            <?php
            if (!empty($skill['descr']))
            {
                print $skill['descr'] . '<br>';
            }

            foreach ($skill['stats'] as $stat => $val)
            {
                print ucwords($stat) . ': +' . $val;
            }
            ?>

        </td><td class="col-xs-4 text-tiny<?= $skill['skillable'] === false ? ' red' : '' ?>" style="vertical-align: middle">

            <?php
            if ($skill['grade_next'] > 0)
            {
                print '[ Next grade ]<br> Level ' . $skill['grade_next'] . '<br>';

                foreach ($skill['stats_next'] as $stat => $val)
                {
                    print ucwords($stat) . ': +' . $val;
                }
            }
            ?>

        </td></tr>
    <?php } ?>
</table>

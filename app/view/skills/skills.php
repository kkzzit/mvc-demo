<div style="float: left">Available Skill Points: <?= $this->Player->SPoints ?></div>

<h1>skills</h1>

<table style="width: 30%">
    <?php foreach ($user_skills_list as $id => $skill) { ?>
        <tr><td style="padding-bottom: 15px; vertical-align: top">

            <b><?= $skill['name'] ?></b><br />
            Grade: <?= $skill['grade'] ?><br />
            <div<?= $skill['skillable'] === false ? ' class="red"' : '' ?> style="font-size: 10px">Next grade: level <?= $skill['grade_next'] ?></div>

        </td><td style="vertical-align: middle">

            <?= $skill['skillable'] === true ? $this->linkCreate('skills/upgrade?skill=' . $id, '++', 'Skill Up') : '' ?>

        </td></tr>
    <?php } ?>
</table>

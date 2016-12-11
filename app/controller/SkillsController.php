<?php
class SkillsController extends Controller
{
    public function index()
    {
        $this->User->isNotLoggedThrowLogin();

        $user_skills = Skill::getUserSkills($this->User->IDX);

        /*
         * Create Player's list of Skills
         */
        $user_skills_list = [];
        foreach (Skill::$SKILL as $id => $skill)
        {

            $grade = $user_skills[$id]['skillGrade'] ?? 0;          // curent skill grade
            $grade_next = $skill['grades'][$grade] ?? 0;            // next grade level req
            $skillable = $grade_next <= $this->Player->Level && $grade_next > 0 ? true : false;
            $descr = '';
            $stats = [];
            $stats_next = [];
            $vars = [];

            foreach ($skill as $isvar => $val)
            {
                if (substr($isvar, 0, 5) == 'stat_')
                {
                    $stat = substr($isvar, 5, 10);

                    $stats[$stat] = $skill['stat_' . $stat][$grade - 1];

                    if ($grade_next > 0)
                        $stats_next[$stat] = $skill['stat_' . $stat][$grade];
                }
                else if (substr($isvar, 0, 3) == 'var')
                {
                    // add potentional logic for handling variables in skill descr (possibly extend to other skill vars to allow math in them)
                    /*
                    if (preg_match("/\[(\w+)\]/i", $val, $t))
                    {

                    }
                     *
                    $x = preg_replace("/\[(\w+)\]/i", "$\1 = 2", $val);

                    // TODO: a lot.
                    $x = preg_replace_callback(
                        "/\[(\w+)\]/i",
                        function ($matches) use ($grade, $grade_next)
                        {
                            return ${$matches[1]};
                        },
                        $val
                    );

                    //print $x;
                    */

                    array_push($vars, $x ?? $val);
                }
                else
                {
                    continue;
                }
            }

            if ($skill['descr'])
            {
                $descr = vsprintf($skill['descr'], $vars);
            }

            $user_skills_list[$id] = [
                'name' => $skill['name'],
                'descr' => $descr,
                'grade' => $grade,
                'grade_next' => $grade_next,
                'stats' => $stats,
                'stats_next' => $stats_next,
                'skillable' => $skillable
            ];
        }

        require APP_VIEW . 'header.php';
        require APP_VIEW . 'skills/skills.php';
        require APP_VIEW . 'footer.php';

    }

    public function upgrade()
    {
        $this->User->isNotLoggedThrowLogin();

        $skillid = isset($_GET['skill']) && is_numeric($_GET['skill']) ? $_GET['skill'] : 0;

        Skill::upSkill($this->User->IDX, $skillid);

        //Redirect::to('skills');
    }
}

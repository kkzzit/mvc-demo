<?php
class SkillsController extends Controller
{
    public function index()
    {
        $this->User->isNotLoggedThrowLogin();
        
        $this->newModel('Skill');
        
        $user_skills = Skill::getUserSkills($this->User->IDX);
        
        /*
         * User's list of Skills
         */
        $user_skills_list = [];
        foreach (Skill::$SKILL as $id => $skill)
        {
            $skillgrade = $user_skills[$id]['skillGrade'] ?? 0; // curent skill grade
            $nextgrade = $skill['grades'][$skillgrade] ?? 0; // next grade level req
            $skillable = $nextgrade <= $this->Player->Level && $nextgrade > 0 ? true : false;
            
            $user_skills_list[$id] = [
                'name' => $skill['name'],
                'grade' => $skillgrade,
                'grade_next' => $nextgrade,
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
        
        $this->newModel('Skill');
        
        Skill::upSkill($this->User->IDX, $skillid);
        
        Redirect::to('skills');
    }
}

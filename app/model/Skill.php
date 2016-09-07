<?php
class Skill extends Model
{
    /*
     * List of all available skills
     * TODO: maybe move this to some kind of global game config
     */
    public static $SKILL = [
        1 => [
            'name' => 'Preservation',
            'grades' => [5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
            ],
        2 => [
            'name' => 'testi',
            'grades' => [7, 9, 12, 15, 20]
        ]
    ];
    
    /*
     * Get all Skills of a Player
     * Create an array with skillid => (grade, ...)
     * @param int $useridx
     */
    public static function getUserSkills(int $useridx): array
    {
        $skills = [];
        
        self::query('SELECT skillId, skillGrade FROM skill WHERE uIdx = :useridx');
        self::bind(':useridx', $useridx);
        //$skills = self::fetch();
        
        foreach (self::fetch() as $skill)
        {
            $skillid = $skill['skillId'];
            array_shift($skill);
            
            $skills[$skillid] = [];
            
            foreach ($skill as $key => $val)
            {
               $skills[$skillid][$key] = $val; 
            }
        }
        
        return $skills;
    }
    
    /*
     * Get skill grade of the specified skill
     * @param int $useridx
     * @param int $skillid
     */
    public static function getSkillGrade(int $useridx, int $skillid): int
    {
        self::query('SELECT skillGrade FROM skill WHERE uIdx = :useridx AND skillId = :skillid');
        self::bind(':useridx', $useridx);
        self::bind(':skillid', $skillid);
        
        if (is_array($skill = self::fetchSingle()) === true)
        {
            return $skill['skillGrade'];
        }
        else
        {
            return 0;
        }
    }
    
    /*
     * Skill up a skill
     * @param int $useridx
     * @param int $skillid
     */
    public static function upSkill(int $useridx, int $skillid)
    {
        /*
         * Check if skill exists, exit if not
         */
        if (array_key_exists($skillid, self::$SKILL) === false) return false;
        
        $curgrade = self::getSkillGrade($useridx, $skillid);
        
        self::query('SELECT level, spoints FROM player WHERE uIdx = :useridx');
        self::bind(':useridx', $useridx);
        if (is_array($player = self::fetchSingle()) === false) return false;
        
        
        if ($curgrade + 1 > count(self::$SKILL[$skillid]['grades']))
        {
            ErrorMessage::set('ERROR_MESSAGE_SKILL_GRADEMAX');
        }
        else if ($player['level'] < self::$SKILL[$skillid]['grades'][$curgrade])
        {
            ErrorMessage::set('ERROR_MESSAGE_SKILL_LEVEL');
        }
        else if ($player['spoints'] < 1) {
            ErrorMessage::set('ERROR_MESSAGE_SKILL_SPOINTS');
        }
        else
        {
            if ($curgrade > 0)
            {
                self::query('UPDATE skill SET skillGrade = skillGrade + 1 WHERE uidx = :useridx AND skillId = :skillid');
                self::bind(':useridx', $useridx);
                self::bind(':skillid', $skillid);
                self::execute();
            }
            else
            {
                self::query('INSERT INTO skill (skillId, skillGrade, uidx) VALUES (:skillid, 1, :useridx)');
                self::bind(':useridx', $useridx);
                self::bind(':skillid', $skillid);
                self::execute();
            }
            
            self::query('UPDATE player SET spoints = spoints - 1 WHERE uidx = :useridx');
            self::bind(':useridx', $useridx);
            self::execute();
        }
    }
}
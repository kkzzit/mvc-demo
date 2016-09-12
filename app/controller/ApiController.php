<?php
require(APP . 'core/model.php');
        
class ApiData extends Model
{
    public function getRanking()
    {
        $ranking = [];
        
        self::query('SELECT player.name, player.level, player.exp, user.picture FROM player LEFT JOIN user ON (player.uidx = user.idx) ORDER BY player.level DESC, player.exp DESC, user.creation_date LIMIT 10');
        $ranking = self::fetch();
        
        return $ranking;
    }
}

class ApiController
{   
    private $DB = null;
    
    public function __construct()
    {
        $this->DB = new ApiData();
        
        header('Content-Type: application/json');
    }
    
    public function ranking()
    {
        $json = ['ranking'];
        
        $num = 1;
        foreach ($this->DB->getRanking() as $player)
        {
            $lvl = $player['level'];
            $exp = $lvl < 20 ? round($player['exp'] / EXP[$lvl], 2, PHP_ROUND_HALF_DOWN) : 0;
            $pic = $player['picture'] ?? 'noicon';
            $pic .= '.png';
            
            $json[$num]['num'] = $num;
            $json[$num]['name'] = $player['name'];
            $json[$num]['pic'] = $pic;
            $json[$num]['level'] = $lvl;
            $json[$num]['exp'] = $exp;
            
            $num++;
        }
        
        print json_encode($json);
    }
}
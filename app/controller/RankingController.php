<?php
class RankingController extends Controller
{
    public function index()
    {
        $ranking = $this->getRanking();
        
        require APP_VIEW . 'header.php';
        require APP_VIEW . 'ranking/ranking.php';
        require APP_VIEW . 'footer.php';
    }
    
    private function getRanking()
    {
        $json_string = file_get_contents("https://mvc.kalranking.com/api/ranking");
        $ranking = json_decode($json_string, true);
        
        array_shift($ranking);
        
        return $ranking;
    }
}
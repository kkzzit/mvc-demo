<?php
class TravelController extends Controller
{
    public function index()
    {
        $this->User->isNotLoggedThrowLogin();

        if ($this->Player->hasStatus(1) === true)
        {
            $travel = self::newModel('TravelModel', $this->Player);
            $travel_info = $travel->getTravelInfo();
        }

        if ($this->Player->Location === null)
            $curTownName = '?';
        else
            $curTownName = TOWN[$this->Player->Location]['name'];


        require APP_VIEW . 'header.php';
        require APP_VIEW . 'travel/travel.php';
        require APP_VIEW . 'footer.php';
    }

    public function travel($town)
    {
        $this->User->isNotLoggedEXIT();

        $doTravel = self::newModel('TravelModel', $this->Player);
        $doTravel->startTravel($town);

        Redirect::to('travel');
    }
}

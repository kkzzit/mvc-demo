<?php
class TravelModel extends Model
{
    private $Player = null;

    public function __construct(Player $p)
    {
        $this->Player = $p;
    }

    /*
     * Start a travel
     * @param string $town Destination town
     */
    public function travelTo($town)
    {
        $p = $this->Player;

        // Check if the Player is valid and destination Town exists
        if ($p->IDX <= 0 || !array_key_exists($town, TOWN)) return false;

        // Check if Player is already traveling and exit if he is
        if ($p->hasStatus(1) === true)
        {
            ErrorMessage::add('Already traveling');
            return false;
        }

        // Check if destination is different from current location
        if ($town === $p->Location)
        {
            ErrorMessage::add('Cannot travel to where you already are');
            return false;
        }

        // Consume 1 energy for traveling, or exit if Player has no energy
        if ($p->energy(-1) === false)
        {
            ErrorMessage::add('Not enough energy');
            return false;
        }

        $dst = TOWN[$town];
        $travelTime = GameCore::TownTravelTime($p->Location, $town);

        // Insert travel data into the DB
        self::query('INSERT INTO travel (uIdx, from_x, from_y, to_x, to_y, town_from, town_to, date_start, date_end, status) VALUES (:useridx, :pX, :pY, :dstX, :dstY, :pTown, :dstTown, NOW(), NOW() + INTERVAL :travelTime MINUTE, 1)');
        self::bind(':useridx', $p->IDX);
        self::bind(':pX', $p->X);
        self::bind(':pY', $p->Y);
        self::bind(':dstX', $dst['x']);
        self::bind(':dstY', $dst['y']);
        self::bind(':pTown', $p->Location);
        self::bind(':dstTown', $town);
        self::bind(':travelTime', $travelTime);
        self::execute();

        // Change Player status to "in travel"
        $p->updateStatus(1);

        ErrorMessage::add('Traveling to ' . $dst['name']);
    }

    /*
     * Check if Player completed his travel
     */
    public function checkTravelComplete()
    {
        $p = $this->Player;

        // Check if the Player is valid and is currently traveling
        if ($p->IDX <= 0 || $p->hasStatus(1) === false) return false;

        // Player should be traveling - check current Travel progress
        self::query('SELECT * FROM travel WHERE uIdx = :useridx AND status = 1 ORDER BY date_start ASC LIMIT 1');
        self::bind(':useridx', $p->IDX);

        if (is_array($travel = self::fetchSingle()) === true)
        {
            // Travel found
            $date_now = new DateTime();
            $date_end = new DateTime($travel['date_end']);

            // Check if travel is completed
            if (($date_end->getTimestamp() - $date_now->getTimestamp()) / 60 < 0)
            {
                // Complete the travel
                self::query('UPDATE travel SET status = 0 WHERE idx = :idx AND uIdx = :useridx LIMIT 1');
                self::bind(':idx', $travel['idx']);
                self::bind(':useridx', $p->IDX);
                self::execute();

                // Change Player status to "not in travel"
                $p->updateStatus(-1);

                // Grant EXP award
                $p->exp(100);

                ErrorMessage::add('You have reached your destination and were awarded 100 EXP!');
            }
        }
        else
        {
            // If status was set to "in travel", but there is no actual travel taking place set status to "not in travel"        NOTE: possibly unnecessary
            $p->updateStatus(-1);
        }
    }

    /*
     * Get details about Player's travel
     */
    public function getTravelInfo(): array
    {
        $info = [
            'town_to' => '???',
            'town_to_full' => '?',
            'date_end' => '0:00',
            'eta' => 0
        ];

        // Check if the Player is valid and is currently traveling
        if ($this->Player->IDX > 0 && $this->Player->hasStatus(1) === true)
        {
            // Player should be traveling - check current Travel progress
            self::query('SELECT * FROM travel WHERE uIdx = :useridx AND status = 1 ORDER BY date_start ASC LIMIT 1');
            self::bind(':useridx', $this->Player->IDX);

            if (is_array($travel = self::fetchSingle()) === true)
            {
                $date_end = new DateTime($travel['date_end']);

                $info['town_to'] = $travel['town_to'];
                $info['town_to_full'] = TOWN[$travel['town_to']]['name'];
                $info['date_end'] = $date_end->format('H:i');
                $info['eta'] = 0;      // TODO: make the ETA work; use similar concept to that in checkTravelComplete()
            }
        }

        return $info;
    }
}

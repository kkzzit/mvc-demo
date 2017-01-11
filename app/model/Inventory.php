<?php
/*
 * require Item.php since it is necessary for certain functions of the inventory to work
 */
require(APP . 'model/Item.php');

/*
 * Inventory class
 * itemIdx - unique inventory item id
 * itemId - item id, identifies the item
 * itemId 0-10 reserved for currencies:
 *   1 = dollar = current money, handled as money
 *   2-10 = other currencies = unusable money, handled as items
 * itemStatus - item information (bitwise):
 *   1 = equipped
 *
 * Currently equipped items should be loaded at the beginning here, to acquire those item's stats and load them into Player stats
 */
class Inventory extends Model
{
    /*
     * Equipment structure:
     *   id => [name, icon, ...]
     */
    public static $EQUIPMENT = [
        1 => ['name' => 'Wheel'],
        2 => ['name' => 'Helmet'],
        3 => ['name' => 'Backpack']
    ];

    /*
     * Return the amount of money a Player has
     * @param int $useridx
     */
    public static function getMoney(int $useridx): int
    {
        $money = 0;

        self::query('SELECT itemNum FROM inventory WHERE uIdx = :useridx AND itemId = 1');
        self::bind(':useridx', $useridx);

        if (is_array($item = self::fetchSingle()) === true)
        {
            $money = $item['itemNum'];
        }

        return $money;
    }

    /*
     * Get Player's item inventory
     * @param int $useridx
     */
    public static function getInventory(int $useridx): array
    {
        $items = [];

        self::query('SELECT inventory.idx, inventory.itemId, inventory.itemNum, inventory.itemStatus, IFNULL(item.name,\'NULL\') AS itemName, item.description as itemDescr, item.type as itemType, item.slot as itemSlot FROM inventory LEFT JOIN item ON inventory.itemId = item.id WHERE inventory.uIdx = :useridx AND item.id > 10 ORDER BY inventory.idx ASC');
        self::bind(':useridx', $useridx);
        $items = self::fetch();

        return $items;
    }

    /*
     * Get an item's IDX by useridx and itemid
     * Returns 0 if Player doesn't own the item
     * @param int $useridx
     * @param int $userid
     */
    public static function getItemIDX(int $useridx, int $itemid): int
    {
        self::query('SELECT idx FROM inventory WHERE uIdx = :useridx AND itemId = :itemid');
        self::bind(':useridx', $useridx);
        self::bind(':itemid', $itemid);

        if (is_array($item = self::fetchSingle()) === true)
        {
            return $item['idx'];
        }
        else
        {
            return 0;
        }
    }

    /*
     * Checks if Player has X item in database
     * CONSIDER: delete 0 amount items periodically
     * REDUNDANT
     *
     *
    public static function checkUserHasItemId(int $useridx, int $itemid): bool
    {
        self::query('SELECT itemNum FROM inventory WHERE uidx = :useridx AND itemId = :itemid');
        self::bind(':useridx', $useridx);
        self::bind(':itemid', $itemid);

        if (is_array($item = self::fetchSingle()) === true)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
     * Return how many of X item the player has
     * TODO: create a variation that counts via idx not Id
     */
    public static function getItemNum(int $useridx, int $itemid): int
    {
        self::query('SELECT itemNum FROM inventory WHERE uIdx = :useridx AND itemId = :itemid');
        self::bind(':useridx', $useridx);
        self::bind(':itemid', $itemid);

        if (is_array($item = self::fetchSingle()) === true)
        {
            return $item['itemNum'];
        }
        else
        {
            return 0;
        }
    }

    /*
     * Use an item from Player's inventory
     * Could be altered to first try and eat an item, if that fails check if it's a wearable
     *
     */
    public static function useItem(int $useridx, int $itemidx)
    {
        self::query('SELECT * FROM inventory INNER JOIN item ON inventory.itemId = item.id WHERE inventory.uIdx = :useridx AND inventory.idx = :itemidx AND inventory.itemNum > 0 AND item.type & 2');
        self::bind(':useridx', $useridx);
        self::bind(':itemidx', $itemidx);

        if (is_array($item = self::fetchSingle()) === true)
        {
            // Item is wearable
            if ($item['slot'] > 0)
            {
                // If equipped then unequip
                if ($item['itemStatus'] & 1)
                {
                    self::query('UPDATE inventory SET itemStatus = itemStatus ^ 1 WHERE idx = :itemidx AND uIdx = :useridx');
                    self::bind(':itemidx', $itemidx);
                    self::bind(':useridx', $useridx);
                    self::execute();

                    ErrorMessage::add($item['name'] . ' unequipped.');
                }
                // Otherwise equip (but first unequip any other equipped item in that slot)
                else
                {
                    self::query('UPDATE inventory INNER JOIN item ON item.id = inventory.itemId SET itemStatus = itemStatus ^ 1 WHERE inventory.uIdx = :useridx AND item.slot = :itemslot AND inventory.itemStatus & 1');
                    self::bind(':useridx', $useridx);
                    self::bind(':itemslot', $item['slot']);
                    self::execute();

                    // if (self::rowCount() > 0) ErrorMessage::add('Unequipped whatever was in this slot.');

                    self::query('UPDATE inventory SET itemStatus = itemStatus ^ 1 WHERE idx = :itemidx AND uIdx = :useridx');
                    self::bind(':itemidx', $itemidx);
                    self::bind(':useridx', $useridx);
                    self::execute();

                    ErrorMessage::add($item['name'] . ' equipped.');
                }
            }
            // Item is edible
            else
            {
                self::query('UPDATE inventory SET itemNum = itemNum - 1 WHERE idx = :itemidx AND uIdx = :useridx');
                self::bind(':itemidx', $itemidx);
                self::bind(':useridx', $useridx);
                self::execute();

                /*
                 * Change to a more general approach later:
                 * Create some kind of global handler for item stats (especially equipped ones), but for edibles the stats will work as regen/heal/etc values
                 * Item stats will be held in the Item database
                 */
                $p = new Player($useridx);

                switch ($item['id'])
                {
                    // Item: Tylenol
                    case 20:
                        $p->hp(200);

                        //ErrorMessage::add('HP increased by 200.');
                        break;

                    // Item: Big Tylenol
                    case 21:
                        $p->hp(500);

                        //ErrorMessage::add('HP increased by 500.');
                        break;

                    default:
                        break;
                }

                ErrorMessage::add($item['name'] . ' consumed.');
            }
        }
    }

    /*
     * Add an item to Player's inventory
     * @param int $useridx
     * @param int $itemid
     * @param int $itemnum Amount
     */
    public static function addItem(int $useridx, int $itemid, int $itemnum)
    {
        if ($itemid <= 0 || $itemnum <= 0 || $useridx <= 0) return false;

        $itemidx = self::getItemIDX($useridx, $itemid);

        /*
         * Check if Player has that item already and if it's stackable (&1 = non-stackable)
         * If yes + yes, then update the amount
         * Otherwise insert the item
         *
         * Multiple non-stackable items insert implementation added
         */
        if ($itemidx > 0 && Item::isType($itemid, 1) === false)
        {
            self::query('UPDATE inventory SET itemNum = itemNum + :itemnum WHERE idx = :itemidx AND uidx = :useridx');
            self::bind(':itemnum', $itemnum);
            self::bind(':itemidx', $itemidx);
            self::bind(':useridx', $useridx);
            self::execute();
        }
        else if (Item::isType($itemid, 1) === true && $itemnum > 1)
        {
            self::beginTransaction();
            self::query('INSERT INTO inventory (uIdx, itemId, itemNum) VALUES (:useridx, :itemid, :itemnum)');
            self::bind(':useridx', $useridx);
            self::bind(':itemid', $itemid);
            self::bind(':itemnum', 1);
            self::executeMultiple($itemnum);
            self::endTransaction();
        }
        else
        {
            self::query('INSERT INTO inventory (uIdx, itemId, itemNum) VALUES (:useridx, :itemid, :itemnum)');
            self::bind(':useridx', $useridx);
            self::bind(':itemid', $itemid);
            self::bind(':itemnum', $itemnum);
            self::execute();
        }
    }
}

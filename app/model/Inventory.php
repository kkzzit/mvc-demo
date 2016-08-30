<?php
/*
 * require Item.php since it is necessary for certain functions of inventory to work
 */
require(APP . 'model/Item.php');

/*
 * Inventory class
 * itemIdx - unique inventory item id
 * itemId - item id, identifies the item
 * itemId 0-10 reserves for currencies:
 *  1 = dollar = current money, handled as money
 *  2-10 = other currencies = unusable money, handled as items
 */
class Inventory extends Model
{
    /*
     * Return the amount of money a Player has
     * @param int $useridx
     */
    public static function returnMoney(int $useridx): int
    {
        $money = 0;
        
        self::query('SELECT itemNum FROM inventory WHERE uidx = :useridx AND itemId = 1');
        self::bind(':useridx', $useridx);
        
        if (is_array($item = self::fetchSingle()) === true)
        {
            $money = $item['itemNum'];
        }
        
        return $money;
    }
    
    /*
     * Add money
     * REDUNDANT - USE addItem()
     *
    public static function addMoney(int $useridx, int $amount)
    {
        if ($amount <= 0) return false;
        
        $itemidx = self::getItemIDX($useridx, 1);
        
        if ($itemidx > 0)
        {
            self::query('UPDATE inventory SET itemNum = itemNum + :amount WHERE idx = :itemidx');
            self::bind(':amount', $amount);
            self::bind(':itemidx', $itemidx);
            self::execute();
        }
        else
        {
            self::query('INSERT INTO inventory (uIdx, itemId, itemNum) VALUES (:useridx, :itemid, :amount)');
            self::bind(':useridx', $useridx);
            self::bind(':itemid', 1);
            self::bind(':amount', $amount);
            self::execute();
        }
    }
    
    /*
     * Get Player's item inventory
     * @param int $useridx
     */
    public static function listItems(int $useridx): array
    {
        $items = [];
        
        self::query('SELECT inventory.idx, inventory.itemId, inventory.itemNum, IFNULL(item.name,\'NULL\') AS itemName, item.description as itemDescr FROM inventory LEFT JOIN item ON (inventory.itemId=item.id) WHERE uidx = :useridx AND itemId > 10');
        self::bind(':useridx', $useridx);
        $items = self::fetch();
        
        return $items;
    }
    
    /*
     * Get an item's IDX by useridx and itemid
     * @param int $useridx
     * @param int $userid
     */
    public static function getItemIDX(int $useridx, int $itemid): int
    {
        self::query('SELECT idx FROM inventory WHERE uidx = :useridx AND itemId = :itemid');
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
     */
    public static function getItemNum(int $useridx, int $itemid): int
    {
        self::query('SELECT itemNum FROM inventory WHERE uidx = :useridx AND itemId = :itemid');
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
     * Add an item to Player's inventory
     * @param int $useridx
     * @param int $itemid
     * @param int $itemnum Amount
     */
    public static function addItem(int $useridx, int $itemid, int $itemnum)
    {
        if ($itemid <= 0 || $itemnum <= 0) return false;
        
        $itemidx = self::getItemIDX($useridx, $itemid);
        
        /*
         * Check if Player has that item already and if it's stackable (&1 = non-stackable)
         * If yes + yes, then update the amount
         * Otherwise insert the item
         */
        /*
        if ($itemidx > 0 && Item::checkType($itemid, 1) === false)
        {
            self::query('UPDATE inventory SET itemNum = itemNum + :itemnum WHERE idx = :itemidx');
            self::bind(':itemnum', $itemnum);
            self::bind(':itemidx', $itemidx);
            self::execute();
        }
        else
        {
            self::query('INSERT INTO inventory (uIdx, itemId, itemNum) VALUES (:useridx, :itemid, :itemnum)');
            self::bind(':useridx', $useridx);
            self::bind(':itemid', $itemid);
            self::bind(':itemnum', $itemnum);
            self::execute();
        }
         * 
         */
        
        /*
         * Multiple non-stackable items insert implementation added
         */
        if ($itemidx > 0 && Item::checkType($itemid, 1) === false)
        {
            self::query('UPDATE inventory SET itemNum = itemNum + :itemnum WHERE idx = :itemidx AND uidx = :useridx');
            self::bind(':itemnum', $itemnum);
            self::bind(':itemidx', $itemidx);
            self::bind(':useridx', $useridx);
            self::execute();
        }
        else if (Item::checkType($itemid, 1) === true && $itemnum > 1)
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

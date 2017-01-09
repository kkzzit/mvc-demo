<?php
/*
 * Item handles Item names, types, base stats, etc.
 * Consider saving these as constants (enum-like)
 * Types (bitwise):
 *   0 = normal
 *   1 = non-stackable
 *   2 = usable (if slot > 0 then wearable)
 */
class Item extends Model
{
    /*
     * Static list of items
     * Considering using instead of the Item table in the DB
     *
    private static $ITEM = [
        [
            'idx' => 11,
            'name' => 11,
            'type' => 0
        ],
        [
            'idx' => 12,
            'name' => 12,
            'name' => 12,
            'type' => 0
        ],
        [
            'idx' => 13,
            'name' => 13,
            'type' => 1
        ],
    ]
     *
     */

    /*
     * MOST LIKELY OBSOLETE
     *
    public static function getItemList(): array
    {
        $items = [];

        self::query('SELECT id, name FROM item ORDER BY id ASC');
        $items = self::fetch();

        return $items;
    }
    */

    /*
     * Get Item data
     * @param @itemid
     */
    public static function getItem(int $itemid): array
    {
        $item = [];

        self::query('SELECT * FROM item WHERE id = :itemid');
        self::bind(':itemid', $itemid);
        $item = self::fetchSingle();

        return $item;
    }

    /*
     * Returns an Item's name
     * @param $itemid
     */
    public static function getItemName(int $itemid): string
    {
        self::query('SELECT name FROM item WHERE id = :itemid');
        self::bind(':itemid', $itemid);

        if (is_array($name = self::fetchSingle()) === true)
        {
            return $name;
        }
        else
        {
            return '[NULL]';
        }
    }

    /*
     * Checks whether an item (id) has a certain type (bitwise)
     * &1 = non-stackable
     * @param int $itemid
     * @param int $type Type to check
     */
    public static function isType(int $itemid, int $type): bool
    {
        self::query('SELECT type FROM item WHERE id = :itemid');
        self::bind(':itemid', $itemid);

        if (is_array($item = self::fetchSingle()) === true)
        {
            if ($item['type'] & $type)
            {
                return true;
            }
        }

        return false;
    }
}

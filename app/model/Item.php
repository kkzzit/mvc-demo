<?php
/*
 * Item handles Item names, types, base stats, etc.
 * Types (bitwise):
 *   0 = normal
 *   1 = non-stackable
 *   2 = usable (if slot > 0 then wearable)
 */
class Item extends Model
{
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
            return "[NULL]";
        }
    }

    /*
     * Checks whether an item (id) has a certain type (bitwise)
     * &1 = non-stackable
     * @param $itemid
     * @param $type Type to check
     */
    public static function isType(int $itemid, int $type): bool
    {
        $ret = false;

        self::query('SELECT type FROM item WHERE id = :itemid');
        self::bind(':itemid', $itemid);

        if (is_array($item = self::fetchSingle()) === true)
        {
            if ($item['type'] & $type)
            {
                $ret = true;
            }
        }

        return $ret;
    }
}
